<?php

namespace App\Enum;

enum Permissions
{
    const TEAM_READ = 'team__read';
    const TEAM_CREATE = 'team__create';
    const TEAM_UPDATE = 'team__update';
    const TEAM_DELETE = 'team__delete';
    const TEAM_ASSIGN_PERMISSIONS = 'team__assign_permissions';

    const ROLE_READ = 'role__read';
    const ROLE_CREATE = 'role__create';
    const ROLE_UPDATE = 'role__update';
    const ROLE_DELETE = 'role__delete';
    const ROLE_ASSIGN_PERMISSIONS = 'role__assign_permissions';

    const USER_READ = 'user__read';
    const USER_CREATE = 'user__create';
    const USER_UPDATE = 'user__update';
    const USER_DELETE = 'user__delete';
    const USER_ASSIGN_ROLES = 'user__assign_roles';

    const ARTICLE_READ = 'article__read';
    const ARTICLE_CREATE = 'article__create';
    const ARTICLE_UPDATE = 'article__update';
    const ARTICLE_DELETE = 'article__delete';

    public static function all(): array
    {
        $reflection = new \ReflectionClass(static::class);
        return array_values($reflection->getConstants());
    }
}
