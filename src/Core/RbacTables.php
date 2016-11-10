<?php

/**
 * RbacTables
 * @author monky
 */
class RbacTables {

    private $tableName;
    /** @var PDO */
    private $dbMaster;
    /** @var PDO */
    private $dbSlave;

    private function getDb($name, $select) {
        return DBFactory::factory($name, $select);
    }

    public function init($name) {
        $this->table = $name;
        $this->dbMaster = $this->getDb('rbac', 'master');
        $this->dbSlave = $this->getDb('rbac', 'slave');
    }

    public function select($fields, $conditions, $order = '', $limit = ''){
        $where_array = [];
        $pdo_params = [];
        foreach ($conditions as $key => $val){
            $pkey = ":key";
            $where_array[] = "$key = $pkey";
            $pdo_params[$pkey] = $val;
        }
        $where = implode(' AND ', $where_array);
        try{
            $sql = "SELECT {$fields} FROM " . $this->tableName . " WHERE {$where} {$order} {$limit}";
            $stmt = $this->dbSlave->prepare($sql);
            $stmt->execute($pdo_params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $ex) {
            throw new RBACException($ex);
        }
    }

    public function insert($data){
        $f = $v = '';
        $array = array();
        foreach ($data as $key => $value) {
            $f .= ",`$key`";
            $v .= ",:$key";
            $array[':' . $key] = $value;
        }
        try {
            $sql = " INSERT INTO `" . $this->tableName . "` (" . substr($f, 1) . ") "
                . "VALUES (" . substr($v, 1) . ") ";
            $stmt = $this->dbMaster->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $stmt->execute($array);
            return $this->dbMaster->lastInsertId();
        } catch (Exception $ex) {
            throw new RBACException($ex);
        }
    }

    public function update($id, $data){
        if (!$id || empty($data)) {
            throw new RBACException();
        }

        $f = '';
        $array = array(':id' => $id);
        foreach ($data as $key => $value) {
            if ($value === null) {
                continue;
            }
            $f .= ",`" . $key . "` = :$key";
            $array[':' . $key] = $value;
        }

        try {
            $sql = " UPDATE `" . $this->tableName . "` SET " . substr($f, 1) . " WHERE `id` = :id ";
            $stmt = $this->dbMaster->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            return $stmt->execute($array);
        } catch (Exception $ex) {
            throw new RBACException($ex);
        }
    }
}
