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
    <title>热门数字 冷门数字偏差追踪系统 - <?php echo $cfg_seotitle; ?></title>
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
    <script type="text/javascript" src="bootstrap/js/jquery.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
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
            <?php $miss_count = getAvgMiss(); ?>

            <!-- <h1 class="text-center">投资<?php echo $payget['pay_num'] ?>元，回报<?php echo $payget['get_num'] ?>元，回报率<?php echo round($payget['get_num'] / $payget['pay_num'], 4)*100 . '%'; ?></h1> -->
            <!-- <p class="lead">历史平均总计值：<?php echo $miss_count['miss_avg']; ?>，平均遗漏：<?php echo $miss_count['lt4_miss_avg']; ?></p> -->
            <blockquote>
                <p>总计值和平均值越小，则当期中奖号码中的数字越热门。你可以从图表中看到：极大或极小的总计值会迅速向相反方向回复。</p>
                <p>历史平均总计值：<?php echo $miss_count['miss_avg']; ?>，平均遗漏：<?php echo $miss_count['lt4_miss_avg']; ?></p>
            </blockquote> 

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
                        <?php foreach (array(5=>'前5期', 6=>'前6期', 7=>'前7期', 8=>'前8期', 9=>'前9期', 10=>'前10期', 30=>'前30期') as $b_days => $seltext) { ?>
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
                            <th width="10%">期数</th>
                            <th width="10%">开奖日期</th>
                            <th width="20%">中奖号码</th>
                            <th width="20%">遗漏情况</th>
                            <th width="20%">遗漏<5次数字</th>
                            <th width="10%">总计</th>
                            <th width="10%">平均</th>
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
                                $cp_dayid = $row['cp_dayid'];
                                $red_num  = explode(',', $row['red_num']);
                                $missarr  = getCurMiss($cp_dayid);
                                $red_miss = array();
                                $miss_lt5 = 0;
                                foreach ($missarr['red_miss_arr'] as $tmp_red => $miss_num) {
                                    if(in_array($tmp_red, $red_num)){
                                        $red_miss[] = $miss_num;
                                        $miss_num <= 4 && $miss_lt5++;
                                    }
                                }

                        ?>
                        <tr class="<?php echo $row['id'] % 2 == 0 ? 'active' : 'info'; ?>">
                            <td><?php echo $row['cp_dayid'] ?></td>
                            <td><?php echo $row['cp_day'] ?></td>
                            <td><?php echo implode(' - ', $red_num) ?></td>
                            <td><?php echo implode(' - ', $red_miss) ?></td>
                            <td><?php echo $miss_lt5 ?></td>
                            <td><?php echo $missarr['miss_sum'] ?></td>
                            <td><?php echo round($missarr['miss_sum'] / 6, 1) ?></td>
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
