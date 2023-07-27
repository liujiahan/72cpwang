<?php

require_once dirname(__FILE__).'/../include/config.inc.php';
require_once dirname(__FILE__).'/core/ssq.config.php';


LoginCheck();

$redSpace = array();

foreach (array(3, 4, 5, 6, 7, 8, 9, 10, 11) as $qujian) {
    if(!isset($redSpace[$qujian])){
        $redSpace[$qujian] = array();
    }
    $pg = ceil(33 / $qujian);
    for ($i=0; $i < $pg; $i++) {
        $left  = $i * $qujian + 1;
        $right = ($i+1) * $qujian > 33 ? 33 : ($i+1) * $qujian;
        $redSpace[$qujian][] = array($left, $right);
    }
}

?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>末位数字偏差追踪系统 - <?php echo $cfg_seotitle; ?></title>
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
    <script type="text/javascript" src="bootstrap/js/jquery.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript">
    function godayurl() {
        window.location.href = "?cp_dayid=" + $("#cp_dayid").val()+"&before_days=" + $("#before_days").val()+"&tailstr=" + $("#tailstr").val();
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
                        <option value="">--请选择--</option>
                        <?php foreach (getDaySel() as $daynum => $daytxt) { ?>
                        <option value="<?php echo $daynum ?>" <?php echo isset($cp_dayid) && $cp_dayid==$daynum ? 'selected' : '' ?>>
                            <?php echo $daytxt ?>
                        </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="exampleInputEmail2">近N期统计</label>
                    <select class="form-control" name="before_days" id="before_days" onchange="godayurl()">
                        <?php foreach (array(5=>'近5期', 6=>'近6期', 7=>'近7期', 8=>'近8期', 9=>'近9期', 10=>'近10期') as $b_days => $seltext) { ?>
                        <option value="<?php echo $b_days ?>" <?php echo isset($before_days) && $before_days==$b_days ? 'selected' : '' ?>>
                            <?php echo $seltext ?>
                        </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                  <label for="exampleInputEmail2">尾数组合</label>
                  <input type="text" class="form-control" name="tailstr" id="tailstr" style="width: 100px;" value="<?php echo isset($tailstr) ? $tailstr : ''; ?>">
                </div>
                <a href="javascript:;" class="btn btn-primary" onclick="godayurl()" role="button">查询</a>
            </form>
            <!-- <a href="javascript:;" class="btn btn-danger btn-sm active pull-right" id="duiJiangBtn" role="button">大小四区间</a> -->
            <div class="bs-example" data-example-id="contextual-table">
                <table class="table">
                    <thead>
                        <?php
                            $before_days = isset($before_days) && !empty($before_days) ? $before_days : 5;
                        ?>
                        <tr>
                            <th>尾数</th>
                            <th>尾数出球数量</th>
                            <th>近<?php echo $before_days ?>期开出的红球</th>
                            <th>本期开出红球</th>
                            <th><?php echo $before_days ?>期外开出红球</th>
                            <th>冷尾球</th>
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

                            $alltail = array();
                            for ($i=1; $i < 34; $i++) { 
                            	$i = $i<10 ? '0'.$i : $i;
                            	$tail = $i % 10;
                            	if(!isset($alltail[$tail])){
                            		$alltail[$tail] = array();
                            	}
                            	$alltail[$tail][] = $i;
                            }


                            if(isset($cp_dayid) && !empty($cp_dayid)){
                                $sql = "SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<$cp_dayid ORDER BY cp_dayid DESC LIMIT $before_days";
                            }else{
                                $sql = "SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid DESC LIMIT $before_days";
                            }

                            $dosql->Execute($sql);
                            $day_reds = array();
                            while($row = $dosql->GetArray()){
                                // $cp_dayid = $row['cp_dayid'];
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

                            $win_reds = array();
                            if(isset($cp_dayid) && !empty($cp_dayid)){
                                $sql = "SELECT * FROM `#@__caipiao_history` WHERE cp_dayid=$cp_dayid";
	                            $win_reds = $dosql->GetOne($sql);
	                            $win_reds = explode(',', $win_reds['red_num']);
                            }

                        ?>
                        <?php foreach ($weishu as $ws => $num) { ?>
                        <?php 
                        	if(!empty($tailstr) && false === strpos($tailstr, "$ws")) continue;
                        ?>
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
                            <td>
                                <?php foreach ($win_reds as $tmp_red) { ?>
	                                <?php if (in_array($tmp_red, $wsarrs[$ws])) { ?>
                                    <button class="btn btn-danger" type="button"><?php echo $tmp_red; ?></span></button>
	                                <?php } ?>
                                <?php } ?>
                            </td>
                            <td>
                                <?php foreach ($win_reds as $tmp_red) { ?>
                                    <?php if ($tmp_red % 10 == $ws && !in_array($tmp_red, $wsarrs[$ws])) { ?>
                                    <button class="btn btn-danger" type="button"><?php echo $tmp_red; ?></span></button>
                                    <?php } ?>
                                <?php } ?>
                            </td>
                            <td>
                                <?php 
                                $tailarr = $alltail[$ws];
                                $difftail = array_diff($tailarr, $wsarrs[$ws]);
                                foreach ($difftail as $tmp_red) { ?>
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
