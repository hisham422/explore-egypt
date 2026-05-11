<?php
// Simple export script to dump attractions into CSV
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$rows = \DB::table('attractions')
    ->select('id', 'name', 'description', 'updated_at')
    ->orderBy('id')
    ->get();

$dir = __DIR__ . '/../storage/exports';
if (! is_dir($dir)) {
    mkdir($dir, 0755, true);
}

$path = $dir . '/attractions_descriptions.csv';
$fp = fopen($path, 'w');
if (! $fp) {
    echo "Failed to open $path for writing\n";
    exit(1);
}

fputcsv($fp, ['id', 'name', 'description', 'updated_at']);
foreach ($rows as $r) {
    fputcsv($fp, [(int) $r->id, $r->name, $r->description, $r->updated_at]);
}

fclose($fp);
echo "Wrote " . count($rows) . " rows to $path\n";
