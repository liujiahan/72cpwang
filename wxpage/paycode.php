<?php
require_once dirname(__FILE__).'/../include/config.inc.php';
require_once dirname(__FILE__).'/core/ssq.config.php';

$maxid = maxDayid();
$maxid = $maxid + 1;

$yuan2tip = 0;
$tmprowss = $dosql->GetOne("SELECT COUNT(*) AS total FROM `#@__caipiao_weermy_code` WHERE cp_dayid='$maxid' AND codetype='1' AND status IN (0)");
$yuan2tip = $tmprowss['total'];

$yuan5tip = 0;
$tmprowss = $dosql->GetOne("SELECT COUNT(*) AS total FROM `#@__caipiao_weermy_code` WHERE cp_dayid='$maxid' AND codetype='2' AND status IN (0)");
$yuan5tip = $tmprowss['total'];

$yuan10tip = 0;
$tmprowss = $dosql->GetOne("SELECT COUNT(*) AS total FROM `#@__caipiao_weermy_code` WHERE cp_dayid='$maxid' AND codetype='3' AND status IN (0)");
$yuan10tip = $tmprowss['total'];

$curweek = date("w");
$curhour = date("H");

if(in_array($curweek, array(2, 4, 0)) && $curhour >= 20){
	$yuan2tip  = 0;
	$yuan5tip  = 0;
	$yuan10tip = 0;
}

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>双色球领取码——帮助中心</title>
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
        <blockquote class="layui-elem-quote">看彩72变&nbsp;&nbsp;》&nbsp;领取码</blockquote>
        <p style="margin-bottom: 10px;">提示：先把二维码截图，在通过微信“扫一扫”识别进行赞助。微信号：『lsychinadream』长按复试！</p>
        <div class="layui-row" style="float: right; clear: both;">
            <a href="#helper" class="layui-btn" id="ZanZhu">加我微信</a>
        </div>
        <div class="layui-collapse">
            <div class="layui-colla-item">
                <h2 class="layui-colla-title">5元赞助区&nbsp;<?php if(!empty($yuan5tip)){ ?><span class="layui-badge">剩余<?php echo $yuan5tip ?>个名额</span><?php } ?></h2>
                <div class="layui-colla-content layui-show">
                    <p>微信扫描二维码，赞助5元，享受5注数据的权益。加我为微信好友，发送赞助截图（带转账单号），索取数据。</p>
                    <p style="text-align: center;margin: 10px 0;">
                        <a href="images/wxpay5.jpg?v=2018001" target="_blank">
                        <img src="images/wxpay5.jpg?v=2018001" style="width: 60%; height: auto; min-width: 240px;">
                        </a>
                    </p>
                </div>
            </div>
            <div class="layui-colla-item">
                <h2 class="layui-colla-title">2元赞助区&nbsp;<?php if(!empty($yuan2tip)){ ?><span class="layui-badge">剩余<?php echo $yuan2tip ?>个名额</span><?php } ?></h2>
                <div class="layui-colla-content">
                    <p>微信扫描二维码，赞助2元，享受2注数据的权益。加我为微信好友，发送赞助截图（带转账单号），索取数据。</p>
                    <p style="text-align: center;margin: 10px 0;">
                        <a href="images/wxpay2.jpg?v=2018001" target="_blank">
                        <img src="images/wxpay2.jpg?v=2018001" style="width: 60%; height: auto; min-width: 240px;">
                        </a>
                    </p>
                </div>
            </div>
            <div class="layui-colla-item">
                <h2 class="layui-colla-title">10元赞助区</h2>
                <div class="layui-colla-content">
                    <p>微信扫描二维码，赞助10元，享受10注数据的权益。加我为微信好友，发送赞助截图（带转账单号），索取数据。</p>
                    <p style="text-align: center;margin: 10px 0;">
                        <a href="images/wxpay10.jpg?v=2018001" target="_blank">
                        <img src="images/wxpay10.jpg?v=2018001" style="width: 60%; height: auto; min-width: 240px;">
                        </a>
                    </p>
                </div>
            </div>
            <div class="layui-colla-item" id="helper">
                <h2 class="layui-colla-title">帮助</h2>
                <div class="layui-colla-content layui-show">
                    <p>1、有问题请加微信号：『lsychinadream』或扫描下方二维码进行咨询！</p>
                    <p style="text-align: center;margin: 10px 0;">
                        <a href="images/myweixin.jpg?v=2018001" target="_blank">
                        <img src="images/myweixin.jpg?v=2018001" style="width: 60%; height: auto; min-width: 240px;">
                        </a>
                    </p>
                    <p>2、为了保障各位的权益，赞助后请给微信号：『领取码（72变）』发送赞助明细的截图，以免耽误您领取数据！如下图：</p>
                    <p style="text-align: center;margin: 10px 0;">
                        <img src="images/wxorderdemo.png?v=2018001" style="width: 60%; height: auto; min-width: 240px;">
                    </p>
                </div>
            </div>
        </div>
        <p style="margin-bottom: 20px;">&nbsp;</p>
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