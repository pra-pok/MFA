<?php

namespace Database\Seeders;

use App\Enum\Permissions;
use App\Models\Article;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Role;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // fill permissions table
        Artisan::call('permissions:update');

        // super admin
        $superAdminTeam = Team::create(['name' => 'Digital Nepal']);
        $superAdmin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@test.com',
            'password' => bcrypt('password'),
            'team_id' => $superAdminTeam->id
        ]);

        setPermissionsTeamId($superAdminTeam->id);

        $role = Role::firstOrCreate(['name' => 'Super Admin', 'team_id' => $superAdminTeam->id]);
        $superAdmin->assignRole($role);

        // demo user
        $demoTeam = Team::create(['name' => 'Demo Team']);
        $demoUser = User::factory()->create([
            'name' => 'Demo User',
            'email' => 'demo@test.com',
            'password' => bcrypt('password'),
            'team_id' => $demoTeam->id
        ]);

        setPermissionsTeamId($demoTeam->id);

        $role = Role::firstOrCreate(['name' => 'Super Admin', 'team_id' => $demoTeam->id]);
        $permissions = [
            Permissions::ROLE_READ,
            Permissions::ROLE_CREATE,
            Permissions::ROLE_UPDATE,
            Permissions::ROLE_DELETE,
            Permissions::ROLE_ASSIGN_PERMISSIONS,
            Permissions::USER_READ,
            Permissions::USER_CREATE,
            Permissions::USER_UPDATE,
            Permissions::USER_DELETE,
            Permissions::USER_ASSIGN_ROLES,
            Permissions::ARTICLE_READ,
            Permissions::ARTICLE_CREATE,
            Permissions::ARTICLE_UPDATE,
            Permissions::ARTICLE_DELETE
        ];
        $role->syncPermissions($permissions);
        $demoUser->assignRole($role);

        // create articles
        Article::factory()->count(10)->create([
            'team_id' => $demoTeam->id,
            'user_id' => $demoUser->id
        ]);
    }
}
