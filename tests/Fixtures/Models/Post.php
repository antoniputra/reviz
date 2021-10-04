<?php

namespace Antoniputra\Reviz\Tests\Fixtures\Models;

use Antoniputra\Reviz\RevizTrait;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use RevizTrait;

    protected $guarded = [];

    public $revizIgnoreFields = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
