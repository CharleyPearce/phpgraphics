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
