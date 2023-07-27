<?php

require_once dirname(__FILE__).'/../include/config.inc.php';

?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>科学预测 - 玩转数字游戏</title>
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
    <script type="text/javascript" src="bootstrap/js/jquery.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript">
    $(document).ready(function() {
        $('.lan-show').hide();
        // $(function(){
        $('#qiu_type1').click(function() {
            $('.lan-show').hide();
            $('.red-show').show();
        })
        $('#qiu_type2').click(function() {
            $('.red-show').hide();
            $('.lan-show').show();
        })
    })
    </script>
</head>

<body>
<nav class="navbar navbar-default">
    <div class="container-fluid">
        <?php include('navbar.php') ?>
        
        <form class="form-horizontal" action="cpsave.php" method="post">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">选择红色球、蓝色球</label>
                <div class="col-sm-10">
                    <div class="radio">
                        <label>
                            <input type="radio" name="qiu_type" id="qiu_type1" value="1" checked> 红色球
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            <input type="radio" name="qiu_type" id="qiu_type2" value="2"> 蓝色球
                        </label>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">期数</label>
                <div class="col-sm-10">
                    <select class="form-control" name="cp_dayid">
                        <option value="">--请选择--</option>
                        <?php $monthNum = date('m') * 4 * 3; ?>
                        <?php for($i=$monthNum; $i>=1; $i--){ ?>
                        <?php //for($i=1; $i<=32; $i++){ ?>
                        <?php 
                          $cp_dayid = date('Y');
                          if($i >= 100){
                            $cp_dayid .= $i;
                          }else if($i >= 10 && $i < 100){
                            $cp_dayid .= '0'.$i;
                          }else{
                            $cp_dayid .= '00'.$i;
                          }
                        ?>
                        <option value="<?php echo $cp_dayid ?>" <?php echo $cp_dayid=='2017031001' ? 'selected' : ''; ?>>
                            <?php echo $cp_dayid; ?>
                        </option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="form-group red-show">
                <label for="inputEmail3" class="col-sm-2 control-label">算法</label>
                <div class="col-sm-10">
                    <select class="form-control" name="suanfa_id">
                        <option value="">--请选择--</option>
                        <?php for($suanfa_id=1; $suanfa_id<=10; $suanfa_id++){ ?>
                        <option value="<?php echo $suanfa_id ?>">算法
                            <?php echo $suanfa_id; ?>
                        </option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">推荐红球、篮球号</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="tj_num" id="tj_num" placeholder="推荐红球、篮球号">
                </div>
            </div>
            <div class="form-group lan-show">
                <label for="inputEmail3" class="col-sm-2 control-label">推荐杀蓝号</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="kill_lan_num" id="kill_lan_num" placeholder="推荐杀蓝号">
                </div>
            </div>
            <div class="form-group red-show">
                <label for="inputEmail3" class="col-sm-2 control-label">红球猜中数量</label>
                <div class="col-sm-10">
                    <div class="radio">
                        <label>
                            <input type="radio" name="up_down" id="up_down" value="1" checked> 看多
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            <input type="radio" name="up_down" id="up_down" value="2"> 看少
                        </label>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <input type="hidden" name="action" value="cp_suanfa">
                    <p style="margin-top: 30px;"></p>
                    <button type="submit" class="btn btn-primary">添加数据</button>
                </div>
            </div>
        </form>
    </div>
    <!-- /.container-fluid -->
</nav>
</body>

</html>
