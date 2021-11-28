<?php

namespace Antoniputra\Reviz;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class RevizManager
{
    /**
     * Indicates services status
     * @var bool
     */
    protected $enabled = true;

    /**
     * User that responsible doing the changes.
     */
    protected $user;

    /**
     * Collection of changes list
     * @var array
     */
    protected $items = [];

    /**
     * Disable service
     * @return void
     */
    public function disable(): void
    {
        $this->enabled = false;
    }

    /**
     * Enable service
     * @return void
     */
    public function enable(): void
    {
        $this->enabled = true;
    }

    /**
     * Check curent status of service
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setUser($user): void
    {
        $this->user = ($user instanceof \Closure) ? $user() : $user;
    }

    public function getUser()
    {
        $user = $this->user;
        if (empty($user)) {
            $user = Auth::user();
        }
        return $user;
    }

    /**
     * Store action of item into the collection
     * @return array
     */
    public function pushItems(array $item): array
    {
        array_push($this->items, $item);
        return $this->items;
    }

    /**
     * Get items as Laravel Collection
     * 
     * @return \Illuminate\Support\Collection
     */
    public function getItems(): Collection
    {
        return collect($this->items);
    }

    /**
     * Clear current items collection
     * @return void
     */
    public function clearItems(): void
    {
        $this->items = [];
    }

    /**
     * Store current items to the database
     * and clear items
     * 
     * @param $event
     * @return bool
     */
    public function store($event): bool
    {
        $data = $this->transformData($event);
        RevizEloquent::insert($data);
        $this->clearItems();

        return true;
    }

    /**
     * Perform batch rollback
     * 
     * @param int $batch
     * @return void
     */
    public function batchRollback(int $batch)
    {
        return (new RevizEloquent)->batchRollback($batch);
    }

    /**
     * Prepare data before store to the database
     * 
     * @return array
     */
    private function transformData($event): array
    {
        $auth = $this->getUser();
        $batch = $this->getNewBatch();
        $now = now();
        return $this->getItems()->map(function ($item) use ($auth, $batch, $now) {
            $item['batch'] = $batch;
            $item['created_at'] = $now;
            $item['user_id'] = optional($auth)->id;
            $item['old_value'] = json_encode($item['old_value']);
            $item['new_value'] = json_encode($item['new_value']);
            return $item;
        })->toArray();
    }

    /**
     * Get the fresh batch
     * 
     * @return int
     */
    private function getNewBatch(): int
    {
        $batch = RevizEloquent::withTrashed()->max('batch') ?? 0;
        return $batch + 1;
    }
}
