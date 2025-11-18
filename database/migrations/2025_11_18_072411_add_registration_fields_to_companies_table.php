<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('registration_token', 64)->unique()->nullable()->after('id');
            $table->boolean('has_submitted')->default(false)->after('payment_pending');
            $table->timestamp('submitted_at')->nullable()->after('has_submitted');
        });

        // Generate unique tokens for existing companies
        $companies = \App\Models\Company::all();
        foreach ($companies as $company) {
            $company->update([
                'registration_token' => \Illuminate\Support\Str::random(32),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['registration_token', 'has_submitted', 'submitted_at']);
        });
    }
};
