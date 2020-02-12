<?php
/**
 * Created by PhpStorm.
 * User: charl
 * Date: 18/12/2018
 * Time: 22:55
 */
// include("Vector3.php");
// use \Vector3 as Vector3;

class Matrix4
{
    protected $elements = array(
        1, 0, 0, 0,
        0, 1, 0, 0,
        0, 0, 1, 0,
        0, 0, 0, 1
    );

    public function __toString()
    {
        $printString = "";
        for ($i = 0; $i < 16; $i++) {
            $printString.= $this->elements[$i].", ";
            if ($i %4 == 3){
                $printString.="<br>";
            }
        }
        return $printString;
    }

    public function __construct ()
    {
        $numargs = func_num_args();
        if ($numargs == 1) {
            $this->elements =  func_get_arg(0);
        } else {
            $this->elements = array(
                1, 0, 0, 0,
                0, 1, 0, 0,
                0, 0, 1, 0,
                0, 0, 0, 1
            );
        }
    }

    public function val($index)
    {
        return $this->elements[$index];
    }

    public function ToIdentity()
    {
        $this->ToZero();
        $this->elements[0] = 1;
        $this->elements[5] = 1;
        $this->elements[10] = 1;
        $this->elements[15] = 1;
    }

    public function ToZero()
    {
        for ($i = 0; $i < 16; $i++)
        {
            $this->elements[$i] = 0;
        }
    }

    public function GetPositionVector()
    {
        return new Vector3($this->elements[12],$this->elements[13],$this->elements[14]);
    }

    public function SetPositionVector(Vector3 $vec)
    {
        $this->elements[12] = $vec->X();
        $this->elements[13] = $vec->Y();
        $this->elements[14] = $vec->Z();
    }

    public function SetScalingFactor(Vector3 $in)
    {
        $this->elements[0] = $in->X();
        $this->elements[5] = $in->Y();
        $this->elements[10] = $in->Z();
    }

    public static function Perspective($znear, $zfar, $aspect, $fov)
    {
        $h = 1/ tan($fov*pi()/360);
        $neg_depth = $znear-$zfar;

        $m = new Matrix4(
            array(
                $h/$aspect, 0, 0, 0,
                0, $h, 0, 0,
                0, 0, ($zfar + $znear)/$neg_depth, 1,
                0, 0, 2.0*($znear*$zfar)/$neg_depth, 0
            )
        );

        return $m;
    }

    // I'll get back to this later
    public static function Orthographic()
    {
        $m = new Matrix4(
            array(

            )
        );
        return $m;
    }

    /**
     * @param $degrees double the amount to rotate by
     * @param $inAxis Vector3 the axis to rotate around
     */
    public static function rotate($degrees, $inAxis)
    {
        $inAxis->normalize();
        $c = cos(deg2rad($degrees));
        $s = sin(deg2rad($degrees));

        $m = new Matrix4(
            array(
                ($inAxis->X() * $inAxis->X()) * (1 - $c) + $c,
                ($inAxis->Y() * $inAxis->X()) * (1 - $c) + ($inAxis->Z() * $s),
                ($inAxis->Z() * $inAxis->X()) * (1 - $c) - ($inAxis->Y() * $s),
                0,

                ($inAxis->X() * $inAxis->Y()) * (1 - $c) - ($inAxis->Z() * $s),
                ($inAxis->Y() * $inAxis->Y()) * (1 - $c) + $c,
                ($inAxis->Z() * $inAxis->Y()) * (1 - $c) + ($inAxis->X() * $s),
                0,

                ($inAxis->X() * $inAxis->Z()) * (1 - $c) + ($inAxis->Y() * $s),
                ($inAxis->Y() * $inAxis->Z()) * (1 - $c) - ($inAxis->X() * $s),
                ($inAxis->Z() * $inAxis->Z()) * (1 - $c) + $c,
                0,

                0, 0, 0, 1
            )
        );
        return $m;
    }

    public static function Scale(Vector3 $scale)
    {
        $m = new Matrix4(
            array(
                $scale->X(), 0, 0, 0,
                0, $scale->Y(), 0, 0,
                0, 0, $scale->Z(), 0,
                0, 0, 0, 1,
            )
        );

        return $m;
    }

    public static function Translate(Vector3 $trans)
    {
        $m = new Matrix4(
            array(
                1, 0, 0, 0,
                0, 1, 0, 0,
                0, 0, 1, 0,
                $trans->X(), $trans->Y(), $trans->Z(), 1
            )
        );
        return $m;
    }

    public static function BuildViewMatrix(Vector3 $from, Vector3 $at, Vector3 $up)
    {
        $r = new Matrix4();
        $r->SetPositionVector(new Vector3(-$from->X(), -$from->Y(), -$from->Z()));



        $f = ($at->vectorSub($from));
        $f->normalize();

        $s = $f->vectorCross($up);
        $s->normalize();

        $u = $s->vectorCross($f);
        $u->normalize();

        // TODO check these values
        $m = new Matrix4(
            array(
                $s->X(), $u->X(), $f->X(), 0,
                $s->Y(), $u->Y(), $f->Y(), 0,
                $s->Z(), $u->Z(), $f->Z(), 0,
                0, 0, 0, 1

            )
        );
        return $r->matrixMatrixMultiply($m);
    }

    public function matrixMatrixMultiply(Matrix4 $mat)
    {
        $newElems = [];
        for ($r = 0; $r < 4; $r++) {
            for ($c = 0; $c < 4; $c++) {
                $newElems[$c + ($r * 4)] = 0;
                for ($i = 0; $i < 4; $i++) {
                    $newElems[$c + ($r * 4)] += $this->elements[$c + ($i * 4)] * $mat->val(($r * 4) + $i);
                }
            }
        }
        return new Matrix4($newElems);
    }

    public function matrixVectorMultiply(Vector3 $vec)
    {
        $values = $this->elements;
        $x = $vec->X();
        $y = $vec->Y();
        $z = $vec->Z();

        $newx = $x*$values[0] + $y*$values[4] + $z*$values[8] + $values[12];
        $newy = $x*$values[1] + $y*$values[5] + $z*$values[9] + $values[13];
        $newz = $x*$values[2] + $y*$values[6] + $z*$values[10] + $values[14];

        $temp = $x*$this->elements[3] + $y*$this->elements[7] + $z*$this->elements[11] + $this->elements[15];

        $newx = $newx / $temp;
        $newy = $newy / $temp;
        $newz = $newz / $temp;
        return new Vector3($newx, $newy, $newz);
    }

}