<?php

require_once dirname(__FILE__) . '/../include/config.inc.php';
LoginCheck();

function calcBlue($kjh_blue, $sjh_blue){
	if($kjh_blue >= $sjh_blue){
		$big = $kjh_blue;
		$sml = $sjh_blue;
	}else{
		$big = $sjh_blue;
		$sml = $kjh_blue;
	}
	$blues = array();
	$chgblue = array();
	$diff = abs($big - $sml);
	if($diff){
		$sml_diff = $sml - $diff;
		if($sml_diff>0){
			$blues[] = $sml_diff;
			if($sml_diff + 10 <= 16){
				$chgblue[] = $sml_diff + 10;
			}
		}
		$big_diff = $big + $diff;
		if($big_diff <= 16){
			$blues[] = $big_diff;
			if($big_diff - 10 > 0){
				$chgblue[] = $big_diff - 10;
			}
		}
	}

	if($diff % 2 == 0){
		$haf_diff = $diff / 2;
		$blues[] = $sml + $haf_diff;
	}
	return array('blue1'=>$blues,'blue2'=>$chgblue);
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
            window.location.href = "?statdayid=" + $("#statdayid").val() + "&enddayid=" + $("#enddayid").val();
        }

        function gourl2() {
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
	<style>
	address, caption, cite, em, i, u {
		font-style: normal;
		text-decoration: none;
	}
	.kj_qiu em{
		display: inline-block;
		margin: 0 3px;
	}

.kj_qiu a{display:inline-block;float:right;font-size:12px;color:#1772a0;line-height:35px;}
.kj_qiu span{display:inline-block;width:18px;height:18px;line-height:18px;text-align:center;
color:#fff;font-size:14px;font-weight:bold;border-radius:50px;margin:0 2px;}
.kj_qiu .red_qiu{background:#FF5050}
.kj_qiu .blue_qiu{background:#1772a0;}
	</style>
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
            <?php /*if (isset($_COOKIE['isAdmin']) && $_COOKIE['isAdmin'] == $adminkey) { ?>
                <a href="javascript:;" class="btn btn-info btn-sm active pull-right" id="pullSSQInfo" role="button">同步开奖信息</a>&nbsp;&nbsp;
                <a href="javascript:;" class="btn btn-info btn-sm active pull-right" onclick="gourl2()" role="button">下载</a>&nbsp;&nbsp;
            <?php }*/ ?>
            <div class="bs-example" data-example-id="contextual-table">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>期数</th>
                            <th>开机号</th>
                            <th>试机号</th>
                            <th>奖号</th>
                            <th>开机蓝号</th>
                            <th>试机蓝号</th>
                            <th>范围</th>
                            <th>奖号蓝号</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM `#@__caipiao_kjh` WHERE 1 ";
                        // if(!empty($statdayid)){
                        //     $sql .= " AND cp_dayid>=$statdayid";
                        // }
                        // if(!empty($enddayid)){
                        //     $sql .= " AND cp_dayid<=$enddayid";
                        // }
                        $sql .= " ORDER BY cp_dayid DESC";

                        $dopage->GetPage($sql, 50);
                        $i = 0;
                        while ($row = $dosql->GetArray()) {
                            $i++;
                            if ($row['red'] == '' || $row['blue'] == '') {
                                $cpinfo = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid='{$row['cp_dayid']}'");
                                if (isset($cpinfo['id'])) {
                                    $row['red'] = $cpinfo['red_num'];
                                    $row['blue'] = $cpinfo['blue_num'];
                                }
                            }
							$prize = array('red'=>array(),'blue'=>'');
							if(!empty($row['red']) && !empty($row['blue'])){
								$prize['red'] = explode(",", $row['red']);
								$prize['blue'] = $row['blue'];
							}
                            ?>
                            <tr class="active">
                                <th scope="row"><?php echo $i ?></th>
                                <td>
                                    <?php echo $row['cp_dayid'] ?>
                                </td>
                                <td>
									<div class="kj_qiu">
									<?php $ssq_kjh = explode(".", $row['ssq_kjh']); 
									foreach($ssq_kjh as $v){
									?>
									<?php if(in_array($v, $prize['red'])){ ?>
									<span class="red_qiu"><?php echo $v; ?></span>
									<?php }else{?>
									<em><?php echo $v; ?></em>
									<?php }?>
									<?php }?>
									+
									<?php if($prize['blue'] == $row['kjh_blue']){ ?>
									<span class="blue_qiu"><?php echo $row['kjh_blue'] < 10 && strlen($row['kjh_blue']) != 2 ? '0'.$row['kjh_blue'] : $row['kjh_blue']; ?></span>
									<?php }else{?>
									<em><?php echo $row['kjh_blue'] < 10 && strlen($row['kjh_blue']) != 2 ? '0'.$row['kjh_blue'] : $row['kjh_blue']; ?></em>
									<?php }?>
									</div>
                                </td>
                                <td>
									<div class="kj_qiu">
									<?php $ssq_sjh = explode(".", $row['ssq_sjh']); 
									foreach($ssq_sjh as $v){
									?>
									<?php if(in_array($v, $prize['red'])){ ?>
									<span class="red_qiu"><?php echo $v; ?></span>
									<?php }else{?>
									<em><?php echo $v; ?></em>
									<?php }?>
									<?php }?>
									+
									<?php if($prize['blue'] == $row['sjh_blue']){ ?>
									<span class="blue_qiu"><?php echo $row['sjh_blue'] < 10 && strlen($row['sjh_blue']) != 2 ? '0'.$row['sjh_blue'] : $row['sjh_blue']; ?></span>
									<?php }else{?>
									<em><?php echo $row['sjh_blue'] < 10 && strlen($row['sjh_blue']) != 2 ? '0'.$row['sjh_blue'] : $row['sjh_blue']; ?></em>
									<?php }?>
									</div>
                                </td>
                                <td>
                                    <?php if (!empty($row['red'])) { ?>
										<div class="kj_qiu">
										<?php $red = explode(",", $row['red']); 
										foreach($red as $v){
										?>
										<span class="red_qiu"><?php echo $v; ?></span>
										<?php }?>
										<span class="blue_qiu"><?php echo $row['blue'] < 10 && strlen($row['blue']) != 2 ? '0'.$row['blue'] : $row['blue']; ?></span>
										</div>
                                    <?php } ?>
                                </td>
                                <td>
									<em><?php echo $row['kjh_blue'] < 10 && strlen($row['kjh_blue']) != 2 ? '0'.$row['kjh_blue'] : $row['kjh_blue']; ?></em>
								</td>
                                <td>
									<em><?php echo $row['sjh_blue'] < 10 && strlen($row['sjh_blue']) != 2 ? '0'.$row['sjh_blue'] : $row['sjh_blue']; ?></em>
								</td>

                                <td>
                                	<?php
                                		$blues = calcBlue($row['kjh_blue'], $row['sjh_blue']);
                                		$blues1 = $blues['blue1'];
                                		$blues2 = $blues['blue2'];
                                	?>
                                    <?php if (!empty($blues1)) { ?>
										<div class="kj_qiu">
									<?php
										foreach($blues1 as $b){
											$b = $b < 10 && strlen($b) != 2 ? '0'.$b : $b;
										?>
										<?php if($b == $row['blue']){ ?>
										<span class="blue_qiu"><?php echo $b ?></span>
										<?php }else{ ?>
										<em><?php echo $b ?></em>
										<?php }?>
										<?php }?>

										<?php if (!empty($blues2)) { ?>
										&nbsp;|&nbsp;
										<?php
										foreach($blues2 as $b){
											$b = $b < 10 && strlen($b) != 2 ? '0'.$b : $b;
										?>
										<?php if($b == $row['blue']){ ?>
										<span class="blue_qiu"><?php echo $b ?></span>
										<?php }else{ ?>
										<em><?php echo $b ?></em>
										<?php }?>
										<?php }?>
										<?php }?>
										</div>
                                    <?php } ?>
                                </td>
                                <td>
									<em><?php echo empty($row['blue']) ? '' : $row['blue'] < 10 && strlen($row['blue']) != 2 ? '0'.$row['blue'] : $row['blue']; ?></em>
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