<?php

$webtd=new webtdModel();
$all=$webtd->getAll(array($page));
$update=0;
for ($i=0; $i<count($all); $i++)
  if ($all[$i]['nd_update'] > $update)
    $update=$all[$i]['nd_update'];


echo 'modyfikacja: '.Tools::date($update);