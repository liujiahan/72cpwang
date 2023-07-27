<?php

require_once dirname(__FILE__).'/../include/config.inc.php';
require_once dirname(__FILE__).'/core/ssq.config.php';
LoginCheck();

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
    <title>红球双倍中奖比率 - <?php echo $cfg_seotitle; ?></title>
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
    <script type="text/javascript" src="bootstrap/js/jquery.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript">
    function godayurl() {
        window.location.href = "?cp_dayid=" + $("#cp_dayid").val()+"&before_days=" + $("#before_days").val();
    }

    $(document).ready(function() {
        $("#duiJiangBtn").click(function() {
            $(this).html('计算中......');
            $.ajax({
                url: 'ajax/red_offset_do.php',
                dataType: 'html',
                type: 'post',
                data: {
                    action: 'red_double_win'
                },
                success: function(data) {
                    window.location.reload();
                }
            })
        })
        $("#duiJiangBtn2").click(function() {
            $(this).html('计算中......');
            $.ajax({
                url: 'ajax/red_offset_do.php',
                dataType: 'html',
                type: 'post',
                data: {
                    action: 'count_double_num'
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
            <!-- <form class="navbar-form navbar-left" role="search">
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
            </form> -->
            <?php if(isset($_COOKIE['isAdmin']) && $_COOKIE['isAdmin'] == $adminkey){ ?>
            <a href="javascript:;" class="btn btn-danger btn-sm active pull-right" id="duiJiangBtn" role="button">双倍中奖比率</a>
            <a href="javascript:;" class="btn btn-default btn-sm active pull-right" id="duiJiangBtn2" role="button">记录重复</a>
            <?php } ?>
            <div class="bs-example" data-example-id="contextual-table">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="15%">待选数字</th>
                            <th width="10%">中奖1次</th>
                            <th width="10%">中奖2次</th>
                            <th width="10%">中奖3次</th>
                            <th width="10%">中奖4次</th>
                            <th width="10%">中奖5次</th>
                            <th width="10%">中奖6次</th>
                            <th width="10%">中奖7次</th>
                            <th width="15%">双倍中奖比率</th>
                        </tr>
                    </thead>
                    <tbody> 
                        <?php


                            $sql = "SELECT * FROM `#@__caipiao_red_double_win`";

                            $dosql->Execute($sql);
                            while($row = $dosql->GetArray()){

                        ?>
                        <tr>
                            <td><?php echo $row['counttype'] ?></td>
                            <td><?php echo format_num($row['win_1']) ?></td>
                            <td><?php echo format_num($row['win_2']) ?></td>
                            <td><?php echo format_num($row['win_3']) ?></td>
                            <td><?php echo format_num($row['win_4']) ?></td>
                            <td><?php echo format_num($row['win_5']) ?></td>
                            <td><?php echo format_num($row['win_6']) ?></td>
                            <td><?php echo format_num($row['win_7']) ?></td>
                            <td><?php echo format_num($row['double_win']) ?></td>
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
