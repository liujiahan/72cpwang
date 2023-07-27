<?php
require_once dirname(__FILE__).'/../include/config.inc.php';
require_once dirname(__FILE__) . '/core/weer.config.php';
require_once dirname(__FILE__) . '/core/suanfa.func.php';
require_once dirname(__FILE__) . '/core/weer.func.php';

if(!isset($_COOKIE['isAdmin']) || $_COOKIE['isAdmin'] != $adminkey){
    ShowMsg("Permission denied","-1");
    exit;
}
$schemes = $dosql->GetOne("SELECT * FROM `#@__caipiao_weermy` WHERE id='$id'");

// $row = $dosql->GetOne("SELECT * FROM `#@__caipiao_weermy_cpdata` WHERE sid='$id'");

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>微尔算法方案号码</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="/static/layui/css/layui.css?t=1522709297490" media="all">
    <link rel="stylesheet" href="/static/layui/css/global.css?t=1522709297490-3" media="all">
    <!-- 注意：如果你直接复制所有代码到本地，上述css路径需要改成你本地的 -->
    <!-- 让IE8/9支持媒体查询，从而兼容栅格 -->
    <!--[if lt IE 9]>
      <script src="https://cdn.staticfile.org/html5shiv/r29/html5.min.js"></script>
      <script src="https://cdn.staticfile.org/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script type="text/javascript" src="bootstrap/js/jquery.min.js"></script>
    <script type="text/javascript" src="/static/js/jquery.serialize-object.js"></script>
    <script type="text/javascript">
    var id = '<?php echo isset($id) && !empty($id) ? $id : ''; ?>';
    function ajaxWeerData(){
        $.ajax({
            url: 'ajax/red_weer_do.php',
            dataType: 'json',
            type: 'post',
            data: {
                action: 'schemes_ssq',
                id: id,
            },
            success: function(data) {
                $('.table-th').html(data.table_th)
                $('.table-td').html(data.table_td)
            }
        })
    }
    $(function(){
        ajaxWeerData();
        $("#weerSearch").click(function(){
            var cpnum = $("#cpnum").val();
            
            $.ajax({
                url: 'ajax/red_weer_do.php',
                dataType: 'json',
                type: 'post',
                data: {
                    action: 'schemes_ssq',
                    cpnum: cpnum,
                    id: id,
                },
                success: function(data) {
                    $('.table-th').html(data.table_th)
                    $('.table-td').html(data.table_td)
                }
            })
        })
    })
    </script>
</head>

<body>
    <div class="layui-container">
        <blockquote class="layui-elem-quote">微尔算法——我的方案——<?php echo $schemes['scheme_name'] ?></blockquote>
        <!-- <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
            <legend>微尔算法——八步过滤</legend>
        </fieldset> -->
        <div class="layui-row">
            <div class="layui-col-xs12" id="weerlist">
                <div class="layui-form">
                    <fieldset  id="weertitle" class="layui-elem-field layui-field-title" style="margin-top: 10px; margin-bottom: 10px;">
                      <legend>方案号码</legend>
                    </fieldset>
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <label class="layui-form-label" style="padding-left: 0;">查询期数</label>
                            <div class="layui-input-inline">
                                <select name="cpnum" id="cpnum" lay-verify="required" lay-search="">
                                    <option value="30">30</option>
                                    <option value="50">50</option>
                                    <option value="80">80</option>
                                    <option value="100">100</option>
                                    <option value="200">200</option>
                                    <option value="300">300</option>
                                    <option value="500">500</option>
                                </select>
                            </div>
                            <button class="layui-btn layui-btn-normal" id="weerSearch" data-weerindex="1">搜索</button>
                            <button class="layui-btn layui-btn-normal" id="calcIndex">计算指数</button>
                            <button class="layui-btn layui-btn-normal" id="exportData" onclick="window.location='amazeweer_excel.php?id=<?php echo $id ?>';">导出</button>
                        </div>
                    </div>
                    <table class="layui-table" style="width: 50%;">
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
    <!-- 注意：如果你直接复制所有代码到本地，上述js路径需要改成你本地的 -->

    <script>
    layui.use(['element', 'form'], function() {
        var $ = layui.jquery,
            element = layui.element; //Tab的切换功能，切换事件监听等，需要依赖element模块

        $("#calcIndex").click(function(){
            $("#calcIndex").html("计算中...");
            $.ajax({
                url: 'ajax/red_weer_do.php',
                dataType: 'html',
                type: 'post',
                data: {action: 'schemes_ssq_index', id:<?php echo $id ?>},
                success: function(data) {
                    $("#calcIndex").html("计算指数");
                    alert('计算完成！');
                    // window.location.reload();
                }
            })
        })

    });
    </script>
</body>

</html>