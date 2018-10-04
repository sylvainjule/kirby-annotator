<?php

function markerStyle($marker) {
	$style =  'left:'. $marker->x()->toPercentString() .';';
	$style .= ' top:'. $marker->y()->toPercentString() .';';

	if($marker->type()->isNotPin()) {
		$style .= ' width:'. $marker->w()->toPercentString() .';';
		$style .= ' height:'. $marker->h()->toPercentString() .';';
	}

	if($marker->type()->isCircle()) {
		$style .= ' border-radius:50%;';
		$style .= ' transform:translate(-50%, -50%);';
	}

	return $style;
}