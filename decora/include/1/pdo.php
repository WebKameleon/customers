<?php

class PDO2 extends PDO {
    
    private $_more;
    private $_dbhs;
    private $_lasterror;
    
    public function __construct($a)
    {
        $this->_more=array();
        $this->_dbhs=array();
        
        if (is_object($a))
        {
            $o=$a->getOptions();
            return parent::__construct($o['dsn'],$o['username'],$o['password']);
        }
        
        for($i=1;$i<count($a);$i++)
        {
            $this->_more[]=$a[$i];
            $this->_dbhs[]=null;
            
        }
        
        $this->_lasterror = null;
        return parent::__construct($a[0]['dsn'],$a[0]['user'],$a[0]['pass']);
    }
    
    private function _dbh($i)
    {
        if (is_null($this->_dbhs[$i])) {
            try {
                $this->_dbhs[$i] = new PDO($this->_more[$i]['dsn'],$this->_more[$i]['user'],$this->_more[$i]['pass']);
            } catch (Exception $e)
            {
                die(print_r($e,1));
            }
        }
        return $this->_dbhs[$i];
    }
    
    public function import($dir,$file)
    {
        $driver=$this->getAttribute(PDO::ATTR_DRIVER_NAME);
    
        $sql=file_get_contents("$dir/$driver/$file");
        
        $this->beginTransaction();
        
        if (parent::exec($sql)===false)
        {
            $ret=array('errorInfo'=>$this->errorInfo(),'sql'=>$driver.': '.$sql);
            $this->rollBack();
            return $ret;
        }
        
        for($i=0;$i<count($this->_dbhs);$i++)
        {
            $driver=$this->_dbh($i)->getAttribute(PDO::ATTR_DRIVER_NAME);
            $sql=file_get_contents("$dir/$driver/$file");
            
            $this->_dbh($i)->beginTransaction();
            
            if ($this->_dbh($i)->exec($sql)===false)
            {
                $ret=array('errorInfo'=>$this->_dbh($i)->errorInfo(),'sql'=>$driver.': '.$sql);
                
                for ($j=0; $j<=$i; $j++)
                {
                    $this->_dbh($j)->rollBack(); 
                }
                $this->rollBack();
                return $ret;
            }
        }
        
        $this->commit();
    
        for($i=0;$i<count($this->_dbhs);$i++)  $this->_dbh($i)->commit();   
    
    }
    
    public function errorInfo()
    {
        if (!is_null($this->_lasterror)) return $this->_lasterror;
        return parent::errorInfo();
    }
    
    
    public function exec($sql)
    {
        $this->_lasterror = null;
        
        
        
        for($i=0;$i<count($this->_dbhs);$i++)
        {
            $this->_dbh($i)->beginTransaction();

            if ($this->_dbh($i)->exec($sql)===false)
            {
                
                $this->_lasterror=$this->_dbh($i)->errorInfo();
                //mydie($this->_lasterror,$sql);
                
                for ($j=0; $j<=$i; $j++)
                {
                    $this->_dbh($j)->rollBack(); 
                }
                return false;
            }
            
        }
        
        $ret=parent::exec($sql);
        
        if ($ret===false)
        {
            for ($j=0; $j<count($this->_dbhs); $j++)
            {
                $this->_dbh($j)->rollBack(); 
            }            
        }
        else
        {
            for ($j=0; $j<count($this->_dbhs); $j++)
            {
                $this->_dbh($j)->commit(); 
            }            
        }
        
        return $ret;
        
    }
}

