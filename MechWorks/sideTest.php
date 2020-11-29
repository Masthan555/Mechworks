<?php
    $ar1 = array("524004","524003","524001","524008","524006","524000");
    sort($ar1);

    $ar2 = $ar1;
    for($i=0;$i<count($ar2);$i++)
    {
        $ar2[$i] = $ar2[$i]-"524003";

        if($ar2[$i]<0)
        {
            $ar2[$i] = (-$ar2[$i])-0.5;
        }
    }

    $ar3 = $ar2;
    $ar4 = array();
    sort($ar3);

    for($i=0;$i<count($ar3);$i++)
    {
        $ind = array_search($ar3[$i],$ar2);
        $ar4[count($ar4)] = $ar1[$ind];
    }

    print_r($ar4)
/*
    print_r($ar1);
    echo("<br>");
    print_r($ar2);
*/
?>