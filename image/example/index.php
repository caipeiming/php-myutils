<?php
use com\jdk5\blog\Image\Image;
require '../Image.php';

$img = new Image();

$img->load('basketball.gif')->size(100, 200)->fixed_given_size(true)->save('processed/basketball-resize.gif');

$img->load('org.jpg')->width(200)->quality(70)->save('processed/org-width-resize.jpg');

$img->load('org.jpg')->height(200)->save('processed/org-height-resize.jpg');

$img->load('org.png')->fixed_given_size(true)->keep_ratio(true)->save('processed/org-no-size-resize.png');

$img->load('org.png')->bg_color("#a30000")->quality(100)->size(300, 300)->fixed_given_size(true)->keep_ratio(true)->save('processed/org-size-resize.png');

$img->load('org.png')->bg_color("#ff00ff")->size(400, 100)->fixed_given_size(true)->keep_ratio(true)->save('processed/400_100.png');

$img->load('org.png')->bg_color("#dd0000")->size(200, 200)->fixed_given_size(true)->keep_ratio(true)->save('processed/200_200.png');

$img->load('org.png')->rotate(45)->bg_color("#ee2300")->size(250, 187)->fixed_given_size(true)->keep_ratio(true)->save('processed/rotate-45.png');
$img->load('org.png')->rotate(20)->bg_color("#3300ff")->width(250)->fixed_given_size(true)->keep_ratio(true)->quality(90)->save('processed/rotate-20.jpg');

// watermark
$img->load('org.png')->set_watermark('watermarkater.png', Image::CENTER, 0.6, 0, 0, Image::WATERMARK_DIAGONAL_NEG)->save('processed/watermark_diagonal.png');
$img->load('org.png')->set_watermark('watermarkater.png', Image::CENTER, 0.7)->save('processed/watermark.png');

$img->load('butterfly.jpg')->set_watermark('overlay.png', Image::CENTER, 0.8, 0, 0)->save('processed/watermark_handle.png');
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