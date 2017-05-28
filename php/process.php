<?php
define('LBASE_DIR',dirname(__FILE__));
define('CONFIGFILE', '../config.txt');

error_reporting(E_ALL);

$hflip      = $_POST['hflip'];
$vflip      = $_POST['vflip'];
$resolution = $_POST['resolution'];
$interval   = $_POST['interval'];
$duration   = $_POST['duration'];
$wb         = $_POST['whitebalance'];
$exposure   = $_POST['exposure'];
$metering   = $_POST['metering'];
$effects    = $_POST['effects'];
$sharpness  = $_POST['sharpness'];
$contrast   = $_POST['contrast'];
$brightness = $_POST['brightness'];
$saturation = $_POST['saturation'];
$iso        = $_POST['iso'];
$drc        = $_POST['drc'];
$ss         = $_POST['ss'];
$jpgquality = $_POST['jpgquality'];

$fp = fopen(LBASE_DIR . '/' . CONFIGFILE, 'w');

if(!empty($hflip)){
	echo "Horizontal Flip: ".$hflip."<br />";
	fwrite($fp, "hflip=".$hflip."\n");}
if(!empty($vflip)){
	echo "Vertical Flip: ".$vflip."<br />";
	fwrite($fp, "vflip=".$vflip."\n");}
fwrite($fp, "resolution=".$resolution."\n");
fwrite($fp, "interval=".$interval."\n");
fwrite($fp, "duration=".$duration."\n");
fwrite($fp, "whitebalance=".$wb."\n");
fwrite($fp, "exposure=".$exposure."\n");
fwrite($fp, "metering=".$metering."\n");
fwrite($fp, "effects=".$effects."\n");
fwrite($fp, "sharpness=".$sharpness."\n");
fwrite($fp, "contrast=".$contrast."\n");
fwrite($fp, "brightness=".$brightness."\n");
fwrite($fp, "saturation=".$saturation."\n");
fwrite($fp, "iso=".$iso."\n");
fwrite($fp, "drc=".$drc."\n");
fwrite($fp, "ss=".$ss."\n");
fwrite($fp, "jpgquality=".$jpgquality."\n");

fclose($fp);
exit();
?>