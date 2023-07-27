<?php require_once(dirname(__FILE__).'/inc/config.inc.php');IsModelPriv('purview'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>添加权限</title>
<link href="templates/style/admin.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="templates/js/jquery.min.js"></script>
<script type="text/javascript" src="templates/js/getuploadify.js"></script>
<script type="text/javascript" src="templates/js/getcatpsize.js"></script>
<script type="text/javascript" src="templates/js/checkf.func.js"></script>
</head>
<body>
<div class="formHeader"> <span class="title">添加权限</span> <a href="javascript:location.reload();" class="reload">刷新</a> </div>
<form name="form" id="form" method="post" action="purview_save.php" onsubmit="return cfm_infoclass();">
	<table width="100%" border="0" cellspacing="0" cellpadding="0" class="formTable">
		
		<tr>
			<td height="40" align="right">所属权限类：</td>
			<td><select name="parentid" id="parentid" onchange="GetCatpSize(this.value);">
					<option value="0">一级类别</option>
					<?php GetAllType('#@__purview','#@__purview','id'); ?>
				</select>
				<span class="maroon">*</span></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td height="40" align="right">权限名称：</td>
			<td><input name="classname" type="text" id="classname" class="input" />
				<span class="maroon">*</span></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td height="40" align="right">权限代码：</td>
			<td><input name="purviewkey" type="text" id="purviewkey" class="input" />
				<span class="maroon">*</span></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td height="40" align="right">菜单地址：</td>
			<td><input name="url" type="text" id="url" class="input" />
				</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td height="40" align="right">是否菜单：</td>
			<td><select name="ismenu" id="ismenu">
					<option value="1" selected>是</option>
					<option value="0">否</option>
				</select>
				<span class="maroon">*</span></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td height="40" align="right">排列排序：</td>
			<td><input type="text" name="orderid" id="orderid" class="inputs" value="<?php echo GetOrderID('#@__purview'); ?>" /></td>
			<td>&nbsp;</td>
		</tr>
	</table>
	<div class="formSubBtn">
		<input type="submit" class="submit" value="提交" />
		<input type="button" class="back" value="返回" onclick="history.go(-1);" />
		<input type="hidden" name="action" id="action" value="add" />
	</div>
</form>
</body>
</html>