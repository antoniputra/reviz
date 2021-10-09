<?php

namespace Antoniputra\Reviz;

use Antoniputra\Reviz\RevizEloquent;

class RevizRepository
{
    protected $model;

    public function __construct(RevizEloquent $model)
    {
        $this->model = $model;
    }

    public function getListForRollback($perPage = 10)
    {
        return $this->model->paginate($perPage);
    }

    public function getByBatch()
    {
        return [];
    }
}
