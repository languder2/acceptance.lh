<?php

namespace src;
use src\config;

class report{
    public object $config;
    public object $mssql;
    public object $mysql;

    public object $app;
    public object $user;
    public object $family;

    public function __construct(){
        $this->config= new config();
        $this->mssql= new mssql($this->config->mssql);
        $this->mysql= new mysql($this->config->mysql);
    }

    public function test():bool{
        print_r($this->config);
        return true;
    }

    public function convert($object):object{
        $result= (object)[];
        foreach ($object as $key=>$val){
            $key= mb_convert_encoding($key,"utf8","cp1251");
            if(is_object($val))
                $val= $val->format("Y-m-d H:i:s");
            else
                $val= mb_convert_encoding((string)$val,"utf8","cp1251");
            $result->{$key}= $val;
        }
        return $result;
    }
    public function getAppsCount():int{
        return $this->mssql->table("Все_Заявления")->where(["Удалена"=>0])->get()->numRows();
    }
    public function getApp(&$sql,$format= "object",$appID= false):bool{
        $where= ["Удалена"=>0];
        if($appID)
            $where['Код_Заявления>']= $appID;
        $res= $this->mssql->table("Все_Заявления")->where("Удалена=0 AND Код_Заявления>$appID")->get();
        if(!$res->numRows()) return false;
        $this->app= $res->getResult($format);
        $this->app= $this->convert($this->app);
        $sql= [
            "appID"=> $this->app->{"Код_Заявления"},
            "appDate"=> $this->app->{'Дата_Подачи'},
            "uID"=> $this->app->{"ID"},
            "appPriority"=> $this->app->{"Приоритет"},
            "appBasis"=> $this->app->{"Основания"},
            "numLD"=> $this->app->{"НомерЛД"},
            "approval"=> $this->app->{"СогласенНаЗачисление"},
            "appEdForm"=>$this->app->{"КодФормы"},
            "appCourse"=> $this->app->{"Курс"},
            "specID"=>$this->app->{"КодПрофиля"},
            "orderNum"=>$this->app->{"Приказ"},
        ];
        return true;
    }
    public function getApps(){
        return $this->mssql->table("Все_Заявления")->where(["Удалена"=>0])->get()->getResults();
    }
    public function getAppStatus(&$sql,$op= false):bool{
        $variants= [
            6=> "Принят",
            7=> "Отклонено",
            10=> "В обработке"
        ];
        $sql= $variants[$op]??$op;
        return true;
    }
    public function getSubmittingMethod(&$sql,$op= false):bool{
        $variants=[
            1=>"Лично",
            2=>"Личный кабинет",
            3=>"Почтой",
            4=>"ЕПГУ",
        ];
        $sql= $variants[$op]??$op;
        return true;
    }

