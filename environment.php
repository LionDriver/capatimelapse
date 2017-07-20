<?php
error_reporting(E_ALL);

include "db.php";
$dbname = "sensors";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM sensdat ORDER BY sensId DESC LIMIT 1";
$result = $conn->query($sql);

if ($result === false){
    return "0";
}

$value = mysqli_fetch_array($result);
$temp = $value['temp'];
$hum = $value['hum'];
$strgth = $value['strgth'];
$dew = $value['dew'];
$alt = $value['alt'];
$timepoint = $value['timepoint'];
$hostname = $value['hname'];
$sensor1 = "sensor1";
$sensor2 = "sensor2";

$result1 = $conn->query('(SELECT * FROM sensdat WHERE hname="'.$sensor1.'" ORDER BY sensID DESC LIMIT 144) ORDER BY sensId ASC');
   
if ($result1->num_rows > 0) {
    while($row = $result1->fetch_assoc()) {
        $clean = substr($row['timepoint'], 11, -3);
        $timearray1[] = $clean;
        $temparray1[] = $row['temp'];
    }
} else {
    echo "Problem in sql query";
}

$result2 = $conn->query('(SELECT * FROM sensdat WHERE hname="'.$sensor2.'" ORDER BY sensID DESC LIMIT 144) ORDER BY sensId ASC');

if ($result2->num_rows > 0) {
    while($row = $result2->fetch_assoc()) {
        $clean = substr($row['timepoint'], 11, -3);
        $timearray2[] = $clean;
        $temparray2[] = $row['temp'];
    }
} else {
    echo "Problem in sql query";
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Environment Status</title>
  <link rel="stylesheet" href="./css/style.css">
  <link href="images/favicon.ico" rel="shortcut icon" type="image/x-icon" />
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
  <div class="container">
    <h1 class="title page-header">Environment Status</h1>
    <a href=index.html ><span class="glyphicon glyphicon-home"></span><a/>
    <br>
    <br>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);
      google.charts.setOnLoadCallback(drawChart2);
      var times = <?php echo json_encode($timearray1);?>;
      var temps = <?php echo json_encode($temparray1);?>;
      var times2 = <?php echo json_encode($timearray2);?>;
      var temps2 = <?php echo json_encode($temparray2);?>;

      function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Time');
        data.addColumn('number', 'Temperature');
        for (var i = 0; i < temps.length; i++) {
            data.addRow([times[i], parseInt(temps[i])]);
        }

        var options = {
          title: "<?php echo $sensor1;?>" + ' Temperature',
          curveType: 'function',
          width: '1100',
          height: '400',
          legend: 'none',
          grid: 'true'
        };
        var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));
        chart.draw(data, options);
      }

      function drawChart2() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Time');
        data.addColumn('number', 'Temperature');
        for (var i = 0; i < temps2.length; i++) {
            data.addRow([times2[i], parseInt(temps2[i])]);
        }

        var options = {
          title: "<?php echo $sensor2;?>" + ' Temperature',
          curveType: 'function',
          width: '1100',
          height: '400',
          legend: 'none',
          grid: 'true'
        };
        var chart = new google.visualization.LineChart(document.getElementById('curve_chart2'));
        chart.draw(data, options);      
      }
    </script>
  <div id="curve_chart" style="width: 1100px; height: 500px"></div>
  <div id="curve_chart2" style="width: 1100px; height: 500px"></div>
  <br>
  <div class="container">
  </div>
  <script src="js/jquery-3.2.1.slim.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  </body>
</html>
