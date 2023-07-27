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
            <h1><small>双色球选号思路总结中</small></h1>
            <!-- <p class="lead"></p> -->
            <blockquote>
                <p>如何反其道而行之，如何逆向思维，如何选号不被大众化，如何脱颖而出，如何顺势而为又逆流而退！提升自己，加油！！！</p>
            </blockquote>
            <div class="clearfix"></div>
            <div class="panel panel-danger">
                <div class="panel-heading">选红步骤</div>
                <div class="panel-body">
                    <ul class="list-group">
                        <li class="list-group-item">
                            <p>一、先分析<a href="red_coolhot.php" target="_blank">遗漏冷热选号法</a>，分析出遗漏冷热的走势，把选号的范围缩小。【追热不追冷，冷势不挽留，看准反转点，果断舍弃不迷恋。】</p>
                        </li>
                        <li class="list-group-item">
                            <p>二、再分析<a href="offset_hotku.php" target="_blank">冷热数字偏差追踪系统</a>，判断遗漏走势。【注意反向回补】</p>
                            <p>通过<a href="offset_missnum.php" target="_blank">遗漏数字偏差追踪系统</a>，结合遗漏和值、均值走势，以及遗漏热温冷出球比例，首选筛选出遗漏在0-5之间的红球。【记录结果备用】</p>
                        </li>
                        <li class="list-group-item">
                            <p>三、分析33个红球的<a href="red_pinlv_trend.php" target="_blank">出球走势图</a>和<a href="red_miss_win.php" target="_blank">遗漏与中奖分析表</a>，根据33个红球的走势规律和遗漏中奖规律，选出必出的球，可能出球的球，一定不出的球，用于结合其他算法，进行选号舍号杀号！【记录结果备用】</p>
                        </li>
                        <li class="list-group-item">
                            <p>四、根据<a href="red_near_history.php" target="_blank">红球近期走势</a>，
                            分析下期红球的各指标走向。红球指标包括（大小比，奇偶比，质合比，和数值，区间比，AC值，重号，尾数和等等指标），
                            这时可以参考<a href="offset_oddeven.php" target="_blank">大小-区间-偏差系统</a>、<a href="offset_sumvalue.php" target="_blank">和数值偏差系统</a>、
                            </p>
                        </li>
                        <li class="list-group-item">
                            <p>五、根据第四步分析的各指标，对以上的备用红球，进行初次筛选。想要做到精选红球，还要参考<a href="offset_weishuo.php" target="_blank">红球尾数偏差追踪系统</a>，针对热尾球和冷尾球，做出取舍。
                            </p>
                        </li>
                        <li class="list-group-item">
                            <p>六、到第六步，此时肯定选出了一些号码。这时候还需要看几下几个走势图，作为参考和对照自己的选号，是否符合其走势，从而判断自己的选号是否合理。</p>
                            <p>1、<a href="sfone.php" target="_blank">加减乘除算法</a></p>
                            <p>2、<a href="red_gold.php" target="_blank">红球黄金点位推测</a></p>
                            <p>3、<a href="offset_red_percent.php" target="_blank">百分比预测法</a></p>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="panel panel-info">
                <div class="panel-heading">选蓝步骤</div>
                <div class="panel-body">
                    <ul class="list-group">
                        <li class="list-group-item">
                            <p>一、观察分析蓝球的大小、奇偶，0123路等等<a href="blue_road.php" target="_blank">走势图</a>，对蓝球做初步预判</p>
                        </li>
                        <li class="list-group-item">
                            <p>二、参考以下几种算法命中蓝球走势图，从细微处见真知。</p>
                            <p><a href="blue_choose.php" target="_blank">正主选号</a>、<a href="red_gold.php" target="_blank">黄金点位测蓝球</a></p>
                            <p>然后在结合<a href="kill_blue.php" target="_blank">8杀法杀蓝命中走势图</a>，通过这三种方法，基本上80%能锁定蓝球。</p>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="panel panel-warning">
                <div class="panel-heading">选号测评</div>
                <div class="panel-body">
                    <ul class="list-group">
                        <li class="list-group-item">
                            <p>一、结合选红和选蓝结果组号，进行<a href="red_analysis.php" target="_blank"></a>选号测评，对照历史数据，看看选号是否符合大奖气质，从而做到胸有成竹。</p>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </nav>
</body>

</html>
