<?php

require_once dirname(__FILE__) . '/../../include/config.inc.php';
require_once dirname(__FILE__) . '/../core/suanfa.func.php';
require_once dirname(__FILE__) . '/../core/core.func.php';
require_once dirname(__FILE__) . '/../core/choosered.func.php';

LoginCheck();
if(!(isset($_COOKIE['isAdmin']) && $_COOKIE['isAdmin'] == $adminkey)){
    ShowMsg("Permission denied","-1");
    exit;
}

set_time_limit(0);
// ini_set('memory_limit', '1024M');

if(!empty($cp_dayid)){
    $row = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<'$cp_dayid' ORDER BY cp_dayid DESC");
}else{
    $row = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid DESC");
}
$curReds = explode(",", $row['red_num']);
$next_dayid = $row['cp_dayid'] + 1;

$prime = array(1, 2, 3, 5, 7, 11, 13, 17, 19, 23, 29, 31);
  
if(!empty($cp_dayid)){
    $curmiss = redMissing($cp_dayid);
}else{
    $curmiss = redMissing();
}

$chooseReds = rtrim($chooseReds, ',');
$chooseReds = explode(',', $chooseReds);

$chooseBlue = rtrim($lock_blue_ball, ',');

$killReds = !empty($killReds) ? explode(',', $killReds) : array();
$danmaReds = !empty($danmaReds) ? explode(" ", $danmaReds) : array();

//获取组合情况
$myReds = combination($chooseReds, 6);

$total = 0;
$result = array();
$selReds = array();
$sqlData = array();
foreach ($myReds as $myreds) {
    $bignum     = 0; //大号出球
    $oddnum     = 0; //奇数球
    $primenum   = 0; //质数出球
    $repeat_num = 0; //重号出现个数
    $tail43     = 0; //43尾数4尾数
    $tailnum    = 0; //43尾数4尾数

    //三区出球比
    $threeRed = array(0=>0, 1=>0, 2=>0);

    //热温冷遗漏
    $missCfg = array(0=>0, 1=>0, 2=>0);

    //除3余数
    $road3 = array(0=>0, 1=>0, 2=>0);

    $miss_sum   = 0; //遗漏和值
    $sum        = 0; //和数值
    $tailsum    = 0; //尾数和
    $difference = $myreds[5] - $myreds[0]; //跨度
    $tailGroup  = array(); //尾数组数
    
    $hasKill = false;
    $hasdanma = false;
    $tailarr = array();
    foreach ($myreds as $red) {
        if(in_array($red, $killReds)){
            $hasKill = true;
            break;
        }

        if(in_array($red, $danmaReds)) $hasdanma = true;

        $red > 16 && $bignum++;
        $red % 2 == 1 && $oddnum++;
        in_array($red, $prime) && $primenum++;

        $tail        = $red % 10;
        $tailsum     += $tail;
        $tailGroup[] = $tail;

        in_array($tail, array(1, 2, 3)) && $tail43++;

        if($red >=1 && $red <= 11){
            $threeRed[0]++;
        }
        if($red >=12 && $red <= 22){
            $threeRed[1]++;
        }
        if($red >=23 && $red <= 33){
            $threeRed[2]++;
        }

        strlen($red) == 1 && $red < 10 && $red = '0'.$red;
        $curmiss[$red] < 5 && $missCfg[0]++;
        $curmiss[$red] >= 5 && $curmiss[$red] < 10 && $missCfg[1]++;
        $curmiss[$red] >= 10 && $missCfg[2]++;
        $miss_sum += $curmiss[$red];

        $road3[$red%3]++;

        in_array($red, $curReds) && $repeat_num++;
        if(isset($chooseTail) && in_array($tail, $chooseTail) && !isset($tailarr[$tail])){
            $tailarr[$tail] = 1;
        }
    }

    if($danmaReds && $hasdanma == false) continue;

    if(isset($chooseTail) && count($chooseTail) != count($tailarr)) continue;

    if($hasKill) continue;

    //大号出球判断
    if(isset($filter_bigball) && $bignum != $win_bignum) continue;

    //奇数出球判断
    if(isset($filter_odd) && $oddnum != $win_oddnum) continue;

    //质数出球判断
    if(isset($filter_prime) && $primenum != $win_primenum) continue;

    //AC值判断
    $acnum = getAC($myreds);
    if(isset($filter_ac) && ($acnum < $win_ac[0] || $acnum > $win_ac[1])) continue;

    //重号判断
    if(isset($filter_repeat) && $repeat_num != $win_repeat) continue;

    //43尾数比
    if(isset($filter_tail) && $tail43 != $win_tail43) continue;

    //三区出球判断
    if(isset($filter_redarea) && !($threeRed[0] == $win_areanum[0] && $threeRed[1] == $win_areanum[1] && $threeRed[2] == $win_areanum[2])) continue;

    //除3余数判断
    if(isset($filter_3road) && !($road3[0] == $win_3road[0] && $road3[1] == $win_3road[1] && $road3[2] == $win_3road[2])) continue;

    //冷温热
    if(isset($filter_hotcool) && !($missCfg[0] == $win_hotcoll[0] && $missCfg[1] == $win_hotcoll[1] && $missCfg[2] == $win_hotcoll[2])) continue;

    //遗漏和值范围排除
    if(isset($filter_misssum) && ($miss_sum < $win_misssum[0] || $miss_sum > $win_misssum[1])) continue;

    //连号个数
    $lianhao = getLianHao($myreds);
    if(isset($filter_sequence) && isset($lianhao[$sequence_num])){
        if($sequence_num == 0 && $lianhao[0] != 0) continue;
        if($sequence_num > 0 && $lianhao[$sequence_num] == 0) continue;
    }

    //和数值范围排除
    $sum = array_sum($myreds);
    if(isset($filter_sum) && ($sum < $win_sum[0] || $sum > $win_sum[1])) continue;

    //尾数和
    if(isset($filter_mantissaSum) && ($tailsum < $win_mantissa[0] || $tailsum > $win_mantissa[1])) continue;

    //首位跨度
    if(isset($filter_difference) && ($difference < $win_difference[0] || $difference > $win_difference[1])) continue;

    $tailGroup = array_unique($tailGroup);
    $tailGroupCount = count($tailGroup);
    //尾数组数
    if(isset($filter_mantissaGroup) && ($tailGroupCount < $win_mantissaGroup[0] || $tailGroupCount > $win_mantissaGroup[1])) continue;

    $total++;

    foreach ($myreds as $key => $v) {
        if(!isset($selReds[$v])){
            $selReds[$v] = 1;
        }
    }
    sort($myreds);
    $myreds = implode(' ', $myreds);
    $redblue = $myreds . '+' . $chooseBlue;

    $result[] = $redblue;

    if(!empty($savedata) && $savedata == 1){
        $tmp = array();
        $tmp['cp_dayid'] = $next_dayid;
        $tmp['redblue'] = $redblue;
        $tmp['reds'] = $myreds;
        $tmp['blue'] = $chooseBlue;
        $sqlData[] = $tmp;
    }
}

