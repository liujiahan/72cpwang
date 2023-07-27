<?php  

// 宽高
$width = 401;
$height = 401;

//中心原点左边
$center_x = 200;
$center_y = 200;

// 四个圆圈的直径
$diameter1 = 100;
$diameter2 = 200;
$diameter3 = 300;
$diameter4 = 400;

$diameter = 36;
$im = imagecreatetruecolor($width, $height);

//圆和直线的颜色
$color = imagecolorallocate($im,255,255,255);

imageellipse($im,$center_x,$center_y,$diameter4,$diameter4,$color);

imageellipse($im,$center_x,$center_y,$diameter3,$diameter3,$color);

imageellipse($im,$center_x,$center_y,$diameter2,$diameter2,$color);

imageellipse($im,$center_x,$center_y,$diameter1,$diameter1,$color);

imageellipse($im,$center_x,$center_y,$diameter,$diameter,$color);

imageline($im,60,60,340,340,$color);

imageline($im,340,60,60,340,$color);

imageline($im,0,200,400,200,$color);

imageline($im,200,0,200,400,$color);

//3、输出图像
header("content-type: image/png");
imagepng($im);
//4、销毁图像，释放内存
imagedestroy($im);
