<?php

namespace App\Support\Repositories;

use Illuminate\Database\Eloquent\Builder;

abstract class BaseCrudRepository extends AbstractRepository
{
    protected function applyFilters(Builder $query, array $filters): Builder
    {
        foreach ($filters as $field => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            if ($field === 'search') {
                $value = strtolower(trim($value));

                $query->where(function ($builder) use ($value) {
                    $builder->whereRaw('LOWER(name) LIKE ?', ["%{$value}%"])
                        ->orWhereRaw('LOWER(description) LIKE ?', ["%{$value}%"]);
                });

                continue;
            }

            $query->where($field, $value);
        }

        return $query;
    }
}
