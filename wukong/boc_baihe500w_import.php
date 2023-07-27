<?php if(!defined('IN_BKUP')) exit('Request Error!'); ?>
<script type="text/javascript">

function indata()
{
	if($("#picurl").val()=="")
	{
		alert("请上传Excel数据文件");
		return false;
	}
	else
	{
		$("#resultstr").html("<font color=red><b>数据导入中，请稍后……</b></font>");
		$("#btn_in").html("导入……");
		$.ajax({
			url : "boc_baihe500w_do.php?action=<?php echo $action ?>",
			type:"post",
			data:{"excel":"../"+$("#picurl").val()},
			dataType:"html",
			success:function(data){
				$("#resultstr").html(data);				
			}
		});
	}
}
</script>

<form name="form" id="form">
	<table width="100%" border="0" cellspacing="0" cellpadding="0" class="formTable">
		
		<tr>
			<td height="35" align="right">请上传数据：</td>
			<td><input type="text" name="picurl" id="picurl" class="input" />
				<span class="cnote"><span class="grayBtn" onclick="GetUploadify('uploadify','百合500万','*.xls;*.xlsx','excel文件',1,<?php echo $cfg_max_file_size; ?>,'picurl')">上 传</span> <span id="btn_in" class="grayBtn" onclick="indata()">导 入</span></td>
		</tr>
		<tr>
			<td height="35" align="right">数据模板：</td>
			<td>
				<span id="modeldiv">[<a href="templates/import_bv.xlsx" target="_blank">点击下载</a>] </span>
			</td>
		</tr>		
		<tr class="nb">
			<td height="35" align="right">导入结果：</td>
			<td>
			<div id="resultstr" style="line-height:20px;"></div>
			</td>
		</tr>
	</table>
	<div class="formSubBtn">
		<input type="button" class="back" value="返回" onclick="window.history.go(-1);" />
	</div>
</form>