<?php
require_once dirname(__FILE__) . '/../include/config.inc.php';

LoginCheck();

$happy8ids = array();
$dosql->Execute("SELECT * FROM `#@__happy8_history` ORDER BY cp_dayid DESC LIMIT 30");
while ($row = $dosql->GetArray()) {
    $happy8ids[$row['cp_dayid']] = $row['cp_dayid'];
}

$cp_dayid = isset($cp_dayid) ? $cp_dayid : '';

$reds = array();
$row = 0;
for ($i = 1; $i <= 80; $i++) {
    $i = $i < 10 ? '0' . $i : $i;
    $num = $i % 3;
    if ($i <= 20) {
        $index = '20_' . $num;
    } else if ($i > 20 && $i <= 40) {
        $index = '40_' . $num;
    } else if ($i > 40 && $i <= 60) {
        $index = '60_' . $num;
    } else if ($i > 60 && $i <= 80) {
        $index = '80_' . $num;
    }
    if (!isset($reds[$index])) {
        $reds[$index] = array();
    }
    $reds[$index][] = $i;
}

?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"> -->
    <title><?php echo $cfg_seotitle; ?></title>
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="./ssq/matrix.css">
    <script type="text/javascript" src="bootstrap/js/jquery.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript">
        function godayurl() {
            window.location.href = "?cp_dayid=" + $("#cp_dayid").val();
        }
    </script>
    <script type="text/javascript" src="/static/js/baidutj.js"></script>
</head>

<body>
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <?php include('navbar.php') ?>

            <!-- <h1><small>玩转数字游戏，用科学的态度，运用数学的思维，玩玩玩。</small></h1>
        <blockquote>
            <p>先看冷热选范围，再看位置定号码。锁定篮球赢大奖，平常心态来日方长。</p>
        </blockquote> -->
            <form class="navbar-form navbar-left" role="search">
                <div class="form-group">
                    <label for="exampleInputEmail2">选择期数</label>
                    <select class="form-control" name="cp_dayid" id="cp_dayid" onchange="godayurl()">
                        <option value="">--请选择--</option>
                        <?php foreach ($happy8ids as $daynum => $daytxt) { ?>
                            <option value="<?php echo $daynum ?>" <?php echo isset($cp_dayid) && $cp_dayid == $daynum ? 'selected' : '' ?>><?php echo $daytxt ?></option>
                        <?php } ?>
                    </select>
                </div>
                <a href="javascript:;" class="btn btn-primary" onclick="gourl()" role="button">查询</a>
            </form>
            <div class="bs-example" data-example-id="contextual-table">
                <table class="table">
                    <thead>
                        <tr>
                            <th>期数</th>
                            <th>6区</th>
                            <th>6区</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM `#@__happy8_history` WHERE 1 ";
                        if (!empty($cp_dayid)) {
                            $sql .= " AND cp_dayid<=$cp_dayid order by cp_dayid desc limit 10";
                        } else {
                            $sql .= " ORDER BY cp_dayid DESC limit 10";
                        }

                        $dosql->Execute($sql);
                        while ($row = $dosql->GetArray()) {
                            $winBalls = explode(',', $row['opencode']);
                            $data[] = $row;
                            $id = $row['cp_dayid'];
                            $dosql->Execute("SELECT * FROM `#@__happy8_history` WHERE cp_dayid<=$id order by cp_dayid DESC LIMIT 20 ", 'count');

                            $i = 0;
                        ?>
                            <tr class="<?php echo $row['cp_dayid'] % 2 == 0 ? 'active' : '' ?>">
                                <td><?php echo $row['cp_dayid'] ?></td>
                                <td>
                                    <?php
                                    $indexs = array('20_1', '20_2', '20_0', '60_1', '60_2', '60_0');
                                    foreach ($reds as $index => $redlist) {
                                        if (!in_array($index, $indexs)) continue;
                                        foreach ($redlist as $ball) {
                                            $class = in_array($ball, $winBalls) ? 'red_ball active' : 'blue_ball';
                                            echo '<span class="' . $class . '">' . $ball . '</span>';
                                        }
                                        echo '<br/>';
                                    }

                                    ?>
                                </td>
                                <td>
                                    <?php
                                    $indexs = array('40_1', '40_2', '40_0', '80_1', '80_2', '80_0');
                                    foreach ($reds as $index => $redlist) {
                                        if (!in_array($index, $indexs)) continue;
                                        foreach ($redlist as $ball) {
                                            $class = in_array($ball, $winBalls) ? 'red_ball active' : 'blue_ball';
                                            echo '<span class="' . $class . '">' . $ball . '</span>';
                                        }
                                        echo '<br/>';
                                    }

                                    ?>
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