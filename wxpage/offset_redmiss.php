<?php

require_once dirname(__FILE__).'/../include/config.inc.php';
require_once dirname(__FILE__).'/core/ssq.config.php';
LoginCheck();

?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>遗漏数字偏差追踪系统 - <?php echo $cfg_seotitle; ?></title>
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
            <form class="navbar-form navbar-left" role="search">
                <div class="form-group">
                    <label for="exampleInputEmail2">选择期数</label>
                    <select class="form-control" name="cp_dayid" id="cp_dayid" onchange="godayurl()">
                        <!-- <option value="">--请选择--</option> -->
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
                        <?php foreach (array(5=>'前5期', 6=>'前6期', 7=>'前7期', 8=>'前8期', 9=>'前9期', 10=>'前10期') as $b_days => $seltext) { ?>
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
                            <th width="20%">尾数</th>
                            <th width="30%">尾数出球排序</th>
                            <th width="50%">数字</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                            $weishu = array();
                            $wsarrs = array();
                            for ($i=0; $i < 10; $i++) { 
                                $wsarrs[$i] = array();
                                $weishu[$i] = 0;
                            }

                            $before_days = isset($before_days) ? $before_days : 5;
                            $cp_dayid = isset($cp_dayid) ? $cp_dayid : 2017045;

                            if(isset($cp_dayid) && !empty($cp_dayid)){
                                $sql = "SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<$cp_dayid ORDER BY cp_dayid DESC LIMIT $before_days";
                            }else{
                                $sql = "SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid DESC LIMIT $before_days";
                            }

                            $dosql->Execute($sql);
                            $day_reds = array();
                            while($row = $dosql->GetArray()){
                                $cp_dayid = $row['cp_dayid'];
                                $red_num  = explode(',', $row['red_num']);
                                foreach ($red_num as $tmp_red) {
                                    $tmp_ws = $tmp_red % 10;
                                    $weishu[$tmp_ws]++;
                                    if(!in_array($tmp_red, $wsarrs[$tmp_ws])){
                                        $wsarrs[$tmp_ws][] = $tmp_red;
                                    }
                                }
                            }
                            asort($weishu);
                        ?>
                        <?php foreach ($weishu as $ws => $num) { ?>
                        <tr>
                            <td>尾数 <?php echo $ws ?></td>
                            <td>
                                <button class="btn btn-info" type="button"><?php echo $num; ?></span></button>
                            </td>
                            <td>
                                <?php foreach ($wsarrs[$ws] as $tmp_red) { ?>
                                    <button class="btn btn-primary" type="button"><?php echo $tmp_red; ?></span></button>
                                <?php } ?>
                            </td>
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
