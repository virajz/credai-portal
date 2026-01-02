<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Project;
use Prism\Prism\Facades\Prism;
use Prism\Prism\Facades\Tool;

class ChatService
{
    public function chat(string $message, array $conversationHistory = []): array
    {
        // Extract search parameters from the user message
        $searchResults = $this->searchPropertiesFromMessage($message);

        // Build context with search results
        $contextPrompt = $this->buildPromptWithResults($message, $searchResults, $conversationHistory);

        $response = Prism::text()
            ->using('groq', 'llama-3.3-70b-versatile')
            ->withSystemPrompt($this->getSystemPrompt())
            ->withPrompt($contextPrompt)
            ->asText();

        return [
            'response' => $response->text,
            'properties' => $searchResults,
            'tool_calls' => [],
        ];
    }

    protected function createSearchPropertiesTool(): object
    {
        return Tool::as('search_properties')
            ->for('Search for properties in the database based on user criteria like area, budget, category, etc.')
            ->withStringParameter('area', 'The area/location where the user wants to buy property (e.g., Vesu, Adajan, Pal). Leave empty if not specified.', required: false)
            ->withStringParameter('category', 'The property category: "Residential", "Commercial", or "Plotting". Leave empty if not specified.', required: false)
            ->withStringParameter('budget_min', 'Minimum budget in format like "20L", "50L", "1Cr", etc. Leave empty if not specified.', required: false)
            ->withStringParameter('budget_max', 'Maximum budget in format like "50L", "1Cr", "2Cr", etc. Leave empty if not specified.', required: false)
            ->withStringParameter('status', 'Project status: "Ongoing", "Completed", "Upcoming". Leave empty if not specified.', required: false)
            ->using(function (
                ?string $area = null,
                ?string $category = null,
                ?string $budget_min = null,
                ?string $budget_max = null,
                ?string $status = null
            ): string {
                $query = Project::query()
                    ->with(['exhibitor.company']);

                if ($area) {
                    $query->where('area', 'ILIKE', "%{$area}%");
                }

                if ($category) {
                    $query->where('category', 'ILIKE', "%{$category}%");
                }

                if ($budget_min || $budget_max) {
                    $query->where(function ($q) use ($budget_min, $budget_max) {
                        if ($budget_min) {
                            // Search in both budget_range and units columns
                            $q->where(function ($subQ) use ($budget_min) {
                                $subQ->where('budget_range', 'ILIKE', "%{$budget_min}%")
                                    ->orWhereRaw('EXISTS (
                                      SELECT 1 FROM json_array_elements(units) as unit
                                      WHERE unit->>\'budget\' ILIKE ?
                                  )', ["%{$budget_min}%"]);
                            });
                        }
                        if ($budget_max) {
                            // Search in both budget_range and units columns
                            $q->where(function ($subQ) use ($budget_max) {
                                $subQ->where('budget_range', 'ILIKE', "%{$budget_max}%")
                                    ->orWhereRaw('EXISTS (
                                      SELECT 1 FROM json_array_elements(units) as unit
                                      WHERE unit->>\'budget\' ILIKE ?
                                  )', ["%{$budget_max}%"]);
                            });
                        }
                    });
                }

                if ($status) {
                    $query->where('status', 'ILIKE', "%{$status}%");
                }

                $projects = $query->limit(10)->get();

                if ($projects->isEmpty()) {
                    return json_encode([
                        'count' => 0,
                        'message' => 'No properties found matching the criteria.',
                        'projects' => [],
                    ]);
                }

                $result = [
                    'count' => $projects->count(),
                    'message' => "Found {$projects->count()} properties matching the criteria.",
                    'projects' => $projects->map(function ($project) {
                        return [
                            'id' => $project->id,
                            'name' => $project->name,
                            'area' => $project->area,
                            'category' => $project->category,
                            'budget_range' => $project->budget_range,
                            'sq_ft' => $project->sq_ft,
                            'status' => $project->status,
                            'handover_date' => $project->handover_date,
                            'usp' => $project->usp,
                            'exhibitor_name' => $project->exhibitor?->brand_name ?? 'N/A',
                            'contact_person' => $project->contact_person,
                            'url' => route('project.show', $project),
                        ];
                    })->toArray(),
                ];

                return json_encode($result);
            });
    }

    protected function getSystemPrompt(): string
    {
        return <<<'PROMPT'
You are a helpful real estate assistant for CREDAI Property Exhibition. Your role is to help visitors find properties that match their requirements.

IMPORTANT RULES:
- You will receive database search results in the context before each user message
- Base your responses ONLY on the search results provided - do not make up or hallucinate property details
- Keep your text responses SHORT and conversational (1-2 sentences max)
- Property details will be displayed in interactive cards automatically - DO NOT list property details in your response
- If search results show properties, say something like: "I found {count} properties matching your criteria!" or "Here's what I found:"
- If no properties were found, be honest and suggest adjusting the search criteria
- Always be polite, professional, and helpful
- If you're not sure what the user wants, ask clarifying questions

Examples of good responses:
- "I found 2 properties in Vesu! Check them out below."
- "Here are 3 upcoming residential projects matching your budget."
- "No properties found with those exact criteria. Try adjusting your budget or area?"

Remember: The property cards will show all details. Your job is just to provide a friendly, concise introduction to the results.
PROMPT;
    }

    protected function buildPrompt(string $userMessage, array $conversationHistory): string
    {
        $prompt = '';

        // Add conversation history context if exists
        if (! empty($conversationHistory)) {
            $prompt .= "Previous conversation:\n";
            foreach ($conversationHistory as $msg) {
                $role = $msg['role'] === 'assistant' ? 'Assistant' : 'User';
                $prompt .= "{$role}: {$msg['content']}\n";
            }
            $prompt .= "\n";
        }

        $prompt .= "User: {$userMessage}";

        return $prompt;
    }

    protected function extractSearchParameters(string $message): array
    {
        // Use LLM to extract structured search parameters
        $systemPrompt = <<<'PROMPT'
Extract property search parameters from the user's message. Be intelligent about typos and variations.

Available categories: Residential, Commercial, Plotting, Weekend Home & Others
Available areas: Athwa - Vesu, Pal - Adajan - Rander, Saroli, Katargam, Dindoli, Outer City, Within City
Available status: completed, upcoming, ready_to_move, ongoing
Available handover dates: Within 3 months, Within 6 months, Within a year

Return ONLY a valid JSON object with these fields (null if not mentioned):
{
  "category": "Residential|Commercial|Plotting|Weekend Home & Others|null",
  "area": "area name or null",
  "budget": "budget value like 2Cr, 50L or null",
  "size_sqft": number or null,
  "handover_date": "handover date or null",
  "bedrooms": "1 BHK|2 BHK|3 BHK|4 BHK|5+ BHK|null"
}

Examples:
- "need a flat in pal" → {"category":"Residential","area":"Pal - Adajan - Rander","budget":null,"size_sqft":null,"handover_date":null,"bedrooms":null}
- "showroom 4000 sq ft" → {"category":"Commercial","area":null,"budget":null,"size_sqft":4000,"handover_date":null,"bedrooms":null}
- "3 bhk in vesu budget 2 cr" → {"category":"Residential","area":"Athwa - Vesu","budget":"2Cr","size_sqft":null,"handover_date":null,"bedrooms":"3 BHK"}
PROMPT;

        try {
            $response = Prism::text()
                ->using('groq', 'llama-3.3-70b-versatile')
                ->withSystemPrompt($systemPrompt)
                ->withPrompt("User message: {$message}")
                ->asText();

            // Clean response - remove markdown code blocks if present
            $jsonText = $response->text;
            $jsonText = preg_replace('/```json\s*/', '', $jsonText);
            $jsonText = preg_replace('/```\s*$/', '', $jsonText);
            $jsonText = trim($jsonText);

            $params = json_decode($jsonText, true);

            if (! is_array($params)) {
                logger()->warning('LLM returned invalid JSON', ['response' => $response->text, 'cleaned' => $jsonText]);

                return $this->fallbackExtraction($message);
            }

            logger()->info('Extracted parameters', ['params' => $params, 'message' => $message]);

            return $params;
        } catch (\Exception $e) {
            logger()->error('Parameter extraction failed', ['error' => $e->getMessage()]);

            return $this->fallbackExtraction($message);
        }
    }

    protected function fallbackExtraction(string $message): array
    {
        // Fallback to keyword matching if LLM fails
        return [
            'category' => null,
            'area' => null,
            'budget' => null,
            'size_sqft' => null,
            'handover_date' => null,
            'bedrooms' => null,
        ];
    }

    protected function searchPropertiesFromMessage(string $message): array
    {
        // Extract structured parameters using LLM
        $params = $this->extractSearchParameters($message);

        $message = strtolower($message);

        $query = Project::query()->with('exhibitor');

        // Search for exhibitor/builder/developer/group/company mentions
        if (
            str_contains($message, 'from') || str_contains($message, 'by') ||
            str_contains($message, 'group') || str_contains($message, 'builder') ||
            str_contains($message, 'developer') || str_contains($message, 'company')
        ) {
            // Extract potential company name after these keywords
            if (preg_match('/(?:from|by|group|company)\s+([a-z]+)/i', $message, $matches)) {
                $companyName = $matches[1];
                logger()->info('Searching for company', ['company_name' => $companyName, 'message' => $message]);
                $query->whereHas('exhibitor.company', function ($q) use ($companyName) {
                    $q->where('company_name', 'ILIKE', "%{$companyName}%");
                });
            }
        }

        // Search for area - use LLM-extracted param if available, otherwise fallback to keywords
        if (! empty($params['area'])) {
            $query->where('area', 'ILIKE', "%{$params['area']}%");
        } else {
            // Fallback: keyword-based area detection
            $availableAreas = Project::query()
                ->whereNotNull('area')
                ->distinct()
                ->pluck('area')
                ->map(fn ($area) => strtolower($area))
                ->toArray();

            $commonWords = ['within', 'outer', 'city'];
            foreach ($availableAreas as $area) {
                $areaParts = preg_split('/[\s\-]+/', $area);
                foreach ($areaParts as $part) {
                    if (strlen($part) >= 3 && ! in_array($part, $commonWords) && str_contains($message, $part)) {
                        $query->where('area', 'ILIKE', "%{$part}%");
                        break 2;
                    }
                }
            }
        }

        // Search for budget mentions (50L, 1Cr, etc.)
        if (
            preg_match('/(\d+)\s*(?:l|lakh|lakhs)/i', $message, $matches) ||
            preg_match('/(\d+)\s*(?:cr|crore|crores)/i', $message, $matches)
        ) {
            $rawBudget = $matches[0];

            // Normalize budget format to match database (e.g., "2 cr" -> "2Cr", "50 lakh" -> "50L")
            $budget = preg_replace('/\s+/', '', $rawBudget); // Remove spaces
            $budget = preg_replace('/lakh(s)?/i', 'L', $budget); // Replace lakh(s) with L
            $budget = preg_replace('/crore(s)?/i', 'Cr', $budget); // Replace crore(s) with Cr

            // Search in both budget_range column AND units JSON column
            $query->where(function ($q) use ($budget) {
                // Check budget_range column (for Plotting & Weekend Home & Others)
                $q->where('budget_range', 'ILIKE', "%{$budget}%")
                  // Check units column (for Residential & Commercial)
                    ->orWhereRaw('EXISTS (
                      SELECT 1 FROM json_array_elements(units) as unit
                      WHERE unit->>\'budget\' ILIKE ?
                  )', ["%{$budget}%"]);
            });
        }

        // Search for category - use LLM-extracted param if available, otherwise fallback to keywords
        if (! empty($params['category'])) {
            $query->where('category', 'ILIKE', "%{$params['category']}%");
        } else {
            // Fallback: keyword-based category detection
            $categoryMatched = false;

            $categories = ['residential', 'commercial', 'plotting'];
            foreach ($categories as $category) {
                if (str_contains($message, $category)) {
                    $query->where('category', 'ILIKE', "%{$category}%");
                    $categoryMatched = true;
                    break;
                }
            }

            if (! $categoryMatched && (str_contains($message, 'office') || str_contains($message, 'shop') || str_contains($message, 'showroom'))) {
                $query->where('category', 'ILIKE', '%commercial%');
                $categoryMatched = true;
            }

            if (! $categoryMatched && (str_contains($message, 'plot') || str_contains($message, 'land'))) {
                $query->where('category', 'ILIKE', '%plotting%');
                $categoryMatched = true;
            }

            if (! $categoryMatched && (str_contains($message, 'flat') || str_contains($message, 'apartment') || str_contains($message, ' home'))) {
                $query->where('category', 'ILIKE', '%residential%');
                $categoryMatched = true;
            }
        }

        // Search for size/area - use LLM-extracted param if available
        $requestedSize = null;
        if (! empty($params['size_sqft'])) {
            $requestedSize = (int) $params['size_sqft'];
        } elseif (preg_match('/(\d+)\s*(?:sq\.?\s*ft|square\s*feet|sqft)/i', $message, $matches)) {
            $requestedSize = (int) $matches[1];
        }

        if ($requestedSize) {
            $tolerance = $requestedSize * 0.2; // 20% tolerance

            $query->where(function ($q) use ($requestedSize, $tolerance) {
                $q->where(function ($subQ) use ($requestedSize, $tolerance) {
                    $subQ->whereNotNull('sq_ft')
                        ->where('sq_ft', '!=', '')
                        ->whereRaw('CAST(sq_ft AS INTEGER) BETWEEN ? AND ?', [
                            $requestedSize - $tolerance,
                            $requestedSize + $tolerance,
                        ]);
                })
                    ->orWhereRaw("EXISTS (
                      SELECT 1 FROM json_array_elements(units) as unit
                      WHERE unit->>'area' IS NOT NULL
                      AND unit->>'area' != ''
                      AND CAST(unit->>'area' AS INTEGER) BETWEEN ? AND ?
                  )", [
                        $requestedSize - $tolerance,
                        $requestedSize + $tolerance,
                    ]);
            });
        }

        // Search for handover date/timeline mentions
        if (
            preg_match('/within\s+(\d+)\s+(month|months|year)/i', $message, $matches) ||
            preg_match('/in\s+(\d+)\s+(month|months|year)/i', $message, $matches)
        ) {
            $number = $matches[1];
            $unit = strtolower($matches[2]);

            // Normalize to match database format
            if (str_starts_with($unit, 'month')) {
                $handoverDate = "Within {$number} months";
            } else {
                $handoverDate = 'Within a year';
            }

            $query->where('handover_date', 'ILIKE', "%{$handoverDate}%");
        }

        // Search for status - only filter if user explicitly mentions a status
        // Check for explicit status keywords
        if (str_contains($message, 'ready to move') || str_contains($message, 'ready_to_move')) {
            $query->where('status', 'ready_to_move');
        } elseif (str_contains($message, 'completed')) {
            $query->where('status', 'completed');
        } elseif (str_contains($message, 'upcoming')) {
            $query->where('status', 'upcoming');
        } elseif (str_contains($message, 'ongoing') || str_contains($message, 'under construction')) {
            $query->where('status', 'ongoing');
        }
        // Otherwise, show ALL statuses (no filter applied)

        $projects = $query->limit(10)->get();

        return $projects->map(function ($project) {
            return [
                'id' => $project->id,
                'name' => $project->name,
                'area' => $project->area,
                'category' => $project->category,
                'budget_range' => $project->budget_range,
                'sq_ft' => $project->sq_ft,
                'status' => $project->status,
                'handover_date' => $project->handover_date,
                'usp' => $project->usp,
                'exhibitor_name' => $project->exhibitor?->brand_name ?? 'N/A',
                'contact_person' => $project->contact_person,
                'url' => route('project.show', $project),
                'units' => $project->units ?? [], // Include units for Residential/Commercial
            ];
        })->toArray();
    }

    protected function buildPromptWithResults(string $userMessage, array $searchResults, array $conversationHistory): string
    {
        $prompt = '';

        // Add conversation history
        if (! empty($conversationHistory)) {
            $prompt .= "Previous conversation:\n";
            foreach ($conversationHistory as $msg) {
                $role = $msg['role'] === 'assistant' ? 'Assistant' : 'User';
                $prompt .= "{$role}: {$msg['content']}\n";
            }
            $prompt .= "\n";
        }

        // Add search results context
        if (! empty($searchResults)) {
            $prompt .= 'Database search results ('.count($searchResults)." properties found):\n";
            foreach ($searchResults as $index => $property) {
                $num = $index + 1;
                $prompt .= "\nProperty {$num}:\n";
                $prompt .= "- Name: {$property['name']}\n";
                $prompt .= "- Developer: {$property['exhibitor_name']}\n";
                $prompt .= "- Area: {$property['area']}\n";
                $prompt .= "- Category: {$property['category']}\n";
                $prompt .= "- Budget: {$property['budget_range']}\n";
                $prompt .= "- Status: {$property['status']}\n";
            }
            $prompt .= "\n";
        } else {
            $prompt .= "Database search results: No properties found matching the criteria.\n\n";
        }

        $prompt .= "User: {$userMessage}";

        return $prompt;
    }

    protected function extractToolCalls($response): array
    {
        $toolCalls = [];

        if ($response->toolResults) {
            foreach ($response->toolResults as $toolResult) {
                $toolCalls[] = [
                    'name' => $toolResult->toolName,
                    'result' => $toolResult->result,
                ];
            }
        }

        return $toolCalls;
    }

    protected function extractProperties($response): array
    {
        $properties = [];

        logger()->info('Extracting properties', [
            'has_tool_results' => isset($response->toolResults),
            'tool_results_type' => gettype($response->toolResults ?? null),
            'tool_results_count' => is_array($response->toolResults) ? count($response->toolResults) : 0,
            'tool_results_is_empty' => empty($response->toolResults),
        ]);

        if ($response->toolResults && ! empty($response->toolResults)) {
            logger()->info('About to loop through tool results', [
                'count' => count($response->toolResults),
            ]);

            foreach ($response->toolResults as $index => $toolResult) {
                logger()->info('Processing tool result', [
                    'index' => $index,
                    'tool_name' => $toolResult->toolName ?? 'NO_NAME',
                    'has_result' => isset($toolResult->result),
                    'result_preview' => substr($toolResult->result ?? '', 0, 200),
                ]);

                if (isset($toolResult->toolName) && $toolResult->toolName === 'search_properties') {
                    $data = json_decode($toolResult->result, true);
                    logger()->info('Decoded tool result', [
                        'data_structure' => array_keys($data ?? []),
                        'has_projects' => isset($data['projects']),
                        'projects_count' => count($data['projects'] ?? []),
                    ]);

                    if (isset($data['projects']) && is_array($data['projects'])) {
                        $properties = $data['projects'];
                    }
                }
            }
        } else {
            logger()->warning('toolResults is empty or falsy');
        }

        logger()->info('Final extracted properties', ['count' => count($properties)]);

        return $properties;
    }
}
