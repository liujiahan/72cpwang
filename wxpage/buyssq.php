<?php

require_once dirname(__FILE__).'/../include/config.inc.php';

LoginCheck();

if(!(isset($_COOKIE['isAdmin']) && $_COOKIE['isAdmin'] == $adminkey)){
    ShowMsg("该数据只有管理员能查看！", 'index.php');
    exit;
}

$reds = array();
for ($i=1; $i < 34; $i++) { 
    $i < 10 && $i = '0' . $i;
    $reds[] = $i;
}

$blues = array();
for ($i=1; $i < 17; $i++) { 
    $i < 10 && $i = '0' . $i;
    $blues[] = $i;
}

?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>双色球下单 - <?php echo $cfg_seotitle; ?></title>
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="./ssq/matrix.css">
    <script type="text/javascript" src="bootstrap/js/jquery.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript">
    $(document).ready(function() {
        $('.lan-show').hide();
        // $(function(){
        $('#qiu_type1').click(function() {
            $('.lan-show').hide();
            $('.red-show').show();
        })
        $('#qiu_type2').click(function() {
            $('.red-show').hide();
            $('.lan-show').show();
        })
    })

    // var reds = new Array();
    function getReds(){
        reds = new Array();
        $('.red_ball.active').each(function(){
            var red = $(this).html();
            reds.push(red);
            $("#red_num").val(reds);
        })
    }

    function getBlues(){
        blues = new Array();
        $('.blue_ball.active').each(function(){
            blue = $(this).html();
            blues.push(blue);
            $("#blue_num").val(blues);
        })
    }

    function Queren(){
        if($("#cp_dayid").val() == ''){
            alert("请选择期数。");
            return false;
        }
        if(confirm('请确认是发起合买还是个人购买？')){
            return true;
        }else{
            return false;
        }
    }

    function getCost(){
        var r = 0;
        $('.red_ball.active').each(function(){
            r++;
        })
        var b = 0;
        $('.blue_ball.active').each(function(){
            b++;
        })
        if(r>=6 && b>=1){
            $.ajax({
                url: 'ajax/ssq_do.php',
                dataType: 'json',
                type: 'post',
                data: {
                    action: 'getcost',
                    reds: $('#red_num').val(),
                    blues: $('#blue_num').val(),
                    multiple: $('#multiple').val()
                },
                success: function(data) {
                    $(".cost").val(data.cost);
                    $(".buycost").val(data.buycost);
                }
            })
        }
    }

    $(function(){
        $('#red_num').val('');
        $('#blue_num').val('');
        $('.cost').val('');
        $('.buycost').val('');
        $(".red_ball").click(function(){
            if($(this).hasClass('active')){
                $(this).removeClass('active');
            }else{
                $(this).addClass('active');
            }
            getReds();
            getCost();
        })

        $(".blue_ball").click(function(){
            if($(this).hasClass('active')){
                $(this).removeClass('active');
            }else{
                $(this).addClass('active');
            }
            getBlues();
            getCost();
        })
        $("#multiple").change(function(){
            getCost();
        })
    })
    </script>
</head>

<body>
<nav class="navbar navbar-default">
    <div class="container-fluid">
        <?php include('navbar.php'); ?>
        
        <form class="form-horizontal" action="ajax/ssq_do.php" method="post" onsubmit="return Queren()">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">期数</label>
                <div class="col-sm-10">
                    <select class="form-control" id="cp_dayid" name="cp_dayid">
                        <option value="">--请选择--</option>
                        <?php $monthNum = date('m') * 4 * 3+10; ?>
                        <?php for($i=$monthNum; $i>=1; $i--){ ?>
                        <?php //for($i=1; $i<=32; $i++){ ?>
                        <?php 
                          $cp_dayid = date('Y');
                          if($i >= 100){
                            $cp_dayid .= $i;
                          }else if($i >= 10 && $i < 100){
                            $cp_dayid .= '0'.$i;
                          }else{
                            $cp_dayid .= '00'.$i;
                          }
                        ?>
                        <option value="<?php echo $cp_dayid ?>" <?php echo $cp_dayid=='2017031001' ? 'selected' : ''; ?>>
                            <?php echo $cp_dayid; ?>
                        </option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">购买红球</label>
                <div class="col-sm-10">
                    <div class="red_cont" style="margin: 0px 0px;">
                        <?php foreach ($reds as $key => $red) { ?>
                          <span class="red_ball"><?php echo $red ?></span>
                        <?php } ?>
                    </div>
                    <input type="hidden" class="form-control" name="red_num" id="red_num" value="">
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">购买篮球</label>
                <div class="col-sm-10">
                    <div class="blue_cont" style="margin: 0px 0px;">
                        <?php foreach ($blues as $key => $blue) { ?>
                          <span class="blue_ball"><?php echo $blue ?></span>
                        <?php } ?>
                    </div>
                    <input type="hidden" class="form-control" name="blue_num" id="blue_num"  value="">
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">倍数</label>
                <div class="col-sm-10">
                    <select class="form-control" name="multiple" id="multiple">
                        <?php for ($i=1; $i < 11; $i++) { ?>
                        <option value="<?php echo $i; ?>"><?php echo $i.'倍'; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">费用（元）</label>
                <div class="col-sm-10">
                    <input type="hidden" class="form-control buycost" name="buycost" value="">
                    <input type="text" class="form-control cost" name="cost" value="" readonly="readonly">
                </div>
            </div>

            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">发起</label>
                <div class="col-sm-10">
                   <div class="radio">
                      <label>
                        <input type="radio" name="buytype" id="buytype" value="1" checked>
                        个人
                      </label>
                      <label>
                        <input type="radio" name="buytype" id="buytype2" value="2">
                        合买
                      </label>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <input type="hidden" name="action" value="buyssq">
                    <p style="margin-top: 30px;"></p>
                    <button type="submit" class="btn btn-primary">双色球下单</button>
                </div>
            </div>
        </form>
    </div>
    <!-- /.container-fluid -->
</nav>
</body>

</html>
