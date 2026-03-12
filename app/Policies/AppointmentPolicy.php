<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Appointment;

class AppointmentPolicy
{
    /**
     * Determine whether the user can view any appointments.
     */
    public function viewAny(User $user): bool
    {
        // Admins and Health Workers can view all, Patients only their own
        return true;
    }

    /**
     * Determine whether the user can view a specific appointment.
     */
    public function view(User $user, Appointment $appointment): bool
    {
        return $user->isAdmin() ||
               $user->isHealthWorker() ||
               $user->id === $appointment->user_id;
    }

    /**
     * Determine whether the user can create appointments.
     */
    public function create(User $user): bool
    {
        return $user->isPatient();
    }

    /**
     * Determine whether the user can update appointments.
     */
    public function update(User $user, Appointment $appointment): bool
    {
        // Admins and Health Workers can edit any appointment
        if ($user->isAdmin() || $user->isHealthWorker()) {
            return true;
        }

        // Patients can only update (reschedule/cancel) their own pending/rescheduled appointments
        return $user->id === $appointment->user_id &&
               in_array($appointment->status, ['pending', 'rescheduled']);
    }

    /**
     * Determine whether the user can delete appointments.
     */
    public function delete(User $user, Appointment $appointment): bool
    {
        // Only Admins can delete
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can approve or reject appointments.
     */
    public function approve(User $user, Appointment $appointment): bool
    {
        // Admins and Health Workers can approve/reject
        return $user->isAdmin() || $user->isHealthWorker();
    }
}
