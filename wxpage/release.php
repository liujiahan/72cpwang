<?php

// ob_start();

require_once dirname(__FILE__).'/../include/config.inc.php';
require_once dirname(__FILE__).'/core/suanfa.func.php';
require_once dirname(__FILE__).'/core/ssq.config.php';

LoginCheck();

$row = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid DESC");
$red_num = explode(',', $row['red_num']);
$qishu = $row['cp_dayid'] % date('Y');
$qishu < 10 && $qishu = '00' . $qishu;
$qishu >= 10 && $qishu < 100 && $qishu = '0' . $qishu;
$next_qishu = $row['cp_dayid'] + 1;

$curmiss = getCurMiss($row['cp_dayid']);
$coolhot = $curmiss['cool_hot'];

$lianhao_num = 0;
$lianhao = getLianHao($red_num);
foreach ($lianhao as $key => $v) {
    if($v == 1){
        $lianhao_num = $key;
        break;
    }
}

$prime = array(1, 2, 3, 5, 7, 11, 13, 17, 19, 23, 29, 31);
$bigsmall = array(0=>0, 1=>0);
$oddeven  = array(0=>0, 1=>0);
$redarea  = array(0=>0, 1=>0, 2=>0);
$primenum = array(0=>0, 1=>0);

$before_id = $row['cp_dayid'] - 1;
$one = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid=".$before_id);
$before_reds = explode(',', $one['red_num']);
$repeat_num = 0;

$weisum = 0;
foreach ($red_num as $red) {
    $red > 16 && $bigsmall[0]++;
    $red < 17 && $bigsmall[1]++;

    $red % 2 == 1 && $oddeven[0]++;
    $red % 2 == 0 && $oddeven[1]++;

    $red < 12 && $redarea[0]++;
    $red > 11 && $red < 23 && $redarea[1]++;
    $red > 22 && $redarea[2]++;

    $weisum += $red % 10;

    in_array($red, $prime) && $primenum[0]++;
    !in_array($red, $prime) && $red != 1 && $primenum[1]++;

    in_array($red, $before_reds) && $repeat_num++;
}

?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"> -->
    <title>双色球<?php echo "$next_qishu" ?>期预测 - <?php echo $cfg_seotitle; ?></title>
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
    <script type="text/javascript" src="bootstrap/js/jquery.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script> 
</head>

<body>
    <div class="container-fluid">
        <div class="rich_media_content " id="js_content">
            <h1>双色球<?php echo "$next_qishu" ?>期预测</h1>
            <p><span style="font-size: 137.5%; color: rgb(0, 0, 0);">双色球<?php echo $qishu ?>期开奖</span></p>
            <p><span style="font-size: 137.5%; color: rgb(252, 0, 0);"><?php echo implode(" ", $red_num); ?></span><span style="font-size: 137.5%;"><span style="color: rgb(195, 0, 0);"></span> + <span style="color: rgb(14, 95, 243);"><?php echo $row['blue_num'] < 10 ? '0' . $row['blue_num'] : $row['blue_num']; ?></span></span>
            </p>
            <p><span style="color: rgba(0, 0, 0, 0.95);">且看双色球各项指标数据，如下：</span></p>
            <p><span style="color: rgba(0, 0, 0, 0.95);"><br  /></span></p>
            <p><span style="color: rgba(0, 0, 0, 0.95);">大小比：<?php echo implode(":", $bigsmall) ?>，奇偶比：<?php echo implode(":", $oddeven) ?>，质合比：<?php echo implode(":", $primenum) ?></span></p>
            <p><span style="color: rgba(0, 0, 0, 0.95);">三区比：<?php echo implode(":", $redarea) ?>，热温冷比：<?php echo implode(":", $coolhot) ?></span></p>
            <p><span style="color: rgba(0, 0, 0, 0.95);"><br  /></span></p>
            <p><span style="color: rgba(0, 0, 0, 0.95);">和数值：<?php echo array_sum($red_num) ?>，</span>尾数和：<?php echo $weisum ?>，遗漏和值：<?php echo $curmiss['miss_sum'] ?></p>
            <p><span style="color: rgb(0, 0, 0);">连号出球：<?php echo $lianhao_num; ?>，重</span>号：<?php echo $repeat_num ?>个，AC值：<?php echo getAC($red_num); ?></p>
            <p><span style="color: rgb(0, 0, 0);"><br  /></span></p>
            <p>下期预测：</p>
            <p style="text-indent: 2em;"><?php echo $_COOKIE['comments'] ?></p>
            <p style="white-space: normal;">
                <br />
            </p>
            <p>各指标预测：</p>
            <p style="white-space: normal;"><span style="color: rgba(0, 0, 0, 0.95);">大小比：<?php echo $_COOKIE['bigsmall'] ?>，奇偶比：<?php echo $_COOKIE['oddeven'] ?>，质合比：<?php echo $_COOKIE['primenum'] ?></span></p>
            <p style="white-space: normal;"><span style="color: rgba(0, 0, 0, 0.95);">三区比：<?php echo $_COOKIE['redarea'] ?>，热温冷比：<?php echo $_COOKIE['coolhot'] ?></span></p>
            <p style="white-space: normal;"><span style="color: rgba(0, 0, 0, 0.95);"><br  /></span></p>
            <p style="white-space: normal;"><span style="color: rgba(0, 0, 0, 0.95);">和数值：<?php echo $_COOKIE['sum'] ?>，</span>尾数和：<?php echo $_COOKIE['tailsum'] ?>，遗漏和值：<?php echo $_COOKIE['misssum'] ?></p>
            <p style="white-space: normal;">连号出球：<?php echo $_COOKIE['lianhao_num'] ?>，重号：<?php echo $_COOKIE['repeat_num'] ?>个，AC值：<?php echo $_COOKIE['ac'] ?></p>
            <br/>
            <p>红球大底：<span style="color: rgb(255, 41, 65); font-size: 20px;"><?php echo $_COOKIE['chooseReds'] ?></span><span style="color: rgb(217, 33, 66);"></span></p>
            <br/>
            <p>精选红球：<span style="color: rgb(255, 41, 65); font-size: 20px;"><?php echo $_COOKIE['filterReds'] ?></span></p>
            <br/>
            <p><span style="color: rgb(0, 0, 0);">蓝球推荐：<span style="color: rgb(0, 82, 255); font-size: 20px;"><?php echo $_COOKIE['chooseBlue'] ?></span></span>
            </p>
            <br/>
            <p><span style="color: rgb(0, 0, 0);">有效排列组合：</span></p>
            <br/>
            <p><?php echo $_COOKIE['zuhe'] ?></p>
        </div>
    </div>
</body>

</html>
<?php
  // $content = ob_get_contents();//取得php页面输出的全部内容
  // $fp = fopen("html/".$next_qishu.".html", "w");
  // fwrite($fp, $content);
  // fclose($fp);
?>
