<?php

require_once dirname(__FILE__).'/../include/config.inc.php';
require_once dirname(__FILE__).'/core/ssq.config.php';
require_once dirname(__FILE__).'/core/choosered.func.php';
LoginCheck();

?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"> -->
    <title>前12期红球命中情况 - <?php echo $cfg_seotitle; ?></title>
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
    <script type="text/javascript" src="bootstrap/js/jquery.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript">
    function gourl(){
        window.location.href = "?cp_dayid="+$("#cp_dayid").val();
    }
    </script>
</head>

<body>
<nav class="navbar navbar-default">
    <div class="container-fluid">
        <?php include('navbar.php') ?>
        
        <form class="navbar-form navbar-left" role="search">
          <div class="form-group">
            <label for="exampleInputEmail2">期数</label>
            <select class="form-control" name="cp_dayid" id="cp_dayid" onchange="gourl()">
            <option value="">--请选择--</option>
            <?php foreach (getDaySel(30) as $t_cp_dayid => $cp_dayidtxt) { ?>
            <option value="<?php echo $t_cp_dayid ?>" <?php echo isset($cp_dayid) && $t_cp_dayid == $cp_dayid ? 'selected' : '' ?>><?php echo $cp_dayidtxt ?></option>
            <?php } ?>
            </select>
          </div>
        </form> 
        
        <div class="clearfix"></div>
        <div class="bs-example" data-example-id="contextual-table">
            <table class="table">
                <?php 
                    $maxid = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` order by cp_dayid DESC");
                    $maxid = $maxid['cp_dayid'];

                    $cp_dayid = isset($cp_dayid) && !empty($cp_dayid) ? $cp_dayid : '';
                    $redTail = redUpTailTrend($cp_dayid);
                    $redTail2 = array_values($redTail);

                    $allBlue = array();
                    for ($i=0; $i < 10; $i++) { 
                        $allBlue[] = $i;
                    }

                    $curWinTail = array();
                    $curNoWinTail = array();
                    $preWinTail = array();
                    if(!empty($cp_dayid)){
                        $cur = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid='$cp_dayid'");
                        $curRed = explode(",", $cur['red_num']);
                        foreach ($curRed as $tmpred) {
                            $tmptail = $tmpred % 10;
                            if(!in_array($tmptail, $curWinTail)){
                                $curWinTail[] = $tmptail;
                            }
                        }
                        sort($curWinTail);
                        $curNoWinTail = array_diff($allBlue, $curWinTail);

                        $pre = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<'$cp_dayid' ORDER BY cp_dayid DESC");
                        $pre = $dosql->GetOne("SELECT * FROM `#@__caipiao_history` WHERE cp_dayid<'{$pre['cp_dayid']}' ORDER BY cp_dayid DESC");
                        $preRed = explode(",", $pre['red_num']);
                        foreach ($preRed as $tmpred) {
                            $tmptail = $tmpred % 10;
                            if(!in_array($tmptail, $preWinTail)){
                                $preWinTail[] = $tmptail;
                            }
                        }
                        sort($preWinTail);
                    }
                ?>
                <thead>
                    <tr>
                        <th width="">期数</th>
                        <th width="">继续下落的尾</th>
                        <th width="">出尾</th>
                        <th width="">上上尾</th>
                        <th width="">余尾</th>
                        <th width="">上期余尾下落</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="primary">
                        <td>颜色块解释</td>
                        <td><button class="btn btn-danger" type="button">红色</button>上第4期：连续下落的尾</td>
                        <td><button class="btn btn-warning" type="button">橙色</button>上第4期：余尾下落的尾</td>
                        <td><button class="btn btn-danger" type="button">红色</button>上上期：下落的尾</td>
                        <td><button class="btn btn-default" type="button">白色</button>上第4期：未下落的尾</td>
                        <td><button class="btn btn-success" type="button">绿色</button>上第4期：余尾下落的尾</td>
                    </tr>
                    <?php foreach($redTail as $tmp_id => $row) {?>
                    <tr class="info">
                        <td><?php echo $row['cp_dayid'] ?></td>
                        <td>
                        <?php 
                            $lefttail = array();
                            if(isset($redTail[$tmp_id-1])){
                                $lefttail = array_intersect($row['win'], $redTail[$tmp_id-1]['win']);
                            }
                            foreach ($lefttail as $tmptail) {
                                echo '<button class="btn btn-danger" type="button">'.$tmptail . '</button>';
                            };
                            if(isset($redTail[$tmp_id-1])){
                                echo '<button class="btn btn-primary" type="button">'.count($redTail[$tmp_id-1]['win']) . '出' . count($lefttail) . '</button>';
                            }
                        ?>
                        </td>
                        <td>
                        <?php 

                            $nowin_righttail = isset($redTail[$tmp_id-1]['nowin']) ? array_intersect($row['win'], $redTail[$tmp_id-1]['nowin']) : array();
                            foreach ($row['win'] as $tmptail) {
                                if(in_array($tmptail, $nowin_righttail)){
                                    echo '<button class="btn btn-warning" type="button">'.$tmptail . '</button>';
                                }else{
                                    echo '<button class="btn btn-default" type="button">'.$tmptail . '</button>';
                                }
                            };
                        ?>
                        </td>
                        <td>
                        <?php 
                            if(!empty($cp_dayid) && $tmp_id == count($redTail) - 1){
                                $win_nexttail = array_intersect($row['pretail'], $curWinTail);
                                // print_r($win_nexttail);
                            }else{
                                $win_nexttail = isset($redTail[$tmp_id+1]['win']) ? array_intersect($row['pretail'], $redTail[$tmp_id+1]['win']) : array();
                            }
                            foreach ($row['pretail'] as $tmptail) {
                                if(in_array($tmptail, $win_nexttail)){
                                    echo '<button class="btn btn-danger" type="button">'.$tmptail . '</button>';
                                }else{
                                    echo '<button class="btn btn-default" type="button">'.$tmptail . '</button>';
                                }
                            };
                        ?>
                        </td>
                        <td>
                        <?php 
                            $win_lefttail = isset($redTail[$tmp_id-1]['win']) ? array_intersect($row['nowin'], $redTail[$tmp_id-1]['win']) : array();
                            foreach ($row['nowin'] as $tmptail) {
                                if(in_array($tmptail, $win_lefttail)){
                                    echo '<button class="btn btn-default" type="button">'.$tmptail . '</button>';
                                }else{
                                    echo '<button class="btn btn-warning" type="button">'.$tmptail . '</button>';
                                }
                            };
                        ?>
                        </td>
                        <td>
                        <?php 
                            $righttail = array();
                            if(isset($redTail[$tmp_id-1])){
                                $righttail = array_intersect($row['win'], $redTail[$tmp_id-1]['nowin']);
                            }
                            foreach ($righttail as $tmptail) {
                                echo '<button class="btn btn-success" type="button">'.$tmptail . '</button>';
                            };
                            if(isset($redTail[$tmp_id-1])){
                                echo '<button class="btn btn-primary" type="button">'.count($redTail[$tmp_id-1]['nowin']) . '出' . count($righttail) . '</button>';
                            }
                        ?>
                        </td>
                    </tr>
                    <?php } ?>
                    <tr class="danger">
                        <td><?php echo end($redTail2)['cp_dayid']+4 ?></td>
                        <td>
                        <?php 
                            $end = end($redTail);
                            if($curWinTail){
                                $lefttail = array_intersect($curWinTail, $end['win']);
                                foreach ($lefttail as $tmptail) {
                                    echo '<button class="btn btn-danger" type="button">'.$tmptail . '</button>';
                                };
                                echo '<button class="btn btn-primary" type="button">'.count($end['win']) . '出' . count($lefttail) . '</button>';
                            }
                        ?>
                        </td>
                        <td>
                        <?php 
                            if($curNoWinTail){
                                $nowin_righttail = array_intersect($curWinTail, $end['nowin']);
                                foreach ($curWinTail as $tmptail) {
                                    if(in_array($tmptail, $nowin_righttail)){
                                        echo '<button class="btn btn-warning" type="button">'.$tmptail . '</button>';
                                    }else{
                                        echo '<button class="btn btn-default" type="button">'.$tmptail . '</button>';
                                    }
                                };
                            }
                        ?> 
                        </td>
                        <td>
                        <?php 
                            // if($curNoWinTail){
                            //     $win_lefttail = array_intersect($curNoWinTail, $end['win']);
                            //     foreach ($curNoWinTail as $tmptail) {
                            //         if(in_array($tmptail, $win_lefttail)){
                            //             echo '<button class="btn btn-default" type="button">'.$tmptail . '</button>';
                            //         }else{
                            //             echo '<button class="btn btn-warning" type="button">'.$tmptail . '</button>';
                            //         }
                            //     };
                            // }
                        ?>
                        </td>
                        <td>
                        <?php 
                            if($curNoWinTail){
                                $win_lefttail = array_intersect($curNoWinTail, $end['win']);
                                foreach ($curNoWinTail as $tmptail) {
                                    if(in_array($tmptail, $win_lefttail)){
                                        echo '<button class="btn btn-default" type="button">'.$tmptail . '</button>';
                                    }else{
                                        echo '<button class="btn btn-warning" type="button">'.$tmptail . '</button>';
                                    }
                                };
                            }
                        ?>
                        </td>
                        <td>
                        <?php 
                            if($curWinTail){
                                $righttail = array_intersect($curWinTail, $end['nowin']);
                                foreach ($righttail as $tmptail) {
                                    echo '<button class="btn btn-success" type="button">'.$tmptail . '</button>';
                                };
                                echo '<button class="btn btn-primary" type="button">'.count($end['nowin']) . '出' . count($righttail) . '</button>';
                            }
                        ?> 
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <!-- /.container-fluid -->
</nav>
</body>
</html>