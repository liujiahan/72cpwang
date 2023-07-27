<?php 

require_once(dirname(__FILE__).'/inc/config.inc.php');
IsModelPriv('yaojian');

require_once(SNRUNNING_ROOT.'/wxpage/core/ssq.config.php');

$maxid = maxDayid() + 1;

$ssqid = !isset($cp_dayid) ? $maxid : $cp_dayid;


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>领取码管理</title>
<link href="templates/style/admin.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="templates/js/jquery.min.js"></script>
<script type="text/javascript" src="templates/js/forms.func.js"></script>
<script type="text/javascript">
function getm()
{
	window.location='?cp_dayid='+$("#cp_dayid").val()+'&code='+$("#code").val()+'&codetype='+$("#codetype").val();
}
function allowuser()
{
	window.location='amazeweer_code.php';
}

function duijiang(){
	var cp_dayid = $("#cp_dayid").val();
	if(cp_dayid == ''){
		alert('请选择兑奖的期数！');
		return false;
	}
	window.location='amazeweer_code_save.php?action=duijiang&cp_dayid='+cp_dayid;
}

function adduser(userid)
{
	$.ajax({
		url : "boc_yjuser_save.php",
		type:"post",
		data:{"action":"adduser","userid":userid},
		dataType:"html",
		success:function(data){	
			if(data=="yes")
			{
				alert("设置成功！");
				window.location='boc_yjuser_save.php';
			}
			else
				alert(data);
		}
	});
}
</script>
</head>
<body>
<div class="topToolbar"> <span class="title">领取码管理</span> 
<span style="margin-left:30px;color:blue;" class="title">
期数：<select name="cp_dayid" class="input"  id="cp_dayid" style="width: 150px;" >
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
	<option value="<?php echo $tmp_dayid ?>" <?php echo $tmp_dayid==$ssqid ? 'selected' : ''; ?>>
	    <?php echo $tmp_dayid; ?>
	</option>
	<?php } ?>
</select>
类型：<select name="codetype" class="input"  id="codetype" style="width: 150px;" >
	<option value="">--请选择--</option>
	<option value="1" <?php echo isset($codetype) && $codetype == 1 ? 'selected' : ''; ?>>2元码</option>
	<option value="2" <?php echo isset($codetype) && $codetype == 2 ? 'selected' : ''; ?>>5元码</option>
	<option value="3" <?php echo isset($codetype) && $codetype == 3 ? 'selected' : ''; ?>>10元码</option>
</select>
领取码：<input type="text" name="code" class="inputos" id="code" value="<?php  echo isset($code) ? $code : ''; ?>" />

<a href="javascript:getm();">[查询]</a>
<a href="javascript:allowuser();">[全部]</a>
<a href="amazeweer_code_update.php">[新建领取码]</a>
<a href="javascript:duijiang();">[兑奖]</a>
 </span>
<a href="javascript:location.reload();" class="reload">刷新</a></div>
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="dataTable">
	<tr align="left" class="head">
		<td width="5%" height="36" class="firstCol">ID</td>
		<td width="10%">期号</td>
		<td width="10%">领取码</td>
		<td width="5%">类型</td>
		<td width="5%">状态</td>
		<td width="20%">领取号码</td>
		<td width="20%">中奖情况</td>
		<td width="15%">领取时间</td>
		<td width="10%">领取IP</td>
		<!-- <td width="10%">操作</td> -->
	</tr>
	<?php

		$prizename = array(1=>'一',1=>'二',1=>'三',4=>'四',5=>'五',6=>'六');

		$sql = "SELECT * FROM `#@__caipiao_weermy_code` WHERE 1 ";
		if(!empty($cp_dayid)) $sql .= " AND cp_dayid='$cp_dayid'";
		if(!empty($codetype)) $sql .= " AND codetype='$codetype'";
		if(!empty($code)) $sql .= " AND code='$code'";

		$dopage->GetPage($sql);
		$index = 0;
		while($row = $dosql->GetArray())
		{
			$index++;
			$codeinfo = '2元码';
			if($row['codetype'] == 2){
				$codeinfo = '<font color="red">5元码</font>';
			}else if($row['codetype'] == 3){
				$codeinfo = '<font color="blue">10元码</font>';
			}
	?>
	<tr align="left" class="dataTr">
		<td height="36" class="firstCol"><?php echo $index; ?></td>
		<td><?php echo $row['cp_dayid'] ?></td>
		<td><?php echo $row['code']; ?></td>
		<td><?php echo $codeinfo; ?></td>
		<td><?php echo $row['status'] == 1 ? '已领取' : ''; ?></td>
		<td><?php echo !empty($row['ssqinfo']) ? str_replace('|', '<br/>', $row['ssqinfo'])."【發】" : ""; ?></td>
		<td>
		<?php 
			if(!empty($row['wininfo'])){
				$wininfo = unserialize($row['wininfo']);

				foreach ($wininfo as $win_level => $win) {
					echo $win['num']."注".$prizename[$win_level]."等奖，奖金".$win['prizes']."元";
					echo "<br/>";
				}
			}
		?>
		</td>
		<td><?php echo $row['usetime'] != 0 ? date("Y-m-d H:i", $row['usetime']) : ''; ?></td>
		<td><?php echo $row['ipinfo']; ?></td>
		<!-- <td> -->
			<!-- <span>[<a href="boc_yjuser_update.php" >设置身份</a>]</span> -->
			<!-- <span>[<a href="boc_yjuser_save.php?action=del&userid=" onclick="return ConfDel(0);">删除</a>]</span> -->
		<!-- </td> -->
	</tr>
	<?php
		}
	?>
</table>

<?php

//判断无记录样式
if($dosql->GetTotalRow() == 0)
{
	echo '<div class="dataEmpty">暂时没有相关的记录</div>';
}
?>
<div class="bottomToolbar"> <!-- <a href="boc_process_add.php" class="dataBtn">增加审批流程节点</a> --> </div>
<div class="page"> <?php echo $dopage->GetList(); ?> </div>
<?php

//判断是否启用快捷工具栏
if($cfg_quicktool == 'Y')
{
?>
<div class="quickToolbar">
	<div class="qiuckWarp">
		<div class="quickArea"> <!-- <a href="boc_process_add.php" class="dataBtn">增加审批流程节点</a> --> <span class="pageSmall">
			<?php echo $dopage->GetList(); ?>
			</span></div>
		<div class="quickAreaBg"></div>
	</div>
</div>
<?php
}
?>

</body>
</html>