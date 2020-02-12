<?php
include("Vector3.php");
include("Matrix4.php");

$m1 = Matrix4::Scale(new Vector3(3, 3, 1));
$m2 = Matrix4::Translate(new Vector3(3, 0, 0));



$m3 = $m1->matrixMatrixMultiply($m2);
$m4 = $m2->matrixMatrixMultiply($m1);



echo $m4;