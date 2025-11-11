<?php
require __DIR__.'/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== USUARIOS EN RAILWAY ===\n\n";

$users = DB::table('users')->get();
foreach ($users as $user) {
    echo "ID: {$user->id}\n";
    echo "Nombre: {$user->name}\n";
    echo "Email: {$user->email}\n";

    $roles = DB::table('role_user')
        ->join('roles', 'roles.id', '=', 'role_user.role_id')
        ->where('role_user.user_id', $user->id)
        ->pluck('roles.name')
        ->toArray();

    echo "Roles: " . (count($roles) > 0 ? implode(', ', $roles) : 'Sin roles') . "\n";
    echo "---\n";
}

echo "\nTotal usuarios: " . count($users) . "\n";
