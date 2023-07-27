<?php require_once(dirname(__FILE__).'/inc/config.inc.php');IsModelPriv('admingroup'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>修改管理组</title>
<link href="templates/style/admin.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="templates/js/jquery.min.js"></script>
<script type="text/javascript" src="templates/js/checkf.func.js"></script>
<script type="text/javascript" src="templates/js/forms.func.js"></script>
</head>
<body>
<?php
$row = $dosql->GetOne("SELECT * FROM `#@__admingroup` WHERE `id`=$id");
?>
<div class="formHeader"> <span class="title">修改管理组</span> <a href="javascript:location.reload();" class="reload">刷新</a> </div>
<form name="form" id="form" method="post" action="admingroup_save.php" onsubmit="return cfm_admingroup();">
	<table width="100%" border="0" cellspacing="0" cellpadding="0" class="formTable">
		<tr>
			<td width="25%" height="40" align="right">管理组名称：</td>
			<td width="75%"><input type="text" name="groupname" id="groupname" class="input" value="<?php echo $row['groupname']; ?>" />
				<span class="maroon">*</span><span class="cnote">带<span class="maroon">*</span>号表示为必填项</span></td>
		</tr>
		<tr>
			<td height="118" align="right">管理组描述：</td>
			<td><textarea name="description" id="description" class="textarea"><?php echo $row['description']; ?></textarea></td>
		</tr>
		<tr>
			<td height="40" align="right">默认进入站：</td>
			<td><?php
				$dosql->Execute("SELECT * FROM `#@__site` ORDER BY `id` ASC");
				while($row2 = $dosql->GetArray())
				{
					if($row['groupsite'] == $row2['id'])
						$checked = 'checked="checked"';
					else
						$checked = '';

					echo '<input type="radio" name="groupsite" value="'.$row2['id'].'" '.$checked.' />&nbsp;'.$row2['sitename'].'&nbsp;&nbsp;';
				}
				?><span class="cnote">登录成功后自动进入的站点</span></td>
		</tr>
		<tr>
			<td height="40" align="right">管理组状态：</td>
			<td><input type="radio" name="checkinfo" value="true" <?php if($row['checkinfo'] == 'true') echo 'checked="checked"'; ?> />
				启用&nbsp;
				<input type="radio" name="checkinfo" value="false" <?php if($row['checkinfo'] == 'false') echo 'checked="checked"'; ?> />
				未启用</td>
		</tr>
		<tr>
			<td height="35" align="right">模块权限：</td>
			<td><?php
			if($id != 1)
			{
				$dosql->Execute("select * from `#@__purview` where parentid=0 order by orderid asc ");
				while($row1=$dosql->GetArray())
				{
					echo '<div class="purviewTitle" style="color:blue;"><strong>'.$row1['classname'].'</strong></div>';
					echo '<div class="purviewList">';
					$dosql->Execute("select * from `#@__purview` where parentid=".$row1['id']." order by orderid asc ",1);
					while($row2=$dosql->GetArray(1))
					{
						echo '<span><input type="checkbox" name="model[]" value="'.$row2['purviewkey'].'" '.GetModelPriv($row2['purviewkey']).' />'.$row2['classname'].'</span>';
					}
					echo '</div>';
				}
			?>
			
				
				<div class="purviewSel"><a href="javascript:;" onclick="SelModel(true)">全选</a>&nbsp;&nbsp;<a href="javascript:;" onclick="SelModel(false)">反选</a></div>
				<?php
			}
			else
			{
				echo '<strong class="maroon2">所有权限</strong>';
			}
			?></td>
		</tr>
		
		<tr class="nb">
			<td height="40" align="right">栏目权限：</td>
			<td><?php
			if($id != 1)
			{
				echo '<select size="10" style="width:200px" multiple="true" name="classpurview[]">';
				listclass($row['classpurview'],1,$row['id']);
				echo '</select>';
			}
			else
			{
				echo '<strong class="maroon2">所有权限</strong>';
			}
			?></td>
		</tr>
		
		
		
		<?php
		if(empty($id) or $id != 1)
		{
		?>
		<tr class="nb">
			<td height="35" align="right">&nbsp;</td>
			<td><ul class="tipsList">
					<li>按CTRL可多选，选择请注意级联关系。</li>					
				</ul></td>
		</tr>
		<?php
		}
		?>
	</table>
	<div class="formSubBtn">
		<input type="submit" class="submit" value="提交" />
		<input type="button" class="back" value="返回" onclick="history.go(-1);" />
		<input type="hidden" name="action" id="action" value="update" />
		<input type="hidden" name="id" id="id" value="<?php echo $id; ?>" />
	</div>
</form>
<?php

function listclass($purids,$siteid=1, $groupid=0, $id=0, $i=0)
{
	global $dosql;
	if(!empty($purids))
		$purviewarr = explode(',', $purids);
	
	$dosql->Execute("SELECT * FROM `#@__infoclass` WHERE `siteid`=$siteid AND `parentid`=$id ORDER BY `orderid` ASC", $id);
	$i++;
	while($row = $dosql->GetArray($id))
	{
		//设置$classname
		$classname = '';

		//设置空格
		for($n = 1; $n < $i; $n++)
			$classname .= '--';
		
		$classname .= $row['classname'];
		$selected = in_array($row['id'], $purviewarr)?"selected":"";

		echo '<option value="'.$row['id'].'" '.$selected.'>'.$classname.'</option>';

		listclass($purids,$siteid, $groupid, $row['id'], $i+2);
	}
}


function GetModelPriv($m='')
{
	global $dosql,$id;

	$r = $dosql->GetOne("SELECT * FROM `#@__adminprivacy` WHERE `groupid`=$id AND `model`='$m'");
	if(isset($r) && is_array($r))
	{
		return 'checked="checked"'; 
	}
}
?>
</body>
</html>