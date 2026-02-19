<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$permissionService = app(\App\Services\PermissionService::class);

// Get a Manager user
$manager = \App\Models\User::whereHas('roles', function($q) {
    $q->where('name', 'Manager');
})->limit(1)->first();

if ($manager) {
    echo "=== Testing Manager User: {$manager->name} ===\n";
    echo "User ID: {$manager->id}\n";
    echo "Roles: " . $manager->roles->pluck('name')->join(', ') . "\n\n";
    
    // Change users permission to 'none'
    $managerRole = \Spatie\Permission\Models\Role::where('name', 'Manager')->first();
    $permissionService->setRolePermission($managerRole->id, 'users', 'none');
    echo "Changed Manager's users permission to 'none'\n\n";
    
    // Test a few modules
    $testModules = ['users', 'leads', 'dashboard'];
    
    echo "=== Using PermissionService ===\n";
    foreach ($testModules as $module) {
        $perm = $permissionService->getUserPermissionForModule($manager, $module);
        $canView = $permissionService->hasPermission($manager, $module, 'view');
        echo "$module: {$perm} (canView: " . ($canView ? 'YES' : 'NO') . ")\n";
    }
    
    // Now test the User model's method
    echo "\n=== Using User Model Methods ===\n";
    foreach ($testModules as $module) {
        $canView = $manager->canViewModule($module);
        echo "$module: canViewModule = " . ($canView ? 'YES' : 'NO') . "\n";
    }
    
    // Verify DB
    echo "\n=== Database Check ===\n";
    $usersModule = \App\Models\Module::where('slug', 'users')->first();
    $perm = \App\Models\RoleModulePermission::where('role_id', $managerRole->id)
        ->where('module_id', $usersModule->id)
        ->first();
    echo "Database permission_level: " . $perm->permission_level . "\n";
}
