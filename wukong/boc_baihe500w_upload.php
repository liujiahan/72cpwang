<?php	require_once(dirname(__FILE__).'/inc/config.inc.php');IsModelPriv('school_employee');


//定义登入常量
define('IN_BKUP', TRUE);

//初始化变量
$action = isset($action) ? $action : 'baihe_500w';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>百合500万精缩数据上传</title>
<link href="templates/style/admin.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="templates/js/jquery.min.js"></script>
<!-- <script type="text/javascript" src="templates/js/forms.func.js"></script> -->
<!-- <script type="text/javascript" src="templates/js/db.func.js"></script> -->


<script type="text/javascript" src="templates/js/jquery.min.js"></script>
<script type="text/javascript" src="templates/js/getuploadify.js"></script>
<script type="text/javascript" src="templates/js/checkf.func.js"></script>
<script type="text/javascript" src="templates/js/getjcrop.js"></script>
<script type="text/javascript" src="templates/js/getinfosrc.js"></script>
<script type="text/javascript" src="plugin/colorpicker/colorpicker.js"></script>
<script type="text/javascript" src="plugin/calendar/calendar.js"></script>
<script type="text/javascript" src="editor/kindeditor-min.js"></script>
<script type="text/javascript" src="editor/lang/zh_CN.js"></script>
<script type="text/javascript" src="templates/js/My97DatePicker/WdatePicker.js"></script>

</head>
<body>
<div class="topToolbar"> <span class="title">双色球数据导入</span> <a href="javascript:location.reload();" class="reload">刷新</a></div>
<div class="toolbarTab">
	<ul>
		<li <?php if($action == 'baihe_500w') echo 'class="on"'; ?>><a href="?action=baihe_500w">百合500万</a></li>
		<li class="line">-</li>
		<!-- <li <?php if($action == 'import_intergal') echo 'class="on"'; ?>><a href="?action=import_intergal">导入积分</a></li> -->
	</ul>
</div>
<?php

//判断执行操作
switch($action)
{
	case 'baihe_500w':

		require_once('boc_baihe500w_import.php');
		exit();
	break;

	case 'import_intergal':

		require_once('boc_xyxf_import_intergal.php');
		exit();
	break;

	
	default:
}
?>
</body>
</html>