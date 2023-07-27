<?php

require_once dirname(__FILE__).'/../include/config.inc.php';

if(isset($action) && $action == 'login'){
    if(empty($password)){
        ShowMsg("请输入访问口令！", "login.php");
        exit;
    }else{
        $password = md5($password);
        if(!(in_array($password, $pwdList['password']) || in_array($password, $pwdList['parnter']) || in_array($password, $pwdList['admin']))){
           ShowMsg("请输入正确的口令！", "login.php"); 
           exit;
        }else{
           setcookie('password', $password, time()+604800, '/');
           if(in_array($password, $pwdList['admin'])){
             setcookie('isAdmin', $adminkey, time()+604800, '/');
           }if(in_array($password, $pwdList['parnter'])){
             setcookie('isParnter', $adminkey, time()+604800, '/');
           }else{
              if(isset($_SESSION[$password])){
                $_SESSION[$password] = $_SESSION[$password]+1;
                if($_SESSION[$password]+1 >= 1){
                  // ShowMsg("该口令使用人数已满！请联系管理员索要访问口令！QQ：962823142", "-1");
                  // exit;
                }
              }
              else{
                $_SESSION[$password] = 1;
              }
           }
           if(isset($_COOKIE['loginurl']) && !empty($_COOKIE['loginurl'])){
              $loginurl = $_COOKIE['loginurl'];
              setcookie('loginurl', '', time()-3600, '/');
              header("location: $loginurl");
           }else{
             header("location: index.php");
           }
        }
    }
}else{
  if(isset($_COOKIE['password']) && (in_array($_COOKIE['password'], $pwdList['admin']) || in_array($_COOKIE['password'], $pwdList['password']))){
     header("location: index.php");
  }
}

?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"> -->
    <title>请输入口令</title>
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
    <script type="text/javascript" src="bootstrap/js/jquery.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript">
    function godayurl(){
        window.location.href = "?limit_num="+$("#limit_num").val();
    }
    </script>
</head>

<body>
    <div class="container">
        <div style="margin-top: 10%;"></div>
        <h2 style="text-align: center;"><a href="<?php echo $cfg_weburl ?>"><?php echo $cfg_seotitle; ?>欢迎您</a></h2> 
        <div style="margin-top: 10%;"></div>
        <form class="form-horizontal" action="login.php" method="post">
          <input type="hidden" name="action" value="login">
          <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">访问请输入口令</label>
            <div class="col-sm-10">
              <input type="password" class="form-control" id="password" name="password" placeholder="输入口令">
            </div>
          </div>
          <!-- <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
              <div class="checkbox">
                <label>
                  <input type="checkbox"> Remember me
                </label>
              </div>
            </div>
          </div> -->
          <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
              <button type="submit" class="btn btn-default">访问</button>
            </div>
          </div>
        </form>
    </div>
    <!-- /.container-fluid -->
</body>
</html>