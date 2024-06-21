<?php
namespace src;

class mssql{

    private $conn;
    private $res;
    public $results = [];
    private $query;
    private $table;
    private $select= " * ";
    private $where;
    private $limit;
    private $group;
    private $order;
    public function __construct($config){
        $serverName = "$config->protocol:$config->addr,$config->port";
        $connectionOptions = array(
            "Database"=>$config->db,
            "Uid"=>$config->user,
            "PWD"=>$config->pass,
            'TrustServerCertificate' => 'true'
        );
        $this->conn = sqlsrv_connect($serverName, $connectionOptions);
        if($this->conn == false){
            $error= sqlsrv_errors();
            $message=  $error[0]['message'];
            echo $message;
            die();
        }
    }

    public function group($arg= false):object{
        if(empty($arg))
            $this->group= "";
        else
        $this->group= "GROUP BY $arg";
        return $this;
    }
    public function table($table){
        $this->res= false;
        $this->results = [];
        $this->query= false;
        $this->table= false;
        $this->limit= false;
        $this->select= " * ";
        $this->where= false;
        $this->group= false;
        $this->order= false;
        $this->table= "[dbo].[".mb_convert_encoding($table,"cp1251")."]";
        return $this;
    }
    public function table2($table){
        $this->res= false;
        $this->results = [];
        $this->query= false;
        $this->table= false;
        $this->limit= false;
        $this->select= " * ";
        $this->where= false;
        $this->group= false;
        $this->order= false;
        $this->table= $table;
        return $this;
    }

    public function getField($record,$field){
        return $record->{mb_convert_encoding($field,"cp1251")};
    }

    public function select($arg= false){
        if($arg === false) return $this;
        $arg= mb_convert_encoding($arg,"cp1251");
        if(is_array($arg)){
            foreach ($arg as $key=>$value)
                $arg[$key]= "$value";
            $arg= implode(",",$arg);
        }
        $this->select= " $arg ";
        return $this;
    }
    public function order($arg= false){
        if($arg === false) return $this;
        $arg= mb_convert_encoding($arg,"cp1251");
        if(is_array($arg)){
            foreach ($arg as $key=>$value)
                $arg[$key]= "$value";
            $arg= implode(",",$arg);
        }
        $this->order= " ORDER BY $arg";
        return $this;
    }
    public function where($arg= false){
        if($arg === false) return $this;
        $this->where = "WHERE ";
        $arg= mb_convert_encoding($arg,"cp1251");
        if(is_array($arg)){
            foreach ($arg as $field=>$value){
                if($this->where != "WHERE ") $this->where.= " AND ";
                $this->where.= "$field='$value'";
            }
        }
        else
            $this->where.= $arg;
        return $this;
    }

    public function limit($p,$c){
        $this->limit = "LIMIT $p, $c";
        return $this;
    }

    public function get(){
        $this->query= "SELECT $this->select FROM $this->table $this->where $this->group $this->order";
        $this->res = sqlsrv_query($this->conn,$this->query,[],["Scrollable" => SQLSRV_CURSOR_KEYSET ]);
        return $this;
    }

    public function numRows(){
        return sqlsrv_num_rows($this->res);
    }

    public function getResults($type= " object"){
        $results= [];
        if($type== "array")
            while ($obj = sqlsrv_fetch_array($this->res)) $results[]= $obj;
        else
            while ($obj = sqlsrv_fetch_object($this->res)) $results[]= $obj;
        $this->results= $results;
        return $this->results;
    }
    public function getResult($type= " object"){
        if($type== "array")
            return sqlsrv_fetch_array($this->res);
        else
            return sqlsrv_fetch_object($this->res);
    }

    public function test(){
    }


    public function save2XML($arr,$name){
        $xml= '<?xml version="1.0" encoding="cp1251"?>
';
        $xml.= "<table>\n";
            foreach ($arr as $record){
                $xml.= "   <record>\n";
                    foreach ($record as $field=>$value)
                        if(!is_object($value))
                            $xml.= "      <$field>$value</$field>\n";
                $xml.= "   </record>\n";
            }
        $xml.= "</table>\n";
        $fp = fopen("xml/$name.xml", 'w');
        fwrite($fp, $xml);
        fclose($fp);
    }
}

function gf($str):string
{
    return mb_convert_encoding($str,"cp1251");
}
