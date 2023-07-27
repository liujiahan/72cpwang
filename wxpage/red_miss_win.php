<?php

require_once dirname(__FILE__).'/../include/config.inc.php';
require_once dirname(__FILE__).'/core/ssq.config.php';
require_once dirname(__FILE__).'/core/suanfa.func.php';

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
    <title>遗漏与中奖分析表 - <?php echo $cfg_seotitle; ?></title>
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
                    action: 'red_miss_win'
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
            <a href="javascript:;" class="btn btn-danger btn-sm active pull-right" id="duiJiangBtn" role="button">遗漏与中奖分析表</a>
            <?php } ?>
            <div class="bs-example" data-example-id="contextual-table">
                <table class="table">
                    <thead>
                        <tr>
                            <th>红球</th>
                            <th>遗漏次数</th>
                            <?php for ($i=1; $i <= 35; $i++) { ?>
                                <th><?php echo $i; ?></th>
                            <?php } ?>
                            <th>>35</th>
                        </tr>
                    </thead>
                    <tbody> 
                        <?php

                            $curMiss = redMissing();

                            $sql = "SELECT * FROM `#@__caipiao_red_miss_win`";
                            $dosql->Execute($sql);
                            while($row = $dosql->GetArray()){
                                $misswin = unserialize($row['miss_win']);
                        ?>
                        <tr>
                            <td><?php echo $row['redball'] ?></td>
                            <td><?php echo $curMiss[$row['redball']] ?></td>
                            <?php for ($i=1; $i <= 35; $i++) { ?>
                                <td>
                                <?php
                                    if($i == $curMiss[$row['redball']]){
                                        echo '<span style="color: red; font-weight: bold;">'.format_num($misswin[$i]).'</span>';
                                    }else{
                                        echo format_num($misswin[$i]);
                                    }
                                ?>
                                </td>
                            <?php } ?>
                            <td>
                            <?php
                                $gt35 = array();
                                for ($i=36; $i <= 45; $i++) { 
                                    if(isset($misswin[$i])){
                                        $gt35[] = $i;
                                    }
                                }
                                echo implode(', ', $gt35);
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
