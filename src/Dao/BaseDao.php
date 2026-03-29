<?php
namespace App\Dao;

use Doctrine\DBAL\Connection;

class BaseDao {
    
    private $conn;

    function __construct(Connection $connection) {
        $this->conn = $connection;
    }

    public function doQuery($sql, $parr=[]) {

        // $stmt = $this->conn->prepare($sql);  // Doctrine\DBAL\Statement
        // foreach ($parr as $key => $value) {
        //     $stmt->bindValue("{$key}", $value);
        // }
        // $result = $stmt->executeQuery();
        // return $result;

        // $resultSet = $this->conn->executeQuery($sql, $parr);
        // $result_array = [];
        // while ($row = $resultSet->fetchAssociative()){
        //     $result_array[] = $row;
        // }

        $result_array = $this->conn->executeQuery($sql, $parr)->fetchAllAssociative();

        return $result_array;
    }

    public function doSQL($sql, $parr=[]) {
        // $stmt = $this->conn->prepare($sql);
        // $rowCount = $stmt->executeStatement($parr); // returns the affected rows count
        // return $rowCount;

        $rowCount = $this->conn->executeStatement($sql, $parr); // returns the affected rows count
        
        return $rowCount;
    }
}