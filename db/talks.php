<?php

namespace OCA\Owncollab\Db;


use League\Flysystem\Exception;

class Talks
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

    public function get(){
        $sql = "SELECT *
                FROM `{$this->tableName}`";
        $result = $this->connect->queryAll($sql);
        return $result;
    }
    public function getAllTalks(){
        $sql = "SELECT *
                FROM `{$this->tableName}` WHERE rid=0 ";
        $result = $this->connect->queryAll($sql);
        return $result;
    }


}