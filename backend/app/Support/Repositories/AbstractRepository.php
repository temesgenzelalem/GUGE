<?php

namespace App\Support\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class AbstractRepository
{
    abstract protected function model(): string;

    protected function query(): Builder
    {
        return $this->model()::query();
    }

    public function find(int $id): ?Model
    {
        return $this->query()->find($id);
    }

    public function findById(int $id): ?Model
    {
        return $this->find($id);
    }

    public function findBySlug(string $slug): ?Model
    {
        return $this->query()->where('slug', $slug)->first();
    }

    public function all(array $filters = []): Collection
    {
        return $this->applyFilters($this->query(), $filters)->get();
    }

    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return $this->applyFilters($this->query(), $filters)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function create(array $data): Model
    {
        return $this->model()::create($data);
    }

    public function update(Model $model, array $data): Model
    {
        $model->update($data);

        return $model;
    }

    public function delete(Model $model): bool
    {
        return $model->delete();
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        return $query;
    }
}
