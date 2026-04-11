<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\MessageTemplate;
use Illuminate\Support\Facades\Auth;

class TemplateService
{
    /**
     * Available placeholder variables
     */
    public static function getAvailablePlaceholders()
    {
        return [
            '{patient_name}' => 'Full name of the patient',
            '{patient_first_name}' => 'First name of the patient',
            '{patient_age}' => 'Age of the patient',
            '{patient_gender}' => 'Gender of the patient',
            '{guardian_name}' => 'Name of parent/guardian (if dependent)',
            '{recipient_label}' => 'Recipient name (guardian for dependents, patient for adults)',
            '{service_type}' => 'Type of health service (Immunization, Prenatal, etc)',
            '{appointment_date}' => 'Missed appointment date (e.g., Apr 11, 2026)',
            '{appointment_time}' => 'Missed appointment time (e.g., 08:00 AM)',
            '{appointment_version}' => 'Count of missed appointments for this service',
            '{health_center_name}' => 'Name of the health center',
            '{contact_number}' => 'Health center contact number',
            '{midwife_name}' => 'Name of the assigned midwife',
        ];
    }

    /**
     * Render a template with appointment data
     * Returns array with 'subject' and 'body' keys
     */
    public static function render($templateType, Appointment $appointment)
    {
        $template = MessageTemplate::getActiveTemplate($templateType);

        if (!$template) {
            throw new \Exception("No active {$templateType} template found");
        }

        $data = self::prepareData($appointment);
        
        $subject = $templateType === 'email' 
            ? self::replacePlaceholders($template->subject, $data)
            : null;
        
        $body = self::replacePlaceholders($template->body, $data);

        return [
            'subject' => $subject,
            'body' => $body,
            'template_id' => $template->id,
        ];
    }

    /**
     * Prepare all data needed for template rendering
     */
    private static function prepareData(Appointment $appointment)
    {
        $patient = $appointment->user;
        $isDependent = $patient->isDependent();
        $guardian = $isDependent ? $patient->guardian : null;

        $scheduledDate = $appointment->scheduled_at
            ? $appointment->scheduled_at->format('M d, Y')
            : ($appointment->slot ? \Carbon\Carbon::parse($appointment->slot->date)->format('M d, Y') : 'N/A');

        $scheduledTime = $appointment->scheduled_at
            ? $appointment->scheduled_at->format('h:i A')
            : 'N/A';

        // Count missed appointments for this service
        $missedCount = Appointment::where('user_id', $patient->id)
            ->where('service', $appointment->service)
            ->where('status', 'no_show')
            ->count();

        return [
            // Patient info
            'patient_name' => $patient->full_name,
            'patient_first_name' => $patient->first_name,
            'patient_age' => $patient->age ?? 'N/A',
            'patient_gender' => $patient->gender ?? 'N/A',
            
            // Guardian info (for dependents)
            'guardian_name' => $guardian ? $guardian->full_name : '',
            'recipient_label' => $isDependent && $guardian ? $guardian->first_name : $patient->first_name,
            
            // Appointment info
            'service_type' => $appointment->service,
            'appointment_date' => $scheduledDate,
            'appointment_time' => $scheduledTime,
            'appointment_version' => self::getOrdinalCount($missedCount),
            
            // Health center info (from env or config)
            'health_center_name' => config('app.health_center_name', 'E-Barangay Health Center'),
            'contact_number' => config('app.health_center_contact', '(+63) XXXX-XXXX'),
            'midwife_name' => optional(Auth::user())->full_name ?? 'Health Team',
        ];
    }

    /**
     * Replace all placeholders in a template string
     */
    private static function replacePlaceholders($template, $data)
    {
        $placeholders = array_map(fn($key) => '{' . $key . '}', array_keys($data));
        $values = array_values($data);

        return str_replace($placeholders, $values, $template);
    }

    /**
     * Convert number to ordinal (1st, 2nd, 3rd, etc)
     */
    private static function getOrdinalCount($count)
    {
        if ($count <= 0) return '1st';
        
        $ends = ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'];
        
        if (($count % 100) >= 11 && ($count % 100) <= 13) {
            return $count . 'th';
        } else {
            return $count . $ends[$count % 10];
        }
    }
}
