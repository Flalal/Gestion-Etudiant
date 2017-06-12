<?php
/**
 * Created by PhpStorm.
 * User: Frederic
 * Date: 09/06/2017
 * Time: 14:38
 */



function salut($dept,$semestre,$annee,$groupe=null ){

    if($semestre[1]>4 || $semestre[1]<0) {
        error_log("erreur de semestre");
        exit(1);
    }

    if ($semestre[1]>2) {
        for ($cptSemestre = $semestre[1]; $cptSemestre > 2; $cptSemestre--) {
            echo $dept . "_S" . $cptSemestre . "_" . $annee . PHP_EOL;

        }
        $anneeprecedent=$annee;
        $anneeprecedent[(strlen($annee)/2)-1]=$anneeprecedent[(strlen($annee)/2)-1]-1;
        $anneeprecedent[(strlen($annee))-1]=$anneeprecedent[(strlen($annee))-1]-1;
        for ($cptSemestre = $semestre[1]; $cptSemestre > 0; $cptSemestre--) {
            echo $dept . "_S" . $cptSemestre . "_" . $anneeprecedent . PHP_EOL;
        }

        $anneeprecedent[(strlen($annee)/2)-1]=$anneeprecedent[(strlen($annee)/2)-1]-1;
        $anneeprecedent[(strlen($annee))-1]=$anneeprecedent[(strlen($annee))-1]-1;
        for ($cptSemestre =2; $cptSemestre > 0; $cptSemestre--) {
            echo $dept . "_S" . $cptSemestre . "_" . $anneeprecedent . PHP_EOL;


        }
    }
    else{

        for ($cptSemestre = $semestre[1]; $cptSemestre > 0; $cptSemestre--) {
            echo $dept . "_S" . $cptSemestre . "_" . $annee . PHP_EOL;

        }
        $anneeprecedent=$annee;
        $anneeprecedent[(strlen($annee)/2)-1]=$anneeprecedent[(strlen($annee)/2)-1]-1;
        $anneeprecedent[(strlen($annee))-1]=$anneeprecedent[(strlen($annee))-1]-1;
        for ($cptSemestre = $semestre[1]; $cptSemestre > 0; $cptSemestre--) {
            echo $dept . "_S" . $cptSemestre . "_" . $anneeprecedent . PHP_EOL;
        }
    }





}
if ( count($argv)==4){
    salut($argv[1],$argv[2],$argv[3]);/// 1er argument : Departement , 2eme argument : Semestre , 3 eme argument: année
}
else
    salut($argv[1],$argv[2],$argv[3],$argv[4]);// 4 eme argument : le groupe



