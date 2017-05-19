<?php
error_reporting(E_ALL);

function deleteOne($img) {
  #include "../db.php";
  echo "thanks and have a nice day";
  #unlink("../".$img);
  #$conn = new mysqli($servername, $username, $password, $dbname);
  #if ($conn->connect_error) {
  #  return $conn->connect_error;
  #} else {
  #  $val = 'SELECT from imgdat WHERE imgNice = "'.$img.'"';
    #$sql = 'DELETE from imgdat WHERE imgNice = "'.$img.'"';
  #  $ret = $conn->query($val);
  #  mysqli_close($conn);
  #  echo $ret;
  #}
}

function deleteImgs() {
  include "../db.php";
  array_map('unlink', glob(dirname(__FILE__)."/../pics/*.jpg"));
  $conn = new mysqli($servername, $username, $password, $dbname);
  if ($conn->connect_error) {} else {
    $sql = "TRUNCATE TABLE imgdat";
    $conn->query($sql);
    mysqli_close($conn);
  }
}

function sys_cmd($cmd) {
  if(strncmp($cmd, "delimg", strlen("delimg")) == 0) {
    #$image = $_GET['img'];
    echo "have a nice day";
    #deleteOne($image);
  }
}

if(isset($_GET['cmd'])) {
  $cmd=$_GET['cmd'];
  sys_cmd($cmd);
}
?>
