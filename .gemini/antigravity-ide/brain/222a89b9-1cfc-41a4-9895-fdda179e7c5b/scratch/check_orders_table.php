<?php

require __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $columns = \Illuminate\Support\Facades\DB::select("SHOW COLUMNS FROM orders");
    echo "ORDERS COLUMNS:\n";
    foreach ($columns as $c) {
        echo "{$c->Field} | {$c->Type} | Nullable: {$c->Null} | Default: {$c->Default}\n";
    }
} catch (\Exception $e) {
    echo "DB Error: " . $e->getMessage() . "\n";
}
