<?php require_once(dirname(__FILE__).'/inc/config.inc.php');IsModelPriv('wxmenu'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>微信菜单</title>
<link href="templates/style/admin.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="templates/js/jquery.min.js"></script>
<script type="text/javascript" src="templates/js/checkf.func.js"></script>
<script type="text/javascript">
function createmenu()
{
	if(confirm("注：微信菜单将被替换。"))
	{
		$.ajax({
			url : "wxmenu_do.php",
			type:'post',
			data:{"action":"create","menu":$("#menu").val()},
			dataType:'html',
			beforeSend:function(){
				//$("#divuser").html('<div class="loading" style="width:140px;margin:0 auto;"><img src="templates/images/loading.gif">用户列表更新中...</div>');
			},
			success:function(data){
				alert(data);
			}
		});
	}
}

</script>
</head>
<body>
<?php
$row = $dosql->GetOne("SELECT * FROM `#@__wxmenu` ");

?>
<div class="formHeader"> <span class="title">微信菜单</span> <a href="javascript:location.reload();" class="reload">刷新</a> </div>
<form name="form" id="form" method="post" action="wxmenu_do.php?action=save" onsubmit="return cfm_menu();">
	<table width="100%" border="0" cellspacing="0" cellpadding="0" class="formTable">
		<tr>
			<td height="104" align="right">菜单内容：</td>
			<td><textarea name="menu" id="menu" rows="30" cols="120" autofocus><?php echo $row['menu'];?></textarea>
				<div class="hr_5"></div>
				<font color="red">修改菜单要慎重，请咨询管理员</font> </td>
		</tr>	
	</table>
	<div class="formSubBtn">
		<input type="submit" class="submit" value="保存" />
		<input type="button" class="submit" value="更新菜单" onclick="javascript:createmenu();" />
		<input type="button" class="back" value="返回" onclick="history.go(-1);" />
		<input type="hidden" name="id" id="id" value="<?php echo $row['id'];?>" />
	</div>
</form>
</body>
</html>