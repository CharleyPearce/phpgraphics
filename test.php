<?php

require '/xampp/vendor/autoload.php';
use SVG\SVG;
use SVG\Nodes\Shapes\SVGRect;
use SVG\Nodes\Shapes\SVGPolygon;
include("Vector3.php");
include("Matrix4.php");

function random_color_part() {
    return str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
}

function random_color() {
    return "#" . random_color_part() . random_color_part() . random_color_part();
}

// $vec = new Vector3(1, 2, 3);

$mat = new Matrix4(
    array(
        1, 0, 0, 0,
        0, 1, 0, 0,
        0, 0, 1, 0,
        0, 0, 0, 1,
    )
);

$viewWidth = 800;
$viewHeight = 800;

$tris = array(
    array(
        new Vector3(1, 1, -1),
        new Vector3(1, -1, -1),
        new Vector3(-1, -1, -1),
        new Vector3(-1, 1, -1)
    ),
    array(
        new Vector3(1, 1, -1),
        new Vector3(1, -1, -1),
        new Vector3(1, -1, 1),
        new Vector3(1, 1, 1)
    ),
    array(
        new Vector3(-1, 1, -1),
        new Vector3(-1, -1, -1),
        new Vector3(-1, -1, 1),
        new Vector3(-1, 1, 1)
    ),
    array(
        new Vector3(1, 1, 1),
        new Vector3(1, -1, 1),
        new Vector3(-1, -1, 1),
        new Vector3(-1, 1, 1)
    ),

);

// $perspective = $mat->Perspective(1, 100, 1, 45);

// $model = Matrix4::Scale(new Vector3(10, 10, 10))->matrixMatrixMultiply(Matrix4::Translate(new Vector3(0.01, 0.01, 0)));

$model = Matrix4::Scale(new Vector3(5, 5, 5));

// $model = Matrix4::Translate(new Vector3(0.1, 0.1, 0.1))->matrixMatrixMultiply(Matrix4::Scale(new Vector3(10, 10, 10)));
// $model = $model->matrixMatrixMultiply(Matrix4::rotate(0, new Vector3(0, 1, 0)));
// $model = $model->matrixMatrixMultiply(Matrix4::rotate(0, new Vector3(0, 1, 0)));
$view = Matrix4::Translate(new Vector3(5, 5, -10  ));

$proj = Matrix4::Perspective(1, 100, $viewWidth/$viewHeight, 90);

$mvp = ($proj->matrixMatrixMultiply($view));
// $mvp = ($model->matrixMatrixMultiply($view))->matrixMatrixMultiply($proj);



// $mvp = $proj->matrixMatrixMultiply($view)->matrixMatrixMultiply($model);

for ($i = 0; $i < 16; $i++) {
    // echo $mvp->val($i).", ";
    if ($i %4 == 3){
        // echo "<br>";
    }
}

// image with 100x100 viewport
$image = new SVG($viewWidth, $viewHeight);
$doc = $image->getDocument();

for($i = 0; $i < count($tris); $i++) {
    for($j = 0; $j < count($tris[$i]); $j++) {
        $newTris[$i][$j] = $mvp->matrixVectorMultiply($tris[$i][$j]);

        $points[$i][$j][0] = abs($newTris[$i][$j]->X() * $viewWidth);
        $points[$i][$j][1] = abs($newTris[$i][$j]->Y() * $viewHeight);

        // echo $points[$i][$j][0]. ", ";
        // echo $points[$i][$j][1]. ", ";
        // echo "<br>";
    }
    $poly = new SVGPolygon($points[$i]);
    $poly->setStyle('fill', random_color());
    $doc->addChild($poly);

}


header('Content-Type: image/svg+xml');
echo $image;
