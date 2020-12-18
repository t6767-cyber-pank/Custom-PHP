<?php
$conn=mysql_pconnect('localhost','xxxxxxxxxxxxx','xxxxxxxxxxxxxxx', 'salonkrasoty') or die("error connecting to database");
mysql_query("set names utf8");

  function mysql_pconnect($server,$username,$password, $dbname){
      $link = mysqli_connect($server, $username, $password, $dbname);
    return $link;
  }
/*  function mysql_select_db($dbname,$myconn=''){
    global $conn;
    if ($myconn=='')$myconn=$conn;
    return mysq_select_db($myconn,$dbname);
  }
*/
  function mysql_query($query,$myconn=''){
    global $conn;
    if ($myconn=='')$myconn=$conn;
    return mysqli_query($myconn,$query);
  }
  function mysql_real_escape_string($str,$myconn=''){
    global $conn;
    if ($myconn=='')$myconn=$conn;
    return mysqli_real_escape_string($myconn,$str);
  }
  function mysql_insert_id($myconn=''){
    global $conn;
    if ($myconn=='')$myconn=$conn;
    return mysqli_insert_id($myconn);
  }
  function mysql_affected_rows($myconn=''){
    global $conn;
    if ($myconn=='')$myconn=$conn;
    return mysqli_affected_rows($myconn);
  }
  function mysql_fetch_array($r){
    return mysqli_fetch_array($r);
  }
  function mysql_num_rows($r){
    return mysqli_num_rows($r);
  }
  function mysql_data_seek($r,$i){
    return mysqli_data_seek($r,$i);
  }
  function mysql_result($res,$row=0,$col=0){
    $numrows = mysqli_num_rows($res);
    if ($numrows && $row <= ($numrows-1) && $row >=0){
        mysqli_data_seek($res,$row);
        $resrow = (is_numeric($col)) ? mysqli_fetch_row($res) : mysqli_fetch_assoc($res);
        if (isset($resrow[$col])){
            return $resrow[$col];
        }
    }
    return false;
  }
?>