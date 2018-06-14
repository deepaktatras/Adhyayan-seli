<?php

/* 
A small PHP program and function that draws a pie chart in SVG format. 

Written by Branko Collin in 2008.

This code is hereby released into the public domain. In case this is not legally possible: I, Branko Collin, hereby grant anyone the right to use this work for any purpose, without any conditions, unless such conditions are required by law.*/ 

/* Working with relative values confused me, so I worked with absolute ones 
instead. Generally this should not be a problem, as the only relative values you 
need are the chart's centre coordinates and its radius, and these are a linear
function of the bounding box size or canvas size. See the sample values for how 
this could work out. */

/* Sample values */
$values[] = 27;
$values[] = 177;
$values[] = 15;
$values[] = 81; 
$width = 400; // canvas size
$height = 400; 
$centerx = $width / 2; // centre of the pie chart
$centery = $height / 2;
$radius = min($centerx,$centery) - 10; // radius of the pie chart
if ($radius < 5) {
	die("Your chart is too small to draw.");
} 

/* Draw and output the SVG file. */

header('Content-type: image/svg+xml');

echo <<<END
<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<svg xmlns:svg="http://www.w3.org/2000/svg" xmlns="http://www.w3.org/2000/svg" version="1.0" width="$width" height="$height" id="svg2">

  <title>Pie chart</title>
  <desc>Picture of a pie chart</desc>
END;

print piechart($values,200,200,190);

print "\n</svg>\n";


/* 
	The piechart function
	
	Arguments are an aray of values, the centre coordinates x and y, and 
	the radius of the piechart.	
*/

function piechart($data, $cx, $cy, $radius) {
	$chartelem = "";

	$max = count($data);
	
	$colours = array('red','orange','yellow','green','blue');
	
	$sum = 0;
	foreach ($data as $key=>$val) {
		$sum += $val;
	}
	$deg = $sum/360; // one degree
	$jung = $sum/2; // necessary to test for arc type
	
	/* Data for grid, circle, and slices */ 
	
	$dx = $radius; // Starting point: 
	$dy = 0; // first slice starts in the East
	$oldangle = 0;
	
	/* Loop through the slices */
	for ($i = 0; $i<$max; $i++) {
		$angle = $oldangle + $data[$i]/$deg; // cumulative angle
		$x = cos(deg2rad($angle)) * $radius; // x of arc's end point
		$y = sin(deg2rad($angle)) * $radius; // y of arc's end point
	
		$colour = $colours[$i];
	
		if ($data[$i] > $jung) {
			// arc spans more than 180 degrees
			$laf = 1;
		}
		else {
			$laf = 0;
		}
	
		$ax = $cx + $x; // absolute $x
		$ay = $cy + $y; // absolute $y
		$adx = $cx + $dx; // absolute $dx
		$ady = $cy + $dy; // absolute $dy
		$chartelem .= "\n";
		$chartelem .= "<path d=\"M$cx,$cy "; // move cursor to center
		$chartelem .= " L$adx,$ady "; // draw line away away from cursor
		$chartelem .= " A$radius,$radius 0 $laf,1 $ax,$ay "; // draw arc
		$chartelem .= " z\" "; // z = close path
		$chartelem .= " fill=\"$colour\" stroke=\"black\" stroke-width=\"2\" ";
		$chartelem .= " fill-opacity=\"0.5\" stroke-linejoin=\"round\" />";
		$dx = $x; // old end points become new starting point
		$dy = $y; // id.
		$oldangle = $angle;
	}
	
	return $chartelem; 
}

?>
