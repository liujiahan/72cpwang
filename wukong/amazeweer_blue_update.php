<?php 

require_once(dirname(__FILE__).'/inc/config.inc.php');
IsModelPriv('amazeweer');

require_once(SNRUNNING_ROOT.'/wxpage/core/ssq.config.php');

$row = $dosql->GetOne("SELECT * FROM `#@__caipiao_weermy_blue` WHERE id='$id'");
$blueinfo = unserialize($row['blueinfo']);
$gailvarr = unserialize($row['gailvarr']);
$maxid = $row['cp_dayid'];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>蓝号概率设定</title>
<link href="templates/style/admin.css?view=9" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="https://cdn.bootcss.com/jquery/1.9.1/jquery.js"></script>
<script type="text/javascript" src="templates/js/getuploadify.js"></script>
<script type="text/javascript" src="templates/js/checkf.func.js"></script>
<script type="text/javascript" src="templates/js/getjcrop.js"></script>
<script type="text/javascript" src="templates/js/getinfosrc.js"></script>
<script type="text/javascript" src="plugin/colorpicker/colorpicker.js"></script>
<script type="text/javascript" src="plugin/calendar/calendar.js"></script>
<script type="text/javascript" src="editor/kindeditor-min.js"></script>
<script type="text/javascript" src="editor/lang/zh_CN.js"></script>
<script type="text/javascript" src="templates/js/layer.js"></script> 
<script type='text/javascript' src='templates/js/LocalResizeIMG.js'></script>
<style>
 #floatLayer
        {
            position: fixed;
            height: 120px;
            left: 32%;
            top: 238px;
            background-color: white;
            z-index: 10000;
        }

#floatLayer {
    display:none;
}
</style>
</head>
<body>
<div class="topToolbar"> <span class="title">蓝号概率设定</span> <a class="reload" href="javascript:location.reload();">刷新</a> </div>
<form name="form" id="form" method="post" action="amazeweer_blue_save.php">
	<table width="100%" border="0" cellspacing="0" cellpadding="0" class="formTable">
		<tr>
			<td width="25%" height="40" align="right">期数：</td>
			<td width="75%">
				<select name="cp_dayid" class="input"  id="cp_dayid" style="width: 150px;" >
					<option value="">--请选择--</option>
					<?php 
						$monthNum = date('m') * 4 * 3+10;
						if(date("m")==1){
							$fromM    = 1;
						}else{
							$preM     = date("m", strtotime('-1 month'));
							$fromM    = $preM * 4 * 3;
						}
						$curdayid = date('Y');
						if($preM == 12){
							$curdayid = $curdayid - 1;
						}
						for($i=$monthNum; $i>=$fromM; $i--){
							$tmp_dayid = $curdayid;
							if($i >= 100){
							  $tmp_dayid .= $i;
							}else if($i >= 10 && $i < 100){
							  $tmp_dayid .= '0'.$i;
							}else{
							  $tmp_dayid .= '00'.$i;
							}
					?>
					<option value="<?php echo $tmp_dayid ?>" <?php echo $tmp_dayid==$maxid ? 'selected' : ''; ?>>
					    <?php echo $tmp_dayid; ?>
					</option>
					<?php } ?>
				</select>
			</td>
		</tr>
		<tr>
			<td width="25%" height="40" align="right">启用蓝号位数：</td>
			<td width="75%">
				<input type="text" name="bluenum" class="input" style="width: 100px;" value="<?php echo $row['bluenum'] ?>" />位
			</td>
		</tr>
		<tr>
			<td width="25%" height="40" align="right">随机1个全匹配：</td>
			<td width="75%">
				<input type="checkbox" name="bluematch" class="input" style="width: 20px;" value="1" <?php echo $row['bluematch'] == 1 ? 'checked' : ''; ?>/>开启
			</td>
		</tr>
		<?php for ($i=1; $i <= 8; $i++) { ?>
		<tr>
			<td width="25%" height="40" align="right">蓝号第<?php echo $i ?>位及分配概率：</td>
			<td width="75%">
				蓝号<input type="text" name="blues[<?php echo $i ?>][blue]" class="input" style="width: 40px;" value="<?php echo isset($blueinfo[$i]) ? $blueinfo[$i] : '' ?>" />
				概率<input type="text" name="blues[<?php echo $i ?>][gailv]" class="input" style="width: 40px;" value="<?php echo isset($gailvarr[$i]) ? $gailvarr[$i] : '' ?>" />
			</td>
		</tr>
		<?php } ?>
	</table>
	<div class="formSubBtn">
		<input type="submit" class="submit" value="提交" />
		<input type="button" class="back" value="返回" onclick="history.go(-1);" />
		<input type="hidden" name="action" id="action" value="blue_update" />
		<input type="hidden" name="id" id="id" value="<?php echo $row['id'] ?>" />
	</div>
</form>
</body>
<script type="text/javascript">
$(document).ready(function() {

}); 
</script>


</html>