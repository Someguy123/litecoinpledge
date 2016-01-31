<?php

namespace App\Policies;

use App\Project;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function verify(User $user, Project $project)
    {
        return $user->is_mod();
    }

    public function destroy(User $user, Project $project)
    {
        return $user->owns($project)
            || $user->is_mod();
    }
}
