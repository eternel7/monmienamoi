<?php
header('Content-Type: image/png');
include("php/tracePNG_lib.php");

//image creation

$width=500;
$height=500;
$P[0] = array();
$P[1] = array();
$P[2] = array();
$P[3] = array();
//coordonnee avec (0,0) == (width/2,height/2)
$P[0][0]=20;
$P[0][1]=50;
$P[1][0]=120;
$P[1][1]=0;
$P[2][0]=0;
$P[2][1]=-50;
$P[3][0]=-86;
$P[3][1]=0;
$P[4][0]=-86;
$P[4][1]=50;
$P[5][0]=-66;
$P[5][1]=20;
for ($i=0;$i<count($P);$i++){
	$P[$i] = translatpoint($P[$i],$width,$height);
}
$image = imagecreate($width, $height);
// The first allocated color will be the background color:
imagecolorallocate($image, 255, 255, 255);
//ajout des axes
$color = imagecolorallocate($image, 55, 55, 55);
traceaxesortho($image,$color,$width,$height);

$color = imagecolorallocate($image, 255, 0, 0);
lierpointsparcourbe($image,$P,$color,0.9);

//ajout des points de de construction de la courbe de bezier
$color = imagecolorallocate($image, 0, 0, 255);
for($i=0;$i<count($P);$i++){
	tracepoint($image,$color,$P[$i],1);
        imagestring($image,1,$P[$i][2]-10,$P[$i][3],$i,$color);
}
//tracedroite($image, 1, 0, $color, $width, $height);
$cx = $P[0][0];
$cy = $P[0][1];
$cR = $width/4;
$colfrom = imagecolorallocate($image, 0, 255, 0);
$colto = imagecolorallocate($image, 0, 0, 255);
tracecerclepleindeg($image,$cx,$cy,$cR,$colfrom,$colto);


imagepng($image);
imagedestroy($image);