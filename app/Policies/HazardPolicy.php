<?php

namespace App\Policies;

use App\Models\Hazard;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class HazardPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Hazard $hazard): bool
    {
        // ✅ Admin (role_id = 1) selalu bisa mengakses
        if ($user->roles()->where('role_id', 1)->exists()) {
            return true;
        }

        // ✅ Penanggung jawab bisa melihat
        if ($hazard->penanggungJawab && $user->id === $hazard->penanggungJawab->id) {
            return true;
        }

        // ✅ Pelapor bisa melihat
        if ($hazard->pelapor && $user->id === $hazard->pelapor->id) {
            return true;
        }

        // ✅ Assigned ERM bisa melihat (berdasarkan hazard_erm_assignments)
        if ($hazard->assignedErms()->wherePivot('erm_id', $user->id)->exists()) {
            return true;
        }

        // ✅ Moderator hanya bisa akses hazard berdasarkan event_type_id yang ditugaskan
        //    Pastikan tabel moderator_assignments memiliki field event_type_id
        $isAssignedModerator = $user->moderatorAssignments()
            ->where('event_type_id', $hazard->event_type_id)
            ->exists();

        if ($isAssignedModerator) {
            return true;
        }

        // ❌ Jika tidak memenuhi semua kondisi di atas, akses ditolak
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
    public function update(User $user, Hazard $hazard): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Hazard $hazard): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Hazard $hazard): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Hazard $hazard): bool
    {
        return false;
    }
}
