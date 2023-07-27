<?php	require_once(dirname(__FILE__).'/inc/config.inc.php');IsModelPriv('allowuser');

//初始化参数
$tbname = '#@__wxuser';
$gourl  = 'wxuser.php';
$action = isset($action) ? $action : '';

//添加管理员
if($action == 'adduser')
{
	//判断用户名是否存在
	if($dosql->GetOne("SELECT `id` FROM `$tbname` WHERE `userid`='$userid'"))
	{
		echo '当前人员已设置！如需修改，请先删除后添加。';
		exit();
	}
	$udata = GetBocUser($userid);
	
	$sql = "INSERT INTO `$tbname` ( userid, name, gender, mobile, avatar, departid, departname, parentid, parentname, posttime) VALUES ('$userid', '".$udata['name']."','".$udata['gender']."','".$udata['mobile']."','".$udata['avatar']."','".$udata['departid']."','".$udata['departname']."','".$udata['parentid']."','".$udata['parentname']."','".time()."')";
	if($dosql->ExecNoneQuery($sql))
	{
		echo 'yes';
	}
	else
		echo '设置中产生错误。';
	
}

else if($action == 'update')
{
	if(!empty($userid)&&!empty($usertype))
	{
		if($user_center_data == 0){
			//不同步中心库
			$dosql->ExecNoneQuery("update `$tbname` set departid='$departid', departname='$departname', usertype=".$usertype.", user_center_data=".$user_center_data." WHERE `userid`='$userid' ");
		}else{
			//同步
			$u = GetBocUser($userid);
			$dosql->ExecNoneQuery("update `$tbname` set departid='".$u['departid']."', departname='".$u['departname']."', usertype=".$usertype.", user_center_data=".$user_center_data." WHERE `userid`='$userid' ");
		}
	}
	header("location:$gourl");
	exit();
	
}

else if($action == 'del')
{
	$row = $dosql->GetOne("SELECT * FROM `#@__customer_binduser` WHERE `userid`='$userid'");
	if(isset($row['id'])){
		ShowMsg("该客户经理下有用户，不能删除！", '-1');
		exit();
	}
	$dosql->ExecNoneQuery("DELETE FROM `$tbname` WHERE `userid`='$userid' ");
	header("location:$gourl");
	exit();
	
}


//无条件返回
else
{
    header("location:$gourl");
	exit();
}
?>