<?php
require_once('province.php');
$key=$_REQUEST['term'];
$foo = get_provinces();
$bar = array();
foreach($foo as $v){
        if($v['name']!= "" && strpos(strtolower($v['name']),strtolower($key))!==false){
                $bar[] = $v['name'];
        }
}
echo json_encode($bar);

