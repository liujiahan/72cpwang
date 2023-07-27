<?php
require_once dirname(__FILE__).'/../include/config.inc.php';
require_once dirname(__FILE__) . '/core/weer.config.php';



?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>和值除数定胆算法红球</title>
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
</head>

<body>
    <div class="layui-container">
        <blockquote class="layui-elem-quote">和数值除数定胆法</blockquote>
        <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
            <legend>2018042期该算法红球数据</legend>
        </fieldset>
        
        <div class="layui-row">
            <div class="layui-col-xs12" style="margin-bottom: 10px;">
                <div class="grid-demo grid-demo-bg1  layui-bg-blue">移动：12/12、桌面：8/12移动：12/12、桌面：8/12移动：12/12、桌面：8/12移动：12/12、桌面：8/12移动：12/12、桌面：8/12移动：12/12、桌面：8/12移动：12/12、桌面：8/12</div>
            </div>
        </div>

        <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
            <legend>往期历史数据</legend>
        </fieldset>

        <div class="layui-row">
            <div class="layui-col-xs12" style="margin-bottom: 10px;">
                <div class="grid-demo grid-demo-bg1  layui-bg-red">移动：12/12、桌面：8/12移动：12/12、桌面：8/12移动：12/12、桌面：8/12移动：12/12、桌面：8/12移动：12/12、桌面：8/12移动：12/12、桌面：8/12移动：12/12、桌面：8/12</div>
            </div>
            <div class="layui-col-xs12" style="margin-bottom: 10px;">
                <div class="grid-demo grid-demo-bg1  layui-bg-red">移动：12/12、桌面：8/12移动：12/12、桌面：8/12移动：12/12、桌面：8/12移动：12/12、桌面：8/12移动：12/12、桌面：8/12移动：12/12、桌面：8/12移动：12/12、桌面：8/12</div>
            </div>
            <div class="layui-col-xs12" style="margin-bottom: 10px;">
                <div class="grid-demo">移动：12/12、桌面：8/12移动：12/12、桌面：8/12移动：12/12、桌面：8/12移动：12/12、桌面：8/12移动：12/12、桌面：8/12移动：12/12、桌面：8/12移动：12/12、桌面：8/12</div>
            </div>
        </div>
        <fieldset class="layui-elem-field layui-field-title" style="margin-top: 50px;">
            <legend>移动设备、平板、桌面端的复杂组合响应式展现</legend>
        </fieldset>
        <div class="layui-row">
            <div class="layui-col-xs6 layui-col-sm6 layui-col-md4">
                <div class="grid-demo grid-demo-bg1">移动：6/12 | 平板：6/12 | 桌面：4/12</div>
            </div>
            <div class="layui-col-xs6 layui-col-sm6 layui-col-md4">
                <div class="grid-demo layui-bg-red">移动：6/12 | 平板：6/12 | 桌面：4/12</div>
            </div>
            <div class="layui-col-xs4 layui-col-sm12 layui-col-md4">
                <div class="grid-demo layui-bg-blue">移动：4/12 | 平板：12/12 | 桌面：4/12</div>
            </div>
            <div class="layui-col-xs4 layui-col-sm7 layui-col-md8">
                <div class="grid-demo layui-bg-green">移动：4/12 | 平板：7/12 | 桌面：8/12</div>
            </div>
            <div class="layui-col-xs4 layui-col-sm5 layui-col-md4">
                <div class="grid-demo layui-bg-black">移动：4/12 | 平板：5/12 | 桌面：4/12</div>
            </div>
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
</body>

</html>