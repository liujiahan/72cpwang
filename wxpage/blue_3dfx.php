<?php

require_once dirname(__FILE__).'/../include/config.inc.php';
require_once dirname(__FILE__).'/core/suanfa.func.php';
require_once dirname(__FILE__).'/core/ssq.config.php';
require_once dirname(__FILE__).'/core/choosered.func.php';
require_once dirname(__FILE__).'/core/wuxing.func.php';

LoginCheck();

?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"> -->
    <title>蓝球深度分析3D走势图 - <?php echo $cfg_seotitle; ?></title>
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="./ssq/matrix.css">
    <script type="text/javascript" src="bootstrap/js/jquery.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript">
    function godayurl(){
        window.location.href = "?limit_num="+$("#limit_num").val();
    }

    $(document).ready(function() {
        $('#limit_num').change(function(){
            window.location.href = "?limit_num="+$("#limit_num").val();
        })
        $("#dataCalcBtn").click(function() {
            $(this).html('计算中...');
            $.ajax({
                url: 'ajax/blue_suanfa_do.php',
                dataType: 'html',
                type: 'post',
                data: {
                    action: 'blue_3d'
                },
                success: function(data) {
                    alert("成功计算"+data+"期");
                    window.location.reload();
                }
            })
        })
    })
    </script>
    <style type="text/css">
        .red_ball{
            width: 25px;
            height: 25px;
            line-height: 25px;
        }
        .blue_ball{
            width: 25px;
            height: 25px;
            line-height: 25px;
        }
    </style>
</head>

<body>
<nav class="navbar navbar-default">
    <div class="container-fluid">
        <?php include('navbar.php') ?>
        <form class="navbar-form navbar-left" role="search">
          <div class="form-group">
              <label for="exampleInputEmail2">选择期数</label>
              <select class="form-control" name="limit_num" id="limit_num" onchange="godayurl()">
                  <option value="">--请选择--</option>
                  <?php foreach (getSelArr() as $daynum => $daytxt) { ?>
                    <option value="<?php echo $daynum ?>" <?php echo isset($limit_num) && $limit_num == $daynum ? 'selected' : '' ?>><?php echo $daytxt ?></option>
                  <?php } ?>
              </select>
          </div>
          <a href="rtn_prize_rate.php?cpnum=<?php echo isset($limit_num) ? $limit_num : 30 ?>" class="btn btn-primary" target="_blank" role="button">蓝号返奖率</a>
          <a href="rtn_prize_miss.php?cpnum=<?php echo isset($limit_num) ? $limit_num : 30 ?>" class="btn btn-primary" target="_blank" role="button">蓝号遗漏</a>
          <a href="rtn_prize_hotcool.php?cpnum=<?php echo isset($limit_num) ? $limit_num : 30 ?>" class="btn btn-primary" target="_blank" role="button">蓝号冷热</a>
          <a href="rtn_prize_zf.php?cpnum=<?php echo isset($limit_num) ? $limit_num : 30 ?>" class="btn btn-primary" target="_blank" role="button">蓝号振幅</a>
          <a href="rtn_prize_prime.php?cpnum=<?php echo isset($limit_num) ? $limit_num : 30 ?>" class="btn btn-primary" target="_blank" role="button">蓝号质合</a>
        </form> 
        <?php if(isset($_COOKIE['isAdmin']) && $_COOKIE['isAdmin'] == $adminkey){ ?>
        <a href="javascript:;" class="btn btn-info btn-sm active pull-right" id="dataCalcBtn" role="button">蓝球五行关系</a>
        <?php } ?>
        <div class="clearfix"></div>
    </div>
    <!-- /.container-fluid -->
</nav>
</body>
</script>