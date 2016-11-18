<?php

namespace OCA\Owncollab\Db;


use League\Flysystem\Exception;

class Users
{
    /** @var Connect $connect object instance working with database */
    private $connect;

    /** @var string $tableName table name in database */
    private $tableName;
    

    /**
     * Project constructor.
     * @param $connect
     * @param $tableName
     */
    public function __construct($connect, $tableName) {
        $this->connect = $connect;
        $this->tableName = '*PREFIX*' . $tableName;
    }

    public function getAllUsers(){
        $sql = "SELECT *
                FROM `{$this->tableName}` ";
        $result = $this->connect->queryAll($sql);
        return $result;
    }
    public function getUser($userId){
        $sql = "SELECT * FROM `{$this->tableName}` where `uid` = :userId";
        $result = $this->connect->query($sql,[':userId'=>$userId]);
        return $result;
    }

}