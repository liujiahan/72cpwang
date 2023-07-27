<?php	require_once(dirname(__FILE__).'/inc/config.inc.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>后台首页</title>
<link href="templates/style/admin.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="templates/js/jquery.min.js"></script>
<script type="text/javascript">
$(function(){
	//控制便签
	$("#homeNote").focus(function(){
		$(".notearea").addClass("borderOn");
		if($.trim($(this).val()) == "点击输入便签内容..."){
			$(this).val("");
		}
	}).blur(function(){
		$(".notearea").removeClass("borderOn");
		if($.trim($(this).val()) == ""){
			$.ajax({
				url : "ajax_do.php",
				type:'post',
				data:{"action":"deladminnotes"},
				dataType:'html',
				success:function(data){	
				}
			});
			$(this).val("点击输入便签内容...");
		}else{
			$.ajax({
				url : "ajax_do.php",
				type:'post',
				data:{"action":"adminnotes", "body":$.trim($(this).val())},
				dataType:'html',
				success:function(data){
				}
			});
		}
	});

	//$("#showad").html('<iframe src="showad.php" width="100%" height="25" scrolling="no" frameborder="0" allowtransparency="true"></iframe>');
});
</script>
</head>
<body>
<div class="homeHeader">
	<div class="header"><span class="title">首页</span><a href="javascript:location.reload();" class="reload">刷新</a></div>
	<div class="news">
		<div class="title">特别注意</div >
		<div id="showad"> 微信菜单更新请联系管理员，切勿私自更新。 </div>
	</div>
</div>
<div class="homeCont">
	<div class="leftArea">
		<div class="homeQuick">
			<h2 class="title">快捷</h2>
			<div class="lnkarea">
				<?php
				// $dosql->Execute("SELECT * FROM `#@__lnk` ORDER BY orderid ASC LIMIT 0, 8");
				// while($row = $dosql->GetArray())
				// {
				// 	echo '<a href="'.$row['lnklink'].'" class="lnk">';
				// 	if($row['lnkico'] != '') echo '<img src="'.$row['lnkico'].'" />';
				// 	echo $row['lnkname'].'</a>';
				// }
				?>
				<div class="cl"></div>
			</div>
		</div>
		<div class="site">
			<h2 class="title">系统</h2>
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td height="33" colspan="2">软件版本号： <span title="<?php echo $cfg_vertime; ?>"><?php echo $cfg_vernum; ?></span></td>
				</tr>
				<tr>
					<td width="50%" height="33">服务器版本： <span title="<?php echo $_SERVER['SERVER_SOFTWARE']; ?>"><?php echo ReStrLen($_SERVER['SERVER_SOFTWARE'],7,''); ?></span></td>
					<td width="50%">操作系统： <?php echo PHP_OS; ?></td>
				</tr>
				<tr>
					<td height="33">PHP版本号： <?php echo PHP_VERSION; ?></td>
					<td>GDLibrary： <?php echo ShowResult(function_exists('imageline')); ?></td>
				</tr>
				<tr>
					<td height="33">MySql版本： <?php echo $dosql->GetVersion(); ?></td>
					<td height="28">ZEND支持： <?php echo ShowResult(function_exists('zend_version')); ?></td>
				</tr>
				<tr class="nb">
					<td height="33" colspan="2">支持上传的最大文件：<?php echo ini_get('upload_max_filesize'); ?></td>
				</tr>
			</table>
		</div>
	</div>
	<div class="rightArea">
		<div class="homeEvent">
			<h2 class="title">日志<span><a href="sysevent.php">更多&gt;&gt;</a></span></h2>
			<ul>
				<?php
				$sql = "SELECT * FROM `#@__sysevent` ORDER BY `id` DESC LIMIT 0,2";

				$dosql->Execute($sql);
				while($row = $dosql->GetArray())
				{
					$r = $dosql->GetOne("SELECT `sitename` FROM `#@__site` WHERE `id`=".$row['siteid']);
					if(empty($r)) $r['sitename'] = '未知站点';

					if($row['model'] == 'login')
					{
				?>
				<li><?php echo MyDate('m-d H:i',$row['posttime']); ?>：用户 <strong><?php echo $row['uname']; ?></strong> 进行了 <span class="blue">登录操作</span> </li>
				<?php
					}
			
					else if($row['model'] == 'logout')
					{
				?>
				<li> <?php echo MyDate('m-d H:i',$row['posttime']); ?>：用户 <strong><?php echo $row['uname']; ?></strong> 进行了 <span class="blue">退出操作</span> </li>
				<?php
					}
					else if($row['classid'] != 0)
					{
						$r2 = $dosql->GetOne("SELECT `classname` FROM `#@__infoclass` WHERE `id`=".$row['classid']);
						
						if($row['action'] == 'add')
							$action = '添加';
						else if($row['action'] == 'update')
							$action = '修改';
						else if($row['action'] == 'del')
							$action = '删除';
						else
							$action = '';
				?>
				<li><?php echo MyDate('m-d H:i',$row['posttime']); ?>：用户 <strong><?php echo $row['uname']; ?></strong> 在 <span class="maroon2"><?php echo $r['sitename']; ?></span> <?php echo $action; ?>了 <span class="blue"><?php echo $r2['classname']; ?></span> </li>
				<?php
					}
					else
					{
				?>
				<li> <?php echo MyDate('m-d H:i',$row['posttime']); ?>：用户 <strong><?php echo $row['uname']; ?></strong> 在 <span class="maroon2"><?php echo $r['sitename']; ?></span> 操作了 <span class="blue"><?php echo $row['model']; ?></span> </li>
				<?php
					}
				}
				?>
			</ul>
		</div>
		
	</div>
	<div class="cl"></div>
</div>

<div class="homeNote">
	<h2 class="title">记事</h2>
	<div class="notearea">
		<textarea name="homeNote" id="homeNote"><?php
		$uname    = $_SESSION['admin'];
		$posttime = time();
		$postip   = GetIP();

		$r = $dosql->GetOne("SELECT `body` FROM `#@__adminnotes` WHERE uname='$uname'");
		if(isset($r['body']))
			echo trim($r['body']);
		else
			echo '点击输入便签内容...';
		?></textarea>
	</div>
</div>

<?php
function ShowResult($revalue)
{
	if($revalue == 1)
		return '<span class="ture">支持</span>';
	else
		return '<span class="flase">不支持</span>';
}
?>
</body>
</html>