<?php

namespace App\Policies;

use App\Models\IncidentReport;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class IncidentReportPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, IncidentReport $incidentReport): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, IncidentReport $incidentReport): bool
    {
        // 1. Admin HSE selalu boleh
        if ($user->hasRole('Administrator')) return true;

        // 2. Jika laporan masih Open, Pembuat (Originator) bisa edit Part 1-2
        if ($incidentReport->status === 'Open' && $user->id === $incidentReport->pelapor_id) {
            return true;
        }

        // 3. Tim Investigasi yang terdaftar di Part 3
        // Cek apakah user ID ada dalam relasi investigationTeams
        if ($incidentReport->investigationTeams()->where('user_id', $user->id)->exists()) {
            return true;
        }
        return false;
    }
    public function close(User $user, IncidentReport $incident)
    {
        // Hanya Manager atau Superintendent yang bisa menutup laporan di Part 9
        $isAssignedModerator = $user->moderatorAssignments()
            ->where('event_type_id', $incident->event_type_id)
            ->where(function (Builder $query) use ($incident) {

                // Kriteria A: Penugasan bersifat umum (tidak spesifik pada Department/Contractor)
                $query->whereNull('department_id')
                    ->whereNull('contractor_id');

                // Kriteria B: Penugasan spesifik untuk Department
                if ($incident->department_id) {
                    $query->orWhere('department_id', $incident->department_id);
                }

                // Kriteria C: Penugasan spesifik untuk Contractor
                if ($incident->contractor_id) {
                    $query->orWhere('contractor_id', $incident->contractor_id);
                }
            })
            ->exists();

        if ($isAssignedModerator) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, IncidentReport $incidentReport): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, IncidentReport $incidentReport): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, IncidentReport $incidentReport): bool
    {
        return false;
    }
}
