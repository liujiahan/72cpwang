<?php

require_once dirname(__FILE__).'/../include/config.inc.php';
require_once dirname(__FILE__).'/core/suanfa.func.php';
require_once dirname(__FILE__).'/core/ssq.config.php';

LoginCheck();

?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>和值偏差追踪系统 - <?php echo $cfg_seotitle; ?></title>
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
    <script type="text/javascript" src="bootstrap/js/jquery.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript">
    function godayurl() {
        window.location.href = "?cp_dayid=" + $("#cp_dayid").val()+"&before_days=" + $("#before_days").val();
    }

    $(document).ready(function() {
        /*$("#duiJiangBtn").click(function() {
            $(this).html('计算中......');
            $.ajax({
                url: 'suanfa_do.php',
                dataType: 'html',
                type: 'post',
                data: {
                    action: 'location_cross'
                },
                success: function(data) {
                    window.location.reload();
                }
            })
        })*/
    })
    </script>
</head>

<body>
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <?php include('navbar.php') ?>

            <!-- <h1 class="text-center">投资<?php echo $payget['pay_num'] ?>元，回报<?php echo $payget['get_num'] ?>元，回报率<?php echo round($payget['get_num'] / $payget['pay_num'], 4)*100 . '%'; ?></h1> -->
            <!-- <p class="lead">历史平均总计值：<?php echo $miss_count['miss_avg']; ?>，平均遗漏：<?php echo $miss_count['lt4_miss_avg']; ?></p> -->
            <!-- <blockquote>
                <p>总计值和平均值越小，则当期中奖号码中的数字越热门。你可以从图表中看到：极大或极小的总计值会迅速向相反方向回复。</p>
                <p>历史平均总计值：<?php echo $miss_count['miss_avg']; ?>，平均遗漏：<?php echo $miss_count['lt4_miss_avg']; ?></p>
            </blockquote>  -->

            <form class="navbar-form navbar-left" role="search">
                <div class="form-group">
                    <label for="exampleInputEmail2">选择期数</label>
                    <select class="form-control" name="cp_dayid" id="cp_dayid" onchange="godayurl()">
                        <option value="">--请选择--</option>
                        <?php foreach (getDaySel() as $daynum => $daytxt) { ?>
                        <option value="<?php echo $daynum ?>" <?php echo isset($cp_dayid) && $cp_dayid==$daynum ? 'selected' : '' ?>>
                            <?php echo $daytxt ?>
                        </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="exampleInputEmail2">前N期统计</label>
                    <select class="form-control" name="before_days" id="before_days" onchange="godayurl()">
                        <?php foreach (array(5=>'前5期', 10=>'前10期', 15=>'前15期', 20=>'前20期', 50=>'前50期') as $b_days => $seltext) { ?>
                        <option value="<?php echo $b_days ?>" <?php echo isset($before_days) && $before_days==$b_days ? 'selected' : '' ?>>
                            <?php echo $seltext ?>
                        </option>
                        <?php } ?>
                    </select>
                </div>
            </form>
            <!-- <a href="javascript:;" class="btn btn-danger btn-sm active pull-right" id="duiJiangBtn" role="button">大小四区间</a> -->
            <div class="bs-example" data-example-id="contextual-table">
                <table class="table">
                    <thead>
                        <tr>
                            <th colspan="2"></th>
                            <th colspan="4"><br/>最小有效和数值21</th>
                            <th colspan="4"><br/>平均和数值102</th>
                            <th colspan="4"><br/>最大有效和数值183</th>
                            <th colspan="2"></th>
                        </tr>
                        <tr>
                            <th width="10%">期数</th>
                            <th width="6%">和数值</th>
                            <th width="6%">21-50</th>
                            <th width="6%">51-60</th>
                            <th width="6%">61-70</th>
                            <th width="6%">71-80</th>
                            <th width="6%">81-90</th>
                            <th width="6%">91-101</th>
                            <th width="6%">102</th>
                            <th width="6%">103-120</th>
                            <th width="6%">121-130</th>
                            <th width="6%">131-140</th>
                            <th width="6%">141-150</th>
                            <th width="6%">151-160</th>
                            <th width="6%">161-170</th>
                            <th width="6%">171-183</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                            $before_days = isset($before_days) && !empty($before_days) ? $before_days : 5;

                            if(isset($cp_dayid) && !empty($cp_dayid)){
                                $sql = "SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<$cp_dayid ORDER BY cp_dayid DESC LIMIT $before_days";
                            }else{
                                $sql = "SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid DESC LIMIT $before_days";
                            }

                            $dosql->Execute($sql);
                            $day_reds = array();
                            while($row = $dosql->GetArray()){
                                $day_reds[] = $row;
                            }
                            $day_reds = array_reverse($day_reds);
                            foreach ($day_reds as $row) {
                                $red_num = explode(',', $row['red_num']);
                                $sumvalue = array_sum($red_num);



                        ?>
                        <tr class="<?php echo $row['id'] % 2 == 0 ? 'active' : 'info'; ?>">
                            <td><?php echo $row['cp_dayid'] ?></td>
                            <td><?php echo $sumvalue ?></td>

                            <td><?php echo $sumvalue <= 50 ? '########' : '------------'; ?></td>
                            <td><?php echo $sumvalue <= 60 ? '########' : '------------'; ?></td>
                            <td><?php echo $sumvalue <= 70 ? '########' : '------------'; ?></td>
                            <td><?php echo $sumvalue <= 80 ? '########' : '------------'; ?></td>
                            <td><?php echo $sumvalue <= 90 ? '########' : '------------'; ?></td>
                            <td><?php echo $sumvalue <= 101 ? '########' : '------------'; ?></td>
                            <td><?php echo $sumvalue == 102 ? '########' : '|------|' ?></td>
                            <td><?php echo $sumvalue >= 103 ? '########' : '------------'; ?></td>
                            <td><?php echo $sumvalue >= 121 ? '########' : '------------'; ?></td>
                            <td><?php echo $sumvalue >= 131 ? '########' : '------------'; ?></td>
                            <td><?php echo $sumvalue >= 141 ? '########' : '------------'; ?></td>
                            <td><?php echo $sumvalue >= 151 ? '########' : '------------'; ?></td>
                            <td><?php echo $sumvalue >= 161 ? '########' : '------------'; ?></td>
                            <td><?php echo $sumvalue >= 171 ? '########' : '------------'; ?></td>
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
