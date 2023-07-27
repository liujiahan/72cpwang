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
    if ($i > 1 && $i % 10 == 1) {
        $row++;
    }
    if (!isset($reds[$row])) {
        $reds[$row] = array();
    }
    $reds[$row][] = $i;
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
                            <th>开奖号码</th>
                            <th>期数</th>
                            <th>开奖号码</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM `#@__happy8_history` WHERE 1 ";
                        if (!empty($cp_dayid)) {
                            $sql .= " AND cp_dayid<=$cp_dayid order by cp_dayid desc limit 4";
                        } else {
                            $sql .= " ORDER BY cp_dayid DESC limit 4";
                        }

                        $dosql->Execute($sql);

                        $data = array();
                        while ($row = $dosql->GetArray()) {
                            $data[] = $row;
                        }
                        $data = array_reverse($data);

                        // $data = $dosql->GetOne($sql);
                        // $winreds = explode(',', $data['opencode']);
                        // print_r($winreds);
                        $i = 0;
                        ?>
                        <tr class="active">
                            <td><?php echo $data[0]['cp_dayid'] ?><br /></td>
                            <td>
                                <?php
                                $winreds = explode(',', $data[0]['opencode']);
                                // $reds = explode(',', $row['red_num']);
                                foreach ($reds as $hang => $redArr) {
                                    foreach ($redArr as $ball) {
                                        $class = in_array($ball, $winreds) ? 'red_ball active' : 'blue_ball';
                                        echo '<span class="' . $class . '">' . $ball . '</span>';
                                    }
                                    echo '<br/>';
                                }

                                ?>
                            </td>
                            <td><?php echo $data[1]['cp_dayid'] ?><br /></td>
                            <td>
                                <?php
                                $winreds = explode(',', $data[1]['opencode']);
                                foreach ($reds as $hang => $redArr) {
                                    foreach ($redArr as $ball) {
                                        $class = in_array($ball, $winreds) ? 'red_ball active' : 'blue_ball';
                                        echo '<span class="' . $class . '">' . $ball . '</span>';
                                    }
                                    echo '<br/>';
                                }

                                ?>
                            </td>
                            <!-- <td><?php echo str_replace(",", '.', $data['opencode']) ?></td> -->
                        </tr>
                        <tr class="active">
                            <td><?php echo $data[2]['cp_dayid'] ?><br /></td>
                            <td>
                                <?php
                                $winreds = explode(',', $data[2]['opencode']);
                                // $reds = explode(',', $row['red_num']);
                                foreach ($reds as $hang => $redArr) {
                                    foreach ($redArr as $ball) {
                                        $class = in_array($ball, $winreds) ? 'red_ball active' : 'blue_ball';
                                        echo '<span class="' . $class . '">' . $ball . '</span>';
                                    }
                                    echo '<br/>';
                                }

                                ?>
                            </td>
                            <td><?php echo $data[3]['cp_dayid'] ?><br /></td>
                            <td>
                                <?php
                                $winreds = explode(',', $data[3]['opencode']);
                                foreach ($reds as $hang => $redArr) {
                                    foreach ($redArr as $ball) {
                                        $class = in_array($ball, $winreds) ? 'red_ball active' : 'blue_ball';
                                        echo '<span class="' . $class . '">' . $ball . '</span>';
                                    }
                                    echo '<br/>';
                                }

                                ?>
                            </td>
                            <!-- <td><?php echo str_replace(",", '.', $data['opencode']) ?></td> -->
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /.container-fluid -->
    </nav>
</body>

</html>