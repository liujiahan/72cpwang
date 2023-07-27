<?php
require_once dirname(__FILE__) . '/../include/config.inc.php';
require_once dirname(__FILE__) . '/core/suanfa.func.php';
LoginCheck();
$missnum = isset($missnum) && !empty($missnum) ? $missnum : 0;
if (!is_numeric($missnum) || $missnum < 0) {
    $missnum = 0;
}

$max = $dosql->GetOne('SELECT MAX(orderid) as orderid FROM `#@__caipiao_3code`');
$orderid = $max['orderid'];
$orderid = $orderid - $missnum;
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
                    <a href="javascript:;" class="btn btn-primary" onclick="gourl()" role="button">三码查询</a>
                    <a href="red_3code_ranking.php" class="btn btn-primary" role="button">最热遗漏排名</a>
                </form>
            </div>
            <div class="bs-example" data-example-id="contextual-table">
                <h5>遗漏<?php echo $missnum ?>期，对应<?php echo $row['cp_dayid'] ?>期，红球是<?php echo $row['red_num'] ?>，以下是该期奖号对应的三码组合，及当时的遗漏统计。</h5>
                <table class="table">
                    <thead>
                        <tr>
                            <!-- <th>期数</th> -->
                            <th>三码</th>
                            <th>前6次</th>
                            <th>前5次</th>
                            <th>前4次</th>
                            <th>前3次</th>
                            <th>前2次</th>
                            <th>前1次</th>
                            <th>当前</th>
                            <th>图表</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $cur3code = $dosql->GetOne("SELECT * FROM `#@__caipiao_3code` WHERE cp_dayid={$row['cp_dayid']}");
                        $cur3code = json_decode($cur3code['all3code'], true);

                        $max = $dosql->GetOne("SELECT MAX(cp_dayid) as cp_dayid FROM `#@__caipiao_3code`");
                        $cur3codemiss = threeCodeMissing($max['cp_dayid'] + 1, $cur3code);

                        $sql = "SELECT * FROM `#@__caipiao_3code_missing` WHERE cp_dayid={$row['cp_dayid']}";
                        $sql .= " ORDER BY id ASC";

                        $dopage->GetPage($sql, 20);
                        $i = 0;
                        while ($row = $dosql->GetArray()) {
                            $dosql->Execute("SELECT * FROM `#@__caipiao_3code_missing` WHERE code='{$row['code']}' ORDER BY cp_dayid DESC LIMIT 6", 'test');
                            $codeMissList = array(1 => '-', 2 => '-', 3 => '-', 4 => '-', 5 => '-', 6 => '-');
                            $miss_index = 1;
                            while ($r2 = $dosql->GetArray('test')) {
                                $codeMissList[$miss_index] = $r2['missnum'];
                                $miss_index++;
                            }
                            // $codeMissList = array_reverse($codeMissList);
                            // print_r($codeMissList);
                            // die;
                            $i++;
                        ?>
                            <tr class="active">
                                <!-- <td>
                                    <?php echo $row['cp_dayid'] ?>
                                </td> -->
                                <td>
                                    <?php echo $row['code'] ?>
                                </td>
                                <?php for ($ii = 6; $ii >= 1; $ii--) {  ?>
                                    <?php //foreach ($codeMissList as $temp_miss_num) { 
                                    ?>
                                    <td>
                                        <?php echo $codeMissList[$ii] ?>
                                    </td>
                                <?php } ?>
                                <td class="danger">
                                    <?php echo isset($cur3codemiss[$row['code']]) ? $cur3codemiss[$row['code']] : '-'; ?>
                                </td>
                                <td>
                                    <a href=" red_3code_chart.php?code=<?php echo $row['code'] ?>" target="_blank" class="btn btn-default" role="button">图表</a>
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