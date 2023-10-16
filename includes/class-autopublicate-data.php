<?php

class BC_DATA
{
    public $table;
    public $db;

    public function __construct()
    {
        global $wpdb;
        $this->db = $wpdb;
        $this->table = $wpdb->prefix . 'benefit_calculator';
    }

    public function all()
    {
        return $this->db->get_results("SELECT * FROM $this->table");
    }

    public function where($column, $logic, $value = null)
    {
        if ($value)
            $data = $this->db->get_results("SELECT * FROM $this->table WHERE $column $logic '$value' ORDER BY id DESC");
        else
            $data = $this->db->get_results("SELECT * FROM $this->table WHERE $column = '$logic' ORDER BY id DESC");

        return $data;
    }

    public function whereRaw($query)
    {
        $data = $this->db->get_results("SELECT * FROM $this->table WHERE $query ORDER BY id DESC");

        return $data;
    }

    public function whereOne($column, $logic, $value = null)
    {
        if ($value)
            $data = $this->db->get_row("SELECT * FROM $this->table WHERE $column $logic '$value' ORDER BY id DESC");
        else
            $data = $this->db->get_row("SELECT * FROM $this->table WHERE $column = '$logic' ORDER BY id DESC");

        return $data;
    }

    public function create($data)
    {
        $qval = "'" . implode("','", $data) . "'";
        $qkey = implode(',', array_keys($data));
        $sql = "INSERT INTO $this->table ($qkey) VALUES ( $qval )";
        return $this->db->query($sql);
    }

    public function update($id, $data)
    {
        $changes = null;
        foreach ($data as $key => $value) {
            if ($data[array_key_first($data)] != $value)
                $changes .= ',';

            if (is_array($value))
                $value = json_encode($value);

            $changes .= $key . "='" . $value . "'";
        }

        $sql = 'UPDATE ' . $this->table . ' SET ' . $changes . ' WHERE id=' . $id;

        return $this->db->query($sql);
    }

    public function find($id)
    {
        return $this->whereOne('id', $id);
    }

    public function delete($id)
    {
        return $this->db->get_row("DELETE FROM $this->table WHERE id=$id");
    }
}
