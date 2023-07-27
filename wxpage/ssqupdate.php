<?php 
require_once(dirname(__FILE__).'/../include/config.inc.php');
require_once(dirname(__FILE__).'/core/ssq.config.php');

LoginCheck();
if(!(isset($_COOKIE['isAdmin']) && $_COOKIE['isAdmin'] == $adminkey)){
    exit;
}
    
$maxid = maxDayid();

$urlarr = ssqUpdateCfg();
    
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title><?php echo $cfg_seotitle ?></title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="/static/layui/css/layui.css?t=1522709297490" media="all">
    <link rel="stylesheet" href="/static/layui/css/global.css?t=1522709297490-3" media="all">
    <script type="text/javascript" src="bootstrap/js/jquery.min.js"></script>
    <!-- 注意：如果你直接复制所有代码到本地，上述css路径需要改成你本地的 -->
</head>

<body>
    <div class="layui-container">
        <blockquote class="layui-elem-quote"><a href="index.php"><?php echo $cfg_seotitle ?></a></blockquote>
        <div class="layui-row">
          <a class="layui-btn layui-btn-normal" id="btnCalc" style="float: right;">更新数据</a>
        </div>
        <div class="layui-row">
            <fieldset class="layui-elem-field layui-field-title" style="margin-top: 30px;">
                <legend>大数据项目轴</legend>
            </fieldset>
            <ul class="layui-timeline">
                <li class="layui-timeline-item">
                    <i class="layui-icon layui-timeline-axis"></i>
                    <div class="layui-timeline-content layui-text">
                        <div class="layui-timeline-title">双色球：目前更新到<?php echo $maxid ?>期</div>
                    </div>
                </li>
                <?php $index = 0; ?>
                <?php foreach ($urlarr as $action => $v) { ?>
                  <li class="layui-timeline-item <?php echo $action ?>" id="update<?php echo $index ?>" style="display: none;">
                      <i class="layui-icon layui-anim layui-anim-rotate layui-anim-loop layui-timeline-axis"></i>
                      <div class="layui-timeline-content layui-text">
                          <h3 class="layui-timeline-title"><?php echo $v['name'] ?></h3>
                          <p></p>
                      </div>
                  </li>
                  <li class="layui-timeline-item <?php echo $action ?>" id="complete<?php echo $index ?>" style="display: none;">
                      <i class="layui-icon layui-timeline-axis"></i>
                      <div class="layui-timeline-content layui-text">
                          <h3 class="layui-timeline-title"><?php echo $v['name'] ?></h3>
                          <p></p>
                      </div>
                  </li>
                  <?php $index++; ?>
                <?php } ?>
            </ul>
        </div>
    </div>
    <script src="/static/layui/layui.js" charset="utf-8"></script>
    <!-- 注意：如果你直接复制所有代码到本地，上述js路径需要改成你本地的 -->
    <script>
    layui.use(['element', 'form'], function() {
        var $ = layui.jquery,
            element = layui.element; //Tab的切换功能，切换事件监听等，需要依赖element模块


    });
    </script>
  <script type="text/javascript">

    function calc_do(index){
      $("#update"+index).show();
      $.ajax({
        async:true,
        url: "ssqupdate_do.php",
        dataType: "json",
        data: {index: index},
        type: "post",
        success: function(data){
          if(data.errcode == 2 || data.errcode == 0){
            alert(data.errmsg);
            // window.location.reload();
            return false;
          }

          if(data.errcode == 1){
            $("#update"+index).hide();
            $("#complete"+index).show();
            // $("#update"+index+" i").removeClass('layui-anim');
            // $("#update"+index+" i").removeClass('layui-anim-rotate');
            // $("#update"+index+" i").removeClass('layui-anim-loop');
            $("#complete"+index+" p").html('更新成功');

            calc_do(data.index);
          }
        }
      })
    }

    $(function(){
      var calc = false;
      var step = 0;
      $("#btnCalc").click(function(){
        //计算按钮只能点一下
        if(calc){
          return;
        }
        calc = true;
        //ajax同步循环请求
        calc_do(step);
      })
    })
  </script>
</body>

</html>