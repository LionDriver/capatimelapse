<?php
error_reporting(E_ALL);

include "db.php";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo 'Sorry folks, the ride is closed today, come back soon, ERROR: ' . $conn->connect_error;
    exit;
}

$sql = "SELECT imgNice FROM imgdat";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $images[] = $row["imgNice"];
    }
}
krsort($images);
$lastimage = key($images);

if (isset($_GET['delit'])) {
	$url = $_GET['delit'];
	echo $url;
	#$sql = 'DELETE from imgdat Where imgNice = "'.$url.'"';
	#$result = $conn->query($sql);
	#unlink($url);
	header("Refresh:0");
}

echo <<< EOT
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="./css/smartphoto.min.css">
	<title>Photo Gallery</title>
	<link rel="stylesheet" href="./css/gallery.css">
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
		<h1 class="title page-header">Photo Gallery</h1>
		<input class="btn btn-primary" type="button" value="Back" onclick="window.history.back()"/>
		<br>
		<br>	
EOT;

if (count($images) <= 0) {
    echo "<h2>No Images Available</h2>";
}
else {
 	echo '<div class="row">';
 	$images = array_slice($images, -100, 100, true);
 	foreach ($images as $image){
 	   $thumb = exif_thumbnail($image, $width, $height, $type);
 	    if ($thumb === false) {
 	    	echo '<div class="thumbnail">Please Wait...</div>';
 		}
 	    else {

 	    	//header('Content-type: ' .image_type_to_mime_type($type));
 	    	$nice = date('D, d M y H:i:s', filectime($image));
 	    	echo '<div class="pictureframe col-lg-3 col-sm-4 col-xs-6 col-6">';
 	    	echo "<a href=".$image.' class="js-img-viwer" data-caption="'.$nice.'" data-id="'.$nice.'">';
	        echo "<img width='$width' class='thumbnail img-responsive' height='$height' src='data:image;base64,".base64_encode($thumb)."'/></a>";
	        echo '<div class="caption">';
 	        echo '<p class="date">'.$nice.'&nbsp;&nbsp;&nbsp;<a href='.$image.' download><span class="glyphicon glyphicon-download-alt"></span></a>';
 	        echo '&nbsp;<a href="" onclick=delimg('.$image.')><span class="glyphicon glyphicon-remove-circle"></span></a></p>';
 	    	echo '</div></div>';
 	    }
 	}
}

echo <<< EOT
		</div>
	</div>
	<script src="./js/smartphoto.js?v=1"></script>
	<script>
	document.addEventListener('DOMContentLoaded',function(){
		new smartPhoto(".js-img-viwer");
	});
	</script>
	<div class="row"></div>
	<div class="container">
	<footer class="footer-bottom">
	      <div class="container">
	        <div class="col-xs-5 col-md-5"></div>
	      	<div class="col-xs-3 col-md-3"></div>
	        <div class="col-xs-4 col-md-4">
	        	<p class="text-right text-muted">
	        	<span class="glyphicon glyphicon-wrench"></span>  
	        	Osteen Industries 2017&#8482</p>
	        </div>
	      </div>
	</footer>
	</div>
	<script type="text/javascript" src="js/gallery.js"></script>
	<script src="js/jquery-3.2.1.slim.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
</body>
</html>
</html>
EOT;
mysqli_close($conn);
?>