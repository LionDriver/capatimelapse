<?php
error_reporting(E_ALL);
$images = glob('../pics/*.jpg', GLOB_BRACE);

krsort($images);
$lastimage = key($images);

if (count($images) <= 0) {
    echo "<h2>No Images available</h2>";
}
else {
	echo '<div class="span3"><a target=_self href='.$images[$lastimage].'><img class="img-responsive" src="'.$images[$lastimage].' " alt="Please Wait"></a>';
    echo '<div class="desc">'.date('D, d M y H:i:s', filectime($images[$lastimage])).'</div></div>';
}

//$images = array_slice($images, 1);
// echo '<div class="carousel-inner"><div class="item active"><div class="row-fluid">'; 
// foreach ($images as $image)
// {
// 	echo '<div class="span3"><a href="#x" class="thumbnail"><img src="'.$image.'" alt="Please Wait" /></a>';
// 	echo '<div class="caption">'.date('D,d M y H:i:s', filectime($image)).'</div></div>';
// }
// echo '</div></div></div>';

// if (count($images) <= 0) {
//     echo "<h2>No Images Available</h2>";
// }
// else {
// 	foreach ($images as $image)
// 	{
// 		echo "<p>ewwecwecewc".$image."</p>";
// 		$thumb = exif_thumbnail($image);
// 		if (($thumb = exif_thumbnail($image) === false) {
// 	    	echo '<div class="thumbnail">Please Wait...</div>';
// 		}
// 	    else {
// 	    	echo '<div class="thumbnail"><a target=_self href='.$image.'><img class="img-responsive" style="width:100%" src="data:image/gif;base64, '.base64_encode($thumb).'" ></a>';
// 	    	echo '<div class="caption">'.date('D, d M y H:i:s', filectime($image).'</div>';
// 	    }
// 	}
// }
// 	$thumb = exif_thumbnail($image);
// 	echo '<div class="gallery_product col-md-6 col-sm-4 col-xs-4 filter hdpe"><a target=_self href='.$image.'><img class="img-responsive" src="data:image/gif;base64, '.base64_encode($thumb).'"></a>';
// 	echo '<div class="caption">'.date('D, d M y H:i:s', filectime($image)).'</div></div>';
// 
?>