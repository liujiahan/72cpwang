<?php
require_once dirname(__FILE__) . '/../include/config.inc.php';
LoginCheck();

$dosql->Execute("SELECT missnum,count(missnum) as total FROM `#@__caipiao_3code_missing` GROUP BY missnum", 'aaa');
$missWinCount = array();
while ($row = $dosql->GetArray('aaa')) {
    $missWinCount[] = $row;
}
sort_array_multi($missWinCount, ['total', 'missnum'], ['desc', 'asc']);

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
                    <!-- <a href="red_3code_ranking.php" class="btn btn-primary" role="button">最热遗漏排名</a> -->
                    <a href="red_3code_ranking.php" class="btn btn-primary" role="button">返回</a>
                </form>
            </div>
            <div class="bs-example" data-example-id="contextual-table">
                <table class="table">
                    <thead>
                        <tr>
                            <th>序号</th>
                            <th>三码</th>
                            <th>遗漏</th>
                            <th>开出期数</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        $ranking = 0;
                        $dosql->Execute("SELECT * FROM `#@__caipiao_3code_missing` WHERE missnum='{$missnum}' order by id desc", 'aaa');
                        while ($row = $dosql->GetArray('aaa')) {
                            $ranking++;
                        ?>
                            <tr class="active">
                                <td>
                                    <?php echo $ranking ?>
                                </td>
                                <td>
                                    <a href="red_3code_chart.php?code=<?php echo $row['code'] ?>"><?php echo $row['code'] ?></a>
                                </td>
                                <td>
                                    <?php echo $row['missnum'] ?>
                                </td>
                                <td>
                                    <?php echo $row['cp_dayid'] ?>
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