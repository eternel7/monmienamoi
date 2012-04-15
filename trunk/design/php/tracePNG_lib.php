<?php
/* 
 * 
*/

function cubicbezier($img, $col, $pt0, $pt1, $pt2, $pt3, $n = 20) {
    $pts = array();

    for($i = 0;$i <= $n;$i++) {
        $t = $i / $n;
        $t1 = 1 - $t;
        $a = pow($t1, 3);
        $b = 3 * $t * pow($t1, 2);
        $c = 3 * pow($t, 2) * $t1;
        $d = pow($t, 3);

        $x = round($a * $pt0[2] + $b * $pt1[2] + $c * $pt2[2] + $d * $pt3[2]);
        $y = round($a * $pt0[3] + $b * $pt1[3] + $c * $pt2[3] + $d * $pt3[3]);
        $pts[$i] = array($x, $y);
    }

    for($i = 0;$i < $n;$i++) {
        imageline($img, $pts[$i][0], $pts[$i][1], $pts[$i+1][0], $pts[$i+1][1], $col);
    }
}
function tracepoint($img,$col,$pt,$val=0) {
    $lg = 4;
    $x = $pt[0];
    $y = $pt[1];
    $ximg = $pt[2];
    $yimg = $pt[3];
    imageline($img, $ximg, $yimg-$lg/2, $ximg, $yimg+$lg/2, $col);
    imageline($img, $ximg-$lg/2, $yimg, $ximg+$lg/2, $yimg, $col);
    if($val==1){
        $ptinfo="x : ".$x;
        imagestring($img,1,$ximg+$lg+1,$yimg,$ptinfo,$col);
        $ptinfo="y : ".$y;
        imagestring($img,1,$ximg+$lg+1,$yimg+10,$ptinfo,$col);
    }
}
function traceaxesortho($img,$col,$x,$y) {
    imageline($img, 0, $y/2, $x, $y/2, $col);
    imageline($img, $x/2, 0, $x/2, $y, $col);
}
//y=ax+b et (0,0) == (width/2,height/2)
function tracedroite($img,$a,$b, $col) {
    $width=imagesx($img);
    $height=imagesy($img);
    $pts = array();
//Calcul du point pour x = -width/2 == 0
    $x = -$width/2;
    $y = $a * $x + $b;
//translation dans le repere avec (0,0) en haut a gauche au lieu de centre
    $ximg = $x + $width/2;
    $yimg = $height/2 - $y;
    $pts[0] = array($ximg, $yimg);
//Calcul du point pour x = +width/2 == width
    $x = $width/2;
    $y = $a * $x + $b;
//translation dans le repere avec (0,0) en haut a gauche au lieu de centre
    $ximg = $x + $width/2;
    $yimg = $height/2 - $y;
    $pts[1] = array($ximg, $yimg);

    imageline($img, $pts[0][0], $pts[0][1], $pts[1][0], $pts[1][1], $col);
}
function tracecercle($im,$cx,$cy,$cR,$col) {
    imagearc ($im, $cx, $cy,$cR, $cR, 0, 360,$col);
}
function tracecercleplein($im,$cx,$cy,$cR,$col) {
    imagefilledellipse($im,$cx,$cy,$cR,$cR,$col);
}
function tracecerclepleindeg($im,$cx,$cy,$cR,$colfrom,$colto) {
    require_once("divers_lib.php");
    require_once("color_lib.php");
    $colfromR=$colfrom[0];
    $colfromG=$colfrom[1];
    $colfromB=$colfrom[2];
    $colfromHSL=rgbToHsl($colfromR,$colfromG,$colfromB);
    $coltoR=$colto[0];
    $coltoG=$colto[1];
    $coltoB=$colto[2];
    $coltoHSL=rgbToHsl($coltoR,$coltoG,$coltoB);
    for($i=0;$i<$cR+1;$i++) {
        $colH=interpolate($colfromHSL[0], $coltoHSL[0], $i, $cR);
        $colS=interpolate($colfromHSL[1], $coltoHSL[1], $i, $cR);
        $colL=interpolate($colfromHSL[2], $coltoHSL[2], $i, $cR);
        $colarray=hslToRgb($colH,$colS,$colL);
        $colR=$colarray[0];
        $colG=$colarray[1];
        $colB=$colarray[2];
        $col = imagecolorallocate($im, $colR, $colG, $colB);
        tracecercleplein($im,$cx,$cy,$cR-$i,$col);
    }
}

