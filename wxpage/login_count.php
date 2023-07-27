<?php

require_once dirname(__FILE__).'/../include/config.inc.php';

echo "当前登录：" . isset($_SESSION[$_COOKIE['password']]) ? $_SESSION[$_COOKIE['password']] : 0; 