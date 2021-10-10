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
        return $this->model->with('revizable', 'user')->paginate($perPage);
    }

    public function getById($id)
    {
        return $this->model->with('revizable', 'user')->find($id);
    }
}
