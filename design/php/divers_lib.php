<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

// return the interpolated value between pBegin and pEnd
function interpolate($pBegin, $pEnd, $pStep, $pMax) {
	if ($pBegin < $pEnd) {
		return (($pEnd - $pBegin) * ($pStep / $pMax)) + $pBegin;
	} else {
		return (($pBegin - $pEnd) * (1 - ($pStep / $pMax))) + $pEnd;
	}
}