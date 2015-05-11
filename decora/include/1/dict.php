<?php

class dict {
    protected $dbh;
    protected $lang;

    
    public function __construct($dbh,$lang)
    {
        $this->dbh=$dbh;
        $this->lang=$lang;

    }

    public function dict($vendor,$key,$type)
    {
        $sql="SELECT * FROM decora_dict WHERE dkey='$key' AND type='$type' AND vendor='$vendor'";
        $q=$this->dbh->query($sql);
        if ($q) foreach ($q AS $row ){
            $row['key']=$row['dkey'];
            decora_row($row,$this->lang);
            return $row;
        }
    }
    
    public function __get($key)
    {
        $sql="SELECT * FROM decora_dict WHERE dkey='$key' AND type='D' AND vendor='decora'";
        $q=$this->dbh->query($sql);
        if ($q) foreach ($q AS $row ){
            $ret=$row['name_'.$this->lang];
            return $ret;
        }
        return $key;
    }
    
}
