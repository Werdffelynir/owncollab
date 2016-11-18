<?php

namespace OCA\Owncollab\Db;


use League\Flysystem\Exception;

class Group_user
{
    /** @var Connect $connect object instance working with database */
    private $connect;

    /** @var string $tableName table name in database */
    private $tableName;

    /** @var string $fields table fields name in database */
    private $fields = [
        'gid',
        'uid'
    ];

    /**
     * Project constructor.
     * @param $connect
     * @param $tableName
     */
    public function __construct($connect, $tableName)
    {
        $this->connect = $connect;
        $this->tableName = '*PREFIX*' . $tableName;
    }

    public function check($gid,$uid)
    {
        $sql = "SELECT * FROM `{$this->tableName}` WHERE `gid`=:gid and `uid` = :uid";
        $result = $this->connect->query($sql, [':gid'=>$gid, ':uid' => $uid]);
        return $result;
    }




}