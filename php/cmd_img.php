<?php


  function deleteOne($imgs) {
    #include "../db.php";
    #unlink($url);
    #$conn = new mysqli($servername, $username, $password, $dbname);
    #if ($conn->connect_error) {} else {
    #  $sql = 'DELETE from imgdat Where imgNice = "'.$url.'"';
    #  $conn->query($sql);
    #  mysqli_close($conn);
    return "done";
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
      deleteOne($GET_['img']);
    }
  }

  if(isset($_GET['cmd'])) {
    $cmd=$_GET['cmd'];
    sys_cmd($cmd);
  }
?>
