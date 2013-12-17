<?php
require_once('comuni.php');
$key=$_REQUEST['term'];
$foo = get_places();
$bar = array();
foreach($foo as $v){
        if($v['name']!= "" && strpos(strtolower($v['name']),strtolower($key))!==false){
                $bar[] = $v['name'];
        }
}
echo json_encode($bar);

