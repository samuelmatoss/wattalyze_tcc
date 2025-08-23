<?php

namespace App\Policies;

use App\Models\Report;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ReportPolicy
{
    /**
     * Determine whether the user can view any models.
     */


    public function view(User $user, Report $report)
    {
        return $user->id === $report->user_id;
    }

    /**
     * Determine whether the user can create models.
     */

}
