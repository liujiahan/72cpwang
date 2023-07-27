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
    <title>五行尾数数据规律 - <?php echo $cfg_seotitle; ?></title>
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
                    $maxid = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` order by cp_dayid DESC");
                    $maxid = $maxid['cp_dayid'];

                    $cp_dayid = isset($cp_dayid) && !empty($cp_dayid) ? $cp_dayid : '';
                    $num = !empty($num) ? $num : 8;
                    $redTail = redUpTailNear($cp_dayid, $num);
                    $redTail2 = array_values($redTail);

                    $allnotail = array();
                    foreach ($redTail as $v) {
                        $allnotail = array_merge($allnotail, $v['nowin']);
                    }

                    $allBlue = array();
                    for ($i=0; $i < 10; $i++) { 
                        $allBlue[] = $i;
                    }

                    $curWinTail = array();
                    $curNoWinTail = array();
                    $preWinTail = array();
                    if(!empty($cp_dayid)){
                        $cur = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid='$cp_dayid'");
                        $curRed = explode(",", $cur['red_num']);
                        foreach ($curRed as $tmpred) {
                            $tmptail = $tmpred % 10;
                            if(!in_array($tmptail, $curWinTail)){
                                $curWinTail[] = $tmptail;
                            }
                        }
                        sort($curWinTail);
                        $curNoWinTail = array_diff($allBlue, $curWinTail);

                        $pre = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<'$cp_dayid' ORDER BY cp_dayid DESC");
                        $preRed = explode(",", $pre['red_num']);
                        foreach ($preRed as $tmpred) {
                            $tmptail = $tmpred % 10;
                            if(!in_array($tmptail, $preWinTail)){
                                $preWinTail[] = $tmptail;
                            }
                        }
                        sort($preWinTail);
                    }

                    $allnotail = array_merge($allnotail, $curNoWinTail);
                    $allnotail = array_unique($allnotail);
                    sort($allnotail);
                ?>
                <thead>
                    <tr class="primary">
                        <th>期数</th>
                        <th>尾数</th>
                        <th><button class="btn btn-warning" type="button">金：4 9</button></th>
                        <th><button class="btn btn-success" type="button">木：3 8</button></th>
                        <th><button class="btn btn-primary" type="button">水：1 6</button></th>
                        <th><button class="btn btn-danger" type="button">火：2 7</button></th>
                        <th><button class="btn btn-info" type="button">土：5 0</button></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($redTail as $tmp_id => $row) {?>
                    <tr>
                        <td><?php echo $row['cp_dayid'] ?></td>
                        <td>
                        <?php 
                            foreach ($row['win'] as $tmptail) {
                                echo '<button class="btn btn-default" type="button">'.$tmptail . '</button>';
                            };
                        ?>
                        </td>
                        <td>
                        <?php 
                            $jin_arr = array(4, 9);
                            foreach ($jin_arr as $tmptail) {
                                if(in_array($tmptail, $row['win'])){
                                    echo '<button class="btn btn-warning" type="button">'.$tmptail . '</button>';
                                }else{
                                    echo '<button class="btn btn-default" type="button">'.$tmptail . '</button>';
                                }
                            }
                        ?>
                        </td>
                        <td>
                        <?php 
                            $mu_arr = array(3, 8);
                            foreach ($mu_arr as $tmptail) {
                                if(in_array($tmptail, $row['win'])){
                                    echo '<button class="btn btn-success" type="button">'.$tmptail . '</button>';
                                }else{
                                    echo '<button class="btn btn-default" type="button">'.$tmptail . '</button>';
                                }
                            }
                        ?>
                        </td>
                        <td>
                        <?php 
                            $shui_arr = array(1, 6);
                            foreach ($shui_arr as $tmptail) {
                                if(in_array($tmptail, $row['win'])){
                                    echo '<button class="btn btn-primary" type="button">'.$tmptail . '</button>';
                                }else{
                                    echo '<button class="btn btn-default" type="button">'.$tmptail . '</button>';
                                }
                            }
                        ?>
                        </td>
                        <td>
                        <?php 
                            $huo_arr = array(2, 7);
                            foreach ($huo_arr as $tmptail) {
                                if(in_array($tmptail, $row['win'])){
                                    echo '<button class="btn btn-danger" type="button">'.$tmptail . '</button>';
                                }else{
                                    echo '<button class="btn btn-default" type="button">'.$tmptail . '</button>';
                                }
                            }
                        ?>
                        </td>
                        <td>
                        <?php 
                            $tu_arr = array(5, 0);
                            foreach ($tu_arr as $tmptail) {
                                if(in_array($tmptail, $row['win'])){
                                    echo '<button class="btn btn-info" type="button">'.$tmptail . '</button>';
                                }else{
                                    echo '<button class="btn btn-default" type="button">'.$tmptail . '</button>';
                                }
                            }
                        ?>
                        </td>
                    </tr>
                    <?php } ?>
                    <tr class="danger">
                        <td><?php echo end($redTail2)['cp_dayid']+1 ?></td>
                        <td>
                        <?php 
                            if($curWinTail){
                                foreach ($curWinTail as $tmptail) {
                                    echo '<button class="btn btn-default" type="button">'.$tmptail . '</button>';
                                };
                            }else{
                                foreach (array(0,1,2,3,4,5,6,7,8,9) as $tmptail) {
                                    echo '<button class="btn btn-default all" type="button">'.$tmptail . '</button>';
                                };
                            }
                        ?>
                        </td>
                        <td>
                        <?php 
                                $jin_arr = array(4, 9);
                            if($curWinTail){
                                foreach ($jin_arr as $tmptail) {
                                    if(in_array($tmptail, $curWinTail)){
                                        echo '<button class="btn btn-warning" type="button">'.$tmptail . '</button>';
                                    }else{
                                        echo '<button class="btn btn-default" type="button">'.$tmptail . '</button>';
                                    }
                                }
                            }else{
                                foreach ($jin_arr as $tmptail) {
                                    echo '<button class="btn btn-default jin" type="button">'.$tmptail . '</button>';
                                }
                            }
                        ?>
                        </td>
                        <td>
                        <?php 
                                $mu_arr = array(3, 8);
                            if($curWinTail){
                                foreach ($mu_arr as $tmptail) {
                                    if(in_array($tmptail, $curWinTail)){
                                        echo '<button class="btn btn-success" type="button">'.$tmptail . '</button>';
                                    }else{
                                        echo '<button class="btn btn-default" type="button">'.$tmptail . '</button>';
                                    }
                                }
                            }else{
                                foreach ($mu_arr as $tmptail) {
                                    echo '<button class="btn btn-default mu" type="button">'.$tmptail . '</button>';
                                }
                            }
                        ?>
                        </td>
                        <td>
                        <?php 
                                $shui_arr = array(1, 6);
                            if($curWinTail){
                                foreach ($shui_arr as $tmptail) {
                                    if(in_array($tmptail, $curWinTail)){
                                        echo '<button class="btn btn-primary" type="button">'.$tmptail . '</button>';
                                    }else{
                                        echo '<button class="btn btn-default" type="button">'.$tmptail . '</button>';
                                    }
                                }
                            }else{
                                foreach ($shui_arr as $tmptail) {
                                    echo '<button class="btn btn-default shui" type="button">'.$tmptail . '</button>';
                                }
                            }
                        ?>
                        </td>
                        <td>
                        <?php 
                                $huo_arr = array(2, 7);
                            if($curWinTail){
                                foreach ($huo_arr as $tmptail) {
                                    if(in_array($tmptail, $curWinTail)){
                                        echo '<button class="btn btn-danger" type="button">'.$tmptail . '</button>';
                                    }else{
                                        echo '<button class="btn btn-default" type="button">'.$tmptail . '</button>';
                                    }
                                }
                            }else{
                                foreach ($huo_arr as $tmptail) {
                                    echo '<button class="btn btn-default huo" type="button">'.$tmptail . '</button>';
                                }
                            }
                        ?>
                        </td>
                        <td>
                        <?php 
                                $tu_arr = array(5, 0);
                            if($curWinTail){
                                foreach ($tu_arr as $tmptail) {
                                    if(in_array($tmptail, $curWinTail)){
                                        echo '<button class="btn btn-info" type="button">'.$tmptail . '</button>';
                                    }else{
                                        echo '<button class="btn btn-default" type="button">'.$tmptail . '</button>';
                                    }
                                }
                            }else{
                                foreach ($tu_arr as $tmptail) {
                                    echo '<button class="btn btn-default tu" type="button">'.$tmptail . '</button>';
                                }
                            }
                        ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <!-- /.container-fluid -->
</nav>
</body>
<script>
(function(e) {
    $(".all").click(function(){
        if($(this).hasClass('btn-default')){
            $(this).removeClass('btn-default')
            $(this).addClass('btn-danger')
        }else{
            $(this).removeClass('btn-danger')
            $(this).addClass('btn-default')
        }
    })
    $(".jin").click(function(){
        if($(this).hasClass('btn-default')){
            $(this).removeClass('btn-default')
            $(this).addClass('btn-warning')
        }else{
            $(this).removeClass('btn-warning')
            $(this).addClass('btn-default')
        }
    })
    $(".mu").click(function(){
        if($(this).hasClass('btn-default')){
            $(this).removeClass('btn-default')
            $(this).addClass('btn-success')
        }else{
            $(this).removeClass('btn-success')
            $(this).addClass('btn-default')
        }
    })
    $(".shui").click(function(){
        if($(this).hasClass('btn-default')){
            $(this).removeClass('btn-default')
            $(this).addClass('btn-primary')
        }else{
            $(this).removeClass('btn-primary')
            $(this).addClass('btn-default')
        }
    })
    $(".huo").click(function(){
        if($(this).hasClass('btn-default')){
            $(this).removeClass('btn-default')
            $(this).addClass('btn-danger')
        }else{
            $(this).removeClass('btn-danger')
            $(this).addClass('btn-default')
        }
    })
    $(".tu").click(function(){
        if($(this).hasClass('btn-default')){
            $(this).removeClass('btn-default')
            $(this).addClass('btn-info')
        }else{
            $(this).removeClass('btn-info')
            $(this).addClass('btn-default')
        }
    })
})(window);
</script>
</html>