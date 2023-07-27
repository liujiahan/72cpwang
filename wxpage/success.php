<?php

require_once dirname(__FILE__).'/../include/config.inc.php';
require_once dirname(__FILE__).'/core/ssq.config.php';
LoginCheck();

?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"> -->
    <title><?php echo $cfg_seotitle; ?></title>
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
    <script type="text/javascript" src="bootstrap/js/jquery.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
</head>

<body>
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <?php include('navbar.php') ?>
            <h1><small><?php echo $cfg_seotitle ?>网站升级更新日志</small></h1>
            <!-- <p class="lead"></p> -->
            <blockquote>
                <p>如何反其道而行之，如何逆向思维，如何选号不被大众化，如何脱颖而出，如何顺势而为又逆流而退！提升自己，加油！！！</p>
            </blockquote>
            <div class="clearfix"></div>

            <div class="panel panel-info">
                <div class="panel-heading">计划升级功能</div>
                <div class="panel-body">
                    <ul class="list-group">
                        <li class="list-group-item">
                            <p>1、开发红球边码命中走势图</p>
                            <p>2、关于红球如何定胆尾，杀尾的数据分析（结合末位数字偏差系统）</p>
                            <p>3、开发红球、蓝球预测生成系统，涉及红球胆码、蓝球胆码，还有杀红、杀蓝的预测功能</p>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="panel panel-danger">
                <div class="panel-heading">更新日志</div>
                <div class="panel-body">
                    <ul class="list-group">
                        <li class="list-group-item">
                            <p>1、2017年3月份重新接触双色球，因关注某微信公众号关于双色球的分析特别有价值，于是萌生创建分析双色球大数据分析网站的想法。</p>
                            <p>2、2017年3月份-4月份<?php echo $cfg_seotitle ?>网站架构基本成型。</p>
                            <p>3、2017年5月份拜读了美国作家著作的《聪明组合》等书籍，把书籍中相关规律、算法变成双色球大数据的规律。</p>
                            <p>4、2017年6月-10月，<?php echo $cfg_seotitle ?>中各式各样的算法和规律不断的升级迭代，目的是让分析双色球变得简单而有价值。</p>
                            <!-- <p>5、</p> -->
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </nav>
</body>

</html>
