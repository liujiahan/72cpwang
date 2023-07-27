<?php

require_once dirname(__FILE__).'/../include/config.inc.php';

setcookie('password', '', time()-3600, '/');
setcookie('isAdmin', '', time()-3600, '/');
setcookie('isParnter', '', time()-3600, '/');

// if(isset($_COOKIE['password']) && isset($_SESSION[$_COOKIE['password']])){
// 	if($_SESSION[$_COOKIE['password']] > 1){
// 		$_SESSION[$_COOKIE['password']] -= 1;
// 	}
// }

header("location: login.php");