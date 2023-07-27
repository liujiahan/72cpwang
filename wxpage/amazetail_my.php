<?php
require_once dirname(__FILE__).'/../include/config.inc.php';
require_once dirname(__FILE__) . '/core/weer.config.php';
require_once dirname(__FILE__) . '/core/suanfa.func.php';
require_once dirname(__FILE__) . '/core/weer.func.php';

if(!isset($_COOKIE['isAdmin']) || $_COOKIE['isAdmin'] != $adminkey){
    ShowMsg("Permission denied","-1");
    exit;
}


?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>魔幻尾算法方案</title>
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
    var cp_dayid = '<?php echo isset($cp_dayid) && !empty($cp_dayid) ? $cp_dayid : ''; ?>';
    function ajaxWeerData(){
        $.ajax({
            url: 'ajax/red_tail_do.php',
            dataType: 'json',
            type: 'post',
            data: {
                action: 'schemes',
                cp_dayid: cp_dayid,
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
                url: 'ajax/red_tail_do.php',
                dataType: 'json',
                type: 'post',
                data: {
                    action: 'schemes',
                    cpnum: cpnum,
                    cp_dayid: cp_dayid,
                },
                success: function(data) {
                    $('.table-th').html(data.table_th)
                    $('.table-td').html(data.table_td)
                }
            })
        })

        $("#tailView").click(function(){
            var cpnum = $("#cpnum").val();
            var datatype = $("#datatype").val();

            window.location = 'ajax/ssq_do.php?action=download&cpnum='+cpnum+'&datatype='+datatype;
            
            /*$.ajax({
                url: 'ajax/ssq_do.php',
                dataType: 'json',
                type: 'post',
                data: {
                    action: 'download',
                    cpnum: cpnum,
                    datatype: datatype,
                },
                success: function(data) {
                    $('.table-th').html(data.table_th)
                    $('.table-td').html(data.table_td)
                }
            })*/
        })
    })
    </script>
</head>

<body>
    <div class="layui-container">
        <blockquote class="layui-elem-quote"><a href="index.php">魔幻尾——我的方案</a></blockquote>
        <!-- <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
            <legend>百合算法——八步过滤</legend>
        </fieldset> -->
        <div class="layui-row">
            <div class="layui-col-xs12" id="weerlist">
                <div class="layui-form">
                    <fieldset  id="weertitle" class="layui-elem-field layui-field-title" style="margin-top: 10px; margin-bottom: 10px;">
                      <legend>方案列表</legend>
                    </fieldset>
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <label class="layui-form-label" style="padding-left: 0;">查询期数</label>
                            <div class="layui-input-inline">
                                <select name="cpnum" id="cpnum" lay-verify="required" lay-search="">
                                    <!-- <option value="">选择期数</option> -->
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
                            <a href="amazetail_new.php" target="_blank" class="layui-btn layui-btn-normal" id="newWeer">新建方案</a>
                            <a href="amazetail_youshi.php" target="_blank" class="layui-btn layui-btn-normal" id="newWeer">优胆方案</a>
                            <div class="layui-input-inline">
                                <select name="datatype" id="datatype" lay-verify="required" lay-search="">
                                    <!-- <option value="">选择期数</option> -->
                                    <option value="1">最新</option>
                                    <option value="2">同期</option>
                                    <option value="3">周二</option>
                                    <option value="4">周四</option>
                                    <option value="5">周日</option>
                                </select>
                            </div>
                            <button class="layui-btn layui-btn-normal" id="tailView">尾数试图</button>
                        </div>
                    </div>
                    <table class="layui-table">
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

    });
    </script>
</body>

</html>