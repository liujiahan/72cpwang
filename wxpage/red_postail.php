<?php
require_once dirname(__FILE__).'/../include/config.inc.php';
require_once dirname(__FILE__) . '/core/ssq.config.php';
require_once dirname(__FILE__) . '/core/suanfa.func.php';


?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>定位尾数图表</title>
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
    function ajaxWeerData(){
        $.ajax({
            url: 'ajax/red_postail_do.php',
            dataType: 'json',
            type: 'post',
            data: {
                action: 'postail',
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
                url: 'ajax/red_postail_do.php',
                dataType: 'json',
                type: 'post',
                data: {
                    action: 'postail',
                    cpnum: cpnum,
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
        <blockquote class="layui-elem-quote">定位尾数</blockquote>
        <!-- <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
            <legend>微尔算法——八步过滤</legend>
        </fieldset> -->
        <div class="layui-row">
            <div class="layui-col-xs12" id="weerlist">
                <div class="layui-form">
                    <fieldset  id="weertitle" class="layui-elem-field layui-field-title" style="margin-top: 10px; margin-bottom: 10px;">
                      <legend>图表</legend>
                    </fieldset>
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <label class="layui-form-label" style="padding-left: 0;">查询期数</label>
                            <div class="layui-input-inline">
                                <select name="cpnum" id="cpnum" lay-verify="required" lay-search="">
                                    <option value="">选择期数</option>
                                    <option value="10">10</option>
                                    <option value="15">15</option>
                                    <option value="20">20</option>
                                    <option value="30">30</option>
                                    <option value="50">50</option>
                                    <option value="80">80</option>
                                    <option value="100">100</option>
                                    <option value="200">200</option>
                                    <option value="300">300</option>
                                    <option value="400">400</option>
                                    <option value="500">500</option>
                                </select>
                            </div>
                            <button class="layui-btn layui-btn-normal" id="weerSearch" data-weerindex="1">搜索</button>
                        </div>
                    </div>
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
    <!-- 注意：如果你直接复制所有代码到本地，上述js路径需要改成你本地的 -->

    <script>
    layui.use(['element', 'form'], function() {
        var $ = layui.jquery,
            element = layui.element; //Tab的切换功能，切换事件监听等，需要依赖element模块

        
        $("#WeerSaveBtn").click(function(){
            var id = $(this).data('id');
            var formW1 = $("#weer1Form").serializeObject();
            var formW2 = $("#weer2Form").serializeObject();
            var formW3 = $("#weer3Form").serializeObject();
            var formW4 = $("#weer4Form").serializeObject();
            var formW5 = $("#weer5Form").serializeObject();
            var formW6 = $("#weer6Form").serializeObject();
            var formW7 = $("#weer7Form").serializeObject();
            var formW8 = $("#weer8Form").serializeObject();
            var formW9 = $("#weer9Form").serializeObject();
            var blue   = $("#blueForm").serializeObject();
            var jsonData = {'action': "weer_scheme_update", 'id': id};
            var params = $.extend(true, jsonData, formW1, formW2, formW3, formW4, formW5, formW6, formW7, formW8, formW9, blue);

            $.ajax({
                url: 'ajax/red_weer_do.php',
                dataType: 'json',
                type: 'post',
                data: params,
                success: function(data) {
                    console.log(data);

                    if(data.errcode == 1){
                        layer.msg(data.errmsg);
                    }
                }
            })
        })

        $("#WeerCalcBtn").click(function(){
            var formW1 = $("#weer1Form").serializeObject();
            var formW2 = $("#weer2Form").serializeObject();
            var formW3 = $("#weer3Form").serializeObject();
            var formW4 = $("#weer4Form").serializeObject();
            var formW5 = $("#weer5Form").serializeObject();
            var formW6 = $("#weer6Form").serializeObject();
            var formW7 = $("#weer7Form").serializeObject();
            var formW8 = $("#weer8Form").serializeObject();
            var formW9 = $("#weer9Form").serializeObject();
            var jsonData = {'action': "weer_save"};
            var params = $.extend(true, jsonData, formW1, formW2, formW3, formW4, formW5, formW6, formW7, formW8, formW9);

            $("#WeerCalcBtn").html("计算中...");
            $.ajax({
                async:true,
                url: 'ajax/red_weer_do.php',
                dataType: 'html',
                type: 'post',
                data: params,
                success: function(data) {
                    // layer.closeAll('loading');
                    $("#WeerCalcBtn").html("微尔算法八步过滤");

                    layer.open({
                        type: 1,
                        skin: 'layui-layer-rim', //加上边框
                        area: ['600px', '360px'], //宽高
                        content: data
                    });
                }
            })
        })

    });
    </script>
</body>

</html>