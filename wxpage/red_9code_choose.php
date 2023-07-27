<?php

require_once dirname(__FILE__) . '/../include/config.inc.php';
require_once dirname(__FILE__) . '/core/ssq.config.php';
require_once dirname(__FILE__) . '/core/choosered.func.php';
LoginCheck();


$redTail = red9Code();

$prekong = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
$kong = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

$code3list = array(
    array('03', '06', '09', '01', '04', '07', '02', '05', '08'),
    array('12', '15', '18', '10', '13', '16', '11', '14', '17'),
    array('21', '24', '27', '19', '22', '25', '20', '23', '26'),
    array('30', '33', '&nbsp;', '28', '31', '&nbsp;', '29', '32', '&nbsp;'),
);
// echo "<pre>";
// print_r($redTail);
// die;

?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"> -->
    <title>红球9码数据规律 - <?php echo $cfg_seotitle; ?></title>
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
    <script type="text/javascript" src="bootstrap/js/jquery.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript">
        function gourl() {
            window.location.href = "?cp_dayid=" + $("#cp_dayid").val() + '&num=' + $("#num").val();
        }
    </script>
    <style>
        .btn-tb {
            width: 80px;
        }

        .btn-sml {
            width: 30px;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <?php include('navbar.php') ?>

            <form class="navbar-form navbar-left" role="search">
                <div class="form-group">
                    <label for="exampleInputEmail2">期数</label>
                    <select class="form-control" name="cp_dayid" id="cp_dayid" onchange="gourl()">
                        <option value="">--请选择--</option>
                        <?php foreach (getDaySel(30) as $t_cp_dayid => $cp_dayidtxt) { ?>
                            <option value="<?php echo $t_cp_dayid ?>" <?php echo isset($cp_dayid) && $t_cp_dayid == $cp_dayid ? 'selected' : '' ?>><?php echo $cp_dayidtxt ?></option>
                        <?php } ?>
                    </select>
                </div>
            </form>

            <div class="clearfix"></div>
            <div class="bs-example" data-example-id="contextual-table">
                <table class="table">
                    <?php
                    $maxid = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` order by cp_dayid DESC");

                    $cp_dayid = isset($cp_dayid) && !empty($cp_dayid) ? $cp_dayid : '';
                    $reds = array();
                    if ($cp_dayid) {
                        $win = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid='{$cp_dayid}'");
                        $reds = explode(',', $win['red_num']);
                    }
                    // print_r($reds);
                    // die;
                    ?>
                    <tbody>
                        <?php foreach ($code3list as $key => $list) { ?>
                            <tr>
                                <td style="width: 16%;"></td>
                                <td>
                                    <button class="btn-tb btn btn-<?php echo in_array($list[0], $reds) ? 'danger' : 'default'; ?> win" type="button"><?php echo $list[0] ?></button>
                                    <button class="btn-tb btn btn-<?php echo in_array($list[1], $reds) ? 'danger' : 'default'; ?> win" type="button"><?php echo $list[1] ?></button>
                                    <button class="btn-tb btn btn-<?php echo in_array($list[2], $reds) ? 'danger' : 'default'; ?> win" type="button"><?php echo $list[2] ?></button>
                                </td>
                                <td>
                                    <button class="btn-tb btn btn-<?php echo in_array($list[3], $reds) ? 'danger' : 'default'; ?> win" type="button"><?php echo $list[3] ?></button>
                                    <button class="btn-tb btn btn-<?php echo in_array($list[4], $reds) ? 'danger' : 'default'; ?> win" type="button"><?php echo $list[4] ?></button>
                                    <button class="btn-tb btn btn-<?php echo in_array($list[5], $reds) ? 'danger' : 'default'; ?> win" type="button"><?php echo $list[5] ?></button>
                                </td>
                                <td>
                                    <button class="btn-tb btn btn-<?php echo in_array($list[6], $reds) ? 'danger' : 'default'; ?> win" type="button"><?php echo $list[6] ?></button>
                                    <button class="btn-tb btn btn-<?php echo in_array($list[7], $reds) ? 'danger' : 'default'; ?> win" type="button"><?php echo $list[7] ?></button>
                                    <button class="btn-tb btn btn-<?php echo in_array($list[8], $reds) ? 'danger' : 'default'; ?> win" type="button"><?php echo $list[8] ?></button>
                                </td>
                                <td></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /.container-fluid -->
    </nav>
</body>
<script>
    (function(e) {
        $(".win").click(function() {
            if ($(this).hasClass('btn-default')) {
                $(this).removeClass('btn-default')
                $(this).addClass('btn-danger')
            } else {
                $(this).removeClass('btn-danger')
                $(this).addClass('btn-default')
            }
        })
    })(window);
</script>

</html>