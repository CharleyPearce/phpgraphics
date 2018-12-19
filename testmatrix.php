<?php
include("Vector3.php");
include("Matrix4.php");

$m1 = new Matrix4(
    array(
        1, 2, 3, 4,
        5, 6, 7, 8,
        9, 10, 11, 12,
        13, 14, 15, 16
    )
);

$m2 = new Matrix4(
    array(
        20, 21, 22, 23,
        24, 25, 26, 27,
        28, 29, 30, 31,
        32, 33, 34, 35
    )
);

$m2 = Matrix4::Rotate(45, new Vector3(0, 1, 0));

$m3 = $m1->matrixMatrixMultiply($m2);


echo $m2;