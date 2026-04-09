<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\User;

class ApplicationPolicy
{
    public function update(User $user, Application $application): bool
    {
        return $user->tenant_id === $application->tenant_id;
    }

    public function delete(User $user, Application $application): bool
    {
        return $user->tenant_id === $application->tenant_id;
    }
}