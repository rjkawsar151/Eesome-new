<?php

namespace App\Services;

use App\Models\User;

class AdminPermissionService
{
    private const ROLE_PERMISSIONS = [
        'super admin' => ['*'],
        'admin' => ['*'],
        'manager' => ['dashboard.view', 'orders.manage', 'products.manage', 'categories.manage', 'reviews.manage'],
        'content editor' => ['dashboard.view', 'products.manage', 'categories.manage', 'reviews.manage', 'blog.manage'],
    ];

    public function permissionForRoute(?string $routeName): string
    {
        return match (true) {
            $routeName === 'admin.dashboard' => 'dashboard.view',
            str_starts_with((string) $routeName, 'admin.orders.') => 'orders.manage',
            str_starts_with((string) $routeName, 'admin.products.') => 'products.manage',
            str_starts_with((string) $routeName, 'admin.inventory.') => 'products.manage',
            str_starts_with((string) $routeName, 'admin.categories.') => 'categories.manage',
            str_starts_with((string) $routeName, 'admin.brands.') => 'categories.manage',
            str_starts_with((string) $routeName, 'admin.tags.') => 'categories.manage',
            str_starts_with((string) $routeName, 'admin.reviews.') => 'reviews.manage',
            str_starts_with((string) $routeName, 'admin.blog.') => 'blog.manage',
            str_starts_with((string) $routeName, 'admin.users.') => 'users.manage',
            str_starts_with((string) $routeName, 'admin.settings.') => 'settings.manage',
            str_starts_with((string) $routeName, 'admin.shipping-methods.') => 'settings.manage',
            str_starts_with((string) $routeName, 'admin.payment-methods.') => 'settings.manage',
            str_starts_with((string) $routeName, 'admin.coupons.') => 'settings.manage',
            str_starts_with((string) $routeName, 'admin.navigation-items.') => 'settings.manage',
            str_starts_with((string) $routeName, 'admin.media.') => 'settings.manage',
            str_starts_with((string) $routeName, 'admin.activity.') => 'activity.view',
            str_starts_with((string) $routeName, 'admin.visitors.') => 'visitors.view',
            default => 'admin.access',
        };
    }

    public function allows(User $user, string $permission): bool
    {
        $permissions = self::ROLE_PERMISSIONS[$user->role] ?? [];

        return in_array('*', $permissions, true) || in_array($permission, $permissions, true);
    }
}
