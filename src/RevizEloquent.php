<?php

namespace Antoniputra\Reviz;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

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

    public function revizable()
    {
        return $this->morphTo();
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

    public function getCountUpdatedFieldsAttribute()
    {
        return count($this->old_value);
    }

    public function getCreatedAtAttribute($value)
    {
        return ! empty($value) ? Carbon::parse($value) : null;
    }

    public function getCreatedAtFormattedAttribute()
    {
        return optional($this->created_at)->format('D, d M Y - H.i');
    }

    public function rollback()
    {
        $revision = $this;

        $obj = $revision->revizable;
        $obj->unguard();
        $obj->fill($revision->old_value);
        $obj->withoutEvents(function() use ($obj) {
            return $obj->save();
        });
        $obj->reguard();

        $revision->markAsRollbacked();
    }
    
    /**
     * Group Rollback by given batch value
     * 
     * @param int $batch
     * @return \Illuminate\Support\Collection
     */
    public function batchRollback(int $batch): Collection
    {
        $rows = $this->with('revizable')->where('batch', $batch)
            ->where('is_rollbacked', 0)
            ->get();
        $rows->each(function ($row) {
            $row->rollback();
        });

        return $rows;
    }

    /**
     * Mark revision as rollbacked
     * 
     * @return RevizEloquent
     */
    public function markAsRollbacked()
    {
        $this->is_rollbacked = true;
        $this->save();
        return $this;
    }

    public function scopeBulkMarkAsRollbacked(Builder $query, array $ids = [])
    {
        return $query->whereIn('id', $ids)->update([
            'is_rollbacked' => true
        ]);
    }

    /**
     * Get user as Gravatar 
     * @return string|null
     */
    public function getUserGravatar()
    {
        $email = $this->getUserEmail();
        if ($email) {
            return 'https://www.gravatar.com/avatar/'. md5($email);
        }
    }

    /**
     * Get user email from config
     * @return string|null
     */
    public function getUserEmail()
    {
        $emailField = config('reviz.ui.user_email');
        return optional($this->user)->{$emailField};
    }
    
    /**
     * Get user name from config
     * @return string|null
     */
    public function getUserName()
    {
        $nameField = config('reviz.ui.user_name');
        return optional($this->user)->{$nameField};
    }
}
