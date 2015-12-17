<?php
    function folklor_cache($key,$value=null) {
        
        if (!isset($_SERVER['SERVER_SOFTWARE']) || !strstr(strtolower($_SERVER['SERVER_SOFTWARE']),'engine')) return false;
     
        $memcache = new Memcache;
        
        if (is_null) return $memcache->get($key);
        $memcache->set($key,$value);
        return $value;
    }