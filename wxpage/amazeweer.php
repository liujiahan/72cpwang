<?php
require_once dirname(__FILE__).'/../include/config.inc.php';
require_once dirname(__FILE__) . '/core/weer.config.php';
require_once dirname(__FILE__) . '/core/suanfa.func.php';
require_once dirname(__FILE__) . '/core/weer.func.php';

if(!isset($_COOKIE['isAdmin']) || $_COOKIE['isAdmin'] != $adminkey){
    ShowMsg("Permission denied","-1");
    exit;
}

$weertitle = array(
    'weer1' => '红球位比值A轮',
    'weer2' => '红球位比值B轮',
    'weer3' => '红球位比值C轮',
    'weer4' => '高尾',
    'weer5' => '按位间距奇偶',
    'weer6' => '大小数和值',
    'weer7' => '首尾和差间距尾数012路',
    'weer8' => '位尾数和012路',
);

$cp_dayid = isset($cp_dayid) ? $cp_dayid : 0;
$weermiss = WeerMissing($cp_dayid);

$missinfo = $weermiss['miss'];
unset($weermiss['miss']);

$bluemiss = blueMissing($cp_dayid);

$redarr = array();
if($cp_dayid){
	$row = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid='$cp_dayid'");
	$redarr = explode(',', $row['red_num']);
	$redweer = GetWeerData($redarr);
}

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>微尔算法8步智能过滤</title>
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
    function ajaxWeerData(weerindex){
        $.ajax({
            url: 'ajax/red_weer_do.php',
            dataType: 'json',
            type: 'post',
            data: {
                action: 'weerlist',
                weerindex: weerindex,
                cp_dayid: cp_dayid,
            },
            success: function(data) {
                $('.table-th').html(data.table_th)
                $('.table-td').html(data.table_td)
            }
        })
    }
    $(function(){
        var weertitle = '<?php echo json_encode($weertitle) ?>';
        var weertitle = JSON.parse( weertitle );

        var weerindex = 1;
        ajaxWeerData(1);
        $("ul#weer8step").on("click","li",function(){      //只需要找到你点击的是哪个ul里面的就行
            weerindex = $(this).index() + 1;

            ajaxWeerData(weerindex);

            if(weerindex == 9){
                $("#weerlist").fadeOut();
            }else{
                $("#weerlist").fadeIn();
            }
        });

        $("#weerSearch").click(function(){
            var cpnum = $("#cpnum").val();
            
            $.ajax({
                url: 'ajax/red_weer_do.php',
                dataType: 'json',
                type: 'post',
                data: {
                    action: 'weerlist',
                    cpnum: cpnum,
                    weerindex: weerindex,
                    cp_dayid: cp_dayid,
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
        <blockquote class="layui-elem-quote">微尔算法</blockquote>
        <!-- <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
            <legend>微尔算法——八步过滤</legend>
        </fieldset> -->
        <div class="layui-row">
            <div class="layui-col-xs12">
                <div class="layui-tab layui-tab-card">
                    <ul class="layui-tab-title" id="weer8step">
                        <li class="layui-this">第1步</li>
                        <li>第2步</li>
                        <li>第3步</li>
                        <li>第4步</li>
                        <li>第5步</li>
                        <li>第6步</li>
                        <li>第7步</li>
                        <li>第8步</li>
                        <li>完成</li>
                    </ul>
                    <div class="layui-tab-content" style="height: 320px;">
                        <div class="layui-tab-item layui-show">
                            <form id="weer1Form" class="layui-form">
                            <fieldset class="layui-elem-field layui-field-title" style="margin-top: 10px; margin-bottom: 10px;">
                              <legend>红球位比值A轮</legend>
                            </fieldset>
                            <?php
                                $weerCfg1 = GetWeerCfg(1);
                            ?>
                            <?php foreach ($weerCfg1 as $pos => $weercfg) { ?>
                            <div class="layui-form-item" style="margin-bottom: 5px;">
                                <label class="layui-form-label" style="width: 36px;"><?php echo $pos . '位'; ?></label>
                                <div class="layui-input-block" style="margin-left: 0;">
                                    <?php foreach ($weercfg as $v) { ?>
                                        <input type="checkbox" name="weer1[<?php echo str_replace('-', '_', $pos) ?>][]" lay-skin="primary" title="<?php echo $v ?><span style='color:<?php echo WeerColor($weermiss['weer1'][$pos][$v]); ?>;padding-right:0;'><?php echo $weermiss['weer1'][$pos][$v]<10 ? $weermiss['weer1'][$pos][$v].'&nbsp;&nbsp;' : $weermiss['weer1'][$pos][$v] ?></span>" value="<?php echo $v ?>" <?php echo isset($redweer['weer1'][$pos]) && $redweer['weer1'][$pos] == $v ? 'checked' : '' ?>>
                                    <?php } ?>
                                    <!-- <a class="layui-btn layui-btn-primary layui-btn-xs">全</a> -->
                                    <!-- <a class="layui-btn layui-btn-primary layui-btn-xs">清</a> -->
                                    <span style="border: 1px solid #030303; background-color: #E0E0E0; padding: 5px 10px;">
                                    	<span style="color: red;padding: 0 10px;"><?php echo $missinfo['weer1'][$pos]['hot']['num'] . "热:" . $missinfo['weer1'][$pos]['hot']['misssum'] ?></span>
                                    	<span style="color: orange;padding: 0 10px;"><?php echo $missinfo['weer1'][$pos]['warm']['num'] . "温:" . $missinfo['weer1'][$pos]['warm']['misssum'] ?></span>
                                    	<span style="color: blue;padding: 0 10px;"><?php echo $missinfo['weer1'][$pos]['cool']['num'] . "冷:" . $missinfo['weer1'][$pos]['cool']['misssum'] ?></span>
                                    </span>

                                </div>
                            </div>
                            <?php } ?>
                            </form>
                        </div>
                        <div class="layui-tab-item">
                            <form id="weer2Form"class="layui-form">
                            <fieldset class="layui-elem-field layui-field-title" style="margin-top: 10px; margin-bottom: 10px;">
                              <legend>红球位比值B轮</legend>
                            </fieldset>
                            <?php
                                $weerCfg2 = GetWeerCfg(2);
                            ?>
                            <?php foreach ($weerCfg2 as $pos => $weercfg) { ?>
                            <div class="layui-form-item" style="margin-bottom: 5px;">
                                <label class="layui-form-label" style="width: 36px;"><?php echo $pos . '位'; ?></label>
                                <div class="layui-input-block" style="margin-left: 0;">
                                    <?php foreach ($weercfg as $v) { ?>
                                        <input type="checkbox" name="weer2[<?php echo str_replace('-', '_', $pos) ?>][]" lay-skin="primary" title="<?php echo $v ?><span style='color:<?php echo WeerColor($weermiss['weer2'][$pos][$v]); ?>;padding-right:0;'><?php echo $weermiss['weer2'][$pos][$v]<10 ? $weermiss['weer2'][$pos][$v].'&nbsp;&nbsp;' : $weermiss['weer2'][$pos][$v] ?></span>" value="<?php echo $v ?>" <?php echo isset($redweer['weer2'][$pos]) && $redweer['weer2'][$pos] == $v ? 'checked' : '' ?>>
                                    <?php } ?>
                                    <!-- <a class="layui-btn layui-btn-primary layui-btn-xs">全</a> -->
                                    <!-- <a class="layui-btn layui-btn-primary layui-btn-xs">清</a> -->
                                    <span style="border: 1px solid #030303; background-color: #E0E0E0; padding: 5px 10px;">
                                    	<span style="color: red;padding: 0 10px;"><?php echo $missinfo['weer2'][$pos]['hot']['num'] . "热:" . $missinfo['weer2'][$pos]['hot']['misssum'] ?></span>
                                    	<span style="color: orange;padding: 0 10px;"><?php echo $missinfo['weer2'][$pos]['warm']['num'] . "温:" . $missinfo['weer2'][$pos]['warm']['misssum'] ?></span>
                                    	<span style="color: blue;padding: 0 10px;"><?php echo $missinfo['weer2'][$pos]['cool']['num'] . "冷:" . $missinfo['weer2'][$pos]['cool']['misssum'] ?></span>
                                    </span>
                                </div>
                            </div>
                            <?php } ?>
                            </form>
                        </div>
                        <div class="layui-tab-item">
                            <form id="weer3Form"class="layui-form">
                            <fieldset class="layui-elem-field layui-field-title" style="margin-top: 10px; margin-bottom: 10px;">
                              <legend>红球位比值C轮</legend>
                            </fieldset>
                            <?php
                                $weerCfg3 = GetWeerCfg(3);
                            ?>
                            <?php foreach ($weerCfg3 as $pos => $weercfg) { ?>
                            <div class="layui-form-item" style="margin-bottom: 5px;">
                                <label class="layui-form-label" style="width: 36px;"><?php echo $pos . '位'; ?></label>
                                <div class="layui-input-block" style="margin-left: 0;">
                                    <?php foreach ($weercfg as $v) { ?>
                                        <input type="checkbox" name="weer3[<?php echo str_replace('-', '_', $pos) ?>][]" lay-skin="primary" title="<?php echo $v ?><span style='color:<?php echo WeerColor($weermiss['weer3'][$pos][$v]); ?>;padding-right:0;'><?php echo $weermiss['weer3'][$pos][$v]<10 ? $weermiss['weer3'][$pos][$v].'&nbsp;&nbsp;' : $weermiss['weer3'][$pos][$v] ?></span>" value="<?php echo $v ?>" <?php echo isset($redweer['weer3'][$pos]) && $redweer['weer3'][$pos] == $v ? 'checked' : '' ?>>
                                    <?php } ?>
                                    <!-- <a class="layui-btn layui-btn-primary layui-btn-xs">全</a> -->
                                    <!-- <a class="layui-btn layui-btn-primary layui-btn-xs">清</a> -->
                                    <span style="border: 1px solid #030303; background-color: #E0E0E0; padding: 5px 10px;">
                                    	<span style="color: red;padding: 0 10px;"><?php echo $missinfo['weer3'][$pos]['hot']['num'] . "热:" . $missinfo['weer3'][$pos]['hot']['misssum'] ?></span>
                                    	<span style="color: orange;padding: 0 10px;"><?php echo $missinfo['weer3'][$pos]['warm']['num'] . "温:" . $missinfo['weer3'][$pos]['warm']['misssum'] ?></span>
                                    	<span style="color: blue;padding: 0 10px;"><?php echo $missinfo['weer3'][$pos]['cool']['num'] . "冷:" . $missinfo['weer3'][$pos]['cool']['misssum'] ?></span>
                                    </span>
                                </div>
                            </div>
                            <?php } ?>
                            </form>
                        </div>
                        <div class="layui-tab-item">
                            <form id="weer4Form"class="layui-form">
                            <fieldset class="layui-elem-field layui-field-title" style="margin-top: 10px; margin-bottom: 10px;">
                              <legend>高尾</legend>
                            </fieldset>
                            <?php
                                $weerCfg4 = GetWeerCfg(4);
                            ?>
                            <?php foreach ($weerCfg4 as $pos => $weercfg) { ?>
                            <?php $tmppos = explode('-', $pos); ?>
                            <div class="layui-form-item" style="margin-bottom: 5px;">
                                <label class="layui-form-label" style="width: 60px;"><?php echo $tmppos[0] . '位与' . $tmppos[1] . '位'; ?></label>
                                <div class="layui-input-block" style="margin-left: 0;">
                                    <?php foreach ($weercfg as $v) { ?>
                                        <input type="checkbox" name="weer4[<?php echo str_replace('-', '_', $pos) ?>][]" lay-skin="primary" title="<?php echo $v ?><span style='color:<?php echo WeerColor($weermiss['weer4'][$pos][$v]); ?>;padding-right:0;'><?php echo $weermiss['weer4'][$pos][$v] ?></span>" value="<?php echo $v ?>" <?php echo isset($redweer['weer4'][$pos]) && $redweer['weer4'][$pos] == $v ? 'checked' : '' ?>>
                                    <?php } ?>
                                </div>
                            </div>
                            <?php } ?>
                            </form>
                        </div>
                        <div class="layui-tab-item">
                            <form id="weer5Form"class="layui-form">
                            <fieldset class="layui-elem-field layui-field-title" style="margin-top: 10px; margin-bottom: 10px;">
                              <legend>按位间距奇偶</legend>
                            </fieldset>
                            <?php
                                $weerCfg5 = GetWeerCfg(5);
                            ?>
                            <?php foreach ($weerCfg5 as $pos => $weercfg) { ?>
                            <div class="layui-form-item" style="margin-bottom: 5px; margin-left: -10px;">
                                <label class="layui-form-label" style="width: 100px;"><?php echo $pos; ?></label>
                                <div class="layui-input-block" style="margin-left: 0;">
                                    <?php foreach ($weercfg as $v) { ?>
                                        <input type="checkbox" name="weer5[]" lay-skin="primary" title="<?php echo $v ?><span style='color:<?php echo WeerColor(8); ?>;padding-right:0;'><?php echo $weermiss['weer5'][$v] ?></span>" value="<?php echo $v ?>" <?php echo isset($redweer['weer5']['code']) && $redweer['weer5']['code'] == $v ? 'checked' : '' ?>>
                                    <?php } ?>
                                </div>
                            </div>
                            <?php } ?>
                            </form>
                        </div>
                        <div class="layui-tab-item">
                            <form id="weer6Form"class="layui-form">
                            <fieldset class="layui-elem-field layui-field-title" style="margin-top: 10px; margin-bottom: 10px;">
                              <legend>大小数和值</legend>
                            </fieldset>
                            <?php
                                $weerCfg6 = GetWeerCfg(6);
                            ?>
                            <?php foreach ($weerCfg6 as $pos => $weercfg) { ?>
                            <div class="layui-form-item" style="margin-bottom: 5px;">
                                <label class="layui-form-label" style="width: 75px;"><?php echo $pos; ?></label>
                                <div class="layui-input-block" style="margin-left: 0;">
                                    <?php foreach ($weercfg as $v) { ?>
                                        <input type="checkbox" name="weer6[]" lay-skin="primary" title="<?php echo $v ?><span style='color:<?php echo WeerColor($weermiss['weer6'][$v]); ?>;padding-right:0;'><?php echo $weermiss['weer6'][$v] ?></span>" value="<?php echo $v ?>" <?php echo isset($redweer['weer6']['code']) && $redweer['weer6']['code'] == $v ? 'checked' : '' ?>>
                                    <?php } ?>
                                </div>
                            </div>
                            <?php } ?>
                            </form>
                        </div>
                        <div class="layui-tab-item">
                            <form id="weer7Form"class="layui-form">
                            <fieldset class="layui-elem-field layui-field-title" style="margin-top: 10px; margin-bottom: 10px;">
                              <legend>首尾和差间距尾数012路</legend>
                            </fieldset>
                            <?php
                                $weerCfg7 = GetWeerCfg(7);
                                $weerCfg7 = array_values($weerCfg7);
                                $weerCfg7 = $weerCfg7[0];
                                $weerCfg7_1 = array_slice($weerCfg7, 0, 7);
                                $weerCfg7_2 = array_slice($weerCfg7, 7, 7);
                                $weerCfg7_3 = array_slice($weerCfg7, 14, 7);
                                $weerCfg7_4 = array_slice($weerCfg7, 21, 7);
                            ?>
                            <div class="layui-form-item" style="margin-bottom: 5px;">
                                <div class="layui-input-block" style="margin-left: 20px;">
                                    <?php foreach ($weerCfg7_1 as $v) { ?>
                                        <input type="checkbox" name="weer7[]" lay-skin="primary" title="<?php echo $v ?><span style='color:<?php echo WeerColor(8); ?>;padding-right:0;'><?php echo $weermiss['weer7'][$v]<10 ? $weermiss['weer7'][$v].'&nbsp;&nbsp;' : $weermiss['weer7'][$v] ?></span>" value="<?php echo $v ?>" <?php echo isset($redweer['weer7']['code']) && $redweer['weer7']['code'] == $v ? 'checked' : '' ?>>
                                    <?php } ?>
                                </div>
                                <div class="layui-input-block" style="margin-left: 20px;">
                                    <?php foreach ($weerCfg7_2 as $v) { ?>
                                        <input type="checkbox" name="weer7[]" lay-skin="primary" title="<?php echo $v ?><span style='color:<?php echo WeerColor(8); ?>;padding-right:0;'><?php echo $weermiss['weer7'][$v]<10 ? $weermiss['weer7'][$v].'&nbsp;&nbsp;' : $weermiss['weer7'][$v] ?></span>" value="<?php echo $v ?>" <?php echo isset($redweer['weer7']['code']) && $redweer['weer7']['code'] == $v ? 'checked' : '' ?>>
                                    <?php } ?>
                                </div>
                                <div class="layui-input-block" style="margin-left: 20px;">
                                    <?php foreach ($weerCfg7_3 as $v) { ?>
                                        <input type="checkbox" name="weer7[]" lay-skin="primary" title="<?php echo $v ?><span style='color:<?php echo WeerColor(8); ?>;padding-right:0;'><?php echo $weermiss['weer7'][$v]<10 ? $weermiss['weer7'][$v].'&nbsp;&nbsp;' : $weermiss['weer7'][$v] ?></span>" value="<?php echo $v ?>" <?php echo isset($redweer['weer7']['code']) && $redweer['weer7']['code'] == $v ? 'checked' : '' ?>>
                                    <?php } ?>
                                </div>
                                <div class="layui-input-block" style="margin-left: 20px;">
                                    <?php foreach ($weerCfg7_4 as $v) { ?>
                                        <input type="checkbox" name="weer7[]" lay-skin="primary" title="<?php echo $v ?><span style='color:<?php echo WeerColor(8); ?>;padding-right:0;'><?php echo $weermiss['weer7'][$v]<10 ? $weermiss['weer7'][$v].'&nbsp;&nbsp;' : $weermiss['weer7'][$v] ?></span>" value="<?php echo $v ?>" <?php echo isset($redweer['weer7']['code']) && $redweer['weer7']['code'] == $v ? 'checked' : '' ?>>
                                    <?php } ?>
                                    <input type="checkbox" name="weer7_all[]" lay-skin="primary" title="全选" value="1">
                                    <!-- <a class="layui-btn layui-btn-primary layui-btn-xs">全选</a> -->
                                </div>
                            </div>
                            </form>
                        </div>
                        <div class="layui-tab-item">
                            <form id="weer8Form"class="layui-form">
                            <fieldset class="layui-elem-field layui-field-title" style="margin-top: 10px; margin-bottom: 10px;">
                              <legend>位尾数和012路</legend>
                            </fieldset>
                            <?php
                                $weerCfg8 = GetWeerCfg(8);
                                $weerCfg8 = array_values($weerCfg8);
                                $weerCfg8 = $weerCfg8[0];
                                $weerCfg8_1 = array_slice($weerCfg8, 0, 7);
                                $weerCfg8_2 = array_slice($weerCfg8, 7, 7);
                                $weerCfg8_3 = array_slice($weerCfg8, 14, 7);
                                $weerCfg8_4 = array_slice($weerCfg8, 21, 7);
                            ?>
                            <div class="layui-form-item" style="margin-bottom: 5px;">
                                <div class="layui-input-block" style="margin-left: 20px;">
                                    <?php foreach ($weerCfg8_1 as $v) { ?>
                                        <input type="checkbox" name="weer8[]" lay-skin="primary" title="<?php echo $v ?><span style='color:<?php echo WeerColor(8); ?>;padding-right:0;'><?php echo $weermiss['weer8'][$v]<10 ? $weermiss['weer8'][$v].'&nbsp;&nbsp;' : $weermiss['weer8'][$v] ?></span>" value="<?php echo $v ?>" <?php echo isset($redweer['weer8']['code']) && $redweer['weer8']['code'] == $v ? 'checked' : '' ?>>
                                    <?php } ?>
                                </div>
                                <div class="layui-input-block" style="margin-left: 20px;">
                                    <?php foreach ($weerCfg8_2 as $v) { ?>
                                        <input type="checkbox" name="weer8[]" lay-skin="primary" title="<?php echo $v ?><span style='color:<?php echo WeerColor(8); ?>;padding-right:0;'><?php echo $weermiss['weer8'][$v]<10 ? $weermiss['weer8'][$v].'&nbsp;&nbsp;' : $weermiss['weer8'][$v] ?></span>" value="<?php echo $v ?>" <?php echo isset($redweer['weer8']['code']) && $redweer['weer8']['code'] == $v ? 'checked' : '' ?>>
                                    <?php } ?>
                                </div>
                                <div class="layui-input-block" style="margin-left: 20px;">
                                    <?php foreach ($weerCfg8_3 as $v) { ?>
                                        <input type="checkbox" name="weer8[]" lay-skin="primary" title="<?php echo $v ?><span style='color:<?php echo WeerColor(8); ?>;padding-right:0;'><?php echo $weermiss['weer8'][$v]<10 ? $weermiss['weer8'][$v].'&nbsp;&nbsp;' : $weermiss['weer8'][$v] ?></span>" value="<?php echo $v ?>" <?php echo isset($redweer['weer8']['code']) && $redweer['weer8']['code'] == $v ? 'checked' : '' ?>>
                                    <?php } ?>
                                </div>
                                <div class="layui-input-block" style="margin-left: 20px;">
                                    <?php foreach ($weerCfg8_4 as $v) { ?>
                                        <input type="checkbox" name="weer8[]" lay-skin="primary" title="<?php echo $v ?><span style='color:<?php echo WeerColor(8); ?>;padding-right:0;'><?php echo $weermiss['weer8'][$v]<10 ? $weermiss['weer8'][$v].'&nbsp;&nbsp;' : $weermiss['weer8'][$v] ?></span>" value="<?php echo $v ?>" <?php echo isset($redweer['weer8']['code']) && $redweer['weer8']['code'] == $v ? 'checked' : '' ?>>
                                    <?php } ?>
                                    <input type="checkbox" name="weer8_all[]" lay-skin="primary" title="全选" value="1">
                                </div>
                            </div>
                            </form>
                        </div>
                        <div class="layui-tab-item">
                            <form id="blueForm" class="layui-form">
                            <fieldset class="layui-elem-field layui-field-title" style="margin-top: 10px; margin-bottom: 10px;">
                              <legend>蓝球设置</legend>
                            </fieldset>
                            <?php
                                $allblue = array();
                                for ($i=1; $i < 17; $i++) { 
                                    $allblue[] = $i < 10 ? '0'.$i : $i;
                                }
                                $allblue_1 = array_slice($allblue, 0, 8);
                                $allblue_2 = array_slice($allblue, 8, 8);
                            ?>
                            <div class="layui-form-item" style="margin-bottom: 5px;">
                                <div class="layui-input-block" style="margin-left: 20px;">
                                    <?php foreach ($allblue_1 as $v) { ?>
                                        <input type="checkbox" name="blue[]" lay-skin="primary" title="<?php echo $v ?>" value="<?php echo $v ?>">
                                    <?php } ?>
                                </div>
                                <div class="layui-input-block" style="margin-left: 20px;">
                                    <?php foreach ($allblue_2 as $v) { ?>
                                        <input type="checkbox" name="blue[]" lay-skin="primary" title="<?php echo $v ?>" value="<?php echo $v ?>">
                                    <?php } ?>
                                </div>
                            </div>
                            <div style="margin-left: 20px; margin-top: 20px;">
                                <div class="layui-form-item" style="">
                                    <div class="layui-input-block" style="margin-left: 0;">
                                        <input type="radio" name="blue_comb" value="1" title="随机插入">
                                        <input type="radio" name="blue_comb" value="2" title="顺序插入">
                                    </div>
                                </div>
                            </div>
                            <div style="margin-left: 20px; margin-top: 20px;">
                                <a class="layui-btn layui-btn-primary layui-btn-sm">全</a>
                                <a class="layui-btn layui-btn-primary layui-btn-sm">清</a>
                                <a class="layui-btn layui-btn-primary layui-btn-sm">奇</a>
                                <a class="layui-btn layui-btn-primary layui-btn-sm">偶</a>
                                <a class="layui-btn layui-btn-primary layui-btn-sm">质</a>
                                <a class="layui-btn layui-btn-primary layui-btn-sm">合</a>
                                <a class="layui-btn layui-btn-primary layui-btn-sm">大</a>
                                <a class="layui-btn layui-btn-primary layui-btn-sm">小</a>
                            </div>
                            <div style="margin-left: 20px; margin-top: 20px;">
                                <a class="layui-btn layui-btn-lg layui-btn-primary" id="myBtn" href="amazeweer_my.php" target="_blank">我的方案</a>
                                <a class="layui-btn layui-btn-lg" id="WeerSaveBtn">保存方案</a>
                                <a class="layui-btn layui-btn-lg layui-btn-normal" data-id="" id="WeerCalcBtn" style="display: none;">微尔算法八步过滤</a>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
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

        
        $("#WeerSaveBtn").click(function(){
            layer.prompt(function(val, index){
                var scheme_name = val;

                // if(scheme_name == ''){
                //     alert(scheme_name)
                //     layer.msg('请输入方案名称！', {icon: 5});
                // }

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
                var jsonData = {'action': "weer_scheme", 'scheme_name': scheme_name};
                var params = $.extend(true, jsonData, formW1, formW2, formW3, formW4, formW5, formW6, formW7, formW8, formW9, blue);

                $.ajax({
                    url: 'ajax/red_weer_do.php',
                    dataType: 'json',
                    type: 'post',
                    data: params,
                    success: function(data) {
                        if(data.errcode == 1){
                            $("#WeerCalcBtn").data('id', data.id);
                            $("#WeerCalcBtn").show();
                            layer.msg(data.errmsg);
                        }

                        layer.close(index);
                    }
                })
            });
        })



        $("#WeerCalcBtn").click(function(){
            var obj = $(this).data(id);
            var id = obj.id;

            $("#WeerCalcBtn").html("计算中...");
            $.ajax({
                url: 'ajax/red_weer_do.php',
                dataType: 'html',
                type: 'post',
                async:false, 
                data: {action: 'weer_save', id:id},
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

            /*var formW1 = $("#weer1Form").serializeObject();
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

            layer.msg('微尔算法过滤中', {
                icon: 16,shade: 0.01
            });
            $.ajax({
                url: 'ajax/red_weer_do.php',
                dataType: 'html',
                type: 'post',
                async:false, 
                data: params,
                success: function(data) {
                    layer.closeAll('loading');
                    // console.log(data);

                    layer.open({
                        type: 1,
                        skin: 'layui-layer-rim', //加上边框
                        area: ['600px', '360px'], //宽高
                        content: data
                    });
                }
            })*/
        })

    });
    </script>
</body>

</html>