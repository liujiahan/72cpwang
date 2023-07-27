<?php

require_once dirname(__FILE__).'/../include/config.inc.php';
require_once dirname(__FILE__).'/core/ssq.config.php';

LoginCheck();

$redSpace = array();

foreach (array(3, 4, 5, 6, 7, 8, 9, 10) as $qujian) {
    if(!isset($redSpace[$qujian])){
        $redSpace[$qujian] = array();
    }
    $pg = ceil(33 / $qujian);
    for ($i=0; $i < $pg; $i++) {
        $left  = $i * $qujian + 1;
        $right = ($i+1) * $qujian > 33 ? 33 : ($i+1) * $qujian;
        if(($i+1) * $qujian > 33){
        	$right = 33;
        }else{
        	$right = ($i+1) * $qujian;
	        // if(($qujian == 4 || $qujian == 8) && $i == $pg - 1){
	        // 	$right = $right + 1;
	        // 	$redSpace[$qujian][] = array($left, $right);
	        // 	continue;
	        // }
        }

        $redSpace[$qujian][] = array($left, $right);
    }
}


?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>奇偶大小区间偏差追踪系统 - <?php echo $cfg_seotitle; ?></title>
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
    <script type="text/javascript" src="bootstrap/js/jquery.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript">
    function godayurl() {
        window.location.href = "?cp_dayid=" + $("#cp_dayid").val();
    }

    function showspace(){
        var space_num = $('#space_num').val();
        $('.space_table').each(function(){
            var t_space = $(this).data('space');
            if(t_space == space_num){
                $(this).show();
            }else{
                $(this).hide();
            }
        })
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
                        <?php foreach (getDaySel() as $tmp_cp_dayid => $daytxt) { ?>
                        <option value="<?php echo $tmp_cp_dayid ?>" <?php echo isset($cp_dayid) && $cp_dayid==$tmp_cp_dayid ? 'selected' : '' ?>>
                            <?php echo $daytxt ?>
                        </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="exampleInputEmail2">查看区间偏差</label>
                    <select class="form-control" name="space_num" id="space_num" onchange="showspace()">
                        <option value="">--请选择--</option>
                        <?php foreach (array(3=>'3区间', 4=>'4区间', 5=>'5区间', 6=>'6区间', 7=>'7区间', 8=>'8区间', 9=>'9区间', 10=>'10区间') as $s_num => $s_txt) { ?>
                        <option value="<?php echo $s_num ?>" <?php echo isset($space_num) && $space_num==$s_num ? 'selected' : '' ?>>
                            <?php echo $s_txt ?>
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
                            <th width="15%">期数</th>
                            <th width="15%">奇数</th>
                            <th width="15%">偶数</th>
                            <th width="15%">奇偶偏差</th>
                            <th width="15%">大数</th>
                            <th width="15%">小数</th>
                            <th width="15%">大小偏差</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $dosql->Execute("SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid DESC LIMIT 10");
                            if(isset($cp_dayid) && !empty($cp_dayid)){
                                $dosql->Execute("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<$cp_dayid ORDER BY cp_dayid DESC LIMIT 10");
                            }
                            $redBalls05 = array();
                            $redBalls10 = array();
                            while($row = $dosql->GetArray()){
                                $redBalls10[] = explode(',', $row['red_num']);
                            }
                            $redBalls05 = array_slice($redBalls10, 0, 5);

                            $arrInfo05 = array('odd'=>0, 'even'=>0, 'big'=>0, 'small'=>0);
                            $arrInfo10 = array('odd'=>0, 'even'=>0, 'big'=>0, 'small'=>0);
                            foreach ($redBalls05 as $reds) {
                                foreach ($reds as $red) {
                                    $red % 2 == 1 && $arrInfo05['odd']++;
                                    $red % 2 == 0 && $arrInfo05['even']++;
                                    $red > 16 && $arrInfo05['big']++;
                                    $red < 17 && $arrInfo05['small']++;
                                }
                            }
                            foreach ($redBalls10 as $reds) {
                                foreach ($reds as $red) {
                                    $red % 2 == 1 && $arrInfo10['odd']++;
                                    $red % 2 == 0 && $arrInfo10['even']++;
                                    $red > 16 && $arrInfo10['big']++;
                                    $red < 17 && $arrInfo10['small']++;
                                }
                            }
                            $arrInfo05['odd'] - $arrInfo05['even'] >= 0 && $arrInfo05['odd_even'] = '奇+'.($arrInfo05['odd'] - $arrInfo05['even']);
                            $arrInfo05['odd'] - $arrInfo05['even'] < 0 && $arrInfo05['odd_even'] = '偶+'.($arrInfo05['even'] - $arrInfo05['odd']);
                            $arrInfo05['big'] - $arrInfo05['small'] >= 0 && $arrInfo05['big_small'] = '大+'.($arrInfo05['big'] - $arrInfo05['small']);
                            $arrInfo05['big'] - $arrInfo05['small'] < 0 && $arrInfo05['big_small'] = '小+'.($arrInfo05['small'] - $arrInfo05['big']);

                            $arrInfo10['odd'] - $arrInfo10['even'] >= 0 && $arrInfo10['odd_even'] = '奇+'.($arrInfo10['odd'] - $arrInfo10['even']);
                            $arrInfo10['odd'] - $arrInfo10['even'] < 0 && $arrInfo10['odd_even'] = '偶+'.($arrInfo10['even'] - $arrInfo10['odd']);
                            $arrInfo10['big'] - $arrInfo10['small'] >= 0 && $arrInfo10['big_small'] = '大+'.($arrInfo10['big'] - $arrInfo10['small']);
                            $arrInfo10['big'] - $arrInfo10['small'] < 0 && $arrInfo10['big_small'] = '小+'.($arrInfo10['small'] - $arrInfo10['big']);
                        ?>
                        <tr class="info">
                            <td>前5期</td>
                            <td><?php echo $arrInfo05['odd'] ?></td>
                            <td><?php echo $arrInfo05['even'] ?></td>
                            <td><?php echo $arrInfo05['odd_even'] ?></td>
                            <td><?php echo $arrInfo05['big'] ?></td>
                            <td><?php echo $arrInfo05['small'] ?></td>
                            <td><?php echo $arrInfo05['big_small'] ?></td>
                        </tr>
                        <tr class="active">
                            <td>前10期</td>
                            <td><?php echo $arrInfo10['odd'] ?></td>
                            <td><?php echo $arrInfo10['even'] ?></td>
                            <td><?php echo $arrInfo10['odd_even'] ?></td>
                            <td><?php echo $arrInfo10['big'] ?></td>
                            <td><?php echo $arrInfo10['small'] ?></td>
                            <td><?php echo $arrInfo10['big_small'] ?></td>
                        </tr>
                    </tbody>
                </table>

                <table class="table space_table" data-space="3">
                    <thead>
                        <tr>
                            <th width="10%">3区间</th>
                            <th width="8%">前10期</th>
                            <th width="8%">前9期</th>
                            <th width="8%">前8期</th>
                            <th width="8%">前7期</th>
                            <th width="8%">前6期</th>
                            <th width="8%">前5期</th>
                            <th width="8%">前4期</th>
                            <th width="8%">前3期</th>
                            <th width="8%">前2期</th>
                            <th width="8%">前1期</th>
                            <th width="10%">遗漏次数</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 

                            $redBalls10 = array_reverse($redBalls10);

                            $row3space = array();
                            foreach ($redSpace[3] as $k => $num_space) {
                                $row3space[$k][] = implode(' - ', $num_space);
                                foreach ($redBalls10 as $reds) {
                                    $win_num = 0;
                                    foreach ($reds as $red) {
                                        if($red >= $num_space[0] && $red <= $num_space[1]){
                                            $win_num++;
                                        }
                                    }
                                    $row3space[$k][] = $win_num;
                                }
                            }
                        ?>
                        <?php foreach ($row3space as $k => $rows) { ?>
                        <tr class="<?php echo $k % 2 == 1 ? 'active' : 'info'; ?>">
                            <?php 
                                $miss_num = 0;
                                foreach ($rows as $v) { ?>
                                <td><?php echo $v == 0 ? '-' : $v; ?></td>
                            <?php 
                                $v == 0 && $miss_num++;
                            } ?>
                            <td>
                                <button class="btn btn-primary" type="button"><?php echo $miss_num; ?></span></button>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <table class="table space_table" data-space="4" style="display: none;">
                    <thead>
                        <tr>
                            <th width="10%">4区间</th>
                            <th width="8%">前10期</th>
                            <th width="8%">前9期</th>
                            <th width="8%">前8期</th>
                            <th width="8%">前7期</th>
                            <th width="8%">前6期</th>
                            <th width="8%">前5期</th>
                            <th width="8%">前4期</th>
                            <th width="8%">前3期</th>
                            <th width="8%">前2期</th>
                            <th width="8%">前1期</th>
                            <th width="10%">遗漏次数</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 

                            $row4space = array();
                            foreach ($redSpace[4] as $k => $num_space) {
                                $row4space[$k][] = implode(' - ', $num_space);
                                foreach ($redBalls10 as $reds) {
                                    $win_num = 0;
                                    foreach ($reds as $red) {
                                        if($red >= $num_space[0] && $red <= $num_space[1]){
                                            $win_num++;
                                        }
                                    }
                                    $row4space[$k][] = $win_num;
                                }
                            }
                        ?>
                        <?php foreach ($row4space as $k => $rows) { ?>
                        <tr class="<?php echo $k % 2 == 1 ? 'active' : 'info'; ?>">
                            <?php 
                                $miss_num = 0;
                                foreach ($rows as $v) { ?>
                                <td><?php echo $v == 0 ? '-' : $v; ?></td>
                            <?php 
                                $v == 0 && $miss_num++;
                            } ?>
                            <td>
                                <button class="btn btn-primary" type="button"><?php echo $miss_num; ?></span></button>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <table class="table space_table" data-space="5" style="display: none;">
                    <thead>
                        <tr>
                            <th width="10%">5区间</th>
                            <th width="8%">前10期</th>
                            <th width="8%">前9期</th>
                            <th width="8%">前8期</th>
                            <th width="8%">前7期</th>
                            <th width="8%">前6期</th>
                            <th width="8%">前5期</th>
                            <th width="8%">前4期</th>
                            <th width="8%">前3期</th>
                            <th width="8%">前2期</th>
                            <th width="8%">前1期</th>
                            <th width="10%">遗漏次数</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 

                            $row5space = array();
                            foreach ($redSpace[5] as $k => $num_space) {
                                $row5space[$k][] = implode(' - ', $num_space);
                                foreach ($redBalls10 as $reds) {
                                    $win_num = 0;
                                    foreach ($reds as $red) {
                                        if($red >= $num_space[0] && $red <= $num_space[1]){
                                            $win_num++;
                                        }
                                    }
                                    $row5space[$k][] = $win_num;
                                }
                            }
                        ?>
                        <?php foreach ($row5space as $k => $rows) { ?>
                        <tr class="<?php echo $k % 2 == 1 ? 'active' : 'info'; ?>">
                            <?php 
                                $miss_num = 0;
                                foreach ($rows as $v) { ?>
                                <td><?php echo $v == 0 ? '-' : $v; ?></td>
                            <?php 
                                $v == 0 && $miss_num++;
                            } ?>
                            <td>
                                <button class="btn btn-primary" type="button"><?php echo $miss_num; ?></span></button>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <table class="table  space_table" data-space="6" style="display: none;">
                    <thead>
                        <tr>
                            <th width="10%">6区间</th>
                            <th width="8%">前10期</th>
                            <th width="8%">前9期</th>
                            <th width="8%">前8期</th>
                            <th width="8%">前7期</th>
                            <th width="8%">前6期</th>
                            <th width="8%">前5期</th>
                            <th width="8%">前4期</th>
                            <th width="8%">前3期</th>
                            <th width="8%">前2期</th>
                            <th width="8%">前1期</th>
                            <th width="10%">遗漏次数</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 

                            $row6space = array();
                            foreach ($redSpace[6] as $k => $num_space) {
                                $row6space[$k][] = implode(' - ', $num_space);
                                foreach ($redBalls10 as $reds) {
                                    $win_num = 0;
                                    foreach ($reds as $red) {
                                        if($red >= $num_space[0] && $red <= $num_space[1]){
                                            $win_num++;
                                        }
                                    }
                                    $row6space[$k][] = $win_num;
                                }
                            }
                        ?>
                        <?php foreach ($row6space as $k => $rows) { ?>
                        <tr class="<?php echo $k % 2 == 1 ? 'active' : 'info'; ?>">
                            <?php 
                                $miss_num = 0;
                                foreach ($rows as $v) { ?>
                                <td><?php echo $v == 0 ? '-' : $v; ?></td>
                            <?php 
                                $v == 0 && $miss_num++;
                            } ?>
                            <td>
                                <button class="btn btn-primary" type="button"><?php echo $miss_num; ?></span></button>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <table class="table  space_table" data-space="7" style="display: none;">
                    <thead>
                        <tr>
                            <th width="10%">7区间</th>
                            <th width="8%">前10期</th>
                            <th width="8%">前9期</th>
                            <th width="8%">前8期</th>
                            <th width="8%">前7期</th>
                            <th width="8%">前6期</th>
                            <th width="8%">前5期</th>
                            <th width="8%">前4期</th>
                            <th width="8%">前3期</th>
                            <th width="8%">前2期</th>
                            <th width="8%">前1期</th>
                            <th width="10%">遗漏次数</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 

                            $row7space = array();
                            foreach ($redSpace[7] as $k => $num_space) {
                                $row7space[$k][] = implode(' - ', $num_space);
                                foreach ($redBalls10 as $reds) {
                                    $win_num = 0;
                                    foreach ($reds as $red) {
                                        if($red >= $num_space[0] && $red <= $num_space[1]){
                                            $win_num++;
                                        }
                                    }
                                    $row7space[$k][] = $win_num;
                                }
                            }
                        ?>
                        <?php foreach ($row7space as $k => $rows) { ?>
                        <tr class="<?php echo $k % 2 == 1 ? 'active' : 'info'; ?>">
                            <?php 
                                $miss_num = 0;
                                foreach ($rows as $v) { ?>
                                <td><?php echo $v == 0 ? '-' : $v; ?></td>
                            <?php 
                                $v == 0 && $miss_num++;
                            } ?>
                            <td>
                                <button class="btn btn-primary" type="button"><?php echo $miss_num; ?></span></button>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <table class="table  space_table" data-space="8" style="display: none;">
                    <thead>
                        <tr>
                            <th width="10%">8区间</th>
                            <th width="8%">前10期</th>
                            <th width="8%">前9期</th>
                            <th width="8%">前8期</th>
                            <th width="8%">前7期</th>
                            <th width="8%">前6期</th>
                            <th width="8%">前5期</th>
                            <th width="8%">前4期</th>
                            <th width="8%">前3期</th>
                            <th width="8%">前2期</th>
                            <th width="8%">前1期</th>
                            <th width="10%">遗漏次数</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 

                            $row8space = array();
                            foreach ($redSpace[8] as $k => $num_space) {
                                $row8space[$k][] = implode(' - ', $num_space);
                                foreach ($redBalls10 as $reds) {
                                    $win_num = 0;
                                    foreach ($reds as $red) {
                                        if($red >= $num_space[0] && $red <= $num_space[1]){
                                            $win_num++;
                                        }
                                    }
                                    $row8space[$k][] = $win_num;
                                }
                            }
                        ?>
                        <?php foreach ($row8space as $k => $rows) { ?>
                        <tr class="<?php echo $k % 2 == 1 ? 'active' : 'info'; ?>">
                            <?php 
                                $miss_num = 0;
                                foreach ($rows as $v) { ?>
                                <td><?php echo $v == 0 ? '-' : $v; ?></td>
                            <?php 
                                $v == 0 && $miss_num++;
                            } ?>
                            <td>
                                <button class="btn btn-primary" type="button"><?php echo $miss_num; ?></span></button>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <table class="table  space_table" data-space="9" style="display: none;">
                    <thead>
                        <tr>
                            <th width="10%">9区间</th>
                            <th width="8%">前10期</th>
                            <th width="8%">前9期</th>
                            <th width="8%">前8期</th>
                            <th width="8%">前7期</th>
                            <th width="8%">前6期</th>
                            <th width="8%">前5期</th>
                            <th width="8%">前4期</th>
                            <th width="8%">前3期</th>
                            <th width="8%">前2期</th>
                            <th width="8%">前1期</th>
                            <th width="10%">遗漏次数</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 

                            $row9space = array();
                            foreach ($redSpace[9] as $k => $num_space) {
                                $row9space[$k][] = implode(' - ', $num_space);
                                foreach ($redBalls10 as $reds) {
                                    $win_num = 0;
                                    foreach ($reds as $red) {
                                        if($red >= $num_space[0] && $red <= $num_space[1]){
                                            $win_num++;
                                        }
                                    }
                                    $row9space[$k][] = $win_num;
                                }
                            }
                        ?>
                        <?php foreach ($row9space as $k => $rows) { ?>
                        <tr class="<?php echo $k % 2 == 1 ? 'active' : 'info'; ?>">
                            <?php 
                                $miss_num = 0;
                                foreach ($rows as $v) { ?>
                                <td><?php echo $v == 0 ? '-' : $v; ?></td>
                            <?php 
                                $v == 0 && $miss_num++;
                            } ?>
                            <td>
                                <button class="btn btn-primary" type="button"><?php echo $miss_num; ?></span></button>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <table class="table  space_table" data-space="10" style="display: none;">
                    <thead>
                        <tr>
                            <th width="10%">10区间</th>
                            <th width="8%">前10期</th>
                            <th width="8%">前9期</th>
                            <th width="8%">前8期</th>
                            <th width="8%">前7期</th>
                            <th width="8%">前6期</th>
                            <th width="8%">前5期</th>
                            <th width="8%">前4期</th>
                            <th width="8%">前3期</th>
                            <th width="8%">前2期</th>
                            <th width="8%">前1期</th>
                            <th width="10%">遗漏次数</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 

                            $row10space = array();
                            foreach ($redSpace[10] as $k => $num_space) {
                                $row10space[$k][] = implode(' - ', $num_space);
                                foreach ($redBalls10 as $reds) {
                                    $win_num = 0;
                                    foreach ($reds as $red) {
                                        if($red >= $num_space[0] && $red <= $num_space[1]){
                                            $win_num++;
                                        }
                                    }
                                    $row10space[$k][] = $win_num;
                                }
                            }
                        ?>
                        <?php foreach ($row10space as $k => $rows) { ?>
                        <tr class="<?php echo $k % 2 == 1 ? 'active' : 'info'; ?>">
                            <?php 
                                $miss_num = 0;
                                foreach ($rows as $v) { ?>
                                <td><?php echo $v == 0 ? '-' : $v; ?></td>
                            <?php 
                                $v == 0 && $miss_num++;
                            } ?>
                            <td>
                                <button class="btn btn-primary" type="button"><?php echo $miss_num; ?></span></button>
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
