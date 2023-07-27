<?php

require_once dirname(__FILE__) . '/../include/config.inc.php';
require_once dirname(__FILE__) . '/core/ssq.config.php';
require_once dirname(__FILE__) . '/core/choosered.func.php';
LoginCheck();


$redTail = red9Code();

$prekong = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
$kong = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
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
                <div class="form-group">
                    <label for="exampleInputEmail2">前N期</label>
                    <select class="form-control" name="num" id="num" onchange="gourl()">
                        <option value="">--请选择--</option>
                        <?php foreach (array(10, 30, 50, 100, 150) as $numtmp) { ?>
                            <option value="<?php echo $numtmp ?>" <?php echo isset($num) && $num == $numtmp ? 'selected' : '' ?>>前<?php echo $numtmp ?>期</option>
                        <?php } ?>
                    </select>
                </div>
            </form>

            <div class="clearfix"></div>
            <div class="bs-example" data-example-id="contextual-table">
                <table class="table">
                    <?php
                    $maxid = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` order by cp_dayid DESC");
                    $maxid = $maxid['cp_dayid'];

                    $cp_dayid = isset($cp_dayid) && !empty($cp_dayid) ? $cp_dayid : '';
                    $num = !empty($num) ? $num : 10;
                    $red9Code = red9Code($cp_dayid, $num);

                    $colors = array(
                        '0' => 'btn-default',
                        '1' => 'btn-primary',
                        '2' => 'btn-danger',
                        '3' => 'btn-warning',
                        '4' => 'btn-success',
                    );
                    ?>
                    <thead>
                        <tr class="primary">
                            <th colspan="5" style="text-align: center;">
                                <button class="btn btn-default" type="button">白色表示中0个</button>
                                <button class="btn btn-primary" type="button">蓝色表示中1个</button>
                                <button class="btn btn-danger" type="button">红色表示中2个</button>
                                <button class="btn btn-warning" type="button">橙色表示中3个</button>
                                <button class="btn btn-success" type="button">青色表示中4个</button>
                            </th>
                        </tr>
                        <tr class="primary">
                            <th></th>
                            <th></th>
                            <th>
                                <?php foreach (array(0, 2) as $k => $level) { ?>
                                    <?php echo $prekong
                                    ?>
                                    <?php echo isset($red9Code['9code'][3][$level]) ? $red9Code['9code'][3][$level] : ''; ?>
                                    <?php echo isset($red9Code['9code'][3][$level + 1]) ? $red9Code['9code'][3][$level + 1] : ''; ?>
                                    <?php echo $kong
                                    ?>
                                    <?php echo isset($red9Code['9code'][6][$level]) ? $red9Code['9code'][6][$level] : ''; ?>
                                    <?php echo isset($red9Code['9code'][6][$level + 1]) ? $red9Code['9code'][6][$level + 1] : ''; ?>
                                    <?php echo $kong
                                    ?>
                                    <?php echo isset($red9Code['9code'][9][$level]) ? $red9Code['9code'][9][$level] : ''; ?>
                                    <?php echo isset($red9Code['9code'][9][$level + 1]) ? $red9Code['9code'][9][$level + 1] : ''; ?>
                                    <?php if ($k != 3) { ?>
                                        <br />
                                    <?php } ?>
                                <?php } ?>
                            </th>
                            <th>
                                <?php foreach (array(0, 2) as $k => $level) { ?>
                                    <?php echo $prekong
                                    ?>
                                    <?php echo isset($red9Code['9code'][1][$level]) ? $red9Code['9code'][1][$level] : ''; ?>
                                    <?php echo isset($red9Code['9code'][1][$level + 1]) ? $red9Code['9code'][1][$level + 1] : ''; ?>
                                    <?php echo $kong
                                    ?>
                                    <?php echo isset($red9Code['9code'][4][$level]) ? $red9Code['9code'][4][$level] : ''; ?>
                                    <?php echo isset($red9Code['9code'][4][$level + 1]) ? $red9Code['9code'][4][$level + 1] : ''; ?>
                                    <?php echo $kong
                                    ?>
                                    <?php echo isset($red9Code['9code'][7][$level]) ? $red9Code['9code'][7][$level] : ''; ?>
                                    <?php echo isset($red9Code['9code'][7][$level + 1]) ? $red9Code['9code'][7][$level + 1] : ''; ?>
                                    <?php if ($k != 3) { ?>
                                        <br />
                                    <?php } ?>
                                <?php } ?>
                            </th>
                            <th>
                                <?php foreach (array(0, 2) as $k => $level) { ?>
                                    <?php echo $prekong
                                    ?>
                                    <?php echo isset($red9Code['9code'][2][$level]) ? $red9Code['9code'][2][$level] : ''; ?>
                                    <?php echo isset($red9Code['9code'][2][$level + 1]) ? $red9Code['9code'][2][$level + 1] : ''; ?>
                                    <?php echo $kong
                                    ?>
                                    <?php echo isset($red9Code['9code'][5][$level]) ? $red9Code['9code'][5][$level] : ''; ?>
                                    <?php echo isset($red9Code['9code'][5][$level + 1]) ? $red9Code['9code'][5][$level + 1] : ''; ?>
                                    <?php echo $kong
                                    ?>
                                    <?php echo isset($red9Code['9code'][8][$level]) ? $red9Code['9code'][8][$level] : ''; ?>
                                    <?php echo isset($red9Code['9code'][8][$level + 1]) ? $red9Code['9code'][8][$level + 1] : ''; ?>
                                    <?php if ($k != 3) { ?>
                                        <br />
                                    <?php } ?>
                                <?php } ?>
                            </th>
                        </tr>
                        <tr>
                            <th>期数</th>
                            <th>
                                码数
                            </th>
                            <th>
                                <button class="btn-tb btn btn-default" type="button">3</button>
                                <button class="btn-tb btn btn-default" type="button">6</button>
                                <button class="btn-tb btn btn-default" type="button">9</button>
                            </th>
                            <th>
                                <button class="btn-tb btn btn-default" type="button">1</button>
                                <button class="btn-tb btn btn-default" type="button">4</button>
                                <button class="btn-tb btn btn-default" type="button">7</button>
                            </th>
                            <th>
                                <button class="btn-tb btn btn-default" type="button">2</button>
                                <button class="btn-tb btn btn-default" type="button">5</button>
                                <button class="btn-tb btn btn-default" type="button">8</button>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($red9Code['list'] as $row) { ?>
                            <tr>
                                <td><a href="red_9code_choose.php?cp_dayid=<?php echo $row['cp_dayid'] ?>" target="_blank"><?php echo $row['cp_dayid'] ?></a></td>
                                <td>
                                    <?php
                                    foreach ($red9Code['9ma'] as $tmptail) {
                                        if ($row['code'][$tmptail]['num']) {
                                            echo '<button class="btn-sml btn ' . $colors[$row['code'][$tmptail]['num']] . '" type="button">' . $tmptail . '</button>';
                                        } else {
                                            echo '<button class="btn-sml btn btn-default" type="button">' . $tmptail . '</button>';
                                        }
                                    };
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    $data369 = array(3, 6, 9);
                                    foreach ($data369 as $tmptail) {
                                        if ($row['code'][$tmptail]['num']) {
                                            echo '<button class="btn-tb btn ' . $colors[$row['code'][$tmptail]['num']] . '" type="button">' . $tmptail . '<sup>(' . implode(".", $row['code'][$tmptail]['child']) . ')</sup></button>';
                                        } else {
                                            echo '<button class="btn-tb btn btn-default" type="button">' . $tmptail . '</button>';
                                        }
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    $data147 = array(1, 4, 7);
                                    foreach ($data147 as $tmptail) {
                                        if ($row['code'][$tmptail]['num']) {
                                            echo '<button class="btn-tb btn ' . $colors[$row['code'][$tmptail]['num']] . '" type="button">' . $tmptail . '<sup>(' . implode(".", $row['code'][$tmptail]['child']) . ')</sup></button>';
                                        } else {
                                            echo '<button class="btn-tb btn btn-default" type="button">' . $tmptail . '</button>';
                                        }
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    $data258 = array(2, 5, 8);
                                    foreach ($data258 as $tmptail) {
                                        if ($row['code'][$tmptail]['num']) {
                                            echo '<button class="btn-tb btn ' . $colors[$row['code'][$tmptail]['num']] . '" type="button">' . $tmptail . '<sup>(' . implode(".", $row['code'][$tmptail]['child']) . ')</sup></button>';
                                        } else {
                                            echo '<button class="btn-tb btn btn-default" type="button">' . $tmptail . '</button>';
                                        }
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php } ?>
                        <?php if (empty($cp_dayid)) { ?>
                            <?php for ($i = 0; $i < 5; $i++) { ?>
                                <tr>
                                    <td><?php echo $maxid + 1 ?></td>
                                    <td>
                                        <?php
                                        foreach ($red9Code['9ma'] as $tmptail) {
                                            echo '<button class="btn-sml btn btn-default win" type="button">' . $tmptail . '</button>';
                                        };
                                        ?>
                                    </td>
                                    <td>
                                        <button class="btn-tb btn btn-default win" type="button">3</button>
                                        <button class="btn-tb btn btn-default win" type="button">6</button>
                                        <button class="btn-tb btn btn-default win" type="button">9</button>
                                    </td>
                                    <td>
                                        <button class="btn-tb btn btn-default win" type="button">1</button>
                                        <button class="btn-tb btn btn-default win" type="button">4</button>
                                        <button class="btn-tb btn btn-default win" type="button">7</button>
                                    </td>
                                    <td>
                                        <button class="btn-tb btn btn-default win" type="button">2</button>
                                        <button class="btn-tb btn btn-default win" type="button">5</button>
                                        <button class="btn-tb btn btn-default win" type="button">8</button>
                                    </td>
                                </tr>
                            <?php } ?>
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
                $(this).addClass('btn-primary')
            } else if ($(this).hasClass('btn-primary')) {
                $(this).removeClass('btn-primary')
                $(this).addClass('btn-danger')
            } else if ($(this).hasClass('btn-danger')) {
                $(this).removeClass('btn-danger')
                $(this).addClass('btn-warning')
            } else if ($(this).hasClass('btn-warning')) {
                $(this).removeClass('btn-warning')
                $(this).addClass('btn-success')
            } else if ($(this).hasClass('btn-success')) {
                $(this).removeClass('btn-success')
                $(this).addClass('btn-default')
            }
        })
    })(window);
</script>

</html>