<?php

require_once dirname(__FILE__).'/../include/config.inc.php';
LoginCheck();

if(!(isset($_COOKIE['isAdmin']) && $_COOKIE['isAdmin'] == $adminkey)){
    ShowMsg("该数据只有管理员能查看！", 'index.php');
    exit;
}

?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>我的订单 - <?php echo $cfg_seotitle; ?></title>
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="./ssq/matrix.css">
    <script type="text/javascript" src="bootstrap/js/jquery.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript">

    function gourl(){
        window.location.href = "?cp_dayid="+$("#cp_dayid").val()+"&isdo="+$("#isdo").val();
    }
    $(document).ready(function() {
        $("#duiJiangBtn").click(function(){
            $(this).html('计算中......');
            $.ajax({
                url: 'ajax/ssq_do.php',
                dataType: 'html',
                type: 'post',
                data: {action: 'cash_prize', buytype: 1},
                success: function(data){
                	if(data != 0){
                		alert("老大您中奖"+data+"元，再接再厉，加油哦！！！")
                	}
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

        <?php 
        	// $payget = $dosql->GetOne("SELECT * FROM `#@__caipiao_payandget` WHERE type='ssq'");
        ?>
        <h1>小投资怡情，大投资费神。<small>而时时投资，则敏锐的投资眼光不失。</small></h1>
        <!-- <p class="lead"></p> -->
        <blockquote>
            <p>先看冷热选范围，再看位置定号码。锁定篮球赢大奖，平常心态来日方长。</p>
        </blockquote>
        <form class="navbar-form navbar-left" role="search">
        <div class="form-group">
        <label for="exampleInputEmail2">期数</label>
        <select class="form-control" name="cp_dayid" id="cp_dayid">
            <option value="">--请选择--</option>
            <?php $monthNum = date('m') * 4 * 3; ?>
            <?php for($i=$monthNum; $i>=1; $i--){ ?>
            <?php 
              $sel_cp_dayid = date('Y');
              if($i >= 100){
                $sel_cp_dayid .= $i;
              }else if($i >= 10 && $i < 100){
                $sel_cp_dayid .= '0'.$i;
              }else{
                $sel_cp_dayid .= '00'.$i;
              }
            ?>
            <option value="<?php echo $sel_cp_dayid ?>" <?php echo isset($cp_dayid) && $sel_cp_dayid == $cp_dayid ? 'selected' : ''; ?>>
                <?php echo $sel_cp_dayid; ?>
            </option>
            <?php } ?>
        </select>
        </div>

        <div class="form-group">
	        <?php $isdo = isset($isdo) ? $isdo : 1; ?>
	        <label for="exampleInputEmail2">核对状态</label>
	        <select class="form-control" name="isdo" id="isdo">
	            <option value="1" <?php echo isset($isdo) && $isdo == 1 ? 'selected' : ''; ?>>已核对</option>
	            <option value="0" <?php echo isset($isdo) && $isdo == 0 ? 'selected' : ''; ?>>未核对</option>
	        </select>
        </div>
        <a href="javascript:;" class="btn btn-primary" onclick="gourl()" role="button">查询</a>
        </form>        

        <a href="buyssq.php" class="btn btn-info btn-sm active pull-right" role="button">购买下单</a>
        <a href="javascript:;" class="btn btn-danger btn-sm active pull-right" id="duiJiangBtn" role="button">我要兑奖</a>

        <div class="bs-example" data-example-id="contextual-table">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>期数</th>
                    <th>用户选号</th>
                    <th>开奖号</th>
                    <!-- <th>红球命中数</th> -->
                    <!-- <th>篮球命中数</th> -->
                    <th>投资</th>
                    <th>倍数</th>
                    <th>回报</th>
                    <th>状态</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    $sql = "SELECT * FROM `#@__caipiao_myorder` WHERE buytype=1 ";
                    if(!empty($cp_dayid)){
                        $sql .= " AND cp_dayid='$cp_dayid'";
                    }

                    if(isset($isdo)){
                        // $sql .= " AND isdo='$isdo'";
                    }
                    $sql .= " ORDER BY cp_dayid DESC";
                    
                    $dopage->GetPage($sql);
                    $i = 0;
                    while($row = $dosql->GetArray()){
                        $i++;
                ?>
                <tr>
                    <th scope="row"><?php echo $i ?></th>
                    <td><?php echo $row['cp_dayid'] ?></td>
                    <td>
                    <?php
                        $opencode = array();
                        if(isset($row['opencode'])){
                            $opencode = explode('+', $row['opencode']);
                        }

                        $win_red = isset($opencode[0]) ? explode(',', $opencode[0]) : array();
                        $win_blue = isset($opencode[1]) ? $opencode[1] : 0;

                        $reds = explode(',', $row['red_num']);
                        foreach ($reds as $red) {
                            $class = in_array($red, $win_red) ? 'active' : ''; 
                            echo '<span class="red_ball '.$class.'">'.$red.'</span>';   
                        }
                        $class2 = $win_blue == $row['blue_num'] ? ' active' : '';
                        echo ' + <span class="blue_ball'.$class2.'">'.$row['blue_num'].'</span>';
                    ?>
                    </td>
                    <td>
                    <?php
                        if($win_red && !empty($win_red[0])){
                            foreach ($win_red as $red) {
                                echo '<span class="red_ball active">'.$red.'</span>';   
                            }
                        }
                        if($win_blue){
                            echo '<span class="blue_ball active">'.$win_blue.'</span>';
                        }
                    ?>
                    </td>
                    <!-- <td><?php echo $row['red_win_num'] ?></td> -->
                    <!-- <td><?php echo $row['blue_win_num'] ?></td> -->
                    <td><?php echo $row['buycost'] ?></td>
                    <td><?php echo $row['multiple'] ?></td>
                    <td><?php echo $row['repay'] ?></td>
                    <td><?php echo $row['isdo'] == 1 ? '已对奖' : '未对奖'; ?></td>
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
