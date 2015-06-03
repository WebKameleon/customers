<?php
    $data=json_decode(file_get_contents($argv[1]),1);
    
    print_r($data);
    