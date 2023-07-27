<?php

/*
 * 说明：前端引用文件
**************************
(C)2010-2015 phpMyWind.com
update: 2014-5-31 21:58:30
person: Feng
**************************
*/

// session_start();

if(!isset($_SESSION)){
    session_start();
}

require_once(dirname(__FILE__).'/common.inc.php');
require_once(SNRUNNING_INC.'/func.class.php');
require_once(SNRUNNING_INC.'/page.class.php');
require_once(SNRUNNING_INC.'/weixinfun.class.php');
require_once(SNRUNNING_INC.'/password.inc.php');
require_once(SNRUNNING_INC.'/logincheck.func.php');




if(!defined('IN_SNRUNNING')) exit('Request Error!');


//网站开关
if($cfg_webswitch == 'N')
{
	echo $cfg_switchshow.'<br /><br /><i>'.$cfg_webname.'</i>';
	exit();
}
?>
