<?php
require_once dirname(__FILE__).'/../include/config.inc.php';

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>赞助 - <?php echo $cfg_seotitle; ?></title>
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
    <script type="text/javascript" src="bootstrap/js/jquery.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
</head>

<body>
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <?php include('navbar.php') ?>
            <h1><small>赞助本站</small></h1>
            <p class="lead">『<?php echo $cfg_seotitle ?>』是作者历时近1年，呕心沥血打造的关于“福彩双色球”大数据走势规律的分析网站。</p>
            <div class="clearfix"></div>
            <div class="panel panel-info">
                <div class="panel-heading">赞助本站</div>
                <div class="panel-body">
                    <ul class="list-group">
                        <li class="list-group-item">
                            <p>如果您觉得本网站分析的大数据规律、数据，对您有所帮助，还请不吝赞助！</p>
                            <p>有了您的支持和关爱，『<?php echo $cfg_seotitle ?>』将更用心分析双色球的数据和走势规律，祝各位有缘人早日拿下500万大奖！</p>
                            <p>&nbsp;</p>
                            <div style="text-align: center;">
                                <!-- <p><h4>赞助方式一</h4></p> -->
                                <h4>微信扫一扫赞助</h4>
                                <p>
                                    <a href="images/wxpay_wk.jpg" target="_blank">
                                    <img src="images/wxpay_wk.jpg" style="width: 300px; height: auto; min-width: 240px;">
                                    </a>
                                </p>
                            </div>
                            <!-- <div style="text-align: center;">
                                <p><h4>赞助方式二</h4></p>
                                <p>手机支付宝扫一扫赞助</p>
                                <p>
                                    <a href="images/alipay.png" target="_blank">
                                    <img src="images/alipay.png" style="width: 300px; height: auto; min-width: 240px;">
                                    </a>
                                </p>
                            </div>
                            <p>&nbsp;</p> -->
                            <p style="color: #e5554e;">您的赞助将会用于维持网站运行需要的主机，也是对本站和我的支持，非常感谢！</p>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </nav>
</body>

<!-- <body>
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <?php //include('navbar.php') ?>
            <center>
                <p style="text-align: center;">
                    <img src="http://h.cdn.zhuolaoshi.cn/user/site19765/image/logo/aixin.jpg" style="width: 116px; height: 90px;">
                </p>
                <p><span style="font-size: 11pt;">如果喜欢『解语花中文网』，就赞助一下吧！</span></p>
                <p><span style="font-size: 11pt;">有了您的支持和关爱，『解语花中文网』将更加用心做好网站，让您尽情享受文字的魅力与乐趣！</span></p>
                <p><span style="font-size: 11pt;">1元不嫌少，10元不嫌多哦！</span>
                </p>
                <br>
                <p><span style="font-size: 10pt;">扫一扫二维码微信支付赞助</span></p>
                <p style="text-align: center;"><img src="http://h.cdn.zhuolaoshi.cn/user/site19765/webcode/zanzhu/wxpay.png" style="border-color: rgb(255, 204, 0); width: 256px; height: 257px;" border="2">
                    <br>
                </p>
            </center>
        </div>
        <!-- /.container-fluid -->
    <!-- </nav>
</body> -->

</html>