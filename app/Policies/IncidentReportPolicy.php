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
        if ($user->hasRole('admin-hse')) return true;

        // 2. Pembuat laporan boleh (HANYA jika status masih 'Draft' atau 'Open')
        if ($user->id === $incidentReport->created_by && in_array($incidentReport->status, ['draft', 'open'])) {
            return true;
        }
        return false;
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
