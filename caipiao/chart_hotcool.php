<?php

require_once dirname(__FILE__).'/../include/config.inc.php';
require_once dirname(__FILE__).'/../wxpage/core/ssq.config.php';
require_once dirname(__FILE__).'/../wxpage/core/choosered.func.php';


/**
 * 二位数组排序，支持多字段的升序、降序
 *
 * @param array $arr
 * @param array $keys
 * @param array $order
 * @return void
 */
function sort_array_multi(array &$arr, array $keys, array $order)
{
    if (empty($arr)) {
        return false;
    }
    //校验参数
    if (count($keys) == ($times = count($order))) {
        for ($i = 0, $j = 0; $j < $times; $i += 2, $j++) {
            foreach ($arr as $k => $v) {
                //原数组是否存在该字段
                if (isset($v[$keys[$j]])) {
                    // $params[$i][] = $v[$keys[$j]];    //TODO 中文排序支持
                    //刚才的代码中对中文utf-8排序的支持显然是不够好的，这里稍微改造一下将uft-8转为gbk编码就能很好的支持中文排序了
                    $params[$i][] = iconv('UTF-8', 'GBK', $v[$keys[$j]]);
                } else {
                    return false;
                }
            }
            if (strtoupper($order[$j]) == 'ASC') {
                $params[$i + 1] = SORT_ASC;
            } else {
                $params[$i + 1] = SORT_DESC;
            }
        }
        $params[] = &$arr;
        return call_user_func_array('array_multisort', $params);
    } else {
        return false;
    }
}

// 火金木土水
$qunue = array(
    2,7,12,17,22,27,32,
    4,9,14,19,24,29,
    3,8,13,18,23,28,33,
    5,10,15,20,25,30,
    1,6,11,16,21,26,31,
);

$all_red_miss = redMissing();

$data = array();
foreach($all_red_miss as $red => $miss){
    $data[] = array(
        'red' => $red,
        'miss' => $miss,
    );
}
//调用
sort_array_multi($data, ['miss', 'red'], ['asc', 'asc']);
$qunue = array_column($data, 'red');

$blue_qunue = array(
    2,7,12,4,9,14,3,8,13,
    5,10,15,01,06,11,16
);

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
        $all_blu_miss = blueMissing();
    }else{
        $all_red_miss = unserialize($info['miss_content']);
        $all_blu_miss = unserialize($info['miss_blue']);
    }
    new_sort($all_red_miss);
    new_sort2($all_blu_miss);
    $ssq['reds'] = $all_red_miss;
    $ssq['blues'] = $all_blu_miss;

    $data[] = $ssq;
}
$data = array_reverse($data);

function new_sort(&$all_red_miss){
    global $qunue;
    $data = array();
    foreach ($qunue as $red) {
        $red < 10 && strlen($red)<2 && $red = '0' . $red;
        $data[$red] = $all_red_miss[$red];
    }
    $all_red_miss = $data;
}

function new_sort2(&$all_blu_miss){
    global $blue_qunue;
    $data = array();
    foreach ($blue_qunue as $red) {
        $red < 10 && $red = '0' . $red;
        $data[$red] = $all_blu_miss[$red];
    }
    $all_blu_miss = $data;
}

foreach ($qunue as &$red) {
    $red < 10 && strlen($red)<2 && $red = '0' . $red;
}
foreach ($blue_qunue as &$red) {
    $red < 10 && $red = '0' . $red;
}

$fenge = 13;


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
	<title>五行逆向走势图</title>
	<link rel="icon" type="image/vnd.microsoft.icon" href="https://72cp.wang/favicon.ico">
	<link rel="stylesheet" href="./cp360/all.css">
    <link rel="stylesheet" href="./cp360/ssq.css">
    <style>
        body { zoom: 2; }
    </style>
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
                            <?php foreach($blue_qunue as $tmpblue){ ?>
                            <td class=" w1_9">
                                <?php echo $tmpblue ?>
                            </td>
                            <?php }?>  
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
                            <?php foreach($row['blues'] as $tmpblue => $bluemiss){ ?>
                            <td class="tdbg_3<?php //echo $bluemiss>=11 ? " ysep_12" : ""; ?>">
                                <?php if($bluemiss == 0) {?>
                                    <span class="ball_s10"><?php echo $tmpblue ?></span>
                                <?php }else{?>
                                    <?php echo $bluemiss ?>
                                <?php }?>
                            </td>
                            <?php }?>
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
                        <?php foreach($blue_qunue as $tmpblue){ ?>
                        <td class="">
                            <span class="ball_x4"><?php echo $tmpblue ?></span>
                        </td>
                        <?php }?>  
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
            $(".ball_x4").click(function(){
                var a = $(this).html();
                if($(this).hasClass('ball_x4')){
                    $(this).removeClass('ball_x4')
                    $(this).addClass('ball_s10')
                }else{
                    $(this).removeClass('ball_s10')
                    $(this).addClass('ball_x4')
                }
            })
        })(window);
    </script>
</body>
</html>