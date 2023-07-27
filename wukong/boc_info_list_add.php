<?php require(dirname(__FILE__).'/inc/config.inc.php');IsModelPriv('zixun_list'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>资讯添加</title>
<link href="templates/style/admin.css" rel="stylesheet" type="text/css" />
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
<div class="topToolbar"> <span class="title">资讯添加</span> <a  class="reload"href="javascript:location.reload();">刷新</a> </div>
<form name="form" id="form" method="post" action="boc_info_list_save.php" onsubmit="return cfm_infolm();">
	<table width="100%" border="0" cellspacing="0" cellpadding="0" class="formTable">
		<tr>
			<td width="15%" height="35" align="right">业务条线：</td>
			<td width="85%">
			<select name="blineid" id="blineid">
				<?php if(isset($_SESSION['blineid']) && !empty($_SESSION['blineid'])){ ?>
					<?php 
						$dosql->Execute("SELECT * FROM `#@__p_question_tag` WHERE pid='0' ORDER BY `orderid` ASC");
						while($row = $dosql->GetArray())
						{
							if($row['id'] != $_SESSION['blineid']){
								continue;
							}
							echo '<option value="'.$row['id'].'">'.$row['tagname'].'</option>';
						}
					?>
				<?php }else{ ?>
					<option value="-1">请选择业务条线</option>
					<?php 
						$dosql->Execute("SELECT * FROM `#@__p_question_tag` WHERE pid='0' ORDER BY `orderid` ASC");
						while($row = $dosql->GetArray())
						{
							echo '<option value="'.$row['id'].'">'.$row['tagname'].'</option>';
						}
					?>
				<?php } ?>
			</select>
			<!-- <span ><a href="boc_question_tag.php"><u>管理业务条线</u></a></span></td> -->
		</tr>

		<tr>
			<td height="35" align="right">标　题：</td>
			<td><input type="text" name="title" id="title" class="input" />
			</td>
		</tr>

		<tr>
			<td width="15%" height="35" align="right">资讯类别：</td>
			<td width="85%">
			<select name="info_attrid" id="info_attrid">
				<option value="-1">请选择资讯类别</option>
				<?php 
					$dosql->Execute("SELECT * FROM `#@__info_attr` ORDER BY `orderid` ASC");
					while($row = $dosql->GetArray())
					{
						echo '<option value="'.$row['id'].'">'.$row['attr_name'].'</option>';
					}
				?>
			</select>
		</tr>	
		
		<tr>
			<td height="35" align="right">内容标签：</td>
			<td><input type="text" name="infoflag" class="input" id="infoflag" />
				<span class="cnote">多关键词之间用 | 隔开</span></td>
		</tr>
		
		
		<!-- <tr>
			<td width="15%" height="35" align="right">权重：</td>
			<td width="85%"><select name="weight" id="weight">
					<option value="0">0</option>
					<option value="1">1</option>
					<option value="2">2</option>
					<option value="3">3</option>
					<option value="4">4</option>
					<option value="5">5</option>
					<option value="6">6</option>
					<option value="7">7</option>
					<option value="8">8</option>
					<option value="9">9</option>
				</select>
		</tr> -->
		

		
		
		
		<tr>
			<td height="35" align="right">图片：</td>
			<td>  <input type="text" name="picurl" id="picurl" class="input"  onfocus="show1()" onmouseover="show1()" onmouseout="clear1()" />
				  <input type="file" id="uploadphoto" name="uploadfile" value="请点击上传图片" style="display:none;" /> 

				  <span class="gray_btn"><a href="javascript:void(0);" onclick="uploadphoto.click()" class="uploadbtn">上传</a> </span>
				  
				  <div id = "images"></div>
				  <div class="imglist"></div> 

				<input type="hidden" name="goods_pic" id="goods_pic" value="" />
			</td>
		</tr> 

		<!-- <tr >
			<td height="40" align="right">场景：</td>
			<td>
				<input name="trale" type="checkbox" value="1" 		checked="checked" />旅游&nbsp;
				<input name="mall" 	type="checkbox" value="1" 		checked="checked" />海淘&nbsp;
				<input name="immigrant" type="checkbox" value="1" 	checked="checked" />移民&nbsp;
				<input name="abroad" type="checkbox" value="1" 		checked="checked" />留学&nbsp;
			</td>
		</tr> -->
		
		<tr>
			<td height="104" align="right">摘　要：</td>
			<td><textarea name="description" id="description" class="textdesc"></textarea>
				<div class="hr_5"></div>
				最多能输入 <strong>255</strong> 个字符 </td>
		</tr>
		
		
		<tr>
			<td height="340" align="right">详细内容：</td>
			<td><textarea name="content" id="content" class="kindeditor"></textarea>
				<script>
				var editor;
				KindEditor.ready(function(K) {
					editor = K.create('textarea[name="content"]', {allowFileManager : true,width:'667px',height:'380px',afterBlur: function(){this.sync();}});
				});
				</script>
			</td>
		</tr>
		
		
		
		
		
		
		
	
	</table>
	<div class="formSubBtn">
		<input type="submit" class="submit" value="提交" />
		<input type="button" class="back" value="返回" onclick="history.go(-1)"  />
		<input type="hidden" name="action" id="action" value="add" />
		<input type="hidden" name="cid" id="cid" value="<?php echo ($cid = isset($cid) ? $cid : ''); ?>" />
	</div>
</form>

</body>
<script language="javascript">

function show1(){
   $("#floatLayer").css("display","block");
}

function clear1(){
   $("#floatLayer").css("display","none");
}
</script>
<script type="text/javascript">
$(document).ready(function(e) {

   var nums = 0;
   var max = 10000;
   $('#uploadphoto').localResizeIMG({
      //width: 800,
      quality: 1,
      success: function (result) {  
	  
		if(nums <  max){
			var submitData={
				base64_string:result.clearBase64, 
			}; 
			
			
			 layer.open({
				type: 2
				,content: '图片上载中'
				,time:1
			  });
			$.ajax({
			   type: "POST",
			   url: "boc_upload.php",
			   data: submitData,
			   dataType:"json",
			   success: function(data){
				 if (0 == data.status) {
					alert(data.content);
					return false;
				 }else{ 
				  
					layer.open({
						content: data.content
						,btn: '知道了'
					  });
					$('#goods_pic').val(data.url);  
					$('#picurl').val(data.url);  
					var images = '<input type = "hidden" name="images[]" value="'+data.url+'"   />';
					
					$("#images").append(images);
					//var attstr= '<img src="'+data.url+'">'; 
					//$(".imglist").html(attstr);
					
					//清除之前的样式
					$("#fullScreen,#floatLayer").remove();
					$("body").append
					(
						//浮层区
						'<div id="floatLayer"><img src="'+data.url+'" height="100%"></div>'
					);
					
					
					nums = nums +1 ;
					
					if(nums >= max ){
						$('.uploadbtn').remove();
					}
					return false;
				 }
			   }, 
				complete :function(XMLHttpRequest, textStatus){
				},
				error:function(XMLHttpRequest, textStatus, errorThrown){ //上传失败 
				   alert(XMLHttpRequest.status);
				   alert(XMLHttpRequest.readyState);
				   alert(textStatus);
				}
			}); 
		
		}else{  
		
			  layer.open({
				content: '已经超过最大的图片上传限制'
				,skin: 'msg'
				,time: 2 //2秒后自动关闭
			  });
			  
		}
		  
      }
  });

}); 
</script>
</html>