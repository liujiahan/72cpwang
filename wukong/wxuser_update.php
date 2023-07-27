<?php require_once(dirname(__FILE__).'/inc/config.inc.php');
// IsModelPriv('allowuser');


$dosql->Execute("SELECT * FROM `#@__chgdepart` ORDER BY orderid ASC");
$departSel = array();
while ($row = $dosql->GetArray()) {
	$departSel[$row['departid']] = $row['depart'];
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>设置身份</title>
<link href="templates/style/admin.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="templates/js/jquery.min.js"></script>
<script type="text/javascript">

//验证管理员添加
function cfm_user()
{
	// if($("#departid").val() == ""||$("#departid").val() == undefined)
	// {
	// 	alert("网点信息错误，请联系管理人员添加核对该网点信息！");
	// 	return false;
	// }
	// else 
	var user_center_data = $("input[name='user_center_data']:checked").val();
	if(user_center_data == 0 && $("#departid").val() == ''){
		alert("请选择部门！");
		return false;
	}

	if($("#usertype").val() == "")
	{
		alert("请选择用户身份！");
		return false;
	}
	return true;
}
</script>
</head>
<body>
<?php
$row = $dosql->GetOne("SELECT * FROM `#@__wxuser` WHERE `userid`='".$userid."' ");
?>
<div class="formHeader"> <span class="title">设置身份</span> <a href="javascript:location.reload();" class="reload">刷新</a> </div>
<form name="form" id="form" method="post" action="wxuser_save.php" onsubmit="return cfm_user();">
	<table width="100%" border="0" cellspacing="0" cellpadding="0" class="formTable">		
		<tr>
			<td width="25%" height="40" align="right">用户账号：</td>
			<td width="75%"><?php echo $row['userid']; ?></td>
		</tr>
		<tr>
			<td width="25%" height="40" align="right">姓名：</td>
			<td width="75%"><?php echo $row['name']; ?></td>
		</tr>
		<tr>
			<td width="25%" height="40" align="right">手机：</td>
			<td width="75%"><?php echo $row['mobile']; ?></td>
		</tr>
		<!-- <tr>
			<td width="25%" height="40" align="right">区域：</td>
			<td width="75%">
				<select id="area" name="area">
					<option value="西安市">西安市</option>				
				  </select>
			</td>
		</tr> -->
		<tr>
			<td width="25%" height="40" align="right">网点编码：</td>
			<td width="75%"><?php echo $row['departid']; ?></td> 
		</tr>
		<tr>
			<td width="25%" height="40" align="right">网点名称：</td>
			<td width="75%"><?php echo $row['departname']; ?></td>
		</tr>
		<tr>
			<td width="25%" height="40" align="right">同步中心数据：</td>
			<td width="75%">
				<input type="radio" id="notongbu" name="user_center_data" value="0" <?php echo $row['user_center_data'] == 0 ? 'checked' : ''; ?>>
				<label for="notongbu">不同步</label>
				<input type="radio" id="tongbu" name="user_center_data" value="1" <?php echo $row['user_center_data'] == 1 ? 'checked' : ''; ?>>
				<label for="tongbu">同步</label>
			</td>
		</tr>
		<tr class="local_depart">
			<td width="25%" height="40" align="right">自定义所属网点：</td>
			<td width="75%">
				<select id="departid" name="departid">
					<option value="">--请选择所属网点--</option>			
					<?php foreach ($departSel as $v_departid => $v_depart) { ?>
					<option value="<?php echo $v_departid ?>" <?php echo $row['departid'] == $v_departid ? 'selected' : '' ?>><?php echo $v_depart ?></option>			
					<?php } ?>
			  	</select>
			  	<input type="hidden" name="departname" class="input" id="departname" value="<?php echo $row['departname']; ?>" />
			</td>
		</tr>
		<tr>
			<td width="25%" height="40" align="right">身份：</td>
			<td width="75%">
				<select id="usertype" name="usertype">
				<option value="">--请选择--</option>
				<option value="1" <?php if($row['usertype']==1) {echo 'selected';} ?>>大堂经理</option>
				<option value="2" <?php if($row['usertype']==2) {echo 'selected';} ?>>客户经理</option>
				<option value="3" <?php if($row['usertype']==3) {echo 'selected';} ?>>平台数据</option>
				</select>
			</td>
		</tr>
		
	</table>
	<div class="formSubBtn">
		<input type="submit" class="submit" value="提交" />
		<input type="button" class="back" value="返回" onclick="history.go(-1)"  />
		<input type="hidden" name="action" id="action" value="update" />
		<input type="hidden" name="userid" id="userid" value="<?php echo $userid; ?>" />
	</div>
</form>
<script type="text/javascript">
	$(function(){
		$("#notongbu").click(function(){
			$(".local_depart").show();
		})
		$("#tongbu").click(function(){
			$(".local_depart").hide();
		})

		var user_center_data = <?php echo $row['user_center_data'] ?>;
		if(user_center_data == 0){
			$(".local_depart").show();
		}else{
			$(".local_depart").hide();
		}

		$("#departid").change(function(){
			var departname;
			var departid = $(this).val();
			if(departid != ''){
				departname = $(this).find("option:selected").text();
			}
			$("#departname").val(departname);
		})
	})
</script>
</body>
</html>