    public function getOrderDate(&$sql,$op= false):bool{
        if(empty($op)) return false;
        $res= $this->mssql->table("ПриказыФИС")->select("ДатаПриказа")->where(["UID"=>$op])->get();
        if($res->numRows()==0) return false;
        $tmp= $this->convert($res->getResult());
        $sql= $tmp->{"ДатаПриказа"};
        return true;
    }
    public function getFields($table,$select,$where,$op):bool|string{
        if(empty($op)) return false;
        $res= $this->mssql->table($table)->select($select)->where([$where=>$op])->get();
        if($res->numRows()==0) return false;
        $tmp= $this->convert($res->getResult());
        return $tmp->{$select};
    }
    public function getUser(&$sql):bool{
        if(empty($sql['uID'])) return false;
        $docOriginalType= [
            1=>"Оригинал",
            2=>"Мотивационное заявление",
            3=>"ЕПГУ"
        ];
        $typeNP= [
            1=>"Город федерального значения",
            2=>"Город",
            3=>"Населенный пункт городского типа",
            4=>"Населенный пункт сельского типа",
        ];
        $langs= [
            13=>"Английский язык",
            14=>"Немецкий язык"
        ];
        $this->user= $this->convert($this->mssql->table("Все_Абитуриенты")->where(["ID"=>$sql['uID']])->get()->getResult());
        $res= [
            "operator"=>self::getFields("Пользователи","ФИО","ID",$this->user->{"КодПользователя"}),
            "surname"=> $this->user->{"Фамилия"},
            "name"=> $this->user->{"Имя"},
            "patronymic"=> $this->user->{"Отчество"},
            "birthday"=> empty($this->user->{"Дата_Рождения"})?"":$this->user->{"Дата_Рождения"},
            "snils"=> $this->user->{"СНИЛС"},
            "sex"=> $this->user->{"Пол"},
            "email"=> $this->user->{"E_Mail"},
            "citizenship"=> $this->user->{"Гражданство"},
            "uStatus"=>self::getFields("Статус_Студента","Статус","Код",$this->user->{"Статус"}),
            "benefits"=>self::getFields("Льготы","Льготы","КодЛьгот",$this->user->{"Льготы"}),
            "docType"=>$this->user->{"Тип_Удостоверения"},
            "docSerial"=>$this->user->{"ПаспортСерия"},
            "docNumber"=>$this->user->{"ПаспортНомер"},
            "docWhoIssued "=>$this->user->{"Кем_Выдан"},
            "docDateIssued "=>empty($this->user->{"Дата_Выдачи"})?"":$this->user->{"Дата_Выдачи"},
            "placeBirths"=>$this->user->{"Место_Рождения"},
            "docOriginal"=>$this->user->{"ОригиналДокумента"}?"y":"n",
            "docOriginalType"=>!empty($this->user->{"ОригиналКодВида"})?$docOriginalType[$this->user->{"ОригиналКодВида"}]:" ",
            "address"=>
                $this->user->{"Страна_ПП"}.", ".
                $this->user->{"Регион_ПП"}.", ".
                $this->user->{"Город_ПП"}.", ".
                $this->user->{"Улица_ПП"}.", ".
                $this->user->{"Дом_Кв_ПП"}.", ".
                $this->user->{"Корпус_ПП"}.", ".
                $this->user->{"Квартира_ПП"}.", ",
            "addressActual"=>
                $this->user->{"Страна_Родители"}.", ".
                $this->user->{"Регион_Родители"}.", ".
                $this->user->{"Город_Родители"}.", ".
                $this->user->{"Улица_Родители"}.", ".
                $this->user->{"Дом_Кв_Родители"}.", ".
                $this->user->{"Корпус_Родители"}.", ".
                $this->user->{"Квартира_Родители"}.", ",
            "hostel"=>$this->user->{"Общежитие"}?"y":"n",
            "mobile"=>$this->user->{"Мобильный"},
            "phone"=>$this->user->{"Телефон_ПП"},
            "typeNP"=>empty($this->user->{"ТипНП_ПП"})?"":$typeNP[$this->user->{"ТипНП_ПП"}],
            "edName"=>$this->user->{"Уч_Заведение"},
            "edType"=>$this->user->{"ТипОбразования"},
            "edCountry"=>$this->user->{"Страна_УЗ"},
            "edRegion"=>$this->user->{"Где_Находится_УЗ"},
            "edFinish"=>$this->user->{"Год_Окончания_УЗ"},
            "edLang"=>empty($this->user->{"Изучаемый_Язык"})?"":$langs[$this->user->{"Изучаемый_Язык"}],
            "edDocType"=>$this->user->{"Тип_Образ_Документа"},
            "edDocSerial"=>$this->user->{"Серия_Аттестата"},
            "edDocNumber"=>$this->user->{"Номер_Аттестата"},
            "edDocDate"=>empty($this->user->{"Дата_Выдачи_УД"})?"":$this->user->{"Дата_Выдачи_УД"},
            "edScore"=>$this->user->{"СрБаллАттестата"},
            "docBenefits"=>$this->user->{"ДокументНаЛьготы"},
        ];
        $sql= $sql+$res;
        return true;
    }
    public function getFamily(&$sql):bool{
        if(empty($sql['uID'])) return false;
        $res= $this->mssql->table("Семья")->where(["IDa"=>$sql['uID']])->get()->getResults();
        if(count($res)==0) return true;
        foreach ($res as $rec ){
            $rec= $this->convert($rec);
            $family[]= [
                "fType"=> $rec->{"Родство"},
                "fFIO"=> $rec->{"ФИО"},
                "fPhone"=> $rec->{"Телефон"},
                "fDoc"=> $rec->{"ПаспортТип"},
            ];
        }
        $sql['family']= json_encode($family);
        return true;
    }

    public function getTrials(&$sql):bool{
        if(empty($sql['uID'])) return false;
        $res= $this->mssql->table("Все_Оценки")->where(["ID"=>$sql['uID'],"Код_Заявления"=>$sql['appID']])->get()->getResults();
        if(count($res)==0) return true;
        foreach ($res as $rec) {
            $rec = $this->convert($rec);
            $trials[]= [
                "score"=>$rec->{"Оценка"},
                "discipline"=>self::getFields("АбитДисциплины","Дисциплина","Код",$rec->{"Код_Дисциплины"}),
                "date"=>$rec->{"Дата_Экзамена"},
                "type"=>self::getFields("Вид_Испытания","Название","Код",$rec->{"Код_Испытания"}),
            ];

        }
        $sql['trials']= json_encode($trials);
        return true;
    }
    public function getAchivements(&$sql):bool{
        if(empty($sql['uID'])) return false;
        $res= $this->mssql->table("Достижения")->where(["ID"=>$sql['uID']])->get()->getResults();
        if(count($res)==0) return true;
        foreach ($res as $rec) {
            $rec = $this->convert($rec);
            $ach[]= [
                "achName"=>self::getFields("ДостиженияВиды","Балл","КодДостижения",$rec->{"КодИД"}),
                "achScore"=>self::getFields("ДостиженияВиды","Название","КодДостижения",$rec->{"КодИД"}),
            ];
        }
        $sql['achs']= json_encode($ach);
        return true;
    }
    public function getSpec(&$sql):bool{
        if(empty($sql['uID'])) return false;
        $specCode= self::getFields("СпециальностиПрофили","НаправлениеКод","Код",$sql['specID']);
        $shapeID= self::getFields("Специальности","ФормаОбучения","ОКСО",$specCode);
        $tmp= [
            "specCode"=>$specCode,
            "specName"=>self::getFields("СпециальностиПрофили","НаправлениеНазвание","Код",$sql['specID']),
            "specLevel"=>self::getFields("Специальности","Квалификация","ОКСО",$specCode),
            "specShape"=>self::getFields("Тип_Обучения","Название","Код",$shapeID),
        ];
        $sql= $sql+$tmp;
        return true;
    }

}