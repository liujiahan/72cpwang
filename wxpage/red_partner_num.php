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
    <title>伴侣数字 - <?php echo $cfg_seotitle; ?></title>
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
    <script type="text/javascript" src="bootstrap/js/jquery.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript">
    function godayurl() {
        window.location.href = "?cp_dayid=" + $("#cp_dayid").val()+"&before_days=" + $("#before_days").val();
    }

    $(document).ready(function() {
        $("#duiJiangBtn").click(function() {
        	window.location.href = 'ajax/red_offset_do.php?action=red_partner_num';
            // $(this).html('计算中......');
            // $.ajax({
            //     url: 'suanfa_do.php',
            //     dataType: 'html',
            //     type: 'post',
            //     data: {
            //         action: 'partner_num'
            //     },
            //     success: function(data) {
            //         window.location.reload();
            //     }
            // })
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
            <a href="javascript:;" class="btn btn-danger btn-sm active pull-right" id="duiJiangBtn" role="button">下载伴侣数字表</a>
            <?php } ?>
            <div class="bs-example" data-example-id="contextual-table">
                <table class="table">
                    <thead>
                        <tr>
                            <th>##</th>
                            <?php for ($i=1; $i <= 33; $i++) { ?>
                                <th><?php echo $i < 10 ? '0'.$i : $i; ?></th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody> 
                        <?php

                            $partner = array();
                            for ($i=1; $i < 34; $i++) { 
                            	$i < 10 && $i = '0' . $i;
                            	$partner[$i] = array();
                            	for ($j=1; $j < 34; $j++) { 
                            		$j < 10 && $j = '0' . $j;
                            		if($i == $j){
                            			$partner[$i][$j] = '*	*';
                            		}else{
                            			$partner[$i][$j] = 0;
                            		}
                            	}
                            }
                            // print_r($partner);die;

                            $allWinReds = array();
                            $dosql->Execute("SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid ASC");
                            while($row = $dosql->GetArray()){
                            	$allWinReds[$row['cp_dayid']] = $winReds = explode(',', $row['red_num']);
                            	foreach ($winReds as $red1) {
                            		foreach ($winReds as $red2) {
                            			if($red1 == $red2) continue;
                            			$partner[$red1][$red2]++;
                            			$partner[$red2][$red1]++;
                            		}
                            	}
                            }
                            foreach ($partner as $red => $nums) {
                        ?>
                        <tr>
                            <td><?php echo $red ?></td>
                            <?php for ($i=1; $i <= 33; $i++) { ?>
                                <td>
                                <?php  
                                	$i < 10 && $i = '0' . $i;
                                	echo $partner[$red][$i];
                                ?>
                                </td>
                            <?php } ?>
                        </tr>          
                        <?php } ?>
                        <tr>
                            <td>##</td>
                            <?php for ($i=1; $i <= 33; $i++) { ?>
                                <td><?php echo $i < 10 ? '0'.$i : $i; ?></td>
                            <?php } ?>
                        </tr>
                    </tbody>
                </table>

            </div>
        </div>
        <!-- /.container-fluid -->
    </nav>
</body>

</html>
