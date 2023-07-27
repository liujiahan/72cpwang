<?php

require_once dirname(__FILE__).'/../include/config.inc.php';
require_once dirname(__FILE__).'/ssq.config.php';
require_once dirname(__FILE__).'/suanfa.func.php';

function format_num($num){
    $num = $num - intval($num) > 0 ? $num : intval($num);
    return $num == 0 ? '-' : $num;
}

?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>追踪数字 - 大数据说彩</title>
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
    <script type="text/javascript" src="bootstrap/js/jquery.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript">
    function godayurl() {
        window.location.href = "?cp_dayid=" + $("#cp_dayid").val();
    }

    $(document).ready(function() {
        $("#duiJiangBtn").click(function() {
        	// window.location.href = 'suanfa_do.php?action=partner_num';
            $(this).html('计算中......');
            $.ajax({
                url: 'suanfa_do.php',
                dataType: 'html',
                type: 'post',
                data: {
                    action: 'next_num'
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
            </form>
            <a href="javascript:;" class="btn btn-danger btn-sm active pull-right" id="duiJiangBtn" role="button">追踪数字</a>
            <div class="bs-example" data-example-id="contextual-table">
                <table class="table">
                    <?php
                        if(isset($cp_dayid) && !empty($cp_dayid)){
                            $cur = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid='$cp_dayid'");
                        }else{
                            $cur = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid DESC");
                        }
                        $curReds = explode(",", $cur['red_num']);
                    ?>
                    <thead>
                        <tr>
                            <th>##</th>
                            <?php foreach ($curReds as $curred) { ?>
                                <th><?php echo $curred; ?></th>
                            <?php } ?>
                            <th>总计</th>
                            <th>命中</th>
                            <th>序号</th>
                        </tr>
                    </thead>

                    <?php

                        if(isset($cp_dayid) && !empty($cp_dayid)){
                            $dosql->Execute("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<'$cp_dayid'");
                        }else{
                            $dosql->Execute("SELECT * FROM `#@__caipiao_history`");
                        }
                        $allRed = array();
                        $index = 0;
                        while($row = $dosql->GetArray()){
                            $allRed[$index] = explode(",", $row['red_num']);
                            $index++;
                        }

                        $nextData = array();
                        foreach ($allRed as $k => $tmpReds) {
                            foreach ($tmpReds as $tmpred) {
                                if(in_array($tmpred, $curReds) && isset($allRed[$k+1])){
                                    if(!isset($nextData[$tmpred])){
                                        $nextData[$tmpred] = array();
                                    }
                                    foreach ($allRed[$k+1] as $tmpred2) {
                                        if(!isset($nextData[$tmpred][$tmpred2])){
                                            $nextData[$tmpred][$tmpred2] = 0;
                                        }
                                        $nextData[$tmpred][$tmpred2]++;
                                    }
                                }
                            }
                        }

                        $red33 = array();
                        for ($i=1; $i < 34; $i++) { 
                            $i<10 && $i = '0' . $i;
                            $red33[$i] = 0;

                            foreach ($nextData as $tmpred => $redCount) {
                                $red33[$i] += $redCount[$i];
                            }
                        }

                        arsort($red33);
                        $winRed = array();
                        if(isset($cp_dayid)){
                            $cp_dayid = $cp_dayid + 1;
                            $row = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid=".$cp_dayid);
                            if(isset($row['id'])){
                                $winRed = explode(",", $row['red_num']);
                            }
                        }
                        $iii = 1;
                        $ii = 33;
                    ?>
                    <tbody>
                        <?php foreach ($red33 as $red => $redcount) { ?>
                        <tr>
                            <td><?php echo $red ?></td>
                            <td><?php echo $nextData[$curReds[0]][$red] ?></td>
                            <td><?php echo $nextData[$curReds[1]][$red] ?></td>
                            <td><?php echo $nextData[$curReds[2]][$red] ?></td>
                            <td><?php echo $nextData[$curReds[3]][$red] ?></td>
                            <td><?php echo $nextData[$curReds[4]][$red] ?></td>
                            <td><?php echo $nextData[$curReds[5]][$red] ?></td>
                            <td><?php echo $redcount ?></td>
                            <td>
                            <?php 
                                if(in_array($red, $winRed)){
                                    echo '<button class="btn btn-danger" type="button">' . $red . '</span>
                                    </button>';
                                } 
                            ?>
                            </td>
                            <td><?php echo $iii . ' - ' . $ii; ?></td>
                        </tr>          
                        <?php $iii++;$ii--;} ?>
                    </tbody>
                </table>

            </div>
        </div>
        <!-- /.container-fluid -->
    </nav>
</body>

</html>
