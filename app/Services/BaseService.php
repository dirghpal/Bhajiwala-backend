<?php

namespace App\Services;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

abstract class BaseService
{
    /**
     * Get All Records
     */
    public function getAll(Model $model): Collection
    {
        return $model::latest()->get();
    }

    /**
     * Get By ID
     */
    public function getById(Model $model, int $id): ?Model
    {
        return $model::find($id);
    }

    /**
     * Create Record
     */
    public function create(Model $model, array $data): Model
    {
        return $model::create($data);
    }

    /**
     * Update Record
     */
    public function update(Model $model, Model $record, array $data): Model
    {
        $record->update($data);

        return $record->fresh();
    }

    /**
     * Delete Record
     */
    public function delete(Model $record): bool
    {
        return $record->delete();
    }

    /**
     * Find By Field
     */
    public function findBy(Model $model, string $field, mixed $value): ?Model
    {
        return $model::where($field, $value)->first();
    }
}