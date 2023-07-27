<?php  

require_once dirname(__FILE__) . '/../include/config.inc.php';
require_once dirname(__FILE__) . '/core/suanfa.func.php';
require_once dirname(__FILE__) . '/core/core.func.php';
require_once dirname(__FILE__) . '/core/ssq.config.php';
LoginCheck();

?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"> -->
    <title>红球遗漏冷热走势 - <?php echo $cfg_seotitle; ?></title>
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
    <script type="text/javascript" src="bootstrap/js/jquery.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript">
    function godayurl(){
        window.location.href = "?cp_dayid="+$("#cp_dayid").val();
    }

    $(document).ready(function() {
        $('#cp_dayid').change(function(){
            window.location.href = "?cp_dayid="+$(this).val();
        })
        $("#doData").click(function() {
            $(this).html('同步中......');
            $.ajax({
                url: 'suanfa_do.php',
                dataType: 'html',
                type: 'post',
                data: {
                    action: 'cool_hot'
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
              <?php foreach (getDaySel() as $dayid => $daytxt) { ?>
                <option value="<?php echo $dayid ?>" <?php echo isset($cp_dayid) && $cp_dayid == $dayid ? 'selected' : '' ?>><?php echo $daytxt ?></option>
              <?php } ?>
          </select>
          </div>
        </form> 
        <!-- <a href="javascript:;" class="btn btn-info btn-sm active pull-right" id="doData" role="button">红球遗漏冷热出球</a> -->
        <div class="clearfix"></div>
        <div class="bs-example" data-example-id="contextual-table">

            <table class="table">
                <thead>
                    <tr>
                        <th width="">数字走势</th>
                        <th width="">出现次数</th>
                        <th width="">占比</th>
                        <th width="">命中</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        
                        $dosql->Execute("SELECT * FROM `#@__caipiao_cool_hot` WHERE cp_dayid<2017071");
                        $data = array();
                        $data6 = array();
                        while($row = $dosql->GetArray()){
                        	$data[$row['cp_dayid']] = $row['hot_num'];
                        	if($row['hot_num'] == 4){
                        		$data6[$row['cp_dayid']] = $row['hot_num'];
                        	}
                        }
                        // print_r($data6);die;

                        $result = array();
                        foreach ($data6 as $cp_dayid => $hot_num) {
                        	$newid = $cp_dayid+1;
                        	if(!isset($data[$newid])){
                        		continue;
                        	}
                        	if(!isset($result[$data[$newid]])){
                        		$result[$data[$newid]] = 0;
                        	}
                        	$result[$data[$newid]]++;
                        }
                        arsort($result);


                        $sum = array_sum($result);
                        echo "共：".$sum;
                        echo "<br/>";
                        foreach ($result as $miss => $count) {
                        	echo "{$miss}情况出现 {$count} 次，占比： ".(round($count / $sum, 4)*100).'%';
                        	echo "<br/>";
                        }
                        // die;
                    ?>
                    <tr class="<?php echo $i % 2 == 1 ? 'info' : 'active'; ?>">
                        <td><?php echo $row['cp_dayid'] ?></td>
                        <td><?php echo $row['opencode'] ?></td>
                        <td><?php echo $row['hot_num'] . ' - ' . $row['warm_num'] . ' - ' . $row['cool_num'] ?></td>
                        <td><?php echo $row['miss_sum'] ?></td>
                        <td>
                        <?php 
                        	$win_miss = unserialize($row['win_miss']);
                            foreach ($win_miss as $tmp_red => $tmp_miss) {
                                echo '<button class="btn btn-danger" type="button">'.$tmp_red . ' <span class="badge">' . $tmp_miss.'</button>';
                            }
                        ?>
                        </td>
                        <td>
                        	<?php 
                        		$win_miss = array_keys($win_miss);
								$miss_content = unserialize($row['miss_content']);
                        	    foreach ($miss_content as $tmp_red => $tmp_miss) {
                        	    	if(in_array($tmp_red, $win_miss)){
                        	    		continue;
                        	    	}
                        	        echo '<button class="btn btn-primary" type="button">'.$tmp_red . ' <span class="badge">' . $tmp_miss.'</button>';
                        	    }
                        	?>
                        </td>
                    </tr>
                    <?php //} ?>
                </tbody>
            </table>
            <?php echo $dopage->GetList(); ?>
        </div>
    </div>
    <!-- /.container-fluid -->
</nav>
</body>
</html>