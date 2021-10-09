<?php

namespace Antoniputra\Reviz\Http\Controllers;

use Antoniputra\Reviz\RevizRepository;
use Illuminate\Routing\Controller;

class AdminController extends Controller
{
    protected $repo;

    public function __construct(RevizRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index()
    {
        $rows = $this->repo->getListForRollback();
        return view('reviz::index', [
            'rows' => $rows
        ]);
    }
}
