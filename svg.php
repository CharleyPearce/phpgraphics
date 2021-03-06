<?php

require '/xampp/vendor/autoload.php';
use SVG\SVG;
use SVG\Nodes\Shapes\SVGRect;
use SVG\Nodes\Shapes\SVGPolygon;
include("Vector3.php");
include("Matrix4.php");

// image with 100x100 viewport
$image = new SVG(400, 400);
$doc = $image->getDocument();

// blue 40x40 square at (0, 0)
$square = new SVGRect(0, 0, 80, 40);
$square->setStyle('fill', '#0000FF');
$doc->addChild($square);


$points = array(
    array(0, 0),
    array(400, 0),
    array(0, 400)
);

$poly = new SVGPolygon($points);
$poly->setStyle('fill', '#FF0000');
$doc->addChild($poly);

header('Content-Type: image/svg+xml');
echo $image;