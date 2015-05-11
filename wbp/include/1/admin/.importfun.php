<?php

    function import_addslashes($v)
    {
        return str_replace('\\"','"',addslashes($v));
    }


    function table2file($table,$dbh)
    {
        $sql="SELECT * FROM $table ORDER BY id";
        
        $data=array();
        
        $q=$dbh->query($sql);
        
        
        foreach ($q AS $row)
        {
            foreach ($row AS $k=>$v)
            {
                if (is_integer($k)) unset($row[$k]);
            }
        
            $data[$row['id']]=$row;
        
        }
        WBP::put_data($table,$data);
    }
    
    
