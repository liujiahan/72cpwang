<?php require_once(dirname(__FILE__).'/inc/config.inc.php');IsModelPriv('wxmenu'); 


//初始化参数
$tbname = '#@__wxmenu';
$gourl  = 'admin.php';
$action = isset($action) ? $action : '';

//添加管理员
if($action == 'save')
{
	$time = time();

	$sql = "UPDATE `$tbname` SET menu='$menu', updatetime='$time' WHERE `id`=$id";
	if($dosql->ExecNoneQuery($sql))
	{
		ShowMsg('菜单内容已保存', '-1');
		exit();
	}
}
elseif($action == 'create')
{
	//GetWeixinToken(WEIXIN_ID,'AccessToken');
	//https://qyapi.weixin.qq.com/cgi-bin/menu/create?access_token=ACCESS_TOKEN&agentid=AGENTID
	
	//https://qyapi.weixin.qq.com/cgi-bin/menu/delete?access_token=ACCESS_TOKEN&agentid=AGENTID
	$url = "https://qyapi.weixin.qq.com/cgi-bin/menu/delete?access_token=".getToken()."&agentid=".WEIXIN_ID;  
	PostWeixin($url);

	//create menu
	$row = $dosql->GetOne("SELECT * FROM `#@__wxmenu` ");
	$url = 'https://qyapi.weixin.qq.com/cgi-bin/menu/create?access_token='.getToken()."&agentid=".WEIXIN_ID;   
	$json = PostWeixin($url,$row['menu']);
	echo $json;
	//ShowMsg('菜单已更新', '-1');
	exit();
}
	

?>
