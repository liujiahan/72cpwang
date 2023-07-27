<?php
require_once dirname(__FILE__).'/../include/config.inc.php';
require_once dirname(__FILE__).'/core/ssq.config.php';

$maxid = maxDayid();
// $maxid = $maxid + 1;
$maxid = nextCpDayId($maxid);

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>数据</title>
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
    <script type="text/javascript" src="/static/bootstrap/js/jquery.min.js"></script>
    <script type="text/javascript" src="/static/js/baidutj.js"></script>
</head>

<body>
    <div class="layui-container">
        <blockquote class="layui-elem-quote">头条自媒体《72变》</blockquote>
        <div class="layui-collapse">
            <div class="layui-colla-item">
                <h2 class="layui-colla-title">公告【点我有惊喜】&nbsp;<span class="layui-badge-dot"></span></h2>
                <div class="layui-colla-content"> <!-- layui-show -->
                    <h3>【祝大家国庆节快乐】</h3>
                    <!-- <p>活动范围18114期至18117期，共4期</p> -->
                    <!-- <p>购彩需要技巧，中奖需要运气，不怕千万次失败，但求一次成功！</p> -->
                </div>
            </div>
        </div>

        <p style="margin-bottom: 5px;">&nbsp;</p>
              
        <!-- <div class="">
            <a href="paycode.php" class="layui-btn layui-btn-warm" id="ZanZhu" style="float: right;">
            赞助
            </a>
        </div> -->
        <div class="layui-row">
            <div class="layui-col-xs12" id="weerlist">
                <div class="layui-form">
                    <!-- <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
                        <legend><?php echo $maxid ?>期领取码</legend>
                    </fieldset> -->

                    <!-- <button class="layui-btn layui-btn-normal">领取码：<?php echo $code_info ?></button> -->
                    
                    <fieldset  id="weertitle" class="layui-elem-field layui-field-title" style="margin-top: 10px; margin-bottom: 10px;">
                      <legend><?php //echo $maxid ?>数字数据</legend>
                    </fieldset>
                    <div class="layui-form-item">
                        <label class="layui-form-label" style="padding-left: 0;">验证码</label>
                        <div class="layui-input-inline">
                            <input type="text" name="code" id="code" placeholder="请输入验证码" value="<?php echo isset($code) ? $code : ''; ?>" class="layui-input">
                        </div>
                    </div>
                    <button class="layui-btn layui-btn-fluid" id="CodeBtn">确定</button>
                    <table class="layui-table" style="width: 100%;">
                        <thead class="table-th"></thead>
                        <tbody class="table-td"></tbody>
                    </table>
                </div>
                <p style="margin-bottom: 50px;"></p>
                 <!-- <div class="grid-demo grid-demo-bg2"></div> -->
            </div>
        </div>
    </div>
    <script src="/static/layui/layui.js" charset="utf-8"></script>
    <script src="./js/clipboard.min.js" charset="utf-8"></script>
    <!-- 注意：如果你直接复制所有代码到本地，上述js路径需要改成你本地的 -->

    <script>
    
    function getcodedata(){
        var code = $("#code").val();
        var leng = code.length;
        if(code == '') {
            layer.msg('请输入验证码！');
            return false;
        }

        if(leng != 6) {
            layer.msg('请输入有效的验证码！');
            return false;
        }

        $("#CodeBtn").html("领取中...");
        $.ajax({
            url: 'ajax/red_weer_do.php',
            dataType: 'json',
            type: 'post',
            data: {action: 'vipcode', code:code},
            success: function(data) {
                $("#CodeBtn").html("确定");

                $('.table-th').empty()
                $('.table-td').empty()
                if(data.errcode == 1){
                    $('.table-th').html(data.errmsg.table_th)
                    $('.table-td').html(data.errmsg.table_td)
                }else{
                    layer.msg(data.errmsg);
                }
            }
        })
    }
    layui.use(['element', 'form'], function() {
        var $ = layui.jquery,
            element = layui.element; //Tab的切换功能，切换事件监听等，需要依赖element模块
        <?php if(isset($code) && !empty($code)){ ?>
            // $("#CodeBtn").click();
            getcodedata();
        <?php } ?>

        $("#CodeBtn").click(function(){
            getcodedata();
        })
    });
    </script>
</body>

</html>