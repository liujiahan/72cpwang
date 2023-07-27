<?php
require_once dirname(__FILE__).'/../include/config.inc.php';
require_once dirname(__FILE__).'/../wxpage/core/choosered.func.php';

$row = $dosql->GetOne("SELECT * FROM `#@__caipiao_tail_500w` WHERE id='$id'");
if(!isset($row['id'])){
    exit('未找到方案！');
}
$params = unserialize($row['params']);

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>优势号码方案</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" type="text/css" href="/static/ssq/matrix.css">
    <link rel="stylesheet" href="/static/layui/css/layui.css?t=1522709297490" media="all">
    <link rel="stylesheet" href="/static/layui/css/global.css?t=1522709297490-3" media="all">
    <!-- 注意：如果你直接复制所有代码到本地，上述css路径需要改成你本地的 -->
    <!-- 让IE8/9支持媒体查询，从而兼容栅格 -->
    <!--[if lt IE 9]>
      <script src="https://cdn.staticfile.org/html5shiv/r29/html5.min.js"></script>
      <script src="https://cdn.staticfile.org/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script type="text/javascript" src="bootstrap/js/jquery.min.js"></script>
    <script type="text/javascript" src="/static/js/baidutj.js"></script>
</head>

<body>
<div class="layui-container">

<fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
  <legend>优势号码盛开</legend>
</fieldset>
 
<form class="layui-form" id="myForm" action="">
  <div class="layui-form-item">
    <div class="layui-inline">
      <label class="layui-form-label">优势胆码</label>
      <div class="layui-input-inline" style="width: 60px;">
        <input type="text" name="reds[]" placeholder="优胆" value="<?php echo isset($params['reds'][0]) ? $params['reds'][0] : '' ?>" autocomplete="off" class="layui-input">
      </div>
      <div class="layui-form-mid">-</div>
      <div class="layui-input-inline" style="width: 60px;">
        <input type="text" name="reds[]" placeholder="优胆" value="<?php echo isset($params['reds'][1]) ? $params['reds'][1] : '' ?>" autocomplete="off" class="layui-input">
      </div>
      <div class="layui-form-mid">-</div>
      <div class="layui-input-inline" style="width: 60px;">
        <input type="text" name="reds[]" placeholder="优胆" value="<?php echo isset($params['reds'][2]) ? $params['reds'][2] : '' ?>" autocomplete="off" class="layui-input">
      </div>
      <div class="layui-form-mid">-</div>
      <div class="layui-input-inline" style="width: 60px;">
        <input type="text" name="reds[]" placeholder="优胆" value="<?php echo isset($params['reds'][3]) ? $params['reds'][3] : '' ?>" autocomplete="off" class="layui-input">
      </div>
    </div>
  </div>

  <div class="layui-form-item">
    <label class="layui-form-label">优势反胆</label>
    <div class="layui-input-block" style="width: 500px;">
      <input type="text" name="other_reds" lay-verify="title" autocomplete="off" placeholder="红球号码以“.”分隔" value="<?php echo isset($params['other_reds']) ? $params['other_reds'] : '' ?>" class="layui-input">
    </div>
  </div>

  <!-- <div class="layui-form-item">
    <div class="layui-inline">
      <label class="layui-form-label">胆尾</label>
      <div class="layui-input-inline" style="width: 60px;">
        <input type="text" name="wintail[]" placeholder="尾" value="<?php echo isset($params['wintail'][0]) ? $params['wintail'][0] : '' ?>" autocomplete="off" class="layui-input">
      </div>
      <div class="layui-form-mid">-</div>
      <div class="layui-input-inline" style="width: 60px;">
        <input type="text" name="wintail[]" placeholder="尾" value="<?php echo isset($params['wintail'][1]) ? $params['wintail'][1] : '' ?>" autocomplete="off" class="layui-input">
      </div>
      <div class="layui-form-mid">-</div>
      <div class="layui-input-inline" style="width: 60px;">
        <input type="text" name="wintail[]" placeholder="尾" value="<?php echo isset($params['wintail'][2]) ? $params['wintail'][2] : '' ?>" autocomplete="off" class="layui-input">
      </div>
    </div>
  </div> -->

  <div class="layui-form-item">
    <label class="layui-form-label">方案组合数</label>
    <div class="layui-input-block">
      <input type="checkbox" name="open" lay-skin="switch" lay-filter="switchTest" lay-text="查看|关闭">
      <p class="shownum"></p>
    </div>
  </div>
  <div class="layui-form-item">
    <div class="layui-input-block">
      <input type="hidden" name="sid" value="<?php echo $id ?>">
      <button class="layui-btn" lay-submit="" id="okbtn" lay-filter="demo1">立即提交</button>
      <button type="reset" class="layui-btn layui-btn-primary">重置</button>
    </div>
  </div>
</form>
</div>
 
<script src="/static/layui/layui.js" charset="utf-8"></script>
<!-- 注意：如果你直接复制所有代码到本地，上述js路径需要改成你本地的 -->
<script>
layui.use(['form'], function(){
  var form = layui.form
  ,layer = layui.layer;

  //监听指定开关
  form.on('switch(switchTest)', function(data){
    var data = $("#myForm").serialize();
    $(".shownum").html('');
    $.ajax({
        url: 'ajax/red_tail_do.php?action=adv_boldcode&getnum=1',
        dataType: 'html',
        type: 'post',
        data:data,
        success: function(rest) {
          $(".shownum").html(rest);
            layer.tips(rest, data.othis)
        }
    })
  });
  
  //监听提交
  form.on('submit(demo1)', function(data){
    // layer.alert(JSON.stringify(data.field), {
    //   title: '最终的提交信息'
    // })
    var data = $("#myForm").serialize();
    $("#okbtn").html('计算中...');
    $.ajax({
        url: 'ajax/red_tail_do.php?action=adv_boldcode',
        dataType: 'json',
        type: 'post',
        data:data,
        success: function(rest) {
        $("#okbtn").html('立即提交');
          if(rest.errcode == 0){
            // layer.tips(rest, data.othis)
            alert(rest.total);
            window.location = 'amazetail_my.php';
          }
        }
    })
    return false;
  }); 
  
});
</script>

</body>
</html>