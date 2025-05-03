<?php
require_once 'config.php';
use Illuminate\Database\Capsule\Manager as Capsule;

class MySQLHandler implements DbHandler {
    private $capsule;
    private $items = array();
    private $totalRecords = 0;
    private $pages = 0;

    public function __construct() {
        $this->capsule = new Capsule;
        $this->capsule->addConnection([
            'driver'    => 'mysql',
            'host'      => __HOST__,
            'database'  => __DB__,
            'username'  => __USER__,
            'password'  => __PASS__,
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ]);

        $this->capsule->setAsGlobal();
        $this->capsule->bootEloquent();
    }

    public function connect() {
        try {
            $this->capsule->getConnection()->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function disconnect() {
        try {
            if ($this->capsule->getConnection()->isConnected()) {
                $this->capsule->getConnection()->disconnect();
                return true;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function get_data($fields = array(), $start = 0) {
        $query = Capsule::table('items');
        $this->items = $query->get()->toArray();
        $this->totalRecords = count($this->items);
        $this->pages = ceil($this->totalRecords / __RECORDS_PER_PAGE__);
    
        if (!empty($fields)) {
            $query->select($fields);
        }
        
        return $query->skip($start)
                    ->take(__RECORDS_PER_PAGE__)
                    ->get()
                    ->toArray();
    }

    public function get_record_by_id($id, $primary_key) {
        return Capsule::table('items')
                     ->where($primary_key, $id)
                     ->first();
    }

    public function search_data($searchTerm) {
        $query = Capsule::table('items')
                     ->where('product_name', 'LIKE', '%' . $searchTerm . '%')
                     ->get()
                     ->toArray();
        
        return $query;
    }
    public function get_pages() {
        return $this->pages;
    }
} 