<?php require_once(dirname(__FILE__).'/inc/config.inc.php');IsModelPriv('purview'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>权限管理</title>
<link href="templates/style/admin.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="templates/js/jquery.min.js"></script>
<script type="text/javascript" src="templates/js/forms.func.js"></script>
</head>
<body>
<div class="topToolbar"> <span class="title">权限管理</span> <a href="javascript:location.reload();" class="reload">刷新</a></div>
<form name="form" id="form" method="post" action="">
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="dataTable">
		<tr align="left" class="head">
			<td width="5%" height="36" class="firstCol"><input type="checkbox" name="checkid" onclick="CheckAll(this.checked);" /></td>
			<td width="5%">ID</td>
			<td width="25%">权限名称</td>
			<td width="15%">权限代码</td>
			<td width="20%">菜单地址</td>
			<td width="10%" align="center">排序</td>
			<td class="endCol">操作</td>
		</tr>
	</table>
	<?php

	//循环栏目函数
	function Show($id=0, $i=0)
	{
		global $dosql,$cfg_siteid,$cfg_adminlevel,
		       $catgoryListPriv,$catgoryAddPriv,
			   $catgoryUpdatePriv,$catgoryDelPriv;

		$i++;

		$dosql->Execute("SELECT * FROM `#@__purview` WHERE `siteid`='$cfg_siteid' AND `parentid`=$id ORDER BY `orderid` ASC", $id);
		while($row = $dosql->GetArray($id))
		{

			//设置$classname
			$classname = '';


			//设置空格
			for($n = 1; $n < $i; $n++)
				$classname .= '&nbsp;&nbsp;';


			//设置折叠
			if($row['parentid'] == '0')
				$classname .= '<span class="minusSign" id="rowid_'.$row['id'].'" onclick="DisplayRows('.$row['id'].');">';
			else
				$classname .= '<span class="subType">';


			
			$classname .= $row['classname'].'</span>';
			$addStr = '<a href="purview_add.php?id='.$row['id'].'">添加子权限</a>';
			
			$ismenu = $row['ismenu']==1?" [菜单] ":"";
			
			//信息类型
			$classname .= '<span class="infoTypeTxt">'.$ismenu.'</span>';
			
			
			//修改权限
			$updateStr = '<a href="purview_update.php?id='.$row['id'].'">修改</a>';
			

			//删除权限
			$delStr = '<a href="purview_save.php?action=delpurview&id='.$row['id'].'" onclick="return ConfDel(2);">删除</a>';
			

	?>
	<div rel="rowpid_<?php echo $row['parentid']; ?>">
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="dataTable">
			<tr align="left" class="dataTr">
				<td width="5%" height="36" class="firstCol"><input type="checkbox" name="checkid[]" id="checkid[]" value="<?php echo $row['id']; ?>" /></td>
				<td width="5%"><?php echo $row['id']; ?>
					<input type="hidden" name="id[]" id="id[]" value="<?php echo $row['id']; ?>" /></td>
				<td width="25%"><?php echo $classname; ?></td>
				<td width="15%"><?php echo $row['purviewkey']; ?></td>
				<td width="20%"><?php echo $row['url']; ?></td>
				<td width="10%" align="center"><?php echo $row['orderid']; ?></td>
				<td class="action endCol"><span><?php echo $addStr; ?></span> | <span><?php echo $updateStr; ?></span> | <span class="nb"><?php echo $delStr; ?></span></td>
			</tr>
		</table>
	</div>
	<?php
			Show($row['id'], $i+2);
		}
	}
	Show();


	//判断无记录样式
	if($dosql->GetTotalRow(0) == 0)
	{
		echo '<div class="dataEmpty">暂时没有相关的记录</div>';
	}
	
	
	//判断类别页是否折叠
	if($cfg_typefold == 'Y')
	{
		echo '<script>HideAllRows();</script>';
	}
	?>
</form>
<div class="bottomToolbar"><a href="purview_add.php" class="dataBtn">添加权限</a> </div>
<div class="page">
	<div class="pageText">共有<span><?php echo $dosql->GetTableRow('#@__purview',$cfg_siteid); ?></span>条记录</div>
</div>

<?php

//判断是否启用快捷工具栏
if($cfg_quicktool == 'Y')
{
?>
<div class="quickToolbar">
	<div class="qiuckWarp">
		<div class="quickArea"> <a href="purview_add.php" class="dataBtn">添加权限</a><span class="pageSmall">
			<div class="pageText">共有<span><?php echo $dosql->GetTableRow('#@__infoclass',$cfg_siteid); ?></span>条记录</div>
			</span></div>
		<div class="quickAreaBg"></div>
	</div>
</div>
<?php
}
?>
</body>
</html>