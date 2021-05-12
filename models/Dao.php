<?php

/**
 * Dao数据访问模块
 * @description Holp You Do Good But Not Evil
 * @copyright   Copyright 2014-2015 <ycchen@iwshop.cn>
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chenyong Cai <ycchen@iwshop.cn>
 * @package     Wshop
 * @link        http://www.iwshop.cn
 */
class Dao extends Model {

    private $sqlSelect = 'SELECT ';
    private $sqlFrom = ' FROM ';
    private $sqlWhere = ' WHERE ';
    private $sqlOrWhere = ' OR ';
    private $sqlAndWhere = ' AND ';
    private $sqlLeftJoin = ' LEFT JOIN ';
    private $sqlOn = ' ON ';
    private $sqlInsert = 'INSERT INTO ';
    private $sqlUpdate = 'UPDATE ';

    const VALUE_PLUS = 'Wi0Tf8qNh0J0Com3uc9bBy5i5dpEtOhnJ361fm6xWgM';
    const VALUE_MINUS = 'Wi0Tf8qNh0J0Com3uc9bBy5i5dpEtOhnJ361fm6xWa';
    const FIELD_NOW = 'NOW()';
    
    private $debug = false;

    /**
     * sql字符串
     * @var String 
     */
    private $sqlStr;

    /**
     * 查询结构
     * @var assoc 
     */
    public $ret;

    public function __construct() {
        parent::__construct();
    }

    /**
     * 清空sql
     */
    public function emp() {
        $this->sqlStr = '';
    }

    /**
     * 
     * @param type $as
     * @return \Dao
     */
    public function alias($as) {
        $this->sqlStr .= ' AS ' . $as;
        return $this;
    }

    /**
     * 
     * @param type $condition
     * @return \Dao
     */
    public function having($condition) {
        $this->sqlStr .= ' HAVING(' . $condition . ')';
        return $this;
    }

    /**
     * select查询语句
     * @param type $fields
     * @return \Dao
     */
    public function select($fields = '*') {
        $this->emp();
        $this->sqlStr .= $this->sqlSelect . $fields;
        return $this;
    }

    /**
     * 
     * @param string $table
     * @param array $fields
     * @return \Dao
     */
    public function update($table) {
        $this->emp();
        $this->sqlStr .= $this->sqlUpdate . $table;
        return $this;
    }

    /**
     * 
     * @param array $fields
     * @return \Dao
     */
    public function set($fields) {
        $tmp = array();
        foreach ($fields as $k => $v) {
            if ($v === self::VALUE_PLUS) {
                $tmp[] = "`$k` = `$k` + 1";
            } else if ($v === self::VALUE_MINUS) {
                $tmp[] = "`$k` = `$k` - 1";
            } {
                if ($v !== 'NOW()' && $v !== 'NULL') {
                    $tmp[] = "`$k` = '$v'";
                } else {
                    $tmp[] = "`$k` = $v";
                }
            }
        }
        $this->sqlStr .= ' SET ' . implode(',', $tmp) . ' ';
        return $this;
    }

    /**
     * 统计行数
     * @param type $field
     * @return \Dao
     */
    public function count($field = '*') {
        $this->sqlStr .= ' COUNT(' . $field . ')';
        return $this;
    }

    /**
     * 计算列总值
     * @param type $field
     * @return \Dao
     */
    public function sum($field) {
        $this->sqlStr .= ' SUM(' . $field . ')';
        return $this;
    }

    /**
     * 
     * @param type $table
     * @return \Dao
     */
    public function from($table) {
        $this->sqlStr .= $this->sqlFrom . '`' . $table . '`';
        return $this;
    }

    /**
     * 
     * @param type $condition
     * @return \Dao
     */
    public function where($condition, $second = false) {
        if ($second !== false) {
            $this->sqlStr .= $this->sqlWhere . $condition . " = '$second'";
        } else if ($condition && $condition != '') {
            $this->sqlStr .= $this->sqlWhere . $condition;
        }
        return $this;
    }

