<?php
namespace App\Dao;

class CustomerDao extends BaseDao {
    
    public function allCustomers() {
        $sql = "SELECT * FROM customer";
        return $this->doQuery($sql);
    }
}