<?php
/*
  3D Vector Mathematics Library for PHP
  https://HelloACM.com
*/

class Vector3
{
    private $x = 0;
    private $y = 0;
    private $z = 0;

    // Constructor
    public function __construct ($x, $y, $z)
    {
        $this->x = $x;
        $this->y = $y;
        $this->z = $z;
    }

    // Set X
    public function setX($x)
    {
        $this->x = $x;
    }

    // Set Y
    public function setY($y)
    {
        $this->y = $y;
    }

    // Set Z
    public function setZ($z)
    {
        $this->z = $z;
    }

    // Get X
    public function X()
    {
        return $this->x;
    }

    // Get Y
    public function Y()
    {
        return $this->y;
    }

    // Get Z
    public function Z()
    {
        return $this->z;
    }

    // Vector Add
    public function add($xx, $yy, $zz)
    {
        $this->x += $xx;
        $this->y += $yy;
        $this->z += $zz;
    }

    // Vector Sub
    public function sub($xx, $yy, $zz)
    {
        $this->x -= $xx;
        $this->y -= $yy;
        $this->z -= $zz;
    }

    // Vector Negative
    public function neg()
    {
        $this->x = -$this->x;
        $this->y = -$this->y;
        $this->z = -$this->z;
    }

    // Vector Scale
    public function scale($k)
    {
        $this->x *= $k;
        $this->y *= $k;
        $this->z *= $k;
    }

    // Vector Dot Product
    public function dot($xx, $yy, $zz)
    {
        return ($this->x * $xx+
            $this->y * $yy+
            $this->z * $zz);
    }

    // Vector Length^2
    public function len2()
    {
        return ($this->x * $this->x +
            $this->y * $this->y +
            $this->z * $this->z);
    }

    // Vector Length
    public function len()
    {
        return (sqrt($this->len2()));
    }

    // Normalize Vector
    public function normalize()
    {
        $tmp = $this->len();
        if (abs($tmp) > 1e-7)
        {
            $this->x /= $tmp;
            $this->y /= $tmp;
            $this->z /= $tmp;
        }
        else
        {
            throw new Exception('len = 0');
        }
    }

    // Vector Cross Product
    public function cross($xx, $yy, $zz)
    {
        $cx = $this->y * $zz - $this->z * $yy;
        $cy = $this->z * $xx - $this->x * $zz;
        $cz = $this->x * $yy - $this->y * $xx;
        $this->x = $cx;
        $this->y = $cy;
        $this->z = $cz;
    }
}
?>