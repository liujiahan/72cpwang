<?php
require_once dirname(__FILE__).'/../include/config.inc.php';
require_once dirname(__FILE__).'/../wxpage/core/choosered.func.php';

$baihe = nextBaiHe();

$limit_num = 10;
$sql = "SELECT * FROM `#@__caipiao_baihe` ORDER BY cp_dayid DESC LIMIT {$limit_num}";
$dosql->Execute($sql);

$data = array();
while($row = $dosql->GetArray()){
    $data[$row['cp_dayid']]['ssqid'] = $row['cp_dayid'] . "期";
    $data[$row['cp_dayid']]['red6']  = explode(',', $row['red_num']);
    $data[$row['cp_dayid']]['reds']  = explode(',', $row['sum_reds']);
    $data[$row['cp_dayid']]['wins']  = $row['sum_reds_win'];
}

// $data = array_reverse($data);

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>和值除数定胆算法红球</title>
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
        <blockquote class="layui-elem-quote">看彩72变&nbsp;&nbsp;》&nbsp;和值除数定胆算法</blockquote>
        <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
            <legend><?php echo $baihe['cp_dayid'] ?>期算法红球</legend>
        </fieldset>
        
        <div class="layui-row">
            <div class="layui-col-xs12" style="margin-bottom: 10px;">
                <div class="grid-demo layui-bg-black">
                <?php
                    foreach ($baihe['data_sum_reds'] as $red) {
                        echo '<span class="red_ball">'.$red.'</span>';   
                    }
                ?>
                </div>
            </div>
        </div>

        <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
            <legend>往期历史数据</legend>
        </fieldset>

        <div class="layui-row">
            <?php foreach ($data as $v) { ?>
            <div class="layui-col-xs12">
                <div class="grid-demo layui-bg-green" style="font-size: 20px;">
                <?php echo $v['ssqid'] . "：命中" . $v['wins'] . "红"; ?>
                </div>
            </div>
            <div class="layui-col-xs12" style="margin-bottom: 10px;">
                <div class="grid-demo layui-bg-black">
                    <?php
                        foreach ($v['reds'] as $red) {
                            $active = in_array($red, $v['red6']) ? 'active' : '';
                            echo '<span class="red_ball '.$active.'">'.$red.'</span>';   
                        }
                    ?>
                </div>
            </div>
            <?php } ?>
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