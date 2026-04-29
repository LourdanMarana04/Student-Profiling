<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Department;
$deps = Department::whereIn('name', ['BSIT', 'BSCS'])->get()->map(fn($d) => ['id' => $d->id, 'name' => $d->name]);
echo json_encode($deps, JSON_PRETTY_PRINT);
