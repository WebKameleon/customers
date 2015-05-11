<?php

/*
 * Usage:
 * ?add={sid}&coordinates={coordinates}
 *  sid - pozycji menu (dużego obrazka), coordinates zapisane w stringu
 *
 * ?remove={sid}
 *  sid - hotspota
 *
 * ?move={sid}&coordinates={coordinates}
 *  sid - hotspota
 *
 **/

$data = array();

$session = Bootstrap::$main->session();

if (isset($_GET['add']) && $_GET['add'] > 0 && !empty($_GET['coordinates'])) {
    $weblink = new weblinkModel($_GET['add']);

    if ($weblink->server != $session['server']['id'])
        return;

    if (!$weblink->submenu_id) {
        $weblink->submenu_id = $weblink->get_new_menu_id();
        $weblink->save();
    }
    $hs = $weblink->add_link($weblink->submenu_id, substr($weblink->name . ' ' . basename(strtolower($weblink->img)), 0, 32), '');
    $hotspot = new weblinkModel($hs['sid']);
    $hotspot->description = $_GET['coordinates'];
    $data = $hotspot->save();
}

if (isset($_GET['remove']) && $_GET['remove'] > 0) {
    $weblink = new weblinkModel($_GET['remove']);
    if ($weblink->server != $session['server']['id'])
        return;

    if ($weblink->remove($_GET['remove']))
        $data['status'] = 1;
}

if (isset($_GET['move']) && $_GET['move'] > 0 && !empty($_GET['coordinates'])) {
    $weblink = new weblinkModel($_GET['move']);
    if ($weblink->server != $session['server']['id'])
        return;

    $weblink->description = $_GET['coordinates'];
    $data = $weblink->save();
}


if (isset($_GET['photo']) && isset($_GET['page'])) {
    $webpage = new webpageModel($_GET['page']);
    if ($webpage->server != $session['server']['id'])
        return;

    $pagekey=json_decode($webpage->pagekey);
    if (!is_object($pagekey)) $pagekey=new stdClass();
    $pagekey->ph=$_GET['photo'];
    $webpage->pagekey=json_encode($pagekey);
    $webpage->save();
}


if (isset($_GET['inspiration_type']) && isset($_GET['sid'])) {
    $weblink = new weblinkModel($_GET['sid']);
    
    if ($weblink->server != $session['server']['id']) return;
    
    if (substr($_GET['inspiration_type'],0,1)==',') $_GET['inspiration_type']=substr($_GET['inspiration_type'],1);
    $weblink->description = $_GET['inspiration_type'];
    $weblink->save();
}






Header('Content-type: application/json');
die(json_encode($data));