<?php

namespace Antoniputra\Reviz;

class RevizStoreEvent
{
    public $type;
    public $value;
    
    public function __construct($type = null, $value = null)
    {
        $this->type = $type;
        $this->value = $value;
    }
}
