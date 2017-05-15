<?php
$images = glob('../pics/*.jpg', GLOB_BRACE);
$size = exec('du -mh ../pics/');
$clean = substr($size, 0,-8);
$tar = glob('../pics/*.tar', GLOB_BRACE);
$df = disk_free_space("/var/www/");
$numtar = count($tar);
$numimg = count($images);

function formatSize($bytes)
{
    $types = array( 'B', 'KB', 'MB', 'GB', 'TB' );
    for( $i = 0; $bytes >= 1024 && $i < ( count($types) -1 ); $bytes /= 1024, $i++ );
        return(round( $bytes, 2) . " " . $types[$i]);
}

if ($numimg <= 0) {
	$numimg = 0;
}
echo "Images: ".$numimg.'&nbsp; | &nbsp; Tar Files: '.$numtar.'&nbsp; | &nbsp; Size: '.$clean.'&nbsp; | &nbsp; Free: '.formatSize($df);
echo "<br>";

if ($numtar <= 0) {
	$numtar = 0;
}
else {
	echo "<ol>";
	foreach ($tar as $key => $value) {
		$nice = substr($value, 8, 35);
		echo '<li><a target=_self href='.$value.'>'.$nice.'&emsp;</a></li>';
	}
	echo "</ol>";
}
?>
