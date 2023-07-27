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
			$c = 1;
			$dosql->Execute("select * from `#@__purview` where parentid=0 order by orderid asc ");
			while($row1=$dosql->GetArray())
			{
				$addStr1 = $c==1?" class=\"title on\" id=\"t1\"":" class=\"title\"";
				$addStr2 = $c==1?"":" style=\"display:none\"";
				echo '<div class="menubox">';
				echo '<div '.$addStr1.' onclick="DisplayMenu(\'leftmenu'.$c.'\');" title="点击切换显示或隐藏"> '.$row1['classname'].' </div>';

				echo '<div id="leftmenu'.$c.'" '.$addStr2.'>';
				$dosql->Execute("select * from `#@__purview` where parentid=".$row1['id']." order by orderid asc ",1);
				while($row2=$dosql->GetArray(1))
				{
					echo '<a href="'.$row2['url'].'" target="main">'.$row2['classname'].'</a> ';
				}
				echo '</div>';
				echo '</div>';
				echo '<div class="hr_5"></div>';
				$c++;
			}
			?>
			
			<!--scrollbar end-->
		</div>
	</div>
</div>
<div class="bGradient"></div>

<div class="copyright"> </div>
</body>
</html>
