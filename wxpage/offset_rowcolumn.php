<?php

require_once dirname(__FILE__).'/../include/config.inc.php';
require_once dirname(__FILE__).'/core/ssq.config.php';

LoginCheck();

$rowColumn = getRowColumn();

?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>行列图遗漏追踪系统 - <?php echo $cfg_seotitle; ?></title>
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
    <script type="text/javascript" src="bootstrap/js/jquery.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript">
    function gourl() {
        window.location.href = "?limit_num=" + $("#limit_num").val()+"&cp_dayid=" + $("#cp_dayid").val();
    }

    function showspace(){
        var space_num = $('#space_num').val();
        $('.space_table').each(function(){
            var t_space = $(this).data('space');
            if(t_space == space_num){
                $(this).show();
            }else{
                $(this).hide();
            }
        })
    }

    $(document).ready(function() {
        $("#duiJiangBtn").click(function() {
            var cp_dayid = $("#cp_dayid").val();
            $(this).html('计算中......');
            $.ajax({
                url: 'ajax/red_offset_do.php',
                dataType: 'html',
                type: 'post',
                data: {
                    action: 'offset_rowcolumn',
                    cp_dayid: cp_dayid
                },
                success: function(data) {
                    alert(data)
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
            </form>
            <?php if(isset($_COOKIE['isAdmin']) && $_COOKIE['isAdmin'] == $adminkey){ ?>
            <a href="javascript:;" class="btn btn-danger btn-sm active pull-right" id="duiJiangBtn" role="button">下期行列出球数预判</a>
            <?php } ?>
            <div class="bs-example" data-example-id="contextual-table">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="">期数</th>
                            <th width="">第1行</th>
                            <th width="">第2行</th>
                            <th width="">第3行</th>
                            <th width="">第4行</th>
                            <th width="">第5行</th>
                            <th width="">第6行</th>
                            <th width="">行列</th>
                            <th width="">第1列</th>
                            <th width="">第2列</th>
                            <th width="">第3列</th>
                            <th width="">第4列</th>
                            <th width="">第5列</th>
                            <th width="">第6列</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $limit_num = isset($limit_num) && !empty($limit_num) ? $limit_num : 10;
                            $maxid = maxDayid();
                            $gt_cp_dayid = $maxid - $limit_num+1;

                            if(isset($cp_dayid) && !empty($cp_dayid)){
                                $sql = "SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<$cp_dayid ORDER BY cp_dayid DESC LIMIT $limit_num";
                            }else{
                                $sql = "SELECT * FROM `#@__caipiao_history` ORDER BY cp_dayid DESC LIMIT $limit_num";
                            }
                            $dosql->Execute($sql);

                            $data = array();
                            while($row = $dosql->GetArray()){
                                $data[$row['cp_dayid']] = $row;
                            }
                            $data = array_reverse($data);

                            // $newdata  = end($data);
                            // $curReds  = explode(",", $newdata);
                            // $curWinRC = getWinRowCol($curReds, $rowColumn);
                            // print_r($curWinRC);die;

                            $winrowMiss = array(1=>0, 2=>0, 3=>0, 4=>0, 5=>0, 6=>0);
                            $columnMiss = array(1=>0, 2=>0, 3=>0, 4=>0, 5=>0, 6=>0);

                            foreach ($data as $cp_dayid => $row) {
                                $reds = explode(',', $row['red_num']);

                                $winrow = array(1=>0, 2=>0, 3=>0, 4=>0, 5=>0, 6=>0);
                                foreach ($rowColumn['row'] as $rownum => $rowreds) {
                                    $notin = true;
                                    foreach ($reds as $red) {
                                        if(in_array($red, $rowreds)){
                                            $winrow[$rownum]++;
                                            $notin = false;
                                        }
                                    }
                                    if($notin){
                                        $winrowMiss[$rownum]++;
                                    }
                                }

                                $wincolumn = array(1=>0, 2=>0, 3=>0, 4=>0, 5=>0, 6=>0);
                                foreach ($rowColumn['col'] as $columnnum => $columnreds) {
                                    $notin = true;
                                    foreach ($reds as $red) {
                                        if(in_array($red, $columnreds)){
                                            $wincolumn[$columnnum]++;
                                            $notin = false;
                                        }
                                    }
                                    if($notin){
                                        $columnMiss[$columnnum]++;
                                    }
                                }
                        ?>
                        <tr>
                            <td><?php echo $row['cp_dayid'] ?></td>
                            <td><?php echo $winrow[1] ?></td>
                            <td><?php echo $winrow[2] ?></td>
                            <td><?php echo $winrow[3] ?></td>
                            <td><?php echo $winrow[4] ?></td>
                            <td><?php echo $winrow[5] ?></td>
                            <td><?php echo $winrow[6] ?></td>
                            <td>|</td>
                            <td><?php echo $wincolumn[1] ?></td>
                            <td><?php echo $wincolumn[2] ?></td>
                            <td><?php echo $wincolumn[3] ?></td>
                            <td><?php echo $wincolumn[4] ?></td>
                            <td><?php echo $wincolumn[5] ?></td>
                            <td><?php echo $wincolumn[6] ?></td>
                        </tr>
                        <?php    }
                        ?>
                        <tr>
                            <td><?php echo '列行遗漏统计' ?></td>
                            <td style="color: red; font-size: 18px;"><?php echo $winrowMiss[1] ?></td>
                            <td style="color: red; font-size: 18px;"><?php echo $winrowMiss[2] ?></td>
                            <td style="color: red; font-size: 18px;"><?php echo $winrowMiss[3] ?></td>
                            <td style="color: red; font-size: 18px;"><?php echo $winrowMiss[4] ?></td>
                            <td style="color: red; font-size: 18px;"><?php echo $winrowMiss[5] ?></td>
                            <td style="color: red; font-size: 18px;"><?php echo $winrowMiss[6] ?></td>
                            <td></td>
                            <td style="color: red; font-size: 18px;"><?php echo $columnMiss[1] ?></td>
                            <td style="color: red; font-size: 18px;"><?php echo $columnMiss[2] ?></td>
                            <td style="color: red; font-size: 18px;"><?php echo $columnMiss[3] ?></td>
                            <td style="color: red; font-size: 18px;"><?php echo $columnMiss[4] ?></td>
                            <td style="color: red; font-size: 18px;"><?php echo $columnMiss[5] ?></td>
                            <td style="color: red; font-size: 18px;"><?php echo $columnMiss[6] ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /.container-fluid -->
    </nav>
</body>

</html>
