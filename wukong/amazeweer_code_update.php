<?php 

require_once(dirname(__FILE__).'/inc/config.inc.php');
IsModelPriv('yaojian');

require_once(SNRUNNING_ROOT.'/wxpage/core/ssq.config.php');

$maxid = maxDayid() + 1;


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>生成领取码</title>
<link href="templates/style/admin.css?view=9" rel="stylesheet" type="text/css" />
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
<div class="topToolbar"> <span class="title">生成领取码</span> <a class="reload" href="javascript:location.reload();">刷新</a> </div>
<form name="form" id="form" method="post" action="amazeweer_code_save.php">
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
			<td width="25%" height="40" align="right">领取码数量：</td>
			<td width="75%"><input type="text" name="codenum" class="input" id="codenum" value="" />
			</td>
		</tr>
		<tr>
			<td width="25%" height="40" align="right">领取码类型：</td>
			<td width="75%">
				<input type="radio" id="codetype1" name="codetype" value="1" checked>
				<label for="codetype1">2元码</label>
				<input type="radio" id="codetype2" name="codetype" value="2" >
				<label for="codetype2">5元码</label>
				<input type="radio" id="codetype3" name="codetype" value="3" >
				<label for="codetype3">10元码</label>
			</td>
		</tr>		
	</table>
	<div class="formSubBtn">
		<input type="submit" class="submit" value="提交" />
		<input type="button" class="back" value="返回" onclick="history.go(-1);" />
		<input type="hidden" name="action" id="action" value="create_code" />
	</div>
</form>
</body>
</html>