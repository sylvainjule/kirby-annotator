<?php

function markerStyle($marker) {
	$style =  'left: '. $marker->x()->toPercentString() .';';
	$style .= 'top: '. $marker->y()->toPercentString() .';';
	$style .= 'width: '. $marker->w()->toPercentString() .';';
	$style .= 'height: '. $marker->h()->toPercentString() .';';
	return $style;
}