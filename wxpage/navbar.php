<?php
$uri = $_SERVER['REQUEST_URI'];
?>
<!-- Brand and toggle get grouped for better mobile display -->
<!-- <div class="navbar-header"><a class="navbar-brand" href="index.php" >
            <img src="/static/images/logo.png" style="width: 40px; float: left; margin-top: -14px;">
            <?php echo $cfg_seotitle; ?></a>
        </div> -->
<div class="navbar-header">
    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
    </button>
    <a class="navbar-brand" href="index.php">
        <img src="/static/images/logo.png" style="width: 40px; float: left; margin-top: -14px;">
        <?php echo $cfg_seotitle; ?></a>
    <!-- <a class="navbar-brand" href="index.php"><?php echo $cfg_seotitle; ?></a> -->
</div>
<!-- Collect the nav links, forms, and other content for toggling -->
<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
    <ul class="nav navbar-nav">
        <li <?php echo false !== strpos($uri, 'index') ? 'class="active"' : ''; ?>><a href="index.php">开奖 <span class="sr-only">(current)</span></a></li>

        <!-- <li <?php echo false !== strpos($uri, 'suanfa_chart') ? 'class="active"' : ''; ?>><a href="suanfa_chart.php">算法命中走势图</a></li> -->
        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">红球筛选法 <span class="caret"></span></a>
            <ul class="dropdown-menu">
                <li><a href="red_coolhot.php">遗漏冷热选号法</a></li>
                <li><a href="kjhsjh.php">开机号试机号</a></li>
                <!-- <li><a href="red_3area.php">红球三区走势分析</a></li> -->
                <li><a href="https://win.72cp.wang/ssq-echats/three_zone.html" target="_blank">红球三区走势分析</a></li>
                <!-- <li><a href="red_pinlv_fenqu.php">频率分区选号法</a></li> -->
                <!-- <li><a href="red_pinlv_trend.php">频率趋势选号法</a></li> -->
                <li><a href="red_kchart.php">33红球K线图</a></li>
                <li><a href="red_space_periods.php">间隔期数选号法</a></li>
                <li><a href="list/red11.php">红球三杀</a></li>
                <!-- <li><a href="red_location_cross.php">区间命中走势图</a></li> -->
                <?php if (isset($_COOKIE['isAdmin']) && $_COOKIE['isAdmin'] == $adminkey) { ?>
                    <li role="separator" class="divider"></li>
                    <li><a href="../caipiao/chart_contrary_thinking12.php" target="_blank">逆向12-走势图</a></li>
                    <li><a href="../caipiao/chart_contrary_thinking11.php" target="_blank">逆向11-走势图</a></li>
                    <li><a href="../caipiao/chart_wuxing.php" target="_blank">逆向五行-走势图</a></li>
                    <li><a href="red_wuxing_rel.php">相生相克</a></li>
                    <li role="separator" class="divider"></li>
                    <!-- <li><a href="../caipiao/chart_common.php" target="_blank">正常-走势图</a></li> -->
                <?php } ?>
                <li role="separator" class="divider"></li>
                <li><a href="https://win.72cp.wang/ssq-echats/index.html" target="_blank">6红趋势图</a></li>
                <li><a href="https://win.72cp.wang/ssq-echats/kuadu.html" target="_blank">跨度</a></li>
                <li><a href="https://win.72cp.wang/ssq-echats/coolball_wins.html" target="_blank">冷号必出趋势图</a></li>

                <li role="separator" class="divider"></li>

                <li><a href="red_tail.php">红球尾数命中走势</a></li>
                <li><a href="red_tail_coolhot.php">红球尾数热温冷</a></li>
                <li><a href="red_edgecode.php">红球边码命中走势</a></li>
                <li><a href="red_near_history.php">红球近期指标数据</a></li>
                <li><a href="red_history.php">红球同期指标数据</a></li>
            </ul>
        </li>
        <?php if (isset($_COOKIE['isAdmin']) && $_COOKIE['isAdmin'] == $adminkey) { ?>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">快乐8 <span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li><a href="happy8_index.php">快乐8</a></li>
                    <li><a href="happy8_hot.php">快乐8-热选</a></li>
                    <li><a href="../caipiao/chart_happy8.php" target="_blank">快乐8-走势</a></li>
                    <li><a href="../caipiao/chart_tail_happy8.php" target="_blank">快乐8-尾数</a></li>
                    <li><a href="../caipiao/chart_wuxing_happy8.php" target="_blank">快乐8-逆向五行</a></li>
                </ul>
            </li>
        <?php } ?>
        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">蓝球筛选法 <span class="caret"></span></a>
            <ul class="dropdown-menu">
                <li><a href="blue_perfect_num.php">5期定蓝与心水集团码</a></li>
                <li><a href="blue_choose.php">蓝球正主选号</a></li>
                <?php if (isset($_COOKIE['isAdmin']) && $_COOKIE['isAdmin'] == $adminkey) { ?>
                    <li><a href="https://win.72cp.wang/ssq-echats/blue.html" target="_blank">蓝号趋势图</a></li>
                    <li><a href="blue_3dfx.php">蓝号3D数据分析</a></li>
                    <li role="separator" class="divider"></li>
                    <li><a href="blue_wuxing.php">蓝球五行选号</a></li>
                    <li><a href="blue_wuxing_killblue.php">蓝球五行杀法</a></li>
                <?php } else { ?>
                    <li><a href="blue_wuxing.php">蓝球五行选号</a></li>
                <?php } ?>

                <li role="separator" class="divider"></li>
                <li><a href="red_gold.php">黄金点位测红蓝球</a></li>
                <li><a href="kill_blue.php">八杀法杀蓝法</a></li>
                <!-- <li><a href="new_kill_blue.php">杀蓝新法</a></li> -->
                <li><a href="new_kill_blue2.php">11招绝密杀蓝法</a></li>
                <!-- <li role="separator" class="divider"></li> -->
                <!-- <li><a href="sfhistory_blue.php">蓝球历史同期</a></li> -->
                <!-- <li><a href="blue_road.php">蓝球尾路走势图</a></li> -->
                <!-- <li><a href="red_road.php">红球3、6路走势</a></li> -->
            </ul>
        </li>
        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">大乐透 <span class="caret"></span></a>
            <ul class="dropdown-menu">
                <li><a href="../daletou/blue.php">蓝号</a></li>
                <li role="separator" class="divider"></li>
                <li><a href="#">...</a></li>
            </ul>
        </li>
        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">偏差平衡选号法 <span class="caret"></span></a>
            <ul class="dropdown-menu">
                <li><a href="offset_sumvalue.php">和数值偏差</a></li>
                <li><a href="offset_hotku.php">热门冷门数字偏差</a></li>
                <li><a href="offset_missnum.php">遗漏数字偏差</a></li>
                <li><a href="offset_weishuo.php">末位数字偏差</a></li>
                <li><a href="offset_oddeven.php">奇偶-大小-区间偏差</a></li>
                <li><a href="offset_rowcolumn.php">行列图偏差</a></li>
                <li role="separator" class="divider"></li>
                <li><a href="red_double_win.php">红球双倍中奖比率</a></li>
                <li><a href="red_miss_win.php">遗漏与中奖分析表</a></li>
                <li><a href="red_partner_num.php">伴侣数字</a></li>
                <li><a href="red_next_num.php">追踪数字</a></li>
                <li role="separator" class="divider"></li>
            </ul>
        </li>
        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">百合算法 <span class="caret"></span></a>
            <ul class="dropdown-menu">
                <li><a href="red_data_compare.php">1、百合算法</a></li>
                <li><a href="red_sum_divisor.php">2、和值除数定胆法</a></li>
                <li><a href="offset_red_percent.php">3、百分比预测法</a></li>
                <li role="separator" class="divider"></li>
                <li><a href="red_wuxing_tail_ext.php">4、五行尾数规律</a></li>
                <li><a href="red_9code.php">5、012路9码数据表</a></li>

                <?php if (isset($_COOKIE['isAdmin']) && $_COOKIE['isAdmin'] == $adminkey) { ?>
                    <li><a href="red_9code_query.php">5、查询9码</a></li>
                <?php } ?>
                <li><a href="red_3code.php">6、三码遗漏表</a></li>

                <?php if (isset($_COOKIE['isAdmin']) && $_COOKIE['isAdmin'] == $adminkey) { ?>
                    <li><a href="red_3code55.php">7、三码55期统计</a></li>
                    <li><a href="https://win.72cp.wang/ssq-echats/code3_trend.html" target="_blank">8、三码走势</a></li>
                <?php } ?>

                <?php if (isset($_COOKIE['isAdmin']) && $_COOKIE['isAdmin'] == $adminkey) { ?>
                    <li role="separator" class="divider"></li>
                    <li><a href="red_wuxing_tail.php">5、五行尾数规律旧版</a></li>
                    <li><a href="red_4jtail.php">6、间隔4期尾数规律</a></li>
                    <li><a href="red_wintail.php">7、尾数遗漏规律</a></li>
                    <li><a href="red_tail_sum.php">8、尾数和值遗漏规律</a></li>
                <?php } ?>
            </ul>
        </li>
    </ul>
    <ul class="nav navbar-nav navbar-right">
        <li <?php echo false !== strpos($uri, 'wxpay') ? 'class="active"' : ''; ?>><a href="wxpay.php">赞助本站 <span class="sr-only">(current)</span></a></li>
        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">智能分析频道 <span class="caret"></span></a>
            <ul class="dropdown-menu">
                <li><a href="winning.php">大底指标过滤系统</a></li>
                <li><a href="red_analysis.php">大奖成色检验系统</a></li>
                <li><a href="red_row_column.php">行列图锁定智能分析</a></li>
                <li><a href="choose_step.php">红球蓝球选号思路</a></li>
                <li role="separator" class="divider"></li>
                <li><a href="prize_list.php">500万走势图</a></li>
                <?php if (isset($_COOKIE['isAdmin']) && $_COOKIE['isAdmin'] == $adminkey) { ?>
                    <li><a href="amazebaihe_my.php">百合盛开</a></li>
                    <li><a href="amazetail_my.php">魔幻尾数</a></li>
                    <li><a href="amazeweer_my.php">微尔算法</a></li>
                <?php } ?>
            </ul>
        </li>
        <li <?php echo false !== strpos($uri, 'joinus') ? 'class="active"' : ''; ?>><a href="joinus.php">成长之路 <span class="sr-only">(current)</span></a></li>

        <?php if (isset($_COOKIE['isAdmin']) && $_COOKIE['isAdmin'] == $adminkey) { ?>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">我的 <span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <!-- <li role="separator" class="divider"></li> -->
                    <li><a href="payget.php">投资与回报</a></li>
                    <li><a href="myorder.php">我的彩票单</a></li>
                    <li><a href="myorder2.php">合买彩票单</a></li>
                    <li role="separator" class="divider"></li>
                    <li><a href="buyssq.php">我要下单</a></li>
                    <li><a href="success.php">升级之路</a></li>
                    <li><a href="ssqupdate.php">更新数据</a></li>
                    <li role="separator" class="divider"></li>
                    <li><a href="logout.php">退出</a></li>
                </ul>
            </li>
        <?php } else if (isset($_COOKIE['password'])) { ?>
            <li><a href="logout.php">退出</a></li>
        <?php } ?>
    </ul>
</div>
<!-- /.navbar-collapse -->