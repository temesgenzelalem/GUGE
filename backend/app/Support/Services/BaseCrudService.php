<?php

namespace App\Support\Services;

use App\Support\Repositories\AbstractRepository;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Database\Eloquent\Model;

abstract class BaseCrudService extends AbstractService
{
    public function __construct(protected CacheRepository $cache, protected AbstractRepository $repository)
    {
        parent::__construct($cache);
    }

    public function list(array $filters = [], int $perPage = 20)
    {
        $cacheKey = $this->listCacheKey($filters, $perPage);

        return $this->cacheRemember($cacheKey, fn () => $this->repository->paginate($filters, $perPage));
    }

    public function get(Model $model): Model
    {
        return $model;
    }

    public function create(array $data): Model
    {
        $model = $this->repository->create($data);
        $this->clearListCache();

        return $model;
    }

    public function update(Model $model, array $data): Model
    {
        $model = $this->repository->update($model, $data);
        $this->clearModelCache($model);

        return $model;
    }

    public function delete(Model $model): bool
    {
        $this->clearModelCache($model);

        return $this->repository->delete($model);
    }

    protected function clearListCache(): void
    {
        $this->cacheForget($this->domainPrefix().':list');
    }

    protected function clearModelCache(Model $model): void
    {
        $keys = [$this->domainPrefix().':list'];

        if (isset($model->slug)) {
            $keys[] = $this->domainPrefix().':'.$model->slug;
        }

        $this->cacheForget($keys);
    }

    protected function listCacheKey(array $filters = [], int $perPage = 20): string
    {
        return $this->domainPrefix().':list:'.md5(json_encode([$filters, $perPage]));
    }

    protected function detailCacheKey(string $slug): string
    {
        return $this->domainPrefix().':'.$slug;
    }

    protected function domainPrefix(): string
    {
        return static::class;
    }
}
