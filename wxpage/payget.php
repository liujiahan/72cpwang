<?php

require_once dirname(__FILE__).'/../include/config.inc.php';
LoginCheck();


if(!(isset($_COOKIE['isAdmin']) && $_COOKIE['isAdmin'] == $adminkey)){
    ShowMsg("该数据只有管理员能查看！", 'index.php');
    exit;
}

?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>投资与回报 - <?php echo $cfg_seotitle; ?></title>
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
    <script type="text/javascript" src="bootstrap/js/jquery.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript">

    function gourl(){
        window.location.href = "?cp_dayid="+$("#cp_dayid").val()+"&isdo="+$("#isdo").val();
    }
    $(document).ready(function() {
        $("#duiJiangBtn").click(function(){
            $(this).html('计算中......');
            $.ajax({
                url: 'ajax/ssq_do.php',
                dataType: 'html',
                type: 'post',
                data: {action: 'cash_prize'},
                success: function(data){
                    window.location.reload();
                }
            })
        })
    })
    </script>
</head>

<body>
<nav class="navbar navbar-default">
    <div class="container-fluid">
        <?php include('navbar.php') ?>

        <?php 
        	$payget = $dosql->GetOne("SELECT * FROM `#@__caipiao_payandget` WHERE type='ssq' and buytype=1");
        ?>
        <h3 class="text-center">个人：投资<?php echo $payget['pay_num'] ?>元，回报<?php echo $payget['get_num'] ?>元，亏损<?php echo $payget['pay_num']-$payget['get_num'] ?>元，中奖回报率<?php echo round($payget['get_num'] / $payget['pay_num'], 4)*100 . '%'; ?></h3>
        <?php 
            $payget2 = $dosql->GetOne("SELECT * FROM `#@__caipiao_payandget` WHERE type='ssq' and buytype=2");
        ?>
        <h3 class="text-center">合买：投资<?php echo $payget2['pay_num'] ?>元，回报<?php echo $payget2['get_num'] ?>元，亏损<?php echo $payget2['pay_num']-$payget2['get_num'] ?>元，中奖回报率<?php echo round($payget2['get_num'] / $payget2['pay_num'], 4)*100 . '%'; ?></h3>
        <!-- <p class="lead"></p> -->
        <!-- <blockquote>
            <p>先看冷热选范围，再看位置定号码。锁定篮球赢大奖，平常心态来日方长。</p>
        </blockquote>  -->
    </div>
    <!-- /.container-fluid -->
</nav>
</body>

</html>
