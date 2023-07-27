<?php require_once(dirname(__FILE__).'/inc/config.inc.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>左侧菜单</title>
<link href="templates/style/menu.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="templates/js/jquery.min.js"></script>
<script type="text/javascript" src="templates/js/tinyscrollbar.js"></script>
<script type="text/javascript" src="templates/js/leftmenu.js"></script>
<style type="text/css">
.menubox a.infoattr{top:42px;}
.menubox a.infosrc{top:69px;}
.menubox a.usertype{top:42px;}
.menubox a.adtype{top:69px;}
.menubox a.weblinktype{top:96px;}
.menubox a.goodsinfoattr{top:42px;}
</style>
</head>
<body>
<div class="quickBtn"> <span class="quickBtnLeft"><a href="infolist_add.php" target="main">添列表</a></span> <span class="quickBtnRight"><a href="infoimg_add.php" target="main">添图片</a></span> </div>

<div class="tGradient"></div>
<div id="scrollmenu">
	<div class="scrollbar">
		<div class="track">
			<div class="thumb">
				<div class="end"></div>
			</div>
		</div>
	</div>
	<div class="viewport">
		<div class="overview">
			<!--scrollbar start-->
			<?php

			//获取管理员模块权限
			$dosql->Execute("SELECT `model` FROM `#@__adminprivacy` WHERE `groupid`=".$cfg_adminlevel." AND `model`<>'category'");
			$modelPriv = array();
			while($row = $dosql->GetArray())
			{
				$modelPriv[] = $row['model'];
			}

			if(empty($modelPriv))
			{
				echo '<div class="tc" style="width:180px;">~(>_<)~<br />您暂无任何可操作权限</div>';
			}
			else
			{
				
				$c = 1;
				$dosql->Execute("select * from `#@__purview` where parentid=0 order by orderid asc ");
				while($row1=$dosql->GetArray())
				{
					$haschild = 'false';
					$purstr = '';
					
					$addStr1 = $c==1?" class=\"title on\" id=\"t1\"":" class=\"title\"";
					$addStr2 = $c==1?"":" style=\"display:none\"";
					
					$purstr .= '<div class="menubox">';
					$purstr .= '<div '.$addStr1.' onclick="DisplayMenu(\'leftmenu'.$c.'\');" title="点击切换显示或隐藏"> '.$row1['classname'].' </div>';

					$purstr .= '<div id="leftmenu'.$c.'" '.$addStr2.'>';
					$dosql->Execute("select * from `#@__purview` where parentid=".$row1['id']." order by orderid asc ",1);
					while($row2=$dosql->GetArray(1))
					{
						if(in_array($row2['purviewkey'],$modelPriv))
						{
							$haschild = 'true';
							$purstr .= '<a href="'.$row2['url'].'" target="main">'.$row2['classname'].'</a> ';
						}
					}
					$purstr .= '</div>';
					$purstr .= '</div>';
					$purstr .= '<div class="hr_5"></div>';
					
					if($haschild == 'true')
						echo $purstr;
					$c++;
				}
			
			
			} ?>

			
			
			<!--scrollbar end-->
		</div>
	</div>
</div>
<div class="bGradient"></div>
<!-- <div class="copyright"> © 2016 <a href="http://sxtm.com/" target="_blank">sxtm.com</a><br />
	All Rights Reserved. </div>
<div class="tabMenu">
	<a href="left_menu_user_name.php" title="切换到名称菜单" class="model"></a>
</div> -->
<?php
function GetModelPriv($m='')
{
	global $dosql,$id;

	$r = $dosql->GetOne("SELECT * FROM `#@__adminprivacy` WHERE `groupid`=$id AND `model`='$m'");
	if(isset($r) && is_array($r))
	{
		return TRUE;
	}
}
?>
</body>
</html>
