<?php
namespace src;
class config{
    public object $mssql;
    public object $mysql;

    public function __construct(){
        $this->mssql = (object)[
            "protocol"=>"tcp",
            "addr"=> "10.205.100.32",
            "port"=> "1433",
            "db"=>"Абитуриенты",
            "user"=>"stat",
            "pass"=>"Qw3Rty!",
        ];
        $this->mysql = (object)[
            "host"=>"ucrtecrt.beget.tech",
            "user"=>"ucrtecrt_stats",
            "pass"=>"MGUWebMainPass2024",
            "dbname"=>"ucrtecrt_stats",
        ];
    }

}