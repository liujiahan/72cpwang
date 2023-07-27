<?php
require_once dirname(__FILE__).'/../include/config.inc.php';

LoginCheck();

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
    function gourl(){
        window.location.href = "?statdayid="+$("#statdayid").val()+"&enddayid="+$("#enddayid").val();
    }

    function gourl2(){
        window.location.href = "ajax/ssq_do.php?action=download_ssq_index&num=<?php echo isset($num) ? $num : 0; ?>";
    }
    $(document).ready(function() {
        $("#pullSSQInfo").click(function() {
            $(this).html('同步中......');
            $.ajax({
                url: 'ajax/ssq_do.php',
                dataType: 'html',
                type: 'post',
                data: {
                    action: 'pull_ssqinfo'
                },
                success: function(data) {
                    // window.location.reload();
                }
            })
        })
        $("#pullSSQPrize").click(function() {
            $(this).html('同步中......');
            $.ajax({
                url: 'ajax/ssq_do.php',
                dataType: 'html',
                type: 'post',
                data: {
                    action: 'pull_ssqprize'
                },
                success: function(data) {
                    window.location.reload();
                }
            })
        })
    })
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
            <label for="exampleInputEmail2">开始期数</label>
            <input type="text" class="form-control" name="statdayid" id="statdayid" placeholder="请输入..." value="<?php echo isset($statdayid) ? $statdayid : ''; ?>">
          </div>
          <div class="form-group">
            <label for="exampleInputEmail2">结束期数</label>
            <input type="text" class="form-control" name="enddayid" id="enddayid" placeholder="请输入..." value="<?php echo isset($enddayid) ? $enddayid : ''; ?>">
          </div>
          <a href="javascript:;" class="btn btn-primary" onclick="gourl()" role="button">查询</a>
        </form>
        <?php if(isset($_COOKIE['isAdmin']) && $_COOKIE['isAdmin'] == $adminkey){ ?>
        <a href="javascript:;" class="btn btn-info btn-sm active pull-right" id="pullSSQInfo" role="button">同步开奖信息</a>&nbsp;&nbsp;
        <a href="javascript:;" class="btn btn-info btn-sm active pull-right" onclick="gourl2()" role="button">下载</a>&nbsp;&nbsp;
        <?php } ?>
        <div class="bs-example" data-example-id="contextual-table">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>期数</th>
                        <th>开奖号码</th>
                        <th>一等奖数量</th>
                        <th>一等奖奖金</th>
                        <th>出号顺序</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $sql = "SELECT * FROM `#@__caipiao_history` WHERE 1 ";
                        if(!empty($statdayid)){
                            $sql .= " AND cp_dayid>=$statdayid";
                        }
                        if(!empty($enddayid)){
                            $sql .= " AND cp_dayid<=$enddayid";
                        }
                        $sql .= " ORDER BY cp_dayid DESC";

                        $dopage->GetPage($sql);
                        $i = 0;
                        while($row = $dosql->GetArray()){
                            $i++;
                            $prize = $dosql->GetOne("SELECT * FROM `#@__caipiao_history_prize` WHERE cp_dayid='{$row['cp_dayid']}'");
                    		$p1 = $p1_bonus = '';
                    		if(isset($prize['id'])){
	                    		$p1 = $prize['p1'];
	                    		$p1_bonus = $prize['p1_bonus'];
                    		}
                    ?>
                    <tr class="active">
                        <th scope="row"><?php echo $i ?></th>
                        <td>
                            <?php if(isset($_COOKIE['isAdmin']) && $_COOKIE['isAdmin'] == $adminkey){ ?>
                                <a href="amazeweer.php?cp_dayid=<?php echo $row['cp_dayid'] ?>" target="_blank"><?php echo $row['cp_dayid'] ?></a></td>
                            <?php }else{ ?>
                                <?php echo $row['cp_dayid'] ?>
                            <?php } ?>
                        <td>
                        <?php
                            $reds = explode(',', $row['red_num']);
                            foreach ($reds as $red) {
                                echo '<span class="red_ball active">'.$red.'</span>';   
                            }
                            echo '<span class="blue_ball active">'.$row['blue_num'].'</span>';
                        ?>
                        </td>
                        <td><?php echo $p1 ?></td>
                        <td><?php echo $p1_bonus ?></td>
                        <td>
                        <?php
                            $red_order = explode(',', $row['red_order']);
                            echo implode('、', $red_order);
                            // foreach ($red_order as $red) {
                            //     echo '<span class="red_ball active">'.$red.'</span>';   
                            // }
                        ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php echo $dopage->GetList(); ?>
        </div>
    </div>
    <!-- /.container-fluid -->
</nav>
</body>

</html>