    /**
     * 
     * @param type $condition
     * @return \Dao
     */
    public function ow($condition) {
        if ($condition && $condition != '') {
            $this->sqlStr .= $this->sqlOrWhere . $condition;
        }
        return $this;
    }

    /**
     * 
     * @param type $condition
     * @return \Dao
     */
    public function aw($condition) {
        if ($condition && $condition != '') {
            $this->sqlStr .= $this->sqlAndWhere . $condition;
        }
        return $this;
    }

    /**
     * 
     * @param type $f
     * @param type $t
     * @return \Dao
     */
    public function limit($f, $t = false) {
        if (!$t) {
            $this->sqlStr .= " LIMIT $f";
        } else {
            $this->sqlStr .= " LIMIT $f,$t";
        }
        return $this;
    }

    /**
     * 
     * @param type $table
     * @return \Dao
     */
    public function leftJoin($table) {
        $this->sqlStr .= $this->sqlLeftJoin . $table;
        return $this;
    }

    /**
     * 
     * @param type $condition
     * @return \Dao
     */
    public function on($condition) {
        $this->sqlStr .= $this->sqlOn . $condition;
        return $this;
    }

    /**
     * @return \Dao
     */
    public function delete() {
        $this->emp();
        $this->sqlStr .= 'DELETE';
        return $this;
    }

    /**
     * 
     * @return type
     */
    public function exec($cache = true) {
        $this->sqlStr .= ';';
        if($this->debug){
        	error_log("exec sqlStr=====>".$this->sqlStr);
        }
        $this->ret = $this->Db->query($this->sqlStr, $cache);
        return $this->ret;
    }

    /**
     * 获取一个数据
     * @return type
     */
    public function getOne($cache = true) {
        $this->sqlStr .= ';';
        $this->ret = $this->Db->getOne($this->sqlStr, $cache);
        return $this->ret;
    }

    /**
     * 获取一行数据
     * @return type
     */
    public function getOneRow($cache = true) {
        $this->sqlStr .= ';';
        //error_log('get_one_row sql ====>'.$this->sqlStr);
        $this->ret = $this->Db->getOneRow($this->sqlStr, $cache);
        return $this->ret;
    }

    /**
     * 插入语句
     * @param type $table
     * @param type $fields
     * @return \Dao
     */
    public function insert($table, $fields) {
        $this->emp();
        if (strpos($fields, '`') === -1 || false === strpos($fields, '`')) {
            // 自动补充` 避免sql关键字冲突
            $fields = preg_replace('/(\w+)/', "`$1`", $fields);
        }
        $this->sqlStr .= $this->sqlInsert . $table . "($fields)";
        return $this;
    }

    /**
     * 插入多个数据
     * @param type $fields
     * @return \Dao
     */
    public function values($fields) {
        if (is_array($fields)) {
            $tmpArr = array();
            $this->sqlStr .= ' VALUES ';
            foreach ($fields as &$field) {
                if ($field !== 'NOW()' && $field !== 'NULL') {
                    $field = "'$field'";
                }
            }
            $tmpArr[] = '(' . implode(',', $fields) . ')';
            // compo
            $this->sqlStr .= implode(',', $tmpArr);
        } else {
            $this->sqlStr .= ' VALUES (' . $fields . ')';
        }
        return $this;
    }

    /**
     * orderby
     * @param type $field
     * @return \Dao
     */
    public function orderby($field) {
        $this->sqlStr .= ' ORDER BY ' . $field;
        return $this;
    }

    /**
     * groupby
     * @param type $field
     * @return \Dao
     */
    public function groupby($field) {
        $this->sqlStr .= ' GROUP BY ' . $field;
        return $this;
    }

    /**
     * DESC
     * @return \Dao
     */
    public function desc() {
        $this->sqlStr .= ' DESC';
        return $this;
    }

    /**
     * var_dump
     */
    public function dump() {
        var_dump($this->ret);
    }

    /**
     * 输出sql语句
     */
    public function echoSql() {
        echo $this->sqlStr;
    }

}
