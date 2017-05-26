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

mysqli_close($conn);

function deleteSingleImg($img) {
    if (file_exists($img)){
        include "db.php";
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            echo 'Delete ERROR: ' . $conn->connect_error;
            exit;
        }
        unlink($img);
        $sql = 'DELETE from imgdat WHERE imgNice = "'.$img.'"';
        if ($conn->query($sql) === TRUE) {
    		header("Refresh:0; url=gallery.php");
		} else {
    		echo "Error deleting record: " . $conn->error;
		}
        mysqli_close($conn);
    }
}

function getcount(){
	include "db.php";
	$conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        echo 'Database ERROR: ' . $conn->connect_error;
        exit;
    }
    $sql = 'SELECT * FROM imgdat';
    if ($res= $conn->query($sql)) {
    	$totalimg = $res->num_rows;  	
    	mysqli_close($conn);
    	return $totalimg;
    }
}

function scrolldown($lastimg){
	include "db.php";
	$conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        echo 'Database ERROR: ' . $conn->connect_error;
        exit;
    }
    $sql = 'SELECT id FROM imgdat WHERE imgNice ="'.$lastimg.'"';
    if ($res= $conn->query($sql)) {
    	$totalimg = $res->num_rows;  	
    	mysqli_close($conn);
    	return $totalimg;
    }
}

function scrollup($count){
	include "db.php";
	$conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        echo 'Database ERROR: ' . $conn->connect_error;
        exit;
    }
    $sql = 'SELECT * FROM imgdat';
    if ($res= $conn->query($sql)) {
    	$totalimg = $res->num_rows;  	
    	mysqli_close($conn);
    	return $totalimg;
    }
}


if (isset($_GET['delsingle'])) {
	deleteSingleImg($_GET['delsingle']);
} else if (isset($_GET['down'])) {
	scrolldown($_GET['down']);
} else if (isset($_GET['up'])) {
	scrollup($_GET['up']);
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
EOT;
if (count($images) <= 0) {
    echo "<h2>No Images Available</h2>";
}
else {
	$countimg = getcount();
	krsort($images);
	$lastimage = end($images);
	$images = array_slice($images, -50, 50, true);
	echo '<div class="row"><p class="lead">';
    echo '<a href=index.html ><span class="glyphicon glyphicon-home"></span></a>&nbsp;&nbsp;';
	echo '&nbsp;&nbsp;Total Photos: '.$countimg.'&nbsp;&nbsp;';
	echo '<a href=gallery.php?down='.$countimg.'><span class="glyphicon glyphicon-menu-down"></span></a>';
	echo '<a href=gallery.php?up='.$countimg.'><span class="glyphicon glyphicon-menu-up"></span></a><br><br>';
	echo '</p></div>';
	echo '<p id="datainfo" class="datainfo "></p>';
 	echo '<div class="row">';
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
 	        echo '&nbsp;<a href=gallery.php?delsingle='.$image.'><span class="glyphicon glyphicon-remove-circle"></span></a></p>';
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


?>