function tracecerclepleindegto($im,$cx,$cy,$cR,$cRmin,$colfrom,$colto) {
    require_once("divers_lib.php");
    require_once("color_lib.php");
    $colfromR=$colfrom[0];
    $colfromG=$colfrom[1];
    $colfromB=$colfrom[2];
    $colfromHSL=rgbToHsl($colfromR,$colfromG,$colfromB);
    $coltoR=$colto[0];
    $coltoG=$colto[1];
    $coltoB=$colto[2];
    $coltoHSL=rgbToHsl($coltoR,$coltoG,$coltoB);
    for($i=0;$i<abs($cR-$cRmin)+1;$i++) {
        $colH=interpolate($colfromHSL[0], $coltoHSL[0], $i, abs($cR-$cRmin));
        $colS=interpolate($colfromHSL[1], $coltoHSL[1], $i, abs($cR-$cRmin));
        $colL=interpolate($colfromHSL[2], $coltoHSL[2], $i, abs($cR-$cRmin));
        $colarray=hslToRgb($colH,$colS,$colL);
        $colR=$colarray[0];
        $colG=$colarray[1];
        $colB=$colarray[2];
        $col = imagecolorallocate($im, $colR, $colG, $colB);
        tracecercleplein($im,$cx,$cy,$cR-$i,$col);
    }
}
function translatpoint($pt,$width,$height) {
    $x = $pt[0];
    $y = $pt[1];
    $pt[2] = $x + $width/2;
    $pt[3] = $height/2 - $y;
    return $pt;
}
function translatpointimg($img,$pt) {
    $width=imagesx($img);
    $height=imagesy($img);
    $pt=translatpoint($pt,$width,$height);
    return $pt;
}
function affine($pt1,$pt2) {
    $a=0;
    $b=0;
    $x1=$pt1[2];
    $y1=$pt1[3];
    $x2=$pt2[2];
    $y2=$pt2[3];
    if($y1!=$y2) {
        $a = ($y1-$y2) / ($x1-$x2);
        $b = ($x1*$y2 - $x2*$y1) / ($x1-$x2);
    }
    return array($a,$b);
}
function traceraxesradar($pts) {
    //$nb=count($pts);
}
function lierpointsparcourbe($img,$pts,$col,$facteur) {
    $ptcont = array();
    $nbpoints=count($pts);
    if($facteur>1){
        $facteur=1;
    }
    if($facteur<0){
        $facteur=0;
    }
    //facteur entre 0 et 1 * x pour donner un arrondi smooth
    $facteur=0.2*$facteur;
    //calcul des points de contraite
    for($i=0;$i<$nbpoints;$i++) {
        //ptcont = (pt[i+1] - pt[i-1])/a
        $iplus = ($i+1)%$nbpoints;
        $imoins = ($i-1)%$nbpoints;
        $imoins2 = ($i-2)%$nbpoints;
        if($imoins==-1) {$imoins=$nbpoints-1;}
        if($imoins2==-1) {$imoins2=$nbpoints-1;}
        if($imoins2==-2) {$imoins2=$nbpoints-2;}
        for ($j=0;$j<2;$j++) {
            $dv=$pts[$iplus][$j]-$pts[$imoins][$j];
            $dv2=$pts[$i][$j]-$pts[$imoins2][$j];
            $ptcont[$i][$j] = $pts[$i][$j]-$dv*$facteur;
            $ptcont[$imoins][$j] = $pts[$imoins][$j]+$dv2*$facteur;
        }
        $ptcont[$i]=translatpointimg($img,$ptcont[$i]);
        $ptcont[$imoins]=translatpointimg($img,$ptcont[$imoins]);
        //tracepoint($img, $col, $ptcont[$i],1);
        //imagestring($img,1,$ptcont[$i][2]-10,$ptcont[$i][3],$i,$col);
        //tracepoint($img, $col, $ptcont[$imoins],1);
        //imagestring($img,1,$ptcont[$imoins][2]-10,$ptcont[$imoins][3],$imoins,$col);
        //on trace les courbes de Bezier
        cubicbezier($img, $col, $pts[$imoins], $ptcont[$imoins], $ptcont[$i], $pts[$i]);
    }
}