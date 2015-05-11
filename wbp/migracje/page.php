<?php

    
    $pages=file_get_contents('/dev/stdin');
    
    
    $pages=preg_replace("/[\n\r\t ]+/",',',$pages);
    
    foreach (explode(',',$pages) AS $page)
    {
        $page+=0;
        if (!$page) continue;
        if ($page<500) continue;
        
        $module='';
        foreach (array_keys($_SERVER['plus']) AS $mod)
        {
            if ($page>$_SERVER['plus'][$mod])
            {
                $module=$mod;
                $id=$page-$_SERVER['plus'][$mod];
            }
        }
        
        if (!$module) continue;
        
        $cmd=implode(' ',$argv);
        $cmd=str_replace('page',$module,$cmd);
        $cmd.=" -i $id";
        
        system('php '.$cmd);
        
    }
    
    