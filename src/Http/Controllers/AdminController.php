<?php

namespace Antoniputra\Reviz\Http\Controllers;

use Antoniputra\Reviz\RevizRepository;
use Illuminate\Database\Eloquent\Relations\Relation;
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
        $revisions = $this->repo->getListForRollback(5);
        return view('reviz::index', [
            'revisions' => $revisions
        ]);
    }

    public function show($id)
    {
        $revision = $this->repo->getById($id);
        return view('reviz::show', [
            'revision' => $revision
        ]);
    }
}
