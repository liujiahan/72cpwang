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

$redsCfg = array(0, 1, 2, 3, 4, 5, 6);

?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>行列图智能过滤 - <?php echo $cfg_seotitle; ?></title>
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
    <div class="row clearfix" style="margin-top: 30px;">
        <div class="col-md-6 column">
            <form class="form-horizontal" id="ssqForm" role="form">
                <div class="bs-example" data-example-id="bordered-table">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th></th>
                                <th>第一列</th>
                                <th>第二列</th>
                                <th>第三列</th>
                                <th>第四列</th>
                                <th>第五列</th>
                                <th>第六列</th>
                                <th>行出球</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="row">第一行</th>
                                <td>01</td><td>02</td><td>03</td><td>04</td><td>05</td><td>06</td>
                                <td>
                                    <select class="form-control" name="row_win_num[1]">
                                        <?php foreach ($redsCfg as $num) { ?>
                                        <option value="<?php echo $num ?>" <?php echo $num == 1 ? 'selected="selected"' : ''; ?>><?php echo $num ?></option>
                                        <?php }  ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">第二行</th>
                                <td>07</td><td>08</td><td>09</td><td>10</td><td>11</td><td>12</td>
                                <td>
                                    <select class="form-control" name="row_win_num[2]">
                                        <?php foreach ($redsCfg as $num) { ?>
                                        <option value="<?php echo $num ?>" <?php echo $num == 1 ? 'selected="selected"' : ''; ?>><?php echo $num ?></option>
                                        <?php }  ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">第三行</th>
                                <td>13</td><td>14</td><td>15</td><td>16</td><td>17</td><td>18</td>
                                <td>
                                    <select class="form-control" name="row_win_num[3]">
                                        <?php foreach ($redsCfg as $num) { ?>
                                        <option value="<?php echo $num ?>" <?php echo $num == 1 ? 'selected="selected"' : ''; ?>><?php echo $num ?></option>
                                        <?php }  ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">第四行</th>
                                <td>19</td><td>20</td><td>21</td><td>22</td><td>23</td><td>24</td>
                                <td>
                                    <select class="form-control" name="row_win_num[4]">
                                        <?php foreach ($redsCfg as $num) { ?>
                                        <option value="<?php echo $num ?>" <?php echo $num == 1 ? 'selected="selected"' : ''; ?>><?php echo $num ?></option>
                                        <?php }  ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">第五行</th>
                                <td>25</td><td>26</td><td>27</td><td>28</td><td>29</td><td>30</td>
                                <td>
                                    <select class="form-control" name="row_win_num[5]">
                                        <?php foreach ($redsCfg as $num) { ?>
                                        <option value="<?php echo $num ?>" <?php echo $num == 1 ? 'selected="selected"' : ''; ?>><?php echo $num ?></option>
                                        <?php }  ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">第六行</th>
                                <td>31</td><td>32</td><td>33</td><td></td><td></td><td></td>
                                <td>
                                    <select class="form-control" name="row_win_num[6]">
                                        <?php foreach ($redsCfg as $num) { ?>
                                        <option value="<?php echo $num ?>" <?php echo $num == 1 ? 'selected="selected"' : ''; ?>><?php echo $num ?></option>
                                        <?php }  ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">列出球</th>
                                <td>
                                    <select class="form-control" name="col_win_num[1]">
                                        <?php foreach ($redsCfg as $num) { ?>
                                        <option value="<?php echo $num ?>" <?php echo $num == 1 ? 'selected="selected"' : ''; ?>><?php echo $num ?></option>
                                        <?php }  ?>
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control" name="col_win_num[2]">
                                        <?php foreach ($redsCfg as $num) { ?>
                                        <option value="<?php echo $num ?>" <?php echo $num == 1 ? 'selected="selected"' : ''; ?>><?php echo $num ?></option>
                                        <?php }  ?>
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control" name="col_win_num[3]">
                                        <?php foreach ($redsCfg as $num) { ?>
                                        <option value="<?php echo $num ?>" <?php echo $num == 1 ? 'selected="selected"' : ''; ?>><?php echo $num ?></option>
                                        <?php }  ?>
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control" name="col_win_num[4]">
                                        <?php foreach ($redsCfg as $num) { ?>
                                        <option value="<?php echo $num ?>" <?php echo $num == 1 ? 'selected="selected"' : ''; ?>><?php echo $num ?></option>
                                        <?php }  ?>
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control" name="col_win_num[5]">
                                        <?php foreach ($redsCfg as $num) { ?>
                                        <option value="<?php echo $num ?>" <?php echo $num == 1 ? 'selected="selected"' : ''; ?>><?php echo $num ?></option>
                                        <?php }  ?>
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control" name="col_win_num[6]">
                                        <?php foreach ($redsCfg as $num) { ?>
                                        <option value="<?php echo $num ?>" <?php echo $num == 1 ? 'selected="selected"' : ''; ?>><?php echo $num ?></option>
                                        <?php }  ?>
                                    </select>
                                </td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="form-group">
                     <label for="killReds" class="col-sm-3 control-label">杀号</label>
                    <div class="col-sm-8">
                        <textarea class="form-control" rows="1" id="killReds" name="killReds" ></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label for="win_odd_num" class="col-sm-3 control-label">只显示预测红球</label>
                    <label class="checkbox-inline">
                      <input type="checkbox" id="filter_ssq" name="filter_ssq" value="1"> 只显示预测红球
                    </label>
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
                    <label for="win_odd_num" class="col-sm-3 control-label">和数值</label>
                    <label class="checkbox-inline">
                      <input type="checkbox" id="filter_sum" name="filter_sum" value="1"> 和值
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
                    <label for="win_bignum" class="col-sm-3 control-label">大球出球数</label>
                    <label class="checkbox-inline">
                      <input type="checkbox" id="filter_bigball" name="filter_bigball" value="1"> 大号个数
                    </label>
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
<!-- <script src="./ssq/jquery_002.js"></script>
<script src="./ssq/jquery.js"></script>
<script src="./ssq/bootstrap.js"></script>
<script src="./ssq/respond.js"></script>
<script src="./ssq/alertify.js"></script>
<script src="./ssq/matrix.js"></script> -->
<script>
    // matrixFilter.init();
</script>
<script type="text/javascript">
$(function(){

//数据提交
$('#btnFX, #btnFX2').click(function(){
    var form_data = $('#ssqForm').serialize();
    $(".tip").html('智能分析中......');
    $("#result").empty();
    $.ajax({
        url: 'wininrc.php',
        type: 'post',
        dataType: 'html',
        data: form_data,
        async: true,
        success: function(data){
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
