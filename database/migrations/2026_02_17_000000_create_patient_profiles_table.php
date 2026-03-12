<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->string('civil_status')->nullable();
            $table->string('blood_type')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_relationship')->nullable();
            $table->text('allergies')->nullable();
            $table->text('medical_history')->nullable();
            $table->text('family_history')->nullable();
            $table->text('current_medications')->nullable();
            $table->timestamps();
        });

        $patients = DB::table('users')
            ->where('role', 'patient')
            ->select(
                'id',
                'civil_status',
                'blood_type',
                'emergency_contact_name',
                'emergency_contact_relationship',
                'allergies',
                'medical_history',
                'family_history',
                'current_medications'
            )
            ->get();

        foreach ($patients as $patient) {
            DB::table('patient_profiles')->updateOrInsert(
                ['user_id' => $patient->id],
                [
                    'civil_status' => $patient->civil_status,
                    'blood_type' => $patient->blood_type,
                    'emergency_contact_name' => $patient->emergency_contact_name,
                    'emergency_contact_relationship' => $patient->emergency_contact_relationship,
                    'allergies' => $patient->allergies,
                    'medical_history' => $patient->medical_history,
                    'family_history' => $patient->family_history,
                    'current_medications' => $patient->current_medications,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'civil_status')) {
                $table->dropColumn('civil_status');
            }
            if (Schema::hasColumn('users', 'blood_type')) {
                $table->dropColumn('blood_type');
            }
            if (Schema::hasColumn('users', 'emergency_contact_name')) {
                $table->dropColumn('emergency_contact_name');
            }
            if (Schema::hasColumn('users', 'emergency_contact_relationship')) {
                $table->dropColumn('emergency_contact_relationship');
            }
            if (Schema::hasColumn('users', 'allergies')) {
                $table->dropColumn('allergies');
            }
            if (Schema::hasColumn('users', 'medical_history')) {
                $table->dropColumn('medical_history');
            }
            if (Schema::hasColumn('users', 'family_history')) {
                $table->dropColumn('family_history');
            }
            if (Schema::hasColumn('users', 'current_medications')) {
                $table->dropColumn('current_medications');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('civil_status')->nullable()->after('gender');
            $table->string('blood_type')->nullable()->after('civil_status');
            $table->string('emergency_contact_name')->nullable()->after('emergency_no');
            $table->string('emergency_contact_relationship')->nullable()->after('emergency_contact_name');
            $table->text('allergies')->nullable()->after('status');
            $table->text('medical_history')->nullable()->after('allergies');
            $table->text('family_history')->nullable()->after('medical_history');
            $table->text('current_medications')->nullable()->after('family_history');
        });

        $profiles = DB::table('patient_profiles')->get();

        foreach ($profiles as $profile) {
            DB::table('users')
                ->where('id', $profile->user_id)
                ->update([
                    'civil_status' => $profile->civil_status,
                    'blood_type' => $profile->blood_type,
                    'emergency_contact_name' => $profile->emergency_contact_name,
                    'emergency_contact_relationship' => $profile->emergency_contact_relationship,
                    'allergies' => $profile->allergies,
                    'medical_history' => $profile->medical_history,
                    'family_history' => $profile->family_history,
                    'current_medications' => $profile->current_medications,
                ]);
        }

        Schema::dropIfExists('patient_profiles');
    }
};

