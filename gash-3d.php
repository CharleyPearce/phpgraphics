<?php
// this script will make 3d shapes out of 2d shapes and put into an svg file
?>
<head>
    <style>
        .polygon { fill: none; }
    </style>
</head>
<body>
<h1>Embedded SVG</h1>

<?php
// $shapes[0] = array ("polygon", 0,5,0, 0,5,3 , 5,5,3, 5,5,0) ;
// $shapes[1] = array ("polygon", 0,10,0, 0,10,3 , 5,10,3, 5,10,0) ;
$shapes[0] = array ("polygon", 0,10,0, 0,15,0 , 0,15,3, 0,12.5,5, 0,10,3) ; // side wall
$shapes[1] = array ("polygon", 0,10,0, 0,10,3 , 5,10,3, 5,10,0) ; // front wall
$shapes[2] = array ("polygon", 0,12.5,5, 5,12.5,5, 5,10,3, 0,10,3) ;  // front roof
$shapes[3] = array ("polygon", 0,12.5,5, 5,12.5,5, 5,15,3, 0,15,3) ;  // back roof
$shapes[4] = array ("polygon", 5,10,0, 5,15,0 , 5,15,3, 5,12.5,5, 5,10,3) ; // side wall
$shapes[5] = array ("polygon", 2,10,0, 2,10,2, 3,10,2, 3,10,0) ;  // front door


// echo "<pre>",print_r($shapes),"</pre>";

$viewpoint_x = -3.5;
$viewpoint_y = 0;
$viewpoint_z = 1;



$viewing_plane_y =1;

$canvaswidth= 600;
$canvasheight= 300;

$magnify = 400;
$offset_x = 0;
$offset_y = 0.3;


function convertCoords($x, $y, $z, $viewpoint_x, $viewpoint_y , $viewpoint_z , $viewing_plane_y ) {
    echo "<br> Point is  $x , $y , $z";

    $difference_x = $x - $viewpoint_x ;
    $difference_y = $y - $viewpoint_y ;
    $difference_z = $z - $viewpoint_z ;

    echo "<br> Difference is $difference_x , $difference_y , $difference_z";
    $tan_az = $difference_x / $difference_y;
    if ($difference_y == 0) {echo "<h3>Divide by zero error </h3>";}
    $azimuth = atan($tan_az);

    $diagonal = sqrt ( pow($difference_x, 2) + pow($difference_y, 2));
    $tan_el = $difference_z / $diagonal;
    $elevation = atan($tan_el);

    $x_dash = $azimuth * $viewing_plane_y ;
    $y_dash = $elevation * $viewing_plane_y ;

    $azel['az'] = $azimuth;
    $azel['el'] = $elevation;
    $azel['x_dash'] = $x_dash;
    $azel['y_dash'] = $y_dash;

    // echo "<br>Tan = $tan_az Atan = $azimuth Diagonal $diagonal Tangent  $tan_el Elevation $elevation x_dash $x_dash  y_dash $y_dash";

    return ($azel);
}


echo "<br>Viewpoint is $viewpoint_x, $viewpoint_y , $viewpoint_z ";


$number_of_shapes = sizeof($shapes);
echo "<br>There are $number_of_shapes rows; ";

for ($row =0 ; $row < $number_of_shapes ; $row++ )

{
    echo "<br>Row:  " . $row  . " - ". $shapes[$row][0] ;

$plotshapes[$row][0]= $shapes[$row][0];
    $number_of_points = (sizeof($shapes[$row]) -1 )/3;
    echo "<br>There are $number_of_points sets of points ; ";

    for ($point =0 ; $point < $number_of_points ; $point++ ) {
        $pointer_x = ($point * 3) + 1;
        $pointer_y = ($point * 3) + 2;
        $pointer_z = ($point * 3) + 3;

        echo "<br>Point : $point Location $pointer_x $pointer_y $pointer_z";

        $x = $shapes[$row][$pointer_x];
        $y = $shapes[$row][$pointer_y];
        $z = $shapes[$row][$pointer_z];

        $azel = convertCoords($x, $y, $z, $viewpoint_x, $viewpoint_y , $viewpoint_z , $viewing_plane_y );


        $x_dash = $azel['x_dash'];
        $y_dash = $azel['y_dash'];

        $pointer_x = ($point * 2) + 1 ;
        $pointer_y = ($point * 2) + 2 ;

        $screen_x = ($x_dash + $offset_x) * $magnify;
        $screen_y = ($y_dash + $offset_y) * $magnify;

        $plotshapes[$row][$pointer_x]= $screen_x;
        $plotshapes[$row][$pointer_y]= $screen_y;

        echo "<br> Point $x_dash , $y_dash - $screen_x , $screen_y <br>";



    }

}

echo "<pre>",print_r($plotshapes),"</pre>";

// now we need to output the points

$number_of_shapes = sizeof($plotshapes);
echo "<br>There are $number_of_shapes rows; ";
$svg_contents="";

for ($row =0 ; $row < $number_of_shapes ; $row++ ) {   // plots the points row by row

    $number_of_points = (sizeof($plotshapes[$row]) -1 );
    echo "<br>aaa There are $number_of_points sets of points ; ";
    $line = '<' . $plotshapes[$row][0] . ' points ="' ;

    for ($point =1 ; $point < $number_of_points +1 ; $point++ ) {
        $line = $line . $plotshapes[$row][$point]. ',' ;
        echo $point . "<br> ";
    }
    $line = rtrim($line, ',');

    $line = $line. '" stroke="black" stroke-width="1" class="polygon" > </polygon>';




    $svg_contents = $svg_contents . $line ;

}

echo $svg_contents;

?>

<!-- SVG code -->
<svg width="<?php echo $canvaswidth; ?>px" height="<?php echo $canvasheight; ?>px"
     xmlns="http://www.w3.org/2000/svg"  >
    <g transform="translate(0,<?php echo $canvasheight; ?>)">
        <g transform="scale(1,-1)">
            <?php echo $svg_contents; ?>
        </g>
    </g>
</svg>
end


</body>