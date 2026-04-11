<?php

namespace Database\Seeders;

use App\Models\MessageTemplate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MessageTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Defaulter Recall Email
        MessageTemplate::firstOrCreate([
            'type' => 'email',
            'name' => 'Defaulter Recall Email',
        ], [
            'type' => 'email',
            'name' => 'Defaulter Recall Email',
            'subject' => 'E-Barangay Follow-Up: Missed {service_type} Appointment - {patient_name}',
            'body' => "Dear {recipient_label},

This is a reminder from {health_center_name} regarding a missed {service_type} appointment for {patient_name} on {appointment_date} at {appointment_time}.

This is the {appointment_version} follow-up for this service. Please reply to this email or contact us at {contact_number} to reschedule as soon as possible.

Thank you for your cooperation and commitment to health.

Sincerely,
{midwife_name}
{health_center_name}
{contact_number}",
            'is_active' => true,
            'is_default' => true,
        ]);

        // Defaulter Recall SMS
        MessageTemplate::firstOrCreate([
            'type' => 'sms',
            'name' => 'Defaulter Recall SMS',
        ], [
            'type' => 'sms',
            'name' => 'Defaulter Recall SMS',
            'body' => 'E-Barangay: {recipient_label}, missed {service_type} appointment on {appointment_date}. Call/message {contact_number} to reschedule. Thank you.',
            'is_active' => true,
            'is_default' => true,
        ]);

        // Appointment Reminder Email
        MessageTemplate::firstOrCreate([
            'type' => 'email',
            'name' => 'Appointment Reminder Email',
        ], [
            'type' => 'email',
            'name' => 'Appointment Reminder Email',
            'subject' => 'Reminder: {service_type} Appointment - {appointment_date}',
            'body' => "Dear {recipient_label},

This is a friendly reminder about your upcoming {service_type} appointment for {patient_name}.

Date: {appointment_date}
Time: {appointment_time}
Location: {health_center_name}

If you need to reschedule, please contact us at {contact_number}.

Thank you!",
            'is_active' => true,
            'is_default' => true,
        ]);

        // Appointment Reminder SMS
        MessageTemplate::firstOrCreate([
            'type' => 'sms',
            'name' => 'Appointment Reminder SMS',
        ], [
            'type' => 'sms',
            'name' => 'Appointment Reminder SMS',
            'body' => 'E-Barangay: Reminder of {service_type} appointment for {patient_name} on {appointment_date} at {appointment_time}. Call {contact_number} if you need to reschedule.',
            'is_active' => true,
            'is_default' => true,
        ]);
    }
}
