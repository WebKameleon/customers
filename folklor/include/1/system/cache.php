<?php
    function folklor_cache($key,$value=null,$expire=3600) {
        
        if (!isset($_SERVER['SERVER_SOFTWARE']) || !strstr(strtolower($_SERVER['SERVER_SOFTWARE']),'engine')) {
            return $value?:false;
        }
     
        $memcache = new Memcache;
        
        if (is_null($value)) {
            $v=$memcache->get($key);
            if ( isset($v['v']) && isset($v['e']) && $v['e']>time() ) return $v['v'];
            return false;
        }
        
        $memcache->set($key,['v'=>$value,'e'=>time()+$expire]);
        return $value;
    }
    
    function calendar_cache($key,$value=null,$expire=3600) {
        return folklor_cache($key,$value);
    }