<?php
  
  function outputProgress($current, $total) {
    echo '<div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width:70%"></div>';
  }

  function deleteOne($url) {
    include "../db.php";
    unlink('../'.$url);
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {} else {
      $sql = 'DELETE from imgdat Where imgNice = "'.$url.'"';
      $conn->query($sql);
      mysqli_close($conn);
      echo "done";
    }
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
    if(strncmp($cmd, "reboot", strlen("reboot")) == 0) {
      exec('sudo /sbin/shutdown -r now');
    } else if(strncmp($cmd, "shutdown", strlen("shutdown")) == 0) {
      exec('sudo /sbin/shutdown -h now');
    } else if(strncmp($cmd, "hostname", strlen("hostname")) == 0) {
      $hostname = system("hostname -s");
    } else if(strncmp($cmd, "ip", strlen("ip")) == 0) {
      echo $_SERVER['SERVER_ADDR'];
    } else if(strncmp($cmd, "snap", strlen("snap")) == 0) {
      exec('/home/pi/timelapse/pycam.py');
    } else if(strncmp($cmd, "bobhope", strlen("bobhope")) == 0) {
      deleteImgs();
    } else if(strncmp($cmd, "bobby", strlen("bobby")) == 0) {
      array_map('unlink', glob(dirname(__FILE__)."/../pics/*.tar"));
    } else if(strncmp($cmd, "imgdel", strlen("imgdel")) == 0) {
      deleteOne($_GET['img']);
    } else if(strncmp($cmd, "worldbank", strlen("worldbank")) == 0) {
      exec('sudo pkill pycam');
    } else if(strncmp($cmd, "zipit", strlen("zipit")) == 0) {
      ini_set('max_execution_time', 600);
      $images = glob('../pics/*.jpg', GLOB_BRACE);
      if (count($images) > 0) {
        $datetime = date("n-j-Y_H-i-s");
        $zipname = '../pics/photos-'.$datetime.'.tar';
        exec("tar -cf ".$zipname." ../pics/*.jpg 2>&1", $out, $status);
      } 
    }
  }

  if(isset($_GET['cmd'])) {
    $cmd=$_GET['cmd'];
    sys_cmd($cmd);
  }
?>
