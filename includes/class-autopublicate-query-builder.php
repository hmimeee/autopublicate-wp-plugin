<?php

class AP_DB
{
    use AP_Pluralize;

    protected $db;

    protected $wpdb;

    protected $table;

    private $data;

    private $each;

    private $joins;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table = $this->get_table();

        $this->db = $this->db()->table($this->table);
    }

    private function db()
    {
        $connection = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8', DB_USER, DB_PASSWORD);

        // create a new mysql query builder
        return new \ClanCats\Hydrahon\Builder('mysql', function ($query, $queryString, $queryParameters) use ($connection) {
            $statement = $connection->prepare($queryString);
            $statement->execute($queryParameters);

            // when the query is fetchable return all results and let hydrahon do the rest
            // (there's no results to be fetched for an update-query for example)
            if ($query instanceof \ClanCats\Hydrahon\Query\Sql\FetchableInterface) {
                return $statement->fetchAll(\PDO::FETCH_ASSOC);
            }
            // when the query is a instance of a insert return the last inserted id  
            elseif ($query instanceof \ClanCats\Hydrahon\Query\Sql\Insert) {
                return $connection->lastInsertId();
            }
            // when the query is not a instance of insert or fetchable then
            // return the number os rows affected
            else {
                return $statement->rowCount();
            }
        });
    }

    public function get_table()
    {
        return $this->pluralize($this->table ?? $this->wpdb->prefix . strtolower(str_replace('_Model', '', $this::class)));
    }

    public function _query()
    {
        return $this->db->table($this->table);
    }

    public function _find($value, $key = null)
    {
        $this->data = $this->_query()->select()->find($value, $key ?? ($this->primary_key ?? 'id'));
        $this->loadWith();

        return $this->data;
    }

    public function _get()
    {
        $this->data = $this->db->get();
        $this->loadWith();

        return $this->data;
    }

    public function _where(...$args)
    {
        if(!method_exists($this->db, 'where')) $this->db = $this->db->select();
        $this->db = $this->db->where(...$args);

        return $this->db;
    }

    public function _orWhere(...$args)
    {
        if(!method_exists($this->db, 'orWhere')) $this->db = $this->db->select();
        $this->db = $this->db->orWhere(...$args);

        return $this->db;
    }

    public function _select($fields)
    {
        return $this->db->select($fields);
    }

    public function loadWith()
    {
        if (isset($this->joins)) {
            foreach ($this->joins as $key => $join) {

                if (is_array(reset($this->data))) {
                    foreach ($this->data as $i => $dt) {
                        $this->each = $dt;
                        $this->data[$i][$join] = $this->$join();
                    }
                } else {
                    $this->each = $this->data;
                    $this->data[$join] = $this->$join();
                }
            }
        }
    }

    public function _with($joins, ...$more)
    {
        if (is_string($joins)) {
            $this->joins = array_merge([$joins], $more);
        } else {
            $this->joins = $joins;
        }

        return $this->db->select();
    }

    public function relation($type, $table, $foreign_key, $local_key)
    {
        switch ($type) {
            case 'belongs_to':
                return $this->db()->table($this->wpdb->prefix . $table)
                    ->select()
                    ->where($foreign_key, $this->each[$local_key])
                    ->one();
                break;

            case 'has_many':
                return $this->db()->table($this->wpdb->prefix . $table)
                    ->select()
                    ->where($foreign_key, $this->each[$local_key])
                    ->get();
                break;

            default:
                # code...
                break;
        }
    }

    public function __call($method, $args)
    {
        return $this->call($method, $args);
    }

    public static function __callStatic($method, $args)
    {
        return (new static())->call($method, $args);
    }

    private function call($method, $args)
    {
        if (!method_exists($this, '_' . $method)) {

            if (method_exists($this->db, $method)) {
                $this->db = $this->db->{$method}(...$args);

                return $this;
            } else if (method_exists($this->db->select(), $method)) {
                $this->db = $this->db->select()->{$method}(...$args);

                return $this;
            }

            throw new Exception('Call undefined method ' . $method);
        }

        return $this->{'_' . $method}(...$args);
    }
}
