<?
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];

if (isset($_GET['logout']) && $_GET['logout']==1){
  setcookie("name",'',time()-3600,'/');
  setcookie("password",'',time()-3600,'/');
  header("Location:/program.php");
  exit;
}

include("$DOCUMENT_ROOT/mysql_connect.php");
if (isset($_COOKIE['name']) && $_COOKIE['name']!=''){
  $name = mysql_real_escape_string($_COOKIE['name']);
  $password = mysql_real_escape_string($_COOKIE['password']);
  $r = mysql_query("select * from users where name='$name' and md5(password)='$password'");
  if (mysql_num_rows($r)==0){
    setcookie("name",'',time()-3600,'/');
    setcookie("password",'',time()-3600,'/');
    header("Location:/program.php");
  }else{
    $a = mysql_fetch_array($r);
    $id = $a['id'];
    $type = $a['type'];
    show_content($id,$type);
  }
  exit;
}elseif (isset($_POST['trylogin']) && $_POST['trylogin']==1){//пытаемся залогиниться
  $name = mysql_real_escape_string($_POST['name']);
  $password = mysql_real_escape_string($_POST['password']);
  $r = mysql_query("select * from users where name='$name' and password='$password'");
  if (mysql_num_rows($r)==0){
 //   header("Location:/program.php");
      header("Location:/program.php?notuser=1");
  }else{
    $a = mysql_fetch_array($r);
    if (isset($_POST['longstay']) && $_POST['longstay']==1){
      setcookie("name",$name,time()+3600*24*365,'/');
      setcookie("password",md5($password),time()+3600*24*365,'/');
    }else{
      setcookie("name",$name,0,'/');
      setcookie("password",md5($password),0,'/');
    }
    header("Location:/program.php");
  }
  exit;
}else{
?>
    <html>
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
        <link href="dist/css/main.css" rel="stylesheet" type="text/css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-offset-3 col-md-6">
            <?php if(isset($_GET['notuser'])) { echo "<script> alert('Вы введи неправильный логин или пароль');</script>"; } ?>
            <div align="center"><h2 class="texth2">Войти</h2></div>
            <form method="POST" class="form-horizontal">
                <div class="form-group">
                    <input type="text" class="form-control" name="name" id="name" placeholder="Введите Имя">
                    <input type="hidden" name="trylogin" id="trylogin" value=1>
                    <i class="fa fa-user"></i>
                </div>
                <div class="form-group help">
                    <input type="password" class="form-control" name="password" id="password" placeholder="Введите Пароль">
                    <i class="fa fa-lock"></i>
                </div>
                <div class="form-group" >
                    <div class="main-checkbox">
                        <input type="checkbox" name='longstay' id="checkbox" value="1"/>
                        <label for="checkbox">Оставаться в системе</label>
                    </div>
                </div>
                <button type="submit" class="btn btn-default">Войти</button>
            </form>
        </div>
    </div><!-- /.row -->
</div><!-- /.container -->
<?
}
function show_content($id,$type){
  global $DOCUMENT_ROOT;
  if ($type==3){
    include("$DOCUMENT_ROOT/program_main.php");
  }
  if ($type==6){
    include("$DOCUMENT_ROOT/program_main.php");
  }
}
mysql_close($conn);
?>