<?php

namespace App\Console\Commands;

use App\Enum\Permissions;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class UpdatePermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update permissions table data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $permissions = Permissions::all();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $count = 0;
        foreach ($permissions as $permission) {
            if (!Permission::where('name', $permission)->exists()) {
                Permission::create(['name' => $permission]);
                $count++;
            }
        }

        $this->info("$count permission(s) added successfully.");
    }
}
