<?php	require_once(dirname(__FILE__).'/inc/config.inc.php');IsModelPriv('purview');

/*
**************************
(C)2016-2017 sxtm.com
update: 9:53 2016/10/14
person: DingZQ
**************************
*/


//初始化参数
$tbname = '#@__purview';
$gourl  = 'purview.php';


//引入操作类
require_once(ADMIN_INC.'/action.class.php');


//添加栏目
if($action == 'add')
{
	//权限验证
	//如果parentid等于0，则是新添加的栏目，不验证权限

	$sql = "INSERT INTO `$tbname` (siteid, parentid, classname, purviewkey, orderid, url, ismenu) VALUES ('$cfg_siteid', '$parentid', '$classname', '$purviewkey', '$orderid', '$url', '$ismenu')";
	if($dosql->ExecNoneQuery($sql))
	{
		$lastid = $dosql->GetLastID();
		$title = '权限添加成功';
		$msg = '[<a href="purview_add.php?id='.$parentid.'">继续添加</a>]&nbsp;&nbsp;&nbsp;&nbsp;[<a href="purview.php">权限列表</a>]&nbsp;&nbsp;&nbsp;&nbsp;[<a href="purview_update.php?id='.$lastid.'">修改权限</a>]';
		PageMsg($title,$msg, '');
	}
	
	

	//header("location:$gourl");
	exit();
	
	
	
	
}


//修改栏目
else if($action == 'update')
{

	$sql = "UPDATE `$tbname` SET siteid='$cfg_siteid', parentid='$parentid', classname='$classname', purviewkey='$purviewkey',orderid='$orderid',url='$url',ismenu='$ismenu' WHERE id=$id";
	if($dosql->ExecNoneQuery($sql))
	{
		$title = '权限修改成功';
		$msg = '[<a href="purview.php">权限列表</a>]&nbsp;&nbsp;&nbsp;&nbsp;[<a href="purview_update.php?id='.$id.'">修改权限</a>]';
		PageMsg($title,$msg, '');
		exit();
	}
	
}


//删除栏目
else if($action == 'delpurview')
{
	//权限验证
		if($dosql->ExecNoneQuery("DELETE FROM `$tbname` WHERE `id`=$id")){
		$dosql->ExecNoneQuery("DELETE FROM `$tbname` WHERE `parentid`=$id");
		header("location:$gourl");
		exit();
	}
}


//无条件返回
else
{
    header("location:$gourl");
	exit();
}
?>
