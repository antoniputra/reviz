<?php

namespace Antoniputra\Reviz\Http\Controllers;

use Antoniputra\Reviz\Http\Middleware\Authenticate;
use Antoniputra\Reviz\RevizRepository;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Routing\Controller;

class AdminController extends Controller
{
    protected $repo;

    public function __construct(RevizRepository $repo)
    {
        $this->middleware(Authenticate::class);
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
        if (!$revision) {
            abort(404);
        }

        return view('reviz::show', [
            'revision' => $revision
        ]);
    }
}
