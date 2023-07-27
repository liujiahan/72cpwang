<?php

require_once dirname(__FILE__).'/../include/config.inc.php';
require_once dirname(__FILE__).'/core/suanfa.func.php';
require_once dirname(__FILE__).'/core/ssq.config.php';
require_once dirname(__FILE__) . '/core/choosered.func.php';
LoginCheck();

?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>超流行的 - <?php echo $cfg_seotitle; ?></title>
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

            <!-- <h1 class="text-center">投资<?php echo $payget['pay_num'] ?>元，回报<?php echo $payget['get_num'] ?>元，回报率<?php echo round($payget['get_num'] / $payget['pay_num'], 4)*100 . '%'; ?></h1> -->
            <!-- <p class="lead">历史平均总计值：<?php echo $miss_count['miss_avg']; ?>，平均遗漏：<?php echo $miss_count['lt4_miss_avg']; ?></p> -->
            <!-- <blockquote>
                <p>总计值和平均值越小，则当期中奖号码中的数字越热门。你可以从图表中看到：极大或极小的总计值会迅速向相反方向回复。</p>
                <p>历史平均总计值：<?php echo $miss_count['miss_avg']; ?>，平均遗漏：<?php echo $miss_count['lt4_miss_avg']; ?></p>
            </blockquote>  -->

            <form class="navbar-form navbar-left" role="search">
                <!-- <div class="form-group">
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
                        <option value="">--请选择--</option>
                        <?php foreach (array(5=>'前5期', 6=>'前6期', 7=>'前7期', 8=>'前8期', 9=>'前9期', 10=>'前10期') as $b_days => $seltext) { ?>
                        <option value="<?php echo $b_days ?>" <?php echo isset($before_days) && $before_days==$b_days ? 'selected' : '' ?>>
                            <?php echo $seltext ?>
                        </option>
                        <?php } ?>
                    </select>
                </div> -->
            </form>
            <!-- <a href="javascript:;" class="btn btn-danger btn-sm active pull-right" id="duiJiangBtn" role="button">大小四区间</a> -->
            <div class="bs-example" data-example-id="contextual-table">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="15%">期数</th>
                            <th width="20%">大底</th>
                            <th width="15%">预测</th>
                            <th width="20%">跑分</th>
                            <th width="30%">跑分</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                            $max = $dosql->GetOne("SELECT MAX(cp_dayid) maxid FROM `#@__caipiao_history`");
                            $maxid = $max['maxid'] + 1;

                            $dosql->Execute("SELECT * FROM `#@__caipiao_yuce` WHERE cp_dayid='$maxid' AND is_win=1", '123');

                            $data = array();
                            while($row = $dosql->GetArray('123')){
                                $data[] = $row;
                            }

                            shuffle($data);
                            foreach ($data as $key => $row) {
                                $reds = explode(" ", $row['reds']);
                                $blues = array('06', '07', '10');
                                $index = rand(0, 2);
                                $blue = $blues[$index];
                                $history_win = redHistoryWin($reds, $blue); 
                        ?>
                        <tr>
                            <td><?php echo $row['cp_dayid'] ?></td>
                            <td><?php echo $row['allreds'] ?></td>
                            <td><?php echo $row['reds'] . '+' . $blue ?></td>
                            <td><?php echo $history_win ?></td>
                            <td>谢谢您的支持，看彩72变回馈粉丝，特此精选12红球大底，经过程序智能化计算，为您精选一注：<?php echo $row['reds'] . '+' . $blue ?>，仅作参考，中与不中开心最重要！</td>
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
