<?php

namespace Antoniputra\Reviz;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletes;

class RevizEloquent extends Model
{
    use SoftDeletes;

    public $table = 'reviz';

    public $timestamps = false;

    protected $guarded = [];

    public static function boot()
    {
        parent::boot();

        static::addGlobalScope('descending', function ($builder) {
            $builder->latest('batch');
        });

        static::creating(function ($model) {
            $model->created_at = $model->freshTimestamp();
        });
    }

    public function user()
    {
        $userClass = config('auth.providers.users.model');
        return $this->belongsTo($userClass);
    }

    public function getOldValueAttribute($value)
    {
        return $value ? json_decode($value, true) : null;
    }
    
    public function getNewValueAttribute($value)
    {
        return $value ? json_decode($value, true) : null;
    }
    
    public function getFunnelDetailAttribute($value)
    {
        return $value ? json_decode($value, true) : null;
    }
}
