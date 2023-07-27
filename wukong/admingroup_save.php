<?php	require_once(dirname(__FILE__).'/inc/config.inc.php');IsModelPriv('admingroup');

/*
**************************
(C)2016-2017 sxtm.com
update: 2014-5-30 11:11:47
person: Feng
**************************
*/


//初始化参数
$tbname = '#@__admingroup';
$gourl  = 'admingroup.php';
$action = isset($action) ? $action : '';


//添加管理组
if($action == 'add')
{
	if(empty($classpurview))
    {
        $purviews = '';
    } else {
        $purviews = join(',', $classpurview);
        if($classpurview=='0') $purviews = '';
    }
	
	$sql = "INSERT INTO `$tbname` (groupname, description, groupsite, checkinfo,classpurview,checklevel) VALUES ('$groupname', '$description', '$groupsite', '$checkinfo','$purviews','$checklevel')";
	if($dosql->ExecNoneQuery($sql))
	{
		$lastid = $dosql->GetLastID();

		if(isset($model) && is_array($model))
		{
			foreach($model as $k=>$privmodel)
			{
				$dosql->ExecNoneQuery("INSERT INTO `#@__adminprivacy` (groupid, siteid, model, classid, action) VALUES ('$lastid', '0', '$privmodel', '0', 'all')");
			}
		}

		header("location:$gourl");
		exit();
	}
}


//修改管理组
else if($action == 'update')
{
	if($id == 1 and $checkinfo != 'true')
	{
		ShowMsg('抱歉，不能更改超级管理组状态！','-1');
		exit();
	}
	
	if(empty($classpurview))
    {
        $purviews = '';
    } else {
        $purviews = join(',', $classpurview);
        if($classpurview=='0') $purviews = '';
    }

	$sql = "UPDATE `$tbname` SET groupname='$groupname', description='$description', groupsite='$groupsite', checkinfo='$checkinfo',classpurview='$purviews',checklevel='$checklevel' WHERE id=$id";

	if($dosql->ExecNoneQuery($sql))
	{

		//删除权限
		$dosql->ExecNoneQuery("DELETE FROM `#@__adminprivacy` WHERE `groupid`=$id");

		//整理新权限
		$lastid = $id;

		/*
		if(isset($priv) && is_array($priv))
		{
			foreach($priv as $k=>$siteids)
			{
				foreach($siteids as $k2=>$privids)
				{
					foreach($privids as $k3=>$privid)
					{
						$dosql->ExecNoneQuery("INSERT INTO `#@__adminprivacy` (groupid, siteid, model, classid, action) VALUES ('$lastid', '$k', 'category', '$k2', '$privid')");
					}
				}
			}
		}
		*/

		if(isset($model) && is_array($model))
		{
			foreach($model as $k=>$privmodel)
			{
				$dosql->ExecNoneQuery("INSERT INTO `#@__adminprivacy` (groupid, siteid, model, classid, action) VALUES ('$lastid', '0', '$privmodel', '0', 'all')");
			}
		}


		header("location:$gourl");
		exit();
	}
}


//审核管理组
else if($action == 'check')
{
	if($id == 1)
	{
		ShowMsg('抱歉，不能更改超级管理组状态！','-1');
		exit();
	}


	if($checkinfo == 'true')
		$sql = "UPDATE `$tbname` SET `checkinfo`='false' WHERE id=$id";
	if($checkinfo == 'false')
		$sql = "UPDATE `$tbname` SET `checkinfo`='true' WHERE id=$id";


	if($dosql->ExecNoneQuery($sql))
	{
    	header("location:$gourl");
		exit();
	}
}


//删除管理组
else if($action == 'del')
{
	if($id == 1)
	{
		ShowMsg('抱歉，不能删除超级管理组！','-1');
		exit();
	}

	if($dosql->ExecNoneQuery("DELETE FROM `$tbname` WHERE `id`=$id"))
	{
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
