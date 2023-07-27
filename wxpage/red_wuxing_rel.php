<?php
require_once dirname(__FILE__).'/../include/config.inc.php';
require_once dirname(__FILE__).'/core/suanfa.func.php';
require_once dirname(__FILE__).'/core/ssq.config.php';

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
    function godayurl(){
        window.location.href = "?limit_num="+$("#limit_num").val();
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
        <form class="navbar-form navbar-left" role="search">
          <div class="form-group">
          <label for="exampleInputEmail2">选择期数</label>
          <select class="form-control" name="limit_num" id="limit_num" onchange="godayurl()">
              <option value="">--请选择--</option>
              <?php foreach (getSelArr() as $daynum => $daytxt) { ?>
                <option value="<?php echo $daynum ?>" <?php echo isset($limit_num) && $limit_num == $daynum ? 'selected' : '' ?>><?php echo $daytxt ?></option>
              <?php } ?>
          </select>
          </div>
        </form>
        <div class="bs-example" data-example-id="contextual-table">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>期数</th>
                        <th>开奖日</th>
                        <th>开奖日五行</th>
                        <th>天干地支</th>
                        <th>红1</th>
                        <th>==========></th>                  
                        <th>红2</th>
                        <th>==========></th>     
                        <th>红3</th>
                        <th>==========></th>    
                        <th>红4</th>
                        <th>==========></th>    
                        <th>红5</th>
                        <th>==========></th>      
                        <th>红6</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $limit_num = isset($limit_num) ? $limit_num : 30;
                        $sql = "SELECT * FROM `#@__caipiao_wuxing` WHERE 1 ";
                        $sql .= " ORDER BY cp_dayid DESC limit $limit_num";

                        $i = 0;
                        $rows = array();
                        $dosql->Execute($sql);
                        while($row = $dosql->GetArray()){
                            $rows[] = $row;
                        }

                        $rows = array_reverse($rows);
                        foreach($rows as $row){
                            $i++;
                            $reds = explode(",", $row['red_num']);
                            $wuxing_rel = json_decode($row['wuxing_rel'],true);
                    ?>
                    <tr class="active">
                        <th scope="row"><?php echo $i ?></th>
                        <td><?php echo $row['cp_dayid'] ?></td>
                        <td><?php echo $row['cp_day'] ?></td>
                        <td><?php echo $row['day_attr'] ?></td>
                        <td><?php echo $row['tiangan_attr']."、".$row['dizhi_attr'] ?></td>
                        <td><?php echo $reds[0] ?></td>
                        <td><?php echo '——》'.$wuxing_rel[0].'——》' ?></td>
                        <td><?php echo $reds[1] ?></td>
                        <td><?php echo '——》'.$wuxing_rel[1].'——》' ?></td>
                        <td><?php echo $reds[2] ?></td>
                        <td><?php echo '——》'.$wuxing_rel[2].'——》' ?></td>
                        <td><?php echo $reds[3] ?></td>
                        <td><?php echo '——》'.$wuxing_rel[3].'——》' ?></td>
                        <td><?php echo $reds[4] ?></td>
                        <td><?php echo '——》'.$wuxing_rel[4].'——》' ?></td>
                        <td><?php echo $reds[5] ?></td>
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
