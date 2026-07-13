<?php

/**
 * GUGE Test Bootstrap
 *
 * Runs a fresh PostgreSQL schema once before the full PHPUnit suite.
 */

require_once __DIR__.'/../vendor/autoload.php';

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Database\QueryException;

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

if ($app->environment('testing')) {
    $attempt = 0;
    $maxAttempts = 6;
    $sleepSeconds = 3;

    while (true) {
        try {
            $attempt++;

            $app['config']->set('database.default', 'pgsql');
            if ($app->resolved('db')) {
                $app['db']->purge('pgsql');
                $app['db']->reconnect('pgsql');
            }

            $app->make(Kernel::class)
                ->call('migrate:fresh', ['--force' => true]);

            break;
        } catch (\Throwable $exception) {
            if ($attempt >= $maxAttempts) {
                throw $exception;
            }

            sleep($sleepSeconds);
        }
    }
}
