<?php

/**
 * Node
 * @desc 节点模型
 * @author monky
 */
class Node extends RbacTables{

    use Trait_DB;

    private $tableName = 'node';

    public function __construct() {
        $this->init($this->tableName);
    }

    /**
     * 添加节点
     * @param $name string 节点名称
     * @param $title string 节点标题
     * @param $type int 节点类型
     * @param $pid int 父节点ID
     * @param $sort int 排序值
     * @return string 节点ID
     * @throws RBACException
     */
    public function add($name, $title, $type, $pid, $sort){
        $data['name'] = $name;
        $data['title'] = $title;
        $data['type'] = $type;
        $data['pid'] = $pid;
        $data['sort'] = $sort;
        return $this->insert($data);
    }

    /**
     * 删除节点
     * @param $node_id int 节点ID
     * @return bool
     * @throws RBACException
     */
    public function delete($node_id){
        return $this->update($node_id, ['deleted' => 1]);
    }

    public function getlist($name, $title, $type, $sort){
        $where['name'] = $name;
        $where['title'] = $title;
        $where['type'] = $type;
        $where['sort'] = $sort;
        return $this->select('*', $where);
    }

    public function getInfoById($node_id){
        return $this->select('*', "id = $node_id");
    }
}