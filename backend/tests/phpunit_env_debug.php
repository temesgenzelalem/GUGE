<?php
require_once __DIR__.'/../vendor/autoload.php';
use Illuminate\Contracts\Console\Kernel;

$envs = ['APP_ENV','DB_CONNECTION','DB_DATABASE','DB_HOST','DB_USERNAME','DB_PASSWORD','DB_SSLMODE'];
foreach ($envs as $name) {
    echo "$name BEFORE=>".($_ENV[$name] ?? getenv($name) ?? 'unset')."\n";
}

$app = require __DIR__.'/../bootstrap/app.php';
echo "ENV_FILE BEFORE=>".$app->environmentFile()."\n";
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

echo "APP_ENV AFTER=>".$app->environment()."\n";
foreach ($envs as $name) {
    echo "$name AFTER=>".env($name, 'unset')."\n";
}

echo "ENV_FILE AFTER=>".$app->environmentFile()."\n";
