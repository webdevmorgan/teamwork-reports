<?php
use Illuminate\Support\Collection;
class DataCollector {
    public $collection;
    public $collection_tasklists;

    public function __construct() {
        //$this->collection_tasklists = array();
    }

    public function createCollection($data) {
        $coll = new Collection();
        $this->collection_tasklists = $coll->make($data);
        return $this->collection_tasklists;
    }


}