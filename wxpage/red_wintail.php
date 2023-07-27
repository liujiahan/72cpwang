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
    <title>尾数遗漏规律 - <?php echo $cfg_seotitle; ?></title>
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
            <?php foreach (array(4,6,8,10,12) as $numtmp) { ?>
            <option value="<?php echo $numtmp ?>" <?php echo isset($num) && $num == $numtmp ? 'selected' : '' ?>>前<?php echo $numtmp ?>期</option>
            <?php } ?>
            </select>
          </div>
        </form> 
        
        <div class="clearfix"></div>
        <div class="bs-example" data-example-id="contextual-table">
            <table class="table">
                <?php 
                    $cp_dayid = isset($cp_dayid) && !empty($cp_dayid) ? $cp_dayid : '';
                    $num      = !empty($num) ? $num : 9;
                    $redTail  = GetRedTail($cp_dayid, $num);

                    $isopen = 0;
                ?>
                <thead>
                    <tr>
                        <th width="">继续下落</th>
                        <th width="">期数</th>
                        <th width="">红球尾</th>
                        <th width="">余尾</th>
                        <th width="">上期余尾未中</th>
                        <th width="">余尾下期命中</th>
                        <th width="">操作符</th>
                        <th width="">减尾</th>
                        <th width="">加尾</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($redTail as $tmp_id => $row) {?>
                    <?php 
                        $row['cp_dayid'] == $cp_dayid && $isopen = 1;
                        $cp_dayid == '' && count($redTail) - 1 == $tmp_id && $cp_dayid = $row['cp_dayid'];
                    ?>
                    <tr class="">
                        <td><?php echo implode(" ", $row['repeat_win']) ?></td>
                        <td><?php echo $row['cp_dayid'] ?></td>
                        <td><?php echo implode(" ", $row['win']) ?></td>
                        <td><?php echo implode(" ", $row['nowin']) ?></td>
                        <td><?php echo implode(" ", $row['nowin_nowin']) ?></td>
                        <td><?php echo implode(" ", $row['nowin_nextwin']) ?></td>
                        <td><?php echo $row['op'] ?></td>
                        <?php if($row['op'] == '-'){ ?>
                            <td><?php echo implode(" ", $row['nowin_nextwin2']); ?></td>
                            <td></td>
                        <?php } ?>
                        <?php if($row['op'] == '+'){ ?>
                            <td></td>
                            <td><?php echo implode(" ", $row['nowin_nextwin2']); ?></td>
                        <?php } ?>
                    </tr>
                    <?php } ?>
                    <?php if(!$isopen){ ?>
                    <tr class="">
                        <td></td>
                        <td><?php echo nextCpDayId($cp_dayid) ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
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