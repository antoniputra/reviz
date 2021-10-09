<?php

namespace Antoniputra\Reviz;

use Antoniputra\Reviz\Facades\Reviz;
use Illuminate\Database\Eloquent\Relations\Relation;

trait RevizTrait
{
    public function reviz()
    {
        return $this->morphMany(RevizEloquent::class, 'revizable');
    }

    public function revisionList()
    {
        return $this->reviz()->where('is_rollbacked', 0);
    }

    public function rollbackedList()
    {
        return $this->reviz()->where('is_rollbacked', 1);
    }

    /**
     * If ID given, then we need to
     * rollback the previous revision too
     * 
     * @param int $id
     * @return $this
     */
    public function rollback(?int $id = null)
    {
        $revisions = $this->revisionList;
        
        if ($revisions->isEmpty()) {
            return;
        }

        if (! $id) {
            $revisions->first()->rollback();
            return $this->refresh();
        }

        $ids = [];
        $revisions->each(function ($item) use ($id, &$ids) {
            // Stop the iteration
            if ($item->id == $id) {
                $item->rollback();
                return false;
            }

            // Collect id
            $ids[] = $item->id;
        });

        $this->revisionList()->bulkMarkAsRollbacked($ids);
        return $this->refresh();
    }

    public static function boot()
    {
        parent::boot();

        $tableName = with(new static)->getTable();
        Relation::morphMap([
            $tableName => get_class(),
        ]);
    }

    public static function bootRevizTrait()
    {
        static::saved(function ($model) {
            $model->revizAfterSave();
        });
    }

    /**
     * Implement logic transform data
     * To be push into Bag Collection
     * 
     * @return void
     */
    public function revizAfterSave()
    {
        if (! Reviz::isEnabled()) {
            return;
        }

        $changes = $this->revizFilterChanges();
        if (!$changes) {
            return;
        }

        $changesKeys = array_keys($changes);
        $original = collect($this->getOriginal())->only($changesKeys);

        Reviz::pushItems([
            'revizable_type' => $this->getTable(),
            'revizable_id' => $this->id,
            'old_value' => $original->toArray(),
            'new_value' => $changes,
        ]);
    }

    /**
     * Validate the changes based on the configuration
     * 
     * @return array
     */
    private function revizFilterChanges()
    {
        $changes = $this->getChanges();
        if (!$changes) {
            return;
        }
        
        // Ignore global fields
        $ignoredFields = config('reviz.ignore_fields', []);
        foreach ($ignoredFields as $field) {
            unset($changes[$field]);
        }

        // Ignore entity fields
        if (property_exists($this, 'revizIgnoreFields')) {
            $entityIgnoredFields = $this->revizIgnoreFields;
            foreach ($entityIgnoredFields as $field) {
                unset($changes[$field]);
            }
        }

        return $changes;
    }
}
