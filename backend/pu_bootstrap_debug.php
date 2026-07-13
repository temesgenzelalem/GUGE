<?php
require_once __DIR__.'/vendor/autoload.php';
use Illuminate\Contracts\Console\Kernel;

file_put_contents(sys_get_temp_dir().'/phpunit_bootstrap_env.log', json_encode([
    'cwd' => getcwd(),
    'APP_ENV' => getenv('APP_ENV'),
    'DB_CONNECTION' => getenv('DB_CONNECTION'),
    'DB_DATABASE' => getenv('DB_DATABASE'),
    'DB_HOST' => getenv('DB_HOST'),
    'DB_PORT' => getenv('DB_PORT'),
    'DB_USERNAME' => getenv('DB_USERNAME'),
    'DB_PASSWORD' => getenv('DB_PASSWORD') ? '***' : null,
    'DB_SSLMODE' => getenv('DB_SSLMODE'),
], JSON_PRETTY_PRINT));

$app = require __DIR__.'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

file_put_contents(sys_get_temp_dir().'/phpunit_bootstrap_config.log', json_encode([
    'app_env' => $app->environment(),
    'database' => $app['config']->get('database.connections.pgsql'),
], JSON_PRETTY_PRINT));

if ($app->environment('testing')) {
    echo "TESTING ENV yes\n";
}
