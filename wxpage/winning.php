<?php

require_once dirname(__FILE__).'/../include/config.inc.php';
require_once dirname(__FILE__).'/core/ssq.config.php';

LoginCheck();

$reds = array();
$blues = array();
for ($i=1; $i < 34; $i++) { 
    $i < 10 && $i = '0' . $i;
    $reds[] = $i;
    if($i <= 16){
        $blues[] = $i;
    }
}

?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>双色球智慧过滤 - <?php echo $cfg_seotitle; ?></title>
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
    <!-- <link rel="stylesheet" href="./ssq/swiper.css"> -->
    <!-- <link rel="stylesheet" href="./ssq/font-awesome.css"> -->
    <script type="text/javascript" src="bootstrap/js/jquery.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="./ssq/matrix.css">
    <script type="text/javascript">
    function godayurl() {
        window.location.href = "?cp_dayid=" + $("#cp_dayid").val()+"&before_days=" + $("#before_days").val();
    }

    $(document).ready(function() {
        $("#duiJiangBtn").click(function() {
            $(this).html('计算中......');
            $.ajax({
                url: 'suanfa_do.php',
                dataType: 'html',
                type: 'post',
                data: {
                    action: 'location_cross'
                },
                success: function(data) {
                    // window.location.reload();
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
    <div class="row-fluid clearfix">
        <div class="span12 clearfix">
            <div class="select_ball">
                <div class="red_top">红球：<span><span class="geshu">个&nbsp;&nbsp;<i class="fa fa-sort-desc" aria-hidden="true"></i></span><select class="redBall form-control mr10 col-xs-2" name="sel" id="sel_red">
                        <option value="8" selected="selected">8</option>
                        <option value="9">9</option>
                        <option value="10">10</option>
                        <option value="11">11</option>
                        <option value="12">12</option>
                        <option value="13">13</option>
                        <option value="14">14</option>
                        <option value="15">15</option>
                        <option value="16">16</option>
                        <option value="17">17</option>
                        <option value="18">18</option>
                        <option value="19">19</option>
                        <option value="20">20</option>
                    </select></span>
                    <button class="btn btn-danger" id="jx_red_ball">机选红球</button>
                    <a href="javascript:void%20(0)" id="empty_red_ball">清空</a>
                </div>
                <div class="red_cont">
                    <?php for ($i=01; $i < 34; $i++) { ?>
                    <span class="red_ball"><?php echo $i < 10 ? '0' . $i : $i; ?></span>
                    <?php } ?>
                </div>
            </div>
            <div class="select_ball">
                <p class="blue_top">蓝球：<span><span class="geshu">个&nbsp;&nbsp;<i class="fa fa-sort-desc" aria-hidden="true"></i></span><select class="blueBall form-control mr10 col-xs-2" name="sel" id="sel_blue">
                        <option value="1" selected="selected">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                    </select></span>
                    <button class="btn btn-primary" id="jx_blue_ball">机选蓝球</button>
                    <a href="javascript:void%20(0)" id="empty_blue_ball">清空</a>
                </p>
                <div class="blue_cont">
                    <?php for ($i=01; $i < 17; $i++) { ?>
                    <span class="blue_ball"><?php echo $i < 10 ? '0' . $i : $i; ?></span>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row clearfix" style="margin-top: 30px;">
        <div class="col-md-6 column">
            <form class="form-horizontal" id="ssqForm" role="form">
                <div class="form-group" style="display: none;">
                     <label for="chooseReds" class="col-sm-3 control-label">预杀号</label>
                    <div class="col-sm-8">
                        <input class="form-control" id="killReds" name="killReds" type="text" ></input>
                    </div>
                </div>
                <div class="form-group">
                     <label for="chooseTail_" class="col-sm-3 control-label">小尾数</label>
                    <div class="col-sm-8">
                        <label class="checkbox-inline">
                          <input type="checkbox" name="chooseTail[]" value="0">0尾
                        </label>
                        <label class="checkbox-inline">
                          <input type="checkbox" name="chooseTail[]" value="1">1尾
                        </label>
                        <label class="checkbox-inline">
                          <input type="checkbox" name="chooseTail[]" value="2">2尾
                        </label>
                        <label class="checkbox-inline">
                          <input type="checkbox" name="chooseTail[]" value="3">3尾
                        </label>
                        <label class="checkbox-inline">
                          <input type="checkbox" name="chooseTail[]" value="4">4尾
                        </label>
                    </div>
                </div>
                <div class="form-group">
                     <label for="chooseTail_" class="col-sm-3 control-label">大尾数</label>
                    <div class="col-sm-8">
                        <label class="checkbox-inline">
                          <input type="checkbox" name="chooseTail[]" value="5">5尾
                        </label>
                        <label class="checkbox-inline">
                          <input type="checkbox" name="chooseTail[]" value="6">6尾
                        </label>
                        <label class="checkbox-inline">
                          <input type="checkbox" name="chooseTail[]" value="7">7尾
                        </label>
                        <label class="checkbox-inline">
                          <input type="checkbox" name="chooseTail[]" value="8">8尾
                        </label>
                        <label class="checkbox-inline">
                          <input type="checkbox" name="chooseTail[]" value="9">9尾
                        </label>
                    </div>
                </div>
                <div class="form-group">
                     <label for="chooseReds" class="col-sm-3 control-label">红球胆码</label>
                    <div class="col-sm-8">
                        <input class="form-control" id="danmaReds" name="danmaReds" type="text" ></input>
                    </div>
                </div>
                <div class="form-group">
                    <label for="win_bignum" class="col-sm-3 control-label">大球出球数</label>
                    <label class="checkbox-inline">
                      <input type="checkbox" id="filter_bigball" name="filter_bigball" value="1"> 大号个数
                    </label>
                    <?php if(isset($_COOKIE['isAdmin']) && $_COOKIE['isAdmin'] == $adminkey){ ?>
                    <label class="checkbox-inline">
                      <input type="checkbox" id="history_win" name="history_win" value="1"> 大奖成色检验
                    </label>
                    <label class="checkbox-inline">
                      <input type="checkbox" id="savedata" name="savedata" value="1"> 保存数据
                    </label>
                    <?php } ?>
                    <div class="col-sm-3">
                        <select class="form-control" name="win_bignum">
                            <option value="0">0个</option>
                            <option value="1">1个</option>
                            <option value="2">2个</option>
                            <option value="3" selected>3个</option>
                            <option value="4">4个</option>
                            <option value="5">5个</option>
                            <option value="6">6个</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="win_odd_num" class="col-sm-3 control-label">奇数出球数</label>
                    <label class="checkbox-inline">
                      <input type="checkbox" id="filter_odd" name="filter_odd" value="1"> 奇号个数
                    </label>
                    <div class="col-sm-3">
                        <select class="form-control" name="win_oddnum">
                            <option value="0">0个</option>
                            <option value="1">1个</option>
                            <option value="2">2个</option>
                            <option value="3" selected>3个</option>
                            <option value="4">4个</option>
                            <option value="5">5个</option>
                            <option value="6">6个</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="win_odd_num" class="col-sm-3 control-label">质数出球数</label>
                    <label class="checkbox-inline">
                      <input type="checkbox" id="filter_prime" name="filter_prime" value="1"> 质号个数
                    </label>
                    <div class="col-sm-3">
                        <select class="form-control" name="win_primenum">
                            <option value="0">0个</option>
                            <option value="1">1个</option>
                            <option value="2" selected>2个</option>
                            <option value="3">3个</option>
                            <option value="4">4个</option>
                            <option value="5">5个</option>
                            <option value="6">6个</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="win_odd_num" class="col-sm-3 control-label">AC值</label>
                    <label class="checkbox-inline">
                      <input type="checkbox" id="filter_ac" name="filter_ac" value="1"> AC值
                    </label>
                    <div class="col-sm-3">
                        <select class="form-control" name="win_ac[]">
                            <?php for ($i=0; $i <=10 ; $i++) { ?>
                                <option value="<?php echo $i ?>" <?php echo $i==4 ? 'selected="selected"' : ""; ?>><?php echo $i ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <select class="form-control" name="win_ac[]">
                            <?php for ($i=4; $i <=10 ; $i++) { ?>
                                <option value="<?php echo $i ?>" <?php echo $i==10 ? 'selected="selected"' : ""; ?>><?php echo $i ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="win_odd_num" class="col-sm-3 control-label">重号出球</label>
                    <label class="checkbox-inline">
                      <input type="checkbox" id="filter_repeat" name="filter_repeat" value="1"> 重号出球个数
                    </label>
                    <div class="col-sm-3">
                        <select class="form-control" name="win_repeat">
                            <?php for ($i=0; $i <=6 ; $i++) { ?>
                                <option value="<?php echo $i ?>" <?php echo $i==1 ? 'selected="selected"' : ""; ?>><?php echo $i ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="win_odd_num" class="col-sm-3 control-label">4尾数出球</label>
                    <label class="checkbox-inline">
                      <input type="checkbox" id="filter_tail" name="filter_tail" value="1"> 43尾数比
                    </label>
                    <div class="col-sm-3">
                        <select class="form-control" name="win_tail43">
                            <?php for ($i=0; $i <=6 ; $i++) { ?>
                                <option value="<?php echo $i ?>" <?php echo $i==2 ? 'selected="selected"' : ""; ?>><?php echo $i ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="win_odd_num" class="col-sm-3 control-label">三区出球</label>
                    <label class="checkbox-inline">
                      <input type="checkbox" id="filter_redarea" name="filter_redarea" value="1"> 三区出球
                    </label>
                    <div class="col-sm-2">
                        <select class="form-control" name="win_areanum[]">
                            <?php for ($i=0; $i <=6 ; $i++) { ?>
                                <option value="<?php echo $i ?>" <?php echo $i==2 ? 'selected="selected"' : ""; ?>><?php echo $i ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <select class="form-control" name="win_areanum[]">
                            <?php for ($i=0; $i <=6 ; $i++) { ?>
                                <option value="<?php echo $i ?>" <?php echo $i==2 ? 'selected="selected"' : ""; ?>><?php echo $i ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <select class="form-control" name="win_areanum[]">
                            <?php for ($i=0; $i <=6 ; $i++) { ?>
                                <option value="<?php echo $i ?>" <?php echo $i==2 ? 'selected="selected"' : ""; ?>><?php echo $i ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="win_odd_num" class="col-sm-3 control-label">除3余数</label>
                    <label class="checkbox-inline">
                      <input type="checkbox" id="filter_3road" name="filter_3road" value="1"> 除3余数
                    </label>
                    <div class="col-sm-2">
                        <select class="form-control" name="win_3road[]">
                            <?php for ($i=0; $i <=6 ; $i++) { ?>
                                <option value="<?php echo $i ?>" <?php echo $i==2 ? 'selected="selected"' : ""; ?>><?php echo $i ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <select class="form-control" name="win_3road[]">
                            <?php for ($i=0; $i <=6 ; $i++) { ?>
                                <option value="<?php echo $i ?>" <?php echo $i==2 ? 'selected="selected"' : ""; ?>><?php echo $i ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <select class="form-control" name="win_3road[]">
                            <?php for ($i=0; $i <=6 ; $i++) { ?>
                                <option value="<?php echo $i ?>" <?php echo $i==2 ? 'selected="selected"' : ""; ?>><?php echo $i ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="win_odd_num" class="col-sm-3 control-label">热温冷出球数</label>
                    <label class="checkbox-inline">
                      <input type="checkbox" id="filter_hotcool" name="filter_hotcool" value="1"> 热温冷出球
                    </label>
                    <div class="col-sm-2">
                        <select class="form-control" name="win_hotcoll[]">
                            <?php for ($i=0; $i <=6 ; $i++) { ?>
                                <option value="<?php echo $i ?>" <?php echo $i==2 ? 'selected="selected"' : ""; ?>><?php echo $i ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <select class="form-control" name="win_hotcoll[]">
                            <?php for ($i=0; $i <=6 ; $i++) { ?>
                                <option value="<?php echo $i ?>" <?php echo $i==2 ? 'selected="selected"' : ""; ?>><?php echo $i ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <select class="form-control" name="win_hotcoll[]">
                            <?php for ($i=0; $i <=6 ; $i++) { ?>
                                <option value="<?php echo $i ?>" <?php echo $i==2 ? 'selected="selected"' : ""; ?>><?php echo $i ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="win_odd_num" class="col-sm-3 control-label">遗漏和值</label>
                    <label class="checkbox-inline">
                      <input type="checkbox" id="filter_misssum" name="filter_misssum" value="1"> 遗漏和值
                    </label>
                    <div class="col-sm-3">
                        <input class="form-control input" name="win_misssum[]" type="number" value="25"  />
                    </div>
                    <div class="col-sm-3">
                        <input class="form-control input" name="win_misssum[]" type="number" value="40"  />
                    </div>
                </div>
                <div class="form-group">
                    <label for="win_odd_num" class="col-sm-3 control-label">连号个数</label>
                    <label class="checkbox-inline">
                      <input type="checkbox" id="filter_sequence" name="filter_sequence" value="1"> 连号个数
                    </label>
                    <div class="col-sm-3">
                        <select class="form-control" name="sequence_num">
                            <option value="0">0个</option>
                            <option value="2">2个</option>
                            <option value="3">3个</option>
                            <option value="4">4个</option>
                            <option value="5">5个</option>
                            <option value="6">6个</option>
                        </select>
                    </div>
                </div>
                <!-- <div class="form-group">
                    <label for="win_odd_num" class="col-sm-3 control-label">连号组数</label>
                    <label class="checkbox-inline">
                      <input type="checkbox" id="filter_sequenceGroup" name="filter_sequenceGroup" value="1"> 连号组数
                    </label>
                    <div class="col-sm-3">
                        <select class="form-control" name="sequenceGroup">
                            <option value="0">0组</option>
                            <option value="1">1组</option>
                            <option value="2">2组</option>
                            <option value="3">3组</option>
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <select class="form-control" name="sequenceGroup">
                            <option value="0">0组</option>
                            <option value="1" selected>1组</option>
                            <option value="2">2组</option>
                            <option value="3">3组</option>
                        </select>
                    </div>
                </div> -->
                <div class="form-group">
                    <label for="win_odd_num" class="col-sm-3 control-label">和数值</label>
                    <label class="checkbox-inline">
                      <input type="checkbox" id="filter_sum" name="filter_sum" value="1" checked="checked"> 和值
                    </label>
                    <div class="col-sm-3">
                        <select class="form-control" name="win_sum[]">
                            <option value="21">21</option>
                            <option value="50">50</option>
                            <option value="60">60</option>
                            <option value="70">70</option>
                            <option value="80">80</option>
                            <option value="90" selected="selected">90</option>
                            <option value="100">100</option>
                            <option value="110">110</option>
                            <option value="120">120</option>
                            <option value="130">130</option>
                            <option value="140">140</option>
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <select class="form-control" name="win_sum[]">
                            <option value="49">49</option>
                            <option value="59">59</option>
                            <option value="69">69</option>
                            <option value="79">79</option>
                            <option value="89">89</option>
                            <option value="99">99</option>
                            <option value="109">109</option>
                            <option value="119" selected="selected">119</option>
                            <option value="129">129</option>
                            <option value="139">139</option>
                            <option value="183">183</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="win_odd_num" class="col-sm-3 control-label">尾数和</label>
                    <label class="checkbox-inline">
                      <input type="checkbox" id="filter_mantissaSum" name="filter_mantissaSum" value="1"> 尾数和
                    </label>
                    <div class="col-sm-3">
                        <select class="form-control" name="win_mantissa[]">
                            <?php for ($i=3; $i <=51 ; $i++) { ?>
                                <option value="<?php echo $i ?>" <?php echo $i==14 ? 'selected="selected"' : ""; ?>><?php echo $i ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <select class="form-control" name="win_mantissa[]">
                            <?php for ($i=14; $i <=51 ; $i++) { ?>
                                <option value="<?php echo $i ?>" <?php echo $i==34 ? 'selected="selected"' : ""; ?>><?php echo $i ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="win_odd_num" class="col-sm-3 control-label">首位跨度</label>
                    <label class="checkbox-inline">
                      <input type="checkbox" id="filter_difference" name="filter_difference" value="1"> 首位跨度
                    </label>
                    <div class="col-sm-3">
                        <select class="form-control" name="win_difference[]">
                            <?php for ($i=5; $i <=32 ; $i++) { ?>
                                <option value="<?php echo $i ?>" <?php echo $i==15 ? 'selected="selected"' : ""; ?>><?php echo $i ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <select class="form-control" name="win_difference[]">
                            <?php for ($i=15; $i <=32 ; $i++) { ?>
                                <option value="<?php echo $i ?>" <?php echo $i==32 ? 'selected="selected"' : ""; ?>><?php echo $i ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="win_odd_num" class="col-sm-3 control-label">尾数组数</label>
                    <label class="checkbox-inline">
                      <input type="checkbox" id="filter_mantissaGroup" name="filter_mantissaGroup" value="1"> 尾数组数
                    </label>
                    <div class="col-sm-3">
                        <select class="form-control" name="win_mantissaGroup[]">
                            <?php for ($i=2; $i <=6 ; $i++) { ?>
                                <option value="<?php echo $i ?>" <?php echo $i==3 ? 'selected="selected"' : ""; ?>><?php echo $i ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <select class="form-control" name="win_mantissaGroup[]">
                            <?php for ($i=3; $i <=6 ; $i++) { ?>
                                <option value="<?php echo $i ?>" <?php echo $i==6 ? 'selected="selected"' : ""; ?>><?php echo $i ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="form-group" style="display: none;">
                     <label for="chooseReds" class="col-sm-3 control-label">博主分析</label>
                    <div class="col-sm-8">
                        <textarea class="form-control" rows="8" id="comments" name="comments" ></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-3 col-sm-10">
                         <!-- <button type="submit" class="btn btn-default">开始智能分析</button> -->
                        <input name="cp_dayid" id="cp_dayid" value="<?php echo !empty($cp_dayid) ? $cp_dayid : ''; ?>" type="hidden">
                        <input name="chooseReds" id="lock_red_ball" value="" type="hidden">
                        <input name="lock_blue_ball" id="lock_blue_ball" value="" type="hidden">
                        <input name="lock_blue_length" id="lock_blue_length" value="" type="hidden">
                        <input name="lock_red_length" id="lock_red_length" value="" type="hidden">
                        <a class="weui-btn weui-btn_primary" href="javascript:;" id="btnFX">开始智能分析</a>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-6 column">
            <a class="weui-btn weui-btn_primary" href="javascript:;" id="btnFX">开始智能分析</a>
            <h4>过滤结果：<small class="tip"></small></h4>
            <p id="result" style="font-size: 18px;"></p>
            <!-- <ul>
                <li>
                    05 10 13 24 26 31+04
                </li>
            </ul> -->
        </div>
    </div>
</div>
</nav>

</body>

</html>
<script src="./ssq/jquery_002.js"></script>
<script src="./ssq/jquery.js"></script>
<script src="./ssq/bootstrap.js"></script>
<script src="./ssq/respond.js"></script>
<script src="./ssq/alertify.js"></script>
<script src="./ssq/matrix.js"></script>
<script>
    matrixFilter.init();
</script>
<script type="text/javascript">
$(function(){

//数据提交
$('#btnFX, #btnFX2').click(function(){
    var form_data = $('#ssqForm').serialize();
    // alert(form_data)
    $(".tip").html('智能分析中......');
    $("#result").empty();
    $.ajax({
        url: 'ajax/winning_do.php',
        type: 'post',
        dataType: 'html',
        data: form_data,
        async: true,
        success: function(data){
            // alert(data);
            $(".tip").html('<a href="release.php" target="_blank">预测数据发布</a>');
            $("#result").html(data);
        },
        error: function(e){
            alert(e);
        }
    })
})    
})
</script>
