<?php

namespace Antoniputra\Reviz\Tests\Fixtures\Models;

use Antoniputra\Reviz\RevizTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use RevizTrait;

    protected $guarded = [];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
