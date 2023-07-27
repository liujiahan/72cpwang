<?php require(dirname(__FILE__).'/inc/config.inc.php');IsModelPriv('userupdate'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>修改用户信息</title>
<link href="templates/style/admin.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="templates/js/jquery.min.js"></script>
<script type="text/javascript" src="templates/js/checkf.func.js"></script>
<script type="text/javascript">
//验证管理员添加
function cfm_userupdate()
{
	if($("#password").val() == "")
	{
		alert("请输入密码！");
		$("#password").focus();
		return false;
	}
	if($("#password").val().length<5 || $("#password").val().length>20)
	{
		alert("密码由5-16个字符组成，区分大小写！");
		$("#password").focus();
		return false;
	}
	if($("#repassword").val() == "")
	{
        alert ("请输入确认密码！");
        $("#repassword").focus();
        return false;
    }
	if($("#password").val() != $("#repassword").val())
	{
        alert ("两次密码不同！");
        $("#repassword").focus();
        return false;
    }
}
</script>

</head>
<body>
<?php
$row = $dosql->GetOne("SELECT * FROM `#@__admin` WHERE username='".$_SESSION['admin']."'");
?>
<div class="formHeader"> <span class="title">修改密码</span> <a href="javascript:location.reload();" class="reload">刷新</a> </div>
<form name="form" id="form" method="post" action="user_save.php" onsubmit="return cfm_userupdate();">
	<table width="100%" border="0" cellspacing="0" cellpadding="0" class="formTable">
		<tr>
			<td width="25%" height="35" align="right">用户名：</td>
			<td width="75%"><strong><?php echo $row['username']; ?></strong></td>
		</tr>
		<tr>
			<td height="35" align="right">旧密码：</td>
			<td><input type="password" name="oldpwd" id="oldpwd" class="input" maxlength="16" />
				<span class="maroon">*</span><span class="cnote">若不修改密码请留空</span></td>
		</tr>
		<tr>
			<td height="35" align="right">新密码：</td>
			<td><input type="password" name="password" id="password" class="input" maxlength="16" />
				<span class="maroon">*</span><span class="cnote">6-16个字符组成，区分大小写</span></td>
		</tr>
		<tr>
			<td height="35" align="right">确　认：</td>
			<td><input type="password"  name="repassword" id="repassword" class="input" maxlength="16" />
				<span class="maroon">*</span></td>
		</tr>
		<tr>
			<td height="35" align="right">提　问：</td>
			<td><select name="question" id="question">
				<?php
				$question = array('无安全提问','母亲的名字','爷爷的名字','父亲出生的城市','你其中一位老师的名字', '你个人计算机的型号','你最喜欢的餐馆名称','驾驶执照最后四位数字');
				foreach($question as $k=>$v)
				{
					if($row['question'] == $k)
					{
						$selected = 'selected="selected"';
					}
					else
					{
						$selected = '';
					}

					echo "<option value=\"$k\" $selected>$v</option>";									
				}
				?>
				</select></td>
		</tr>
		<tr>
			<td height="35" align="right">回　答：</td>
			<td><input name="answer" type="text" class="input" id="answer" value="<?php echo $row['answer']; ?>" /></td>
		</tr>
		<tr>
			<td height="35" align="right">备  注：</td>
			<td><?php echo $row['nickname']; ?></td>
		</tr>		
		<tr class="nb">
			<td height="35" align="right">登录IP：</td>
			<td><?php echo $row['loginip']; ?></td>
		</tr>
	</table>
	<div class="formSubBtn">
		<input type="submit" class="submit" value="提交" />
		<input type="button" class="back" value="返回" onclick="history.go(-1)"  />
		<input type="hidden" name="action" id="action" value="update" />
		<input type="hidden" name="id" id="id" value="<?php echo $row['id']; ?>" />
	</div>
</form>
</body>
</html>