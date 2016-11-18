<?php

namespace OCA\Owncollab\Db;


use League\Flysystem\Exception;

class Project_data
{
    /** @var Connect $connect object instance working with database */
    private $connect;

    /** @var string $tableName table name in database */
    private $tableName;

    /** @var string $fields table fields name in database */
    private $fields = [
        'uid',
        'image',
        'description1',
        'description2',
        'street',
        'zip',
        'city',
        'country',
        'comment'
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

    public function getData($userId)
    {
        $sql = "SELECT * FROM `{$this->tableName}` WHERE `uid` = :userId";
        $result = $this->connect->query($sql, [':userId' => $userId]);
        return $result;
    }

    public function update($data, $userId)
    {
        $result = false;


        $sql = "UPDATE {$this->tableName}
                    SET image = :image,
                        description1 = :description1,
                        description2 =:description2,
                        street =:street,
                        zip =:zip,
                        city =:city,
                        country =:country,
                        comment =:comment
                     WHERE uid = :userId";
            $result = $this->connect->db->executeUpdate($sql, [
                ':image'=> $data['image'],
                ':description1'=> $data['description1'],
                ':description2'=> $data['description2'],
                ':street'=> $data['street'],
                ':zip'=> $data['zip'],
                ':city'=> $data['city'],
                ':country'=> $data['country'],
                ':comment'=> $data['comment'],
                ':userId'=>$userId
            ]);
        return $result;
    }

    public function insert($data, $userId){
        $update = [];
        foreach($data as $k=>$v){
            if(in_array($k, $this->fields) and !empty($v)){
                $update[$k] = $v;
            }
        }
        $update['uid'] = $userId;
        try{
            $_result = $this->connect->insert($this->tableName, $update);
            if($_result)
                return true;
        }catch(\Exception $e){
            $result = 'error:' . $e->getMessage();
        }
        return $result;
    }


}