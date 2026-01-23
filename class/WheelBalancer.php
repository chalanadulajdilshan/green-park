<?php

class WheelBalancer
{
    public $id;
    public $code;
    public $name;
    public $remark;
    public $is_active;

    // Constructor to load wheel balancer by ID
    public function __construct($id = null)
    {
        if ($id) {
            $query = "SELECT * FROM `wheel_balancer` WHERE `id` = " . (int) $id;
            $db = Database::getInstance();
            $result = mysqli_fetch_array($db->readQuery($query));

            if ($result) {
                $this->id = $result['id'];
                $this->code = $result['code'];
                $this->name = $result['name'];
                $this->remark = $result['remark'];
                $this->is_active = $result['is_active'];
            }
        }
    }

    // Create a new wheel balancer
    public function create()
    {
        $query = "INSERT INTO `wheel_balancer` (`code`, `name`, `remark`, `is_active`) 
                  VALUES ('" . $this->code . "', '" . $this->name . "', '" . $this->remark . "', '" . $this->is_active . "')";
        $db = Database::getInstance();
        $result = $db->readQuery($query);

        return $result ? mysqli_insert_id($db->DB_CON) : false;
    }

    // Update existing wheel balancer
    public function update()
    {
        $query = "UPDATE `wheel_balancer` SET 
                  `code` = '" . $this->code . "', 
                  `name` = '" . $this->name . "', 
                  `remark` = '" . $this->remark . "', 
                  `is_active` = '" . $this->is_active . "' 
                  WHERE `id` = '" . $this->id . "'";
        $db = Database::getInstance();
        return $db->readQuery($query);
    }

    // Delete wheel balancer
    public function delete()
    {
        $query = "DELETE FROM `wheel_balancer` WHERE `id` = '" . $this->id . "'";
        $db = Database::getInstance();
        return $db->readQuery($query);
    }

    // Fetch all wheel balancers
    public function all()
    {
        $query = "SELECT * FROM `wheel_balancer` ORDER BY `name` ASC";
        $db = Database::getInstance();
        $result = $db->readQuery($query);

        $array_res = array();
        while ($row = mysqli_fetch_array($result)) {
            array_push($array_res, $row);
        }

        return $array_res;
    }

    public function getLastID()
    {
        $query = "SELECT * FROM `wheel_balancer` ORDER BY `id` DESC LIMIT 1";
        $db = Database::getInstance();
        $res = $db->readQuery($query);
        $result = mysqli_fetch_array($res);
        return $result ? $result['id'] : 0;
    }

    public function getActiveWheelBalancers()
    {
        $query = "SELECT * FROM `wheel_balancer` WHERE `is_active` = 1 ORDER BY `id` ASC";
        $db = Database::getInstance();
        $result = $db->readQuery($query);
        $array = [];

        while ($row = mysqli_fetch_array($result)) {
            array_push($array, $row);
        }

        return $array;
    }

    public function getByID($id)
    {
        $db = Database::getInstance();

        $query = "SELECT * FROM `wheel_balancer` WHERE `id` = '$id'";
        $result = $db->readQuery($query);
        $array = [];

        while ($row = mysqli_fetch_array($result)) {
            array_push($array, $row);
        }

        return $array;
    }   

}
?>
