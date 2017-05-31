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
<?php
$rec_limit = 28;
function deleteSingleImg($img) {
    if (file_exists($img)){
        include "db.php";
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            echo 'Database ERROR in delete: ' . $conn->connect_error;
            exit;
        }
        unlink($img);
        $sql = 'DELETE from imgdat WHERE imgNice = "'.$img.'"';
        if ($conn->query($sql) === TRUE) {
        } else {
            echo "Error deleting record: " . $conn->error;
        }
        mysqli_close($conn);
    }
}

function getcount() {
	include "db.php";
	$conn = new mysqli($servername, $username, $password, $dbname);
	if ($conn->connect_error) {
    	echo 'Database ERROR getting count: ' . $conn->connect_error;
    	exit;
	}
	$sql = "SELECT count(imgId) FROM imgdat";
	$result = $conn->query($sql);
	if (! $result) {
	    echo 'unable to get database count: '. $conn->error;
	    exit;
	}
	$row = mysqli_fetch_array($result, MYSQLI_NUM);
	$rec_count = $row[0];
	mysqli_close($conn);
	return $rec_count;
}

$rec_count = getcount();
if ($rec_count < 1) {
    echo '<div class="container-fluid"><div class="row"><p class="lead">';
    echo '<a href=index.html data-toggle="tooltip" title="Home"><span class="glyphicon glyphicon-home"></span></a>&nbsp;&nbsp;';
    echo '<h1>No Images Available</h1></div></p></div></body></html>';
    exit;
}

if (isset($_GET{'page'})) {
    $page = $_GET{'page'} + 1;
    $offset = $rec_limit * $page;
} else if (isset($_GET['delsingle'])) {
    deleteSingleImg($_GET['delsingle']);
    $page = $_GET["loc"] - 1;
    if ($page <= -1) {
        $page = -1;
        $offset=0;
    } else if ($page < 1) {
    	$offset=0;
    } else {
    	$offset = $rec_limit * $page;    	
    }
    header("Refresh:0; url=gallery.php?page=$page");
    exit;
} else {
    $page=0;
    $offset=0;
}

$left_rec = $rec_count - ($page * $rec_limit);
$rec_plus = $left_rec - 28;
if ($rec_plus < 1) {
	$rec_plus = 0;
}

include "db.php";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
	echo 'Database ERROR connecting: ' . $conn->connect_error;
	exit;
}

$sql = "SELECT imgNice FROM imgdat ORDER BY imgId DESC LIMIT $offset, $rec_limit";
$retval = $conn->query($sql);
if (! $retval ){
    echo 'Database error in reading records: '. $conn->error;
    exit;
}
echo '<div class="container-fluid"><div class="row"><p class="lead">';
echo '<a href=index.html data-toggle="tooltip" title="Home"><span class="glyphicon glyphicon-home"></span></a>&nbsp;&nbsp;';
echo '<strong class="through">Photos: '.$rec_plus.' - '.$left_rec.'</strong>';
echo '&nbsp;&nbsp;<strong class="total">Total Photos: '.$rec_count.'</strong>&nbsp;&nbsp;';
echo '</p></div>';
echo '<div class="row">';
echo '<div class="navigation">';
echo '<ul class="nav navbar-nav">';

if ($page <= 0){
 	if ($left_rec <= $rec_limit){
        echo '<li class="disabled"><a class="last" ><span class="glyphicon glyphicon-backward"></span></a></li>';
		echo '<li class="disabled"><a class="next" ><span class="glyphicon glyphicon-forward"></span></a></li>';
	} else {
	    echo '<li class="disabled"><a class="last" ><span class="glyphicon glyphicon-backward"></span></a></li>';
        echo '<li><a class="next" href="gallery.php?page='.$page.'" data-toggle="tooltip" title="Next Set"><span class="glyphicon glyphicon-forward"></span></a></li>';
	}
}
else if ($left_rec < $rec_limit){
    $last = $page - 2;
    echo '<li><a class="last" href="gallery.php?page='.$last.'" data-toggle="tooltip" title="Last Set"><span class="glyphicon glyphicon-backward"></span></a></li>';
	echo '<li class="disabled"><a class="next" ><span class="glyphicon glyphicon-forward"></span></a></li>';
}
else if ($page > 0){
    $last = $page - 2;
    echo '<li><a class="last" href="gallery.php?page='.$last.'" data-toggle="tooltip" title="Last Set"><span class="glyphicon glyphicon-backward"></span></a></li>';    
    echo '<li><a class="next" href="gallery.php?page='.$page.'" data-toggle="tooltip" title="Next Set"><span class="glyphicon glyphicon-forward"></span></a></li>';
}

echo '</ul></div></div></div><div class="row">';

while ($row = mysqli_fetch_array($retval, MYSQLI_ASSOC)){
    $imgfile = $row["imgNice"];
    $thumb = exif_thumbnail($imgfile, $width, $height, $type);
    if ($thumb === false) {
        echo '<div class="thumbnail">Please Wait...</div>';
    }
    else {
        $nice = date('D, d M y H:i:s', filectime($row['imgNice']));
        echo '<div class="pictureframe col-lg-3 col-sm-4 col-xs-6 col-6">';
        echo "<a href=".$imgfile.' class="js-img-viwer" data-caption="'.$nice.'" data-id="'.$nice.'">';
        echo "<img width='$width' class='thumbnail img-responsive' height='$height' src='data:image;base64,".base64_encode($thumb)."'/></a>";
        echo '<strong><div class="caption">';
        echo '<p class="date">'.$nice.'&nbsp;&nbsp;&nbsp;<a href='.$imgfile.' download data-toggle="tooltip" title="Download Photo"><span class="glyphicon glyphicon-download-alt"></span></a>';
        echo '&nbsp;<a href=gallery.php?delsingle='.$imgfile.'&loc='.$page.' data-toggle="tooltip" title="Delete Photo"><span class="glyphicon glyphicon-remove-circle"></span></a></p>';
        echo '</strong></div></div>';
    }
}

mysqli_close($conn);
?>
        </div>
    </div>
    <script src="./js/smartphoto.js?v=1"></script>
    <script>
    document.addEventListener('DOMContentLoaded',function(){
        new smartPhoto(".js-img-viwer");
    });
    </script>
    <br>
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
    <script src="js/jquery-3.2.1.slim.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>
</html>