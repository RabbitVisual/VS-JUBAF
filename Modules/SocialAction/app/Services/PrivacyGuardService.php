<?php

namespace Modules\SocialAction\App\Services;

use App\Models\User;

class PrivacyGuardService
{
    public function canViewSensitiveData(User $user): bool
    {
        // Simple logic: Admin or Pastor role
        // In a real app, check permissions strictly
        return $user->hasRole('admin') || $user->hasRole('pastor') || $user->can('view social sensitive data');
    }

    public function maskData(string $data): string
    {
        if (strlen($data) <= 4) {
            return str_repeat('*', strlen($data));
        }
        return substr($data, 0, 2) . str_repeat('*', strlen($data) - 4) . substr($data, -2);
    }
}
