<?php
    function folklor_cache($key,$value=null) {
        
        if (!isset($_SERVER['SERVER_SOFTWARE']) || !strstr(strtolower($_SERVER['SERVER_SOFTWARE']),'engine')) {
            return $value?:false;
        }
     
        $memcache = new Memcache;
        
        if (is_null($value)) return $memcache->get($key);
        $memcache->set($key,$value);
        return $value;
    }