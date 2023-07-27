<?php

// print_r(unserialize('a:33:{s:2:"01";i:0;s:2:"02";i:6;s:2:"03";i:1;s:2:"04";i:3;s:2:"05";i:3;s:2:"06";i:4;s:2:"07";i:12;s:2:"08";i:14;s:2:"09";i:1;i:10;i:2;i:11;i:5;i:12;i:4;i:13;i:3;i:14;i:7;i:15;i:2;i:16;i:1;i:17;i:1;i:18;i:4;i:19;i:3;i:20;i:0;i:21;i:6;i:22;i:4;i:23;i:0;i:24;i:4;i:25;i:9;i:26;i:0;i:27;i:0;i:28;i:30;i:29;i:2;i:30;i:7;i:31;i:2;i:32;i:0;i:33;i:9;}'));
// exit;

require_once dirname(__FILE__).'/../include/config.inc.php';
require_once dirname(__FILE__).'/../wxpage/core/ssq.config.php';
require_once dirname(__FILE__).'/../wxpage/core/choosered.func.php';

for ($i=1; $i < 34; $i++) {
    $red = $i;
    if($red<10){
        $red = '0'.$red;
    }
    $qunue[] = $red;
}

$num = isset($num)?$num:30;
$dosql->Execute("SELECT * FROM `#@__caipiao_history` order by id DESC LIMIT {$num}");

$data = array();
while ($row = $dosql->GetArray()) {
    $ssq = array();
    $ssq['no'] = substr($row['cp_dayid'],0,4).'-'.substr($row['cp_dayid'],-3);
    $ssq['red_num'] = str_replace(",", " ", $row['red_num']);
    $ssq['blue_num'] = $row['blue_num'];

    $info = $dosql->GetOne("SELECT * FROM `#@__caipiao_cool_hot` WHERE cp_dayid>".$row['cp_dayid'] ." order by cp_dayid ASC");
    if(empty($info)){
        $all_red_miss = redMissing();
    }else{
        $all_red_miss = unserialize($info['miss_content']);
    }
    $ssq['reds'] = $all_red_miss;

    $data[] = $ssq;
}
$data = array_reverse($data);
// echo "<pre>";
// print_r($data);die;


$fenge = 16;
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="renderer" content="webkit">
	<meta name="author" content="看彩72变(72cp.wang)">
	<meta name="copyright" content="Copyright @72cp.wang 版权所有">
	<meta name="Keywords" content="双色球走势图">
	<meta name="Description" content="双色球走势图">
	<title>双色球走势图</title>
	<link rel="icon" type="image/vnd.microsoft.icon" href="https://72cp.wang/favicon.ico">
	<link rel="stylesheet" href="./cp360/all.css">
    <link rel="stylesheet" href="./cp360/ssq.css">
    
    
</head>

