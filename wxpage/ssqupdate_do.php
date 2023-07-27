<?php	
require_once(dirname(__FILE__).'/../include/config.inc.php');
require_once(dirname(__FILE__).'/core/ssq.config.php');

$urlarr = ssqUpdateCfg();

$urlindex = array_keys($urlarr);

$result = array('errcode'=>0, 'errmsg'=>'');

if(!isset($index)){
	$result['errcode'] = 0;
	$result['errmsg']  = '未知错误';
	exit(json_encode($result));
}

if(!isset($urlindex[$index])){
	$result['errcode'] = 2;
	$result['errmsg']  = '数据更新完成';
	exit(json_encode($result));
}

$exeinfo  = $urlarr[$urlindex[$index]];

$exeurl   = WEIXIN_BASE . 'wxpage/' . $exeinfo['url'];
PostWeixin($exeurl);

$result['errcode'] = 1;
$result['errmsg']  = $exeinfo['name'];
$result['index']   = $index + 1;

exit(json_encode($result));
