<?php

namespace App\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\BaseRepositoryInterface;

abstract class BaseRepository implements BaseRepositoryInterface
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->latest()->get();
    }
  
    public function getById(int $id)
    {
        return $this->model->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data)
    {
        $record = $this->getById($id);

        if (!$record) {
            return null;
        }

        $record->update($data);

        return $record->fresh();
    }

    public function delete(int $id)
    {
        $record = $this->getById($id);

        if (!$record) {
            return false;
        }
  
        return $record->delete();
    }
}