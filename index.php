<?php
/**
 * Display 3 dimensional shapes in PHP
 * @author Charley Pearce
 */

include("Vector3.php");

$vec = new Vector3(1, 2, 3);

$tri = array(
    new Vector3(0, 0, 0),
    new Vector3(0.5, 1, 0),
    new Vector3(1, 0, 0)
);

