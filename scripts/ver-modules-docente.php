<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$modules = DB::table('role_modules')->where('role_id', 2)->get();

echo json_encode($modules, JSON_PRETTY_PRINT);
