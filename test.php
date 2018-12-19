<?php

include("Vector3.php");
include("Matrix4.php");

// $vec = new Vector3(1, 2, 3);

$mat = new Matrix4(
    array(
        1, 0, 0, 0,
        0, 1, 0, 0,
        0, 0, 1, 0,
        0, 0, 0, 1,
    )
);

$mat = $mat->Perspective(1, 100, 1, 45);

for($i = 0; $i < 16; $i++){
    echo $mat->val($i);
    echo ", ";
    if ($i % 4 == 3) {
        echo "<br>";
    }
}