if(!empty($history_win) && $history_win == 1){
    foreach ($result as $key => &$myreds) {
        $tmp = explode("+", $myreds);
        $reds = explode(' ', $tmp[0]);
        $blues = $tmp[1];
        $history_win = redHistoryWin($reds, $blues);
        $myreds = $myreds . "【" . $history_win . "】";
    }
}

$selReds = array_keys($selReds);
sort($selReds);

if(!empty($savedata) && $savedata == 1){
    $allreds = implode(' ', $selReds);
    $sqlV = array();
    foreach ($sqlData as $k => $v) {
        $sqlV[] = '("'.$v['cp_dayid'].'", "'.$allreds.'", "'.$v['redblue'].'", "'.$v['reds'].'", "'.$v['blue'].'")';
    }
    if($sqlV){
        $sqlV = implode(",", $sqlV);
        $dosql->ExecNoneQuery("INSERT INTO `#@__caipiao_yuce` (cp_dayid,allreds,redblue,reds,blue) VALUES " . $sqlV);
    }
}

setcookie('chooseReds', implode(" ", $chooseReds), time()+7200, '/');
setcookie('filterReds', implode(" ", $selReds), time()+7200, '/');
setcookie('chooseBlue', $chooseBlue, time()+7200, '/');
setcookie('bigsmall', implode(":", array($win_bignum, 6-$win_bignum)), time()+7200, '/');
setcookie('oddeven', implode(":", array($win_oddnum, 6-$win_oddnum)), time()+7200, '/');
setcookie('primenum', implode(":", array($win_primenum, 6-$win_primenum)), time()+7200, '/');
setcookie('redarea', implode(":", $win_areanum), time()+7200, '/');
setcookie('coolhot', implode(":", $win_hotcoll), time()+7200, '/');
setcookie('repeat_num', isset($repeat_num) ? $repeat_num : 0, time()+7200, '/');
setcookie('sum', implode(" - ", $win_sum), time()+7200, '/');
setcookie('tailsum', implode(" - ", $win_mantissa), time()+7200, '/');
setcookie('misssum', implode(" - ", $win_misssum), time()+7200, '/');
setcookie('lianhao_num', $sequence_num, time()+7200, '/');
setcookie('ac', implode(" - ", $win_ac), time()+7200, '/');
setcookie('comments', $comments, time()+7200, '/');

$content = "";

//统计出组合数
$myzuhe_num = count($myReds);

$content .= "所有的组合数:".$myzuhe_num;
$content .= "<br/>";
$content .= "排除组合数：" . ($myzuhe_num - $total);
$content .= "<br/>";
$content .= "符合组合数：" . $total;
$content .= "<br/>";

$content .= "待选数字：" . implode(' ', $selReds);
$content .= "<br/>";
$content .= "有效组合：<br/>";

$result = implode("<br/>", $result);
setcookie('zuhe', $result, time()+7200, '/');
echo $content;
echo $result;