<?php
require_once dirname(__FILE__) . '/../include/config.inc.php';
require_once dirname(__FILE__) . '/core/suanfa.func.php';

LoginCheck();
$missnum = isset($missnum) && !empty($missnum) ? $missnum : 56;
if (!is_numeric($missnum) || $missnum < 0) {
    $missnum = 56;
}

$max = $dosql->GetOne('SELECT MAX(orderid) as orderid FROM `#@__caipiao_3code`');
$max_orderid = $max['orderid'];
$orderid = $max_orderid - $missnum;
$row = $dosql->GetOne('SELECT * FROM `#@__caipiao_3code` where orderid=' . $orderid);
if (empty($row)) {
    exit("error");
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
                        <label for="exampleInputEmail2">遗漏期数</label>
                        <input type="text" class="form-control" style="width: 120px;" name="missnum" id="missnum" placeholder="输入遗漏期数" value="<?php echo isset($missnum) && !empty($missnum) ? $missnum : ''; ?>">
                    </div>
                    <a href="javascript:;" class="btn btn-primary" onclick="gourl()" role="button">查询</a>
                </form>
            </div>
            <div class="bs-example" data-example-id="contextual-table">
                <h5>遗漏<?php echo $missnum ?>期，对应<?php echo $row['cp_dayid'] ?>期，红球是<?php echo $row['red_num'] ?></h5>
                <table class="table">
                    <thead>
                        <tr>
                            <th>期数</th>
                            <th colspan="20">20组三码组合</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        $max = $dosql->GetOne("SELECT MAX(cp_dayid) as cp_dayid FROM `#@__caipiao_3code`");
                        $dosql->Execute("SELECT * FROM `#@__caipiao_3code` WHERE orderid>={$orderid} order by `orderid` asc");
                        $i = 0;
                        while ($row = $dosql->GetArray()) {
                            $cur3code = json_decode($row['all3code'], true);
                            // $cur3codemiss = threeCodeMissing($max['cp_dayid'] + 1, $cur3code);
                            $cur3codemiss = threeCodeMissing($row['cp_dayid'], $cur3code);
                            $codemiss = threeCodeMissing($max['cp_dayid'] + 1, $cur3code);
                            $miss_orderid = $max_orderid - $row['orderid'];
                            $i++;
                        ?>
                            <tr class="active">
                                <td>
                                    <?php echo substr($row['cp_dayid'], 4) . '-' ?>
                                    <a href="red_3code.php?missnum=<?php echo $miss_orderid; ?>" target="_blank"><?php echo $miss_orderid ?></a>
                                </td>
                                <?php foreach ($cur3code as $code3) {
                                ?>
                                    <td>
                                        <a style="color: black;" href="red_3code_chart.php?code=<?php echo $code3 ?>" target="_blank"><?php echo $code3 ?></a>
                                        <span style="<?php echo $cur3codemiss[$code3] <= 55 ? 'color:red;' : ''; ?>">
                                            <!-- # -->
                                            <?php echo isset($cur3codemiss[$code3]) ? $cur3codemiss[$code3] : '-'; ?>
                                        </span>
                                    </td>
                                <?php } ?>
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