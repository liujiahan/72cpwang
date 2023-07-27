<?php

require_once dirname(__FILE__).'/../include/config.inc.php';
require_once dirname(__FILE__).'/core/ssq.config.php';
require_once dirname(__FILE__).'/core/choosered.func.php';
LoginCheck();

?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"> -->
    <title>尾数和值遗漏规律 - <?php echo $cfg_seotitle; ?></title>
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
    <script type="text/javascript" src="bootstrap/js/jquery.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript">
    function gourl(){
        window.location.href = "?cp_dayid="+$("#cp_dayid").val()+'&num='+$("#num").val();
    }
    </script>
</head>

<body>
<nav class="navbar navbar-default">
    <div class="container-fluid">
        <?php include('navbar.php') ?>
        
        <form class="navbar-form navbar-left" role="search">
          <div class="form-group">
            <label for="exampleInputEmail2">期数</label>
            <select class="form-control" name="cp_dayid" id="cp_dayid" onchange="gourl()">
            <option value="">--请选择--</option>
            <?php foreach (getDaySel(30) as $t_cp_dayid => $cp_dayidtxt) { ?>
            <option value="<?php echo $t_cp_dayid ?>" <?php echo isset($cp_dayid) && $t_cp_dayid == $cp_dayid ? 'selected' : '' ?>><?php echo $cp_dayidtxt ?></option>
            <?php } ?>
            </select>
          </div>
          <div class="form-group">
            <label for="exampleInputEmail2">前N期</label>
            <select class="form-control" name="num" id="num" onchange="gourl()">
            <option value="">--请选择--</option>
            <?php foreach (array(8,10,12,16,20) as $numtmp) { ?>
            <option value="<?php echo $numtmp ?>" <?php echo isset($num) && $num == $numtmp ? 'selected' : '' ?>>前<?php echo $numtmp ?>期</option>
            <?php } ?>
            </select>
          </div>
        </form> 
        
        <div class="clearfix"></div>
        <div class="bs-example" data-example-id="contextual-table">
            <?php
                $tailSum = array();
                for ($i=1; $i < 34; $i++) { 
                    $i = $i < 10 ? '0'.$i : $i;
                    $tail_sum = getSumVal($i);
                    if(!isset($tailSum[$tail_sum])){
                        $tailSum[$tail_sum] = array();
                    }
                    $tailSum[$tail_sum][] = $i;
                }
            ?>
            <table class="table">
                <thead>
                    <tr>
                        <th width="" rowspan="2">尾数和值</th>
                        <th width="">1</th>
                        <th width="">2</th>
                        <th width="">3</th>
                        <th width="">4</th>
                        <th width="">5</th>
                        <th width="">6</th>
                        <th width="">7</th>
                        <th width="">8</th>
                        <th width="">9</th>
                    </tr>
                </thead>
                <tbody>
                    <td>对应红球</td>
                    <td><?php echo implode(" ", $tailSum[1]); ?></td>
                    <td><?php echo implode(" ", $tailSum[2]); ?></td>
                    <td><?php echo implode(" ", $tailSum[3]); ?></td>
                    <td><?php echo implode(" ", $tailSum[4]); ?></td>
                    <td><?php echo implode(" ", $tailSum[5]); ?></td>
                    <td><?php echo implode(" ", $tailSum[6]); ?></td>
                    <td><?php echo implode(" ", $tailSum[7]); ?></td>
                    <td><?php echo implode(" ", $tailSum[8]); ?></td>
                    <td><?php echo implode(" ", $tailSum[9]); ?></td>
                </tbody>
            </table>
            <table class="table">
                <?php 
                    $maxid = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` order by cp_dayid DESC");
                    $maxid = $maxid['cp_dayid'];

                    $cp_dayid = isset($cp_dayid) && !empty($cp_dayid) ? $cp_dayid : '';
                    $num      = isset($num) && !empty($num) ? $num : 8;
                    $num      = $num + 1;
                    $redTail  = redTailSum($cp_dayid, $num);
                ?>
                <thead>
                    <tr>
                        <th width="">期数</th>
                        <th width="">红球</th>
                        <th width="">与上期重复</th>
                        <th width="">尾数和数</th>
                        <th width="">尾数和数遗漏</th>
                        <th width="">在上期遗漏出现</th>
                        <th width="">尾和重复</th>
                        <th width="">尾数</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($redTail as $tmp_id => $row) {?>
                    <tr>
                        <td><?php echo $row['cp_dayid'] ?>期</td>
                        <td><?php echo implode(' ', $row['red_num']); ?></td>
                        <td><?php echo implode(' ', $row['repeat_tail_sum']); ?></td>
                        <td><?php echo implode(' ', $row['red_tail_sum']); ?></td>
                        <td><?php echo implode(' ', $row['tail_sum_miss']); ?></td>
                        <td><?php echo $row['repeat_tailsum_miss'] ? implode(' ', $row['repeat_tailsum_miss']) : '/'; ?></td>
                        <td><?php echo $row['repeat'] ? implode(' ', $row['repeat']) : '/'; ?></td>
                        <td><?php echo implode(' ', $row['red_tail']); ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <!-- /.container-fluid -->
</nav>
</body>
</html>