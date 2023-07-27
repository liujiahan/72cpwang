<?php	
require_once(dirname(__FILE__).'/../include/config.inc.php');
require_once(dirname(__FILE__).'/core/ssq.config.php');

if( !isset($token) || $token != md5('72cpwang') ){
	exit;
}

$urlarr = ssqUpdateCfg();

$urlindex = array_keys($urlarr);

foreach ($urlarr as $v) {
	$url = $v['url'];

	$exeurl = WEIXIN_BASE . 'wxpage/' . $url;
	PostWeixin($exeurl);
}

echo date("Y-m-d H:i:s") . ":update ok.";
echo "\n\r";