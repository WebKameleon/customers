<?php

    if (isset($_SERVER['dbh']) && !isset($KAMELEON_MODE)) {
        $_SERVER['dbh']=null;
    }