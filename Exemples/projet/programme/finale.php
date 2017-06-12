<?php
/**
 * Created by PhpStorm.
 * User: Frederic
 * Date: 09/06/2017
 * Time: 14:38
 */



function salut($dept,$semestre,$annee,$groupe=null ){

    if($semestre[1]>4 ) {
        error_log("erreur de semestre");
        exit(1);
    }

    if ($semestre[1]==4) {
        for ($cptSemestre = $semestre[1]; $cptSemestre > 2; $cptSemestre--) {
            echo $dept . "_S" . $cptSemestre . "_" . $annee . PHP_EOL;
            var_dump(file_exists($dept . "_S" . $cptSemestre . "_" . $annee));

        }
        $anneeprecedent=$annee;
         $anneeprecedent[(strlen($annee)/2)-1]=$anneeprecedent[(strlen($annee)/2)-1]-1;
         $anneeprecedent[(strlen($annee))-1]=$anneeprecedent[(strlen($annee))-1]-1;
        for ($cptSemestre = $semestre[1]; $cptSemestre > 0; $cptSemestre--) {
            if (file_exists($dept . "_S" . $cptSemestre . "_" . $anneeprecedent))
                echo $dept . "_S" . $cptSemestre . "_" . $anneeprecedent . PHP_EOL;
        }

        



    }



}
if ( count($argv)==4){
    salut($argv[1],$argv[2],$argv[3]);
}
else
    salut($argv[1],$argv[2],$argv[3],$argv[4]);



