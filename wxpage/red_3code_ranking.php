<?php
require_once dirname(__FILE__) . '/../include/config.inc.php';
LoginCheck();

$max = $dosql->GetOne("SELECT max(cp_dayid) as cp_dayid FROM `#@__caipiao_history`");
$filename = SNRUNNING_DATA . '/3code_ranking.json';

$bulid = 0;

$missWinCount = array();
if (file_exists($filename)) {
    $data = file_get_contents($filename);
    if (!isset($data['cp_dayid']) || $data['cp_dayid'] != $max['cp_dayid']) {
        $bulid = 1;
    } else {
        $missWinCount = $data['data'];
    }
} else {
    $bulid = 1;
}

if ($bulid) {
    $missnum = isset($missnum) ? $missnum : '';
    if (is_numeric($missnum) && $missnum !== '') {
        $dosql->Execute("SELECT missnum,count(missnum) as total FROM `#@__caipiao_3code_missing` where missnum='{$missnum}' GROUP BY missnum", 'aaa');
    } else {
        $save = 1;
        $dosql->Execute("SELECT missnum,count(missnum) as total FROM `#@__caipiao_3code_missing` GROUP BY missnum", 'aaa');
    }
    $missWinCount2 = array();
    while ($row = $dosql->GetArray('aaa')) {
        $missWinCount2[] = $row;
    }
    sort_array_multi($missWinCount2, ['total', 'missnum'], ['desc', 'asc']);
    if (isset($save)) {
        $data = array('cp_dayid' => $max['cp_dayid'], 'data' => $missWinCount2);
        file_put_contents($filename, json_encode($data));
    }
    $missWinCount = $missWinCount2;
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
        function gourl() {
            window.location.href = "?missnum=" + $("#missnum").val();
        }
    </script>
    <script type="text/javascript" src="/static/js/baidutj.js"></script>
</head>

<body>
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <?php include('navbar.php') ?>
            <div class="row">
                <form class="navbar-form navbar-left" role="search">
                    <div class="form-group">
                        <label for="exampleInputEmail2">心爱的遗漏数字</label>
                        <input type="text" class="form-control" style="width: 120px;" name="missnum" id="missnum" placeholder="心爱数字" value="<?php echo isset($missnum) && !empty($missnum) ? $missnum : ''; ?>">
                    </div>
                    <a href="javascript:;" onclick="gourl()" class="btn btn-primary" role="button">查询</a>
                    <a href="red_3code.php" class="btn btn-primary" role="button">返回</a>
                </form>
            </div>
            <div class="bs-example" data-example-id="contextual-table">
                <table class="table">
                    <thead>
                        <tr>
                            <th>排名</th>
                            <th>遗漏</th>
                            <th>出现次数</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        $ranking = 0;
                        foreach ($missWinCount as $row) {
                            $ranking++;
                        ?>
                            <tr class="active">
                                <td>
                                    <?php echo $ranking ?>
                                </td>
                                <td>
                                    <a href="red_3code_ranking_code.php?missnum=<?php echo $row['missnum'] ?>"><?php echo $row['missnum'] ?></a>
                                </td>
                                <td>
                                    <a href="red_3code_ranking_code.php?missnum=<?php echo $row['missnum'] ?>"><?php echo $row['total'] ?></a>
                                </td>
                                <td>
                                    <a href="red_3code_ranking_code.php?missnum=<?php echo $row['missnum'] ?>">查看</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php //echo $dopage->GetList(); 
                ?>
            </div>
        </div>
        <!-- /.container-fluid -->
    </nav>
</body>

</html>