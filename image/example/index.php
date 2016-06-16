<?php
use com\jdk5\blog\Image\Image;
require '../Image.php';

$img = new Image();

$img->load('basketball.gif')->size(100, 200)->fixed_given_size(true)->save('processed/basketball-resize.gif');

$img->load('org.jpg')->width(200)->quality(70)->save('processed/org-width-resize.jpg');

$img->load('org.jpg')->height(200)->save('processed/org-height-resize.jpg');

$img->load('org.png')->fixed_given_size(true)->keep_ratio(true)->save('processed/org-no-size-resize.png');

$img->load('org.png')->bgcolor(array(100, 0, 0))->quality(100)->size(300, 300)->fixed_given_size(true)->keep_ratio(true)->save('processed/org-size-resize.png');

$img->load('org.png')->bgcolor(array(0, 0, 0))->size(400, 100)->fixed_given_size(true)->keep_ratio(true)->save('processed/400_100.png');

$img->load('org.png')->bgcolor(array(0, 0, 0))->size(200, 200)->fixed_given_size(true)->keep_ratio(true)->save('processed/200_200.png');



header("Content-type: text/html; charset=utf-8");
echo '<h1 style="text-align: center;">生成图片的结果</h1>';
$pre = "./processed/";
if ($handle = opendir($pre)) {
	while (false !== ($entry = readdir($handle))) {
		if ($entry != "." && $entry != "..") {
			list($width, $height) = getimagesize($pre.$entry);
			$size = filesize($pre.$entry);
			$size = number_format($size, 0, '', ',');
			echo "<div style='text-align: center; margin: 10px; font-weight: bold;'>".
				"<img src='$pre$entry' /><br/>filename: {$entry}<br/>".
				"{$width} * {$height}px<br/>".
				"size: {$size} byte</div>";
		}
	}
	closedir($handle);
}