<body class="tzhfb">
    <div class="wrap">
            <div class="chart-sc">
                <ul>
                    <li>
                        <strong>查询：</strong>
                    </li>
                    <li>
                        <a href="?num=30" class="btn-sc btn-sc-cur">近30期</a>
                    </li>
                    <li>
                        <a href="?num=50" class="btn-sc">近50期</a>
                    </li>
                    <li>
                        <a href="?num=100" class="btn-sc">近100期</a>
                    </li>
                </ul>
				<strong>标注：</strong>
				<label><input type="checkbox" name="options" val="sj">&nbsp;遗漏数据&nbsp;</label>
				<label><input type="checkbox" name="options" val="fc" checked="checked">&nbsp;遗漏分层&nbsp;</label>
				<label><input type="checkbox" name="options" val="zx" checked="checked">&nbsp;折线&nbsp;</label>
				<label><input type="checkbox" name="options" val="lh">&nbsp;邻号&nbsp;</label>
				<label><input type="checkbox" name="options" val="ch">&nbsp;重号&nbsp;</label>
				<label><input type="checkbox" name="options" val="lx">&nbsp;连号&nbsp;</label>
            </div>
            <div class="chart-tab" id="chart-tab">
                <table width="100%" class="chart-table">
                    <thead class="zhfb">
                        <tr>
                            <th rowspan="2" class="w94">
                                期号&nbsp; <a href="#" class="tharr tharr-up"></a>
                            </th>
                            <td rowspan="2" class="tdbdr"></td>
                            <th rowspan="2" class="thide">
                                奖号
                            </th>
                            <td rowspan="2" class="tdbdr thide"></td>
                            <th colspan="34" class="noth">
                                红球号码分布
                            </th>
                            <td class="tdbdr"></td>
                            <th colspan="16" class="noth">
                                蓝球号码分布
                            </th>
                        </tr>
                        <tr>
                            <?php foreach($qunue as $tmpred){ ?>
                                <td class=" w1_9">
                                <?php echo $tmpred ?>
                                </td>
                                <?php if($tmpred == $fenge){ ?>
                                <td class="tdbdr tdbdr_nav"></td>
                                <?php } ?>
                            <?php }?>                      
                            <td class="tdbdr"></td>
                            <td class=" w1_9">
                                01
                            </td>
                            <td class=" w1_9">
                                02
                            </td>
                            <td class=" w1_9">
                                03
                            </td>
                            <td class=" w1_9">
                                04
                            </td>
                            <td class=" w1_9">
                                05
                            </td>
                            <td class=" w1_9">
                                06
                            </td>
                            <td class=" w1_9">
                                07
                            </td>
                            <td class=" w1_9">
                                08
                            </td>
                            <td class=" w1_9">
                                09
                            </td>
                            <td class=" w1_9">
                                10
                            </td>
                            <td class=" w1_9">
                                11
                            </td>
                            <td class=" w1_9">
                                12
                            </td>
                            <td class=" w1_9">
                                13
                            </td>
                            <td class=" w1_9">
                                14
                            </td>
                            <td class=" w1_9">
                                15
                            </td>
                            <td class=" w1_9">
                                16
                            </td>
                        </tr>
                    </thead>
                    <tbody id="data-tab" class="zzhfb">
                        <?php foreach($data as $k => $row){ ?>
                        <tr>
                            <td class="tdbg_1">
                            <?php echo $row['no'] ?>
                            </td>
                            <td class="tdbdr"></td>
                            <td class="tdbg_1 thide">
                                <strong class="rednum"><?php echo $row['red_num'] ?></strong>+<strong class="bluenum"><?php echo $row['blue_num'] ?></strong>
                            </td>
                            <td class="tdbdr thide"></td>
                            <?php foreach($row['reds'] as $tmpred => $redmiss){ ?>

                            <?php if($redmiss == 0) {?>
                            <td class="tdbg_5" hit="">
                                <span class="ball_s3"><?php echo $tmpred ?></span>
                            </td>
                            <?php if($tmpred == $fenge){ ?>
                            <td class="tdbdr tdbdr_nav"></td>
                            <?php } ?>
                            <?php }else{?>
                            <td class="tdbg_5">
                            <?php echo $redmiss ?>
                            </td>
                            <?php if($tmpred == $fenge){ ?>
                            <td class="tdbdr tdbdr_nav"></td>
                            <?php } ?>
                            <?php }?>
                        <?php } ?>
                            <!-- 红球结束 -->
                            <td class="tdbdr"></td>
                            <!-- 蓝球结束 -->

                            <td class="tdbg_3">
                                4
                            </td>
                            <td class="tdbg_3">
                                14
                            </td>
                            <td class="tdbg_3">
                                11
                            </td>
                            <td class="tdbg_3">
                                7
                            </td>
                            <td class="tdbg_3 ysep_12">
                                6
                            </td>
                            <td class="tdbg_3">
                                32
                            </td>
                            <td class="tdbg_3">
                                2
                            </td>
                            <td class="tdbg_3">
                                1
                            </td>
                            <td class="tdbg_3">
                                37
                            </td>
                            <td class="tdbg_3" hit="">
                                <span class="ball_s10">10</span>
                            </td>
                            <td class="tdbg_3 ysep_12">
                                33
                            </td>
                            <td class="tdbg_3">
                                13
                            </td>
                            <td class="tdbg_3 ysep_12">
                                27
                            </td>
                            <td class="tdbg_3">
                                5
                            </td>
                            <td class="tdbg_3">
                                16
                            </td>
                            <td class="tdbg_3">
                                23
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot id="addedrow">
                    <tr class="addCodeNum">
                        <td class="tdbg_1">
                            <span class="btn-prese"><a href="#" class="add"></a> <a href="#" class="cut"></a></span> 预选1
                        </td>
                        <td class="tdbdr"></td>
                        <?php foreach($qunue as $tmpred){ ?>
                            <td class="">
                                <span class="ball_x1"><?php echo $tmpred ?></span>
                            </td>
                            <?php if($tmpred == $fenge){ ?>
                            <td class="tdbdr_nav"></td>
                            <?php } ?>
                        <?php }?>
                        <td class="tdbdr"></td>
                        <td class="">
                            <span class="ball_x4">01</span>
                        </td>
                        <td class="">
                            <span class="ball_x4">02</span>
                        </td>
                        <td class="">
                            <span class="ball_x4">03</span>
                        </td>
                        <td class="">
                            <span class="ball_x4">04</span>
                        </td>
                        <td class="">
                            <span class="ball_x4">05</span>
                        </td>
                        <td class="">
                            <span class="ball_x4">06</span>
                        </td>
                        <td class="">
                            <span class="ball_x4">07</span>
                        </td>
                        <td class="">
                            <span class="ball_x4">08</span>
                        </td>
                        <td class="">
                            <span class="ball_x4">09</span>
                        </td>
                        <td class="">
                            <span class="ball_x4">10</span>
                        </td>
                        <td class="">
                            <span class="ball_x4">11</span>
                        </td>
                        <td class="">
                            <span class="ball_x4">12</span>
                        </td>
                        <td class="">
                            <span class="ball_x4">13</span>
                        </td>
                        <td class="">
                            <span class="ball_x4">14</span>
                        </td>
                        <td class="">
                            <span class="ball_x4">15</span>
                        </td>
                        <td class="">
                            <span class="ball_x4">16</span>
                        </td>
                    </tr>
                </tfoot>
                </table>
            </div>
    </div>
    <div style="margin-bottom:100px;"></div>
<!-- <script src="./cp360/libs.js"></script> -->
    <script src="./cp360/jquery-1.8.3.min.js"></script>
    <!-- <script src="./cp360/ssq.js"></script> -->
    <script>
        (function(e) {
            $(".ball_x1").click(function(){
                var a = $(this).html();
                if($(this).hasClass('ball_x2')){
                    $(this).removeClass('ball_x2')
                }else{
                    $(this).addClass('ball_x2')
                }
            })
        })(window);
    </script>
</body>
</html>