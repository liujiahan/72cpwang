<?php	if(!defined('IN_SNRUNNING')) exit('Request Error!');

/**
 * 去服务号认证拿到openid跳回
 * @param array  $get    $_GET参数
 * @param string $cururl 当前URL
 */
function LoginCheck(){
	global $pwdList;

	// return true;

	if(isset($_COOKIE['password']) && (in_array($_COOKIE['password'], $pwdList['password']) || in_array($_COOKIE['password'], $pwdList['parnter']) || in_array($_COOKIE['password'], $pwdList['admin']))){
		if(isset($_SESSION[$_COOKIE['password']]) && $_SESSION[$_COOKIE['password']] >= 1){
			// ShowMsg("该口令使用人数已满！请联系管理员索要访问口令！QQ：962823142", "login.php");
			// exit;
		}
		return true;
	}else {
		$gourl = isset($_SERVER['REQUEST_URI']) ? substr($_SERVER['REQUEST_URI'], 8) : 'index.php';
		setcookie('loginurl', $gourl, time()+604800, '/');
		ShowMsg("请输入口令登录！", "login.php");
		exit;
	}
}