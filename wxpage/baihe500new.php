<?php
require_once dirname(__FILE__).'/../include/config.inc.php';
require_once dirname(__FILE__).'/../wxpage/core/choosered.func.php';

$baihe = nextBaiHe();

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>百合算法方案</title>
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
  <legend>百合花盛开</legend>
</fieldset>
 
<form class="layui-form" id="myForm" action="">
  <div class="layui-form-item">
    <label class="layui-form-label">百合命中</label>
    <div class="layui-input-block" style="width: 80px;">
      <input type="text" name="title" lay-verify="title" autocomplete="off" placeholder="6个" class="layui-input">
    </div>
  </div>
  <div class="layui-form-item">
    <label class="layui-form-label">百分比命中</label>
    <div class="layui-input-block" style="width: 80px;">
      <input type="text" name="title" lay-verify="title" autocomplete="off" placeholder="6个" class="layui-input">
    </div>
  </div>
  <div class="layui-form-item">
    <label class="layui-form-label">和值命中</label>
    <div class="layui-input-block" style="width: 80px;">
      <input type="text" name="title" lay-verify="title" autocomplete="off" placeholder="6个" class="layui-input">
    </div>
  </div>

  <div class="layui-form-item">
    <div class="layui-inline">
      <label class="layui-form-label">综合命中</label>
      <div class="layui-input-inline" style="width: 60px;">
        <input type="text" name="yu_win" id="yu_win" placeholder="余集" autocomplete="off" class="layui-input">
      </div>
      <div class="layui-form-mid">-</div>
      <div class="layui-input-inline" style="width: 60px;">
        <input type="text" name="jj_win" id="jj_win" placeholder="交集" autocomplete="off" class="layui-input">
      </div>
      <div class="layui-form-mid">-</div>
      <div class="layui-input-inline" style="width: 60px;">
        <input type="text" name="ba_win" id="ba_win" placeholder="百减" autocomplete="off" class="layui-input">
      </div>
      <div class="layui-form-mid">-</div>
      <div class="layui-input-inline" style="width: 60px;">
        <input type="text" name="he_win" id="he_win" placeholder="和减" autocomplete="off" class="layui-input">
      </div>
    </div>
  </div>

  <div class="layui-form-item">
    <div class="layui-inline">
      <label class="layui-form-label">杀尾</label>
      <div class="layui-input-inline" style="width: 60px;">
        <input type="text" name="killtail[]" placeholder="尾" autocomplete="off" class="layui-input">
      </div>
      <div class="layui-form-mid">-</div>
      <div class="layui-input-inline" style="width: 60px;">
        <input type="text" name="killtail[]" placeholder="尾" autocomplete="off" class="layui-input">
      </div>
      <div class="layui-form-mid">-</div>
      <div class="layui-input-inline" style="width: 60px;">
        <input type="text" name="killtail[]" placeholder="尾" autocomplete="off" class="layui-input">
      </div>
      <div class="layui-form-mid">-</div>
      <div class="layui-input-inline" style="width: 60px;">
        <input type="text" name="killtail[]" placeholder="尾" autocomplete="off" class="layui-input">
      </div>
      <div class="layui-form-mid">-</div>
      <div class="layui-input-inline" style="width: 60px;">
        <input type="text" name="killtail[]" placeholder="尾" autocomplete="off" class="layui-input">
      </div>
      <div class="layui-form-mid">-</div>
      <div class="layui-input-inline" style="width: 60px;">
        <input type="text" name="killtail[]" placeholder="尾" autocomplete="off" class="layui-input">
      </div>
    </div>
  </div>

  <div class="layui-form-item">
    <div class="layui-inline">
      <label class="layui-form-label">胆尾</label>
      <div class="layui-input-inline" style="width: 60px;">
        <input type="text" name="wintail[]" placeholder="尾" autocomplete="off" class="layui-input">
      </div>
      <div class="layui-form-mid">-</div>
      <div class="layui-input-inline" style="width: 60px;">
        <input type="text" name="wintail[]" placeholder="尾" autocomplete="off" class="layui-input">
      </div>
      <div class="layui-form-mid">-</div>
      <div class="layui-input-inline" style="width: 60px;">
        <input type="text" name="wintail[]" placeholder="尾" autocomplete="off" class="layui-input">
      </div>

      <div class="layui-form-mid">范围</div>
      <div class="layui-input-inline" style="width: 60px;">
        <input type="text" name="dannum[]" placeholder="胆红球" autocomplete="off" class="layui-input">
      </div>
      <div class="layui-form-mid">-</div>
      <div class="layui-input-inline" style="width: 60px;">
        <input type="text" name="dannum[]" placeholder="个数" autocomplete="off" class="layui-input">
      </div>
    </div>
  </div>

  <div class="layui-form-item">
    <label class="layui-form-label">方案组合数</label>
    <div class="layui-input-block">
      <input type="checkbox" name="open" lay-skin="switch" lay-filter="switchTest" lay-text="查看|关闭">
      <p class="shownum"></p>
    </div>
  </div>
  <div class="layui-form-item">
    <div class="layui-input-block">
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
        url: 'ajax/red_baihe_do.php?action=baihe500&getnum=1',
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
        url: 'ajax/red_baihe_do.php?action=baihe500',
        dataType: 'json',
        type: 'post',
        data:data,
        success: function(rest) {
        $("#okbtn").html('立即提交');
          if(rest.errcode == 0){
            // layer.tips(rest, data.othis)
            alert(rest.total);
            window.location = 'amazebaihe_my.php';
          }
        }
    })
    return false;
  }); 
  
});
</script>

</body>
</html>