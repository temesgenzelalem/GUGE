<?php

namespace App\Domain\Dashboard\Metrics;

use App\Domain\Dashboard\DashboardCache;
use Illuminate\Support\Facades\DB;

class SystemMetricsService
{
    public function getMetrics(): array
    {
        return DashboardCache::remember(self::cacheKey(), function () {
            return [
                'storage_usage' => $this->getStorageUsage(),
                'database_status' => $this->getDatabaseStatus(),
                'application_version' => app()->version(),
            ];
        });
    }

    public static function cacheKey(): string
    {
        return DashboardCache::SYSTEM;
    }

    protected function getStorageUsage(): array
    {
        $disk = config('filesystems.default');
        $sizeBytes = 0;

        $root = config("filesystems.disks.{$disk}.root");

        if ($root && is_dir($root)) {
            $sizeBytes = $this->directorySize($root);
        }

        return [
            'disk' => $disk,
            'usage_bytes' => $sizeBytes,
        ];
    }

    protected function directorySize(string $path): int
    {
        if (! is_dir($path)) {
            return 0;
        }

        $size = 0;

        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS)) as $file) {
            $size += $file->getSize();
        }

        return $size;
    }

    protected function getDatabaseStatus(): array
    {
        try {
            $connection = DB::connection();
            $connection->getPdo();

            return [
                'status' => 'ok',
                'driver' => $connection->getDriverName(),
                'version' => $connection->getPdo()->getAttribute(\PDO::ATTR_SERVER_VERSION),
            ];
        } catch (\Throwable $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }
}
