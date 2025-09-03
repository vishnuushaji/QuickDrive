<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function viewAny(User $user)
    {
        return true; // All authenticated users can view projects
    }

    public function view(User $user, Project $project)
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        // Check if user is associated with the project
        return $project->users->contains($user);
    }

    public function create(User $user)
    {
        // Only super admins and developers can create projects
        return $user->isSuperAdmin() || $user->isDeveloper();
    }

    public function update(User $user, Project $project)
    {
        return $user->isSuperAdmin();
    }

    public function delete(User $user, Project $project)
    {
        return $user->isSuperAdmin();
    }
}