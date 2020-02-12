<?php

require_once('../vendor/autoload.php');


// require '/xampp/vendor/autoload.php';
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

function draw($shape, $m, $v, $p, $viewWidth, $viewHeight, $doc) {
    for ($i = 0; $i < count($shape); $i++) {
        for($j = 0; $j < count($shape[$i]); $j++) {
            $mvp = ($p->matrixMatrixMultiply($v))->matrixMatrixMultiply($m);
            $newTris[$i][$j] = $mvp->matrixVectorMultiply($shape[$i][$j]);

            $points[$i][$j][0] = abs($newTris[$i][$j]->X() * $viewWidth + $viewWidth/2);
            $points[$i][$j][1] = abs($newTris[$i][$j]->Y() * $viewHeight + $viewHeight/2);

            // echo $points[$i][$j][0]. ", ";
            // echo $points[$i][$j][1]. ", ";
            // echo "<br>";
        }
        $poly = new SVGPolygon($points[$i]);
        $poly->setStyle('fill', random_color());
        $doc->addChild($poly);
    }
}

class Scene {
    /**
     * @var Polygon[]
     */
    public $polygons = array();
    /**
     * @var Matrix4[]
     */
    protected $models = array();
    /**
     * @var Matrix4
     */
    public $view;


    function addShape3d($polyhedron, $model) {
        // add each face individually
        foreach ($polyhedron as $polygon) {
            $this->addShape2d($polygon, $model);
        }
    }

    function addShape2d($polygon, $model) {

        $poly = new polygon($polygon, $model);
        $poly->distance = Scene::calculateDistance($polygon, $model);


        if (count($this->polygons) == 0) {
            $this->polygons[] = $poly;
        } else {
            for ($i = 0; $i <= count($this->polygons); $i++) {

                if ($i == count($this->polygons)) {
                    $this->polygons[] = $poly;
                    break;
                }

                if ($this->polygons[$i]->distance < $poly->distance) {
                    array_splice( $this->polygons, $i, 0, array($poly));
                    break;
                }
            }
        }
    }

    function calculateDistance($shape, Matrix4 $model) {

        $average = new Vector3(0, 0, 0);

        for ($i = 0; $i < count($shape); $i++) {
            $average = $average->vectorAdd($model->matrixVectorMultiply($shape[$i]));
        }

        $average->scale(1 / count($shape));

        // invert the Z axis
        $inverted = $this->view->GetPositionVector();
        $inverted->setZ(-$inverted->Z());

        return Vector3::distance($inverted, $average);
    }

    function draw(Matrix4 $p, $viewWidth, $viewHeight, $doc) {
        for ($i = 0; $i < count($this->polygons); $i++) {

            for($j = 0; $j < count($this->polygons[$i]->points); $j++) {

                $mvp = ($p->matrixMatrixMultiply($this->view))->matrixMatrixMultiply($this->polygons[$i]->transform);
                $newTris[$i][$j] = $mvp->matrixVectorMultiply($this->polygons[$i]->points[$j]);
                $points[$i][$j][0] = abs($newTris[$i][$j]->X() * $viewWidth + $viewWidth/2);
                $points[$i][$j][1] = abs($newTris[$i][$j]->Y() * $viewHeight + $viewHeight/2);

            }
            $poly = new SVGPolygon($points[$i]);
            $poly->setStyle('fill', random_color());
            $doc->addChild($poly);
        }
    }

}

class Polygon {
    public $points = array();
    public $colour;
    public $transform;
    public $distance;

    /**
     * Polygon constructor.
     * @param $points Vector3[]
     * @param $model Matrix4
     */
    public function __construct($points, $model)
    {
        $this->points = $points;
        $this->transform = $model;


    }
}

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

$cube = array(
    array(
        new Vector3(1, 1, -1),
        new Vector3(1, -1, -1),
        new Vector3(-1, -1, -1),
        new Vector3(-1, 1, -1)
    ),
    array(
        new Vector3(-1, -1, -1),
        new Vector3(-1, -1, 1),
        new Vector3(1, -1, 1),
        new Vector3(1, -1, -1)
    ),
    array(
        new Vector3(-1, 1, -1),
        new Vector3(-1, -1, -1),
        new Vector3(-1, -1, 1),
        new Vector3(-1, 1, 1)
    ),
    array(
        new Vector3(1, 1, -1),
        new Vector3(1, -1, -1),
        new Vector3(1, -1, 1),
        new Vector3(1, 1, 1)
    ),
    array(
        new Vector3(-1, 1, -1),
        new Vector3(-1, 1, 1),
        new Vector3(1, 1, 1),
        new Vector3(1, 1, -1)
    ),
    array(
        new Vector3(1, 1, 1),
        new Vector3(1, -1, 1),
        new Vector3(-1, -1, 1),
        new Vector3(-1, 1, 1)
    ),
);
$phi = 1.6180339;
/*
const phi =  1.6180339;
$ico = Vector3(
    array(
        new Vector3(1, -phi, 0),
        new Vector3(-1, -phi, 0),
        new Vector3(0, -1, phi),
    ),
);
*/

// model matrix: transform object from local space coordinates to world space
$model1 = Matrix4::Translate(new Vector3(3, -3, -5));
$model2 = Matrix4::Translate(new Vector3(-3, -3, -1))->matrixMatrixMultiply(Matrix4::rotate(20, new Vector3(1, 1, 1)));
$model3 = Matrix4::Translate(new Vector3(-3, 3, -1))->matrixMatrixMultiply(Matrix4::Scale(new Vector3(0.5, 1, 1)));

// vew matrix: the position/angle of the camera
$view = Matrix4::Translate(new Vector3(0, 0, -10 ))->matrixMatrixMultiply(Matrix4::rotate(0, new Vector3(0, 1, 0)));

// projection matrix: the projection to be used
$proj = Matrix4::Perspective(1, 100, $viewWidth/$viewHeight, 90);



// image with 100x100 viewport
$image = new SVG($viewWidth, $viewHeight);
$doc = $image->getDocument();

$scene = new Scene();

$scene->view = $view;

$testModel = Matrix4::Translate(new Vector3(3, 3, 0));

//$scene->addShape3d($cube, $model1);
//$scene->addShape3d($cube, $model2);
//$scene->addShape3d($cube, $model3);


for ($i = 0; $i < 10; $i++) {

    $translateVector = new Vector3(
        rand(-7, 7),
        rand(-7, 7),
        rand(-20, -5)
    );


    $rotateVector = new Vector3(
        rand(0, 1),
        rand(0, 1),
        rand(0, 1)
    );

    $rotateVector = new Vector3(1, 1, 1);

    $rotateAmount = rand(0, 360);



    $model = Matrix4::Translate($translateVector)->matrixMatrixMultiply(Matrix4::rotate($rotateAmount, $rotateVector));
    $scene->addShape3d($cube, $model);
}


$scene->draw($proj, $viewWidth, $viewHeight, $doc);


// header('Content-Type: image/svg+xml');
echo $image;
