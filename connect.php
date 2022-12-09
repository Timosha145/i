<?php

$kasutaja='root';//d113373_timofei
$server='localhost';//d113373.mysql.zonevs.eu
$andmebaas='linnad';//d113373_bass
$salasona='';//ainult mina tean!

//teeme käsk mis ühendab

$yhendus = new mysqli($server, $kasutaja, $salasona, $andmebaas);
$yhendus->set_charset('UTF8');
?>