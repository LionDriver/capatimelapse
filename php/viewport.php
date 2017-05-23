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
?>