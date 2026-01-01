<div class="flex h-full w-full flex-1 flex-col gap-6" x-data="{ deleteLeadId: null, deleteLeadName: '' }">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">Partner Leads</flux:heading>
            <flux:subheading>Manage all your saved partner leads</flux:subheading>
        </div>
    </div>

    <div class="flex gap-4">
        <flux:input
            wire:model.live.debounce.300ms="search"
            placeholder="Search by name, phone, or firm..."
            icon="magnifying-glass"
            class="flex-1"
        />
    </div>

    @if($leads->isEmpty())
        <flux:card>
            <div class="py-12 text-center">
                <flux:icon.user-group variant="outline" class="mx-auto size-12 text-zinc-400" />
                <flux:heading size="lg" class="mt-4">No leads yet</flux:heading>
                <flux:subheading class="mt-2">
                    Start scanning partner QR codes to build your lead list
                </flux:subheading>
                <flux:button :href="route('exhibitor.scan')" wire:navigate variant="primary" class="mt-4">
                    Scan QR Code
                </flux:button>
            </div>
        </flux:card>
    @else
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Name</flux:table.column>
                <flux:table.column>Phone</flux:table.column>
                <flux:table.column>Firm</flux:table.column>
                <flux:table.column>Property Types</flux:table.column>
                <flux:table.column>Notes</flux:table.column>
                <flux:table.column>Added On</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach($leads as $lead)
                    <flux:table.row :key="$lead->id">
                        <flux:table.cell>{{ $lead->partner->first_name }} {{ $lead->partner->last_name }}</flux:table.cell>
                        <flux:table.cell>{{ $lead->partner->phone }}</flux:table.cell>
                        <flux:table.cell>{{ $lead->partner->firm_name ?? '-' }}</flux:table.cell>
                        <flux:table.cell>
                            @if($lead->partner->property_types)
                                <div class="flex flex-wrap gap-1">
                                    @foreach($lead->partner->property_types as $type)
                                        <flux:badge size="sm" color="purple">{{ $type }}</flux:badge>
                                    @endforeach
                                </div>
                            @else
                                -
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            @if($lead->notes)
                                <div class="max-w-xs truncate" title="{{ $lead->notes }}">
                                    {{ $lead->notes }}
                                </div>
                            @else
                                -
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>{{ $lead->created_at->format('M d, Y') }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:button
                                @click="deleteLeadId = {{ $lead->id }}; deleteLeadName = '{{ $lead->partner->first_name }} {{ $lead->partner->last_name }}'; $flux.modal('delete-lead').show()"
                                variant="danger"
                                size="sm"
                                icon="trash">
                                Remove
                            </flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>

        <div class="mt-4">
            {{ $leads->links() }}
        </div>
    @endif

    <flux:modal name="delete-lead">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Remove Lead</flux:heading>
                <flux:subheading>Are you sure you want to remove this lead?</flux:subheading>
            </div>

            <div class="text-sm">
                You are about to remove <strong x-text="deleteLeadName"></strong> from your leads list. This action cannot be undone.
            </div>

            <div class="flex gap-2 justify-end">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button
                    variant="danger"
                    @click="$wire.deleteLead(deleteLeadId).then(() => $flux.modal('delete-lead').close())">
                    Remove Lead
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
