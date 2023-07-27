<?php

require_once dirname(__FILE__).'/../include/config.inc.php';
require_once dirname(__FILE__).'/core/ssq.config.php';
LoginCheck();

$reds = array();
for ($i=1; $i < 34; $i++) { 
    $i < 10 && $i = '0' . $i;
    $reds[] = $i;
}

$blues = array();
for ($i=1; $i < 17; $i++) { 
    $i < 10 && $i = '0' . $i;
    $blues[] = $i;
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
    function gourl(){
        window.location.href = "?limit_num="+$("#limit_num").val()+"&cp_dayid="+$("#cp_dayid").val();
    }

	var reds = new Array();
	var blues;
    function getReds(){
    	reds = new Array();
    	$('.red_ball.active').each(function(){
    		var red = $(this).html();
    		reds.push(red);
    	})
    }

    function getBlues(){
    	var i = 0;
    	$('.blue_ball.active').each(function(){
    		i++;
    		blues = $(this).html();
    	})
    	if(i >= 1){
    		return false;
    	}
    }

    $(function(){
	    $('#btnAnalysis').click(function(){
	    	if(reds.length < 6){
	    		alert("请至少选择6个红球");
	    		return false;
	    	}
	    	if(typeof blues == 'undefined'){
	    		// alert("请选择1个蓝球");
	    		return false;
	    	}

	    	$.ajax({
                url: 'ajax/red_suanfa_do.php',
                dataType: 'html',
                type: 'post',
                data: {
                    action: 'red_analysis',
                    cp_dayid: $('#cp_dayid').val(),
                    reds: reds,
                    blues: blues
                },
                success: function(data) {
                	alert(data);
                    // window.location.reload();
                }
            })
	    })

    	$(".red_ball").click(function(){
    		if($(this).hasClass('active')){
    			$(this).removeClass('active');
    		}else{
    			$(this).addClass('active');
    		}
    		getReds();
    	})

    	$(".blue_ball").click(function(){
    		if($(this).hasClass('active')){
    			$(this).removeClass('active');
    		}else{
    			$(this).addClass('active');
    		}
    		getBlues();
    	})
    })

    </script>
</head>

<body>
<nav class="navbar navbar-default">
    <div class="container-fluid">
        <?php include('navbar.php') ?>
        
        <blockquote>
            <p>下期选球，多半会在历史开奖中，中的3+1,4+1等等，所以我们的选号也要包含这些。</p>
            <p>使用方法：</p>
            <p>1、点击“选择红球蓝球”按钮，请选中至少6个红球以及至少1个蓝球。</p>
            <p>2、点击“智能分析”按钮，即可查看结果（由于网络原因可能会卡顿，看结果请等待片刻）。</p>
        </blockquote>
        <form class="navbar-form navbar-left" role="search">

        <div class="form-group">
            <label for="exampleInputEmail2">选择期数</label>
            <select class="form-control" name="limit_num" id="limit_num" onchange="gourl()">
            <option value="">--请选择--</option>
            <?php foreach (getSelArr() as $daynum => $daytxt) { ?>
            <option value="<?php echo $daynum ?>" <?php echo isset($limit_num) && $limit_num == $daynum ? 'selected' : '' ?>><?php echo $daytxt ?></option>
            <?php } ?>
            </select>
        </div>
        <div class="form-group">
            <label for="exampleInputEmail2">选择期数</label>
            <select class="form-control" name="cp_dayid" id="cp_dayid" onchange="gourl()">
            <option value="">--请选择--</option>
            <?php foreach (getDaySel() as $t_cp_dayid => $cp_dayidtxt) { ?>
            <option value="<?php echo $t_cp_dayid ?>" <?php echo isset($cp_dayid) && $t_cp_dayid == $cp_dayid ? 'selected' : '' ?>><?php echo $cp_dayidtxt ?></option>
            <?php } ?>
            </select>
        </div>
        <?php //if(isset($_COOKIE['isAdmin']) && $_COOKIE['isAdmin'] == $adminkey){ ?>
        <a class="btn btn-primary" role="button" data-toggle="collapse" href="#chooseReds" aria-expanded="false" aria-controls="chooseReds">选择红球蓝球</a>
        <a href="javascript:;" class="btn btn-primary" id="btnAnalysis" role="button">智能分析</a>
        <?php //} ?>
        </form>
        <div class="clearfix"></div>
        
        <div class="collapse" id="chooseReds">
          <div class="well">
          		<div class="red_cont">
	          		<?php foreach ($reds as $key => $red) { ?>
			          <span class="red_ball"><?php echo $red ?></span>
	          		<?php } ?>
          		</div>
          		<div class="blue_cont">
	          		<?php foreach ($blues as $key => $blue) { ?>
			          <span class="blue_ball"><?php echo $blue ?></span>
	          		<?php } ?>
          		</div>
          </div>
        </div>
        <div class="bs-example" data-example-id="contextual-table">
            <table class="table">
                <thead>
                    <tr>
                        <th>期数</th>
                        <th>开奖号</th>
                        <th>中3+1次数</th>
                        <th>中4+0次数</th>
                        <th>中4+1次数</th>
                        <th>中5+0次数</th>
                        <th>中5+1次数</th>
                        <th>中6+0次数</th>
                        <th>中6+1次数</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 

                    	$dosql->Execute("SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid DESC");
                    	$cpRest = array();
                    	while($row = $dosql->GetArray()){
                    		$cpRest[$row['cp_dayid']]['cp_dayid'] = $row['cp_dayid'];
                    		$cpRest[$row['cp_dayid']]['red_num'] = explode(",", $row['red_num']);
                    		$cpRest[$row['cp_dayid']]['blue_num'] = $row['blue_num'];
                    	}

                    	$curYearRest = array();
                    	if(isset($cp_dayid) && !empty($cp_dayid)){
                    		$curYearRest[$cp_dayid] = $cpRest[$cp_dayid];
                    	}else{
	                    	$limit_num = isset($limit_num) ? $limit_num : 10;
	                    	$curYearRest = array_slice($cpRest, 0, $limit_num);
                    	}

                    	$rest = array();
                    	foreach ($curYearRest as $key => $cur_row) {
                    		if(!isset($rest[$cur_row['cp_dayid']])){
                    			$rest[$cur_row['cp_dayid']] = array();
                    		}
                    		foreach ($cpRest as $cp_dayid => $row) {
                    			if($cur_row['cp_dayid'] <= $cp_dayid){
                    				continue;
                    			}
                    			$jjarr = array_intersect($cur_row['red_num'], $row['red_num']);
                    			$jjnum = count($jjarr);
                    			$bluewin = $cur_row['blue_num'] == $row['blue_num'] ? 1 : 0;
                    			if($bluewin == 1 && $jjnum >= 3  || $bluewin == 0 && $jjnum > 3){
                    				$arrindex = $jjnum . '+' . $bluewin;
                    				if(!isset($rest[$cur_row['cp_dayid']][$arrindex])){
                    					// $rest[$cur_row['cp_dayid']][$arrindex] = array();
                    					$rest[$cur_row['cp_dayid']][$arrindex] = 0;
                    				}
                    				// $rest[$cur_row['cp_dayid']][$arrindex][] = '【'.$row['cp_dayid'].'】'.implode(",", $row['red_num']);
                    				$rest[$cur_row['cp_dayid']][$arrindex]++;
                    			}
                    		}
                    	}

                        foreach ($rest as $cp_dayid => $row) {
                    ?>
                    <tr class="active">
                        <td><?php echo $cp_dayid ?></td>
                        <td><?php echo implode(" ", $cpRest[$cp_dayid]['red_num']).'+'.$cpRest[$cp_dayid]['blue_num'] ?></td>
                        <td><?php echo isset($row['3+1']) ? $row['3+1'] : '-'; ?></td>
                        <td><?php echo isset($row['4+0']) ? $row['4+0'] : '-'; ?></td>
                        <td><?php echo isset($row['4+1']) ? $row['4+1'] : '-'; ?></td>
                        <td><?php echo isset($row['5+0']) ? $row['5+0'] : '-'; ?></td>
                        <td><?php echo isset($row['5+1']) ? $row['5+1'] : '-'; ?></td>
                        <td><?php echo isset($row['6+0']) ? $row['6+0'] : '-'; ?></td>
                        <td><?php echo isset($row['6+1']) ? $row['6+1'] : '-'; ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php //echo $dopage->GetList(); ?>
        </div>
    </div>
    <!-- /.container-fluid -->
</nav>
</body>
<!-- 模态框（Modal） -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
					&times;
				</button>
				<h4 class="modal-title" id="myModalLabel">
					模态框（Modal）标题
				</h4>
			</div>
			<div class="modal-body">
				在这里添加一些文本
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">关闭
				</button>
				<button type="button" class="btn btn-primary">
					提交更改
				</button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal -->
</div>

</html>