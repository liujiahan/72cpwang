<?php 

require_once(dirname(__FILE__).'/inc/config.inc.php');IsModelPriv('allowuser');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>成员管理</title>
<link href="templates/style/admin.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="templates/js/jquery.min.js"></script>
<script type="text/javascript" src="templates/js/forms.func.js"></script>
<script type="text/javascript">
function getm()
{
	window.location='?u='+$("#userid").val();
}
function allowuser()
{
	window.location='wxuser.php';
}

function adduser(userid)
{
	$.ajax({
		url : "wxuser_save.php",
		type:"post",
		data:{"action":"adduser","userid":userid},
		dataType:"html",
		success:function(data){	
			if(data=="yes")
			{
				alert("设置成功！");
				window.location='wxuser_save.php';
			}
			else
				alert(data);
		}
	});
}
</script>
</head>
<body>
<div class="topToolbar"> <span class="title">成员管理</span> 
<span style="margin-left:30px;color:blue;" class="title">
工号：<input type="text" name="userid" class="inputos" id="userid" value="<?php  echo isset($u) ? $u : ''; ?>" />
<!-- 姓名：<input type="text" name="uname" class="inputos" id="uname" value="<?php  echo isset($n) ? $n : ''; ?>" /> -->
<!-- 手机：<input type="text" name="mobile" class="inputos" id="mobile" value="<?php  echo isset($m) ? $m : ''; ?>" /> -->

<a href="javascript:getm();">[查询]</a>
<a href="javascript:allowuser();">[所有允许人员]</a>
 </span>
<a href="javascript:location.reload();" class="reload">刷新</a></div>
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="dataTable">
	<tr align="left" class="head">
		<td width="5%" height="36" class="firstCol">ID</td>
		<td width="5%">头像</td>
		<td width="10%">员工编号</td>
		<td width="10%">姓名</td>
		<td width="10%">手机号</td>
		<td width="25%">网点/部门</td>
		<td width="10%">角色</td>
		<td width="25%">操作</td>
	</tr>
	<?php
	if(!empty($u))
	{
		$row = GetBocUser($u);
		if(!empty($row['userid'])){
			?>
		<tr align="left" class="dataTr">
			<td height="36" class="firstCol"><?php echo '1';//$row['id']; ?></td>
			<td><img src="<?php echo $row['avatar']; ?>" style="width:20px;height:20px;"></td>
			<td><?php echo $row['userid']; ?></td>
			<td><?php echo $row['name']; ?></td>
			<td><?php echo $row['mobile']; ?></td>
			<td><?php echo $row['departname']; ?></td>
			<td></td>
			<td>
				<a href="javascript:;" onclick="adduser('<?php echo $row['userid']; ?>');">设置角色</a>
			</td>
		</tr>
		<?php
		}
	}
	else
	{
		$sql = "SELECT * FROM `#@__wxuser` where 1  ORDER BY id DESC";		
			
		$dosql->Execute($sql);
		while($row = $dosql->GetArray())
		{
			//查询原表中的用户信息
			// $depart = '';
			// $uinfo = $dosqlu->GetOne("SELECT avatar FROM `#@__wxuser` WHERE `userid`='".$row['userid']."'");
			// if(isset($uinfo['avatar'])) $avatar = $uinfo['avatar'];
			
			// $udepart = $dosql->GetOne("SELECT depart FROM `#@__s_depart` WHERE `departid`='".$row['departid']."' ");
			// if(isset($udepart['depart'])) $depart = $udepart['depart'];
	?>
	<tr align="left" class="dataTr">
		<td height="36" class="firstCol"><?php echo $row['id']; ?></td>
		<td><img src="<?php echo $row['avatar']; ?>" style="width:20px;height:20px;"></td>
		<td><?php echo $row['userid']; ?></td>
		<td><?php echo $row['name']; ?></td>
		<td><?php echo $row['mobile']; ?></td>
		<td><?php echo $row['departname']; ?></td>	
		<td><?php 
			if($row['usertype']==1)
				echo '大堂经理';
			else if($row['usertype']==2)				
				echo '客户经理';
			else 
				echo '未设置';
		?></td>	
		<td>
		<span>[<a href="wxuser_update.php?userid=<?php echo $row['userid']; ?>" >设置身份</a>]</span>
		<span>[<a href="wxuser_save.php?action=del&userid=<?php echo $row['userid']; ?>" onclick="return ConfDel(0);">删除</a>]</span>
		
		</td>
	</tr>
	<?php
		}
	}
	?>
</table>

</body>
</html>