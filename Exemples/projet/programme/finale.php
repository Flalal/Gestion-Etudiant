<?php
/**
 * Created by PhpStorm.
 * User: Frederic
 * Date: 09/06/2017
 * Time: 14:38
 */

$ToutDepartement=["INFO","MMI","TC","GEA"];
$JSONEtudiant=null;

function salut($dept,$semestre,$annee,$groupe=null ){
    global $ToutDepartement;
    $dept= strtoupper($dept);
    global $JSONEtudiant;
    if($semestre[1]>4 || $semestre[1]<0) {
        error_log("erreur de semestre");
        exit(1);
    }

    if(strlen($annee)!=8){
        error_log("erreur sur l'année ");
        exit(1);

    }
    if(in_array($dept,$ToutDepartement)=== false){
        error_log("erreur sur le département ");
        exit(1);
    }




    if ($semestre[1]>2) {
        for ($cptSemestre = $semestre[1]; $cptSemestre > 2; $cptSemestre--) {
            //echo $dept . "_S" . $cptSemestre . "_" . $annee . PHP_EOL;
            $intituleFichier=$dept . "_S" . $cptSemestre . "_" . $annee."/json/".$dept . "_S" . $cptSemestre . "_" . $annee.".json";
            if (file_exists($intituleFichier)){
               // echo $intituleFichier.PHP_EOL;
                modifcationJson($intituleFichier);

            }


        }
        $anneeprecedent=$annee;
        $anneeprecedent[(strlen($annee)/2)-1]=$anneeprecedent[(strlen($annee)/2)-1]-1;
        $anneeprecedent[(strlen($annee))-1]=$anneeprecedent[(strlen($annee))-1]-1;
        for ($cptSemestre = $semestre[1]; $cptSemestre > 0; $cptSemestre--) {
            //echo $dept . "_S" . $cptSemestre . "_" . $anneeprecedent . PHP_EOL;
            $intituleFichier=$dept . "_S" . $cptSemestre . "_" . $anneeprecedent."/json/".$dept . "_S" . $cptSemestre . "_" . $anneeprecedent.".json";
            if (file_exists($intituleFichier)){
                // echo $intituleFichier.PHP_EOL;
                modifcationJson($intituleFichier);

            }
        }

        $anneeprecedent[(strlen($annee)/2)-1]=$anneeprecedent[(strlen($annee)/2)-1]-1;
        $anneeprecedent[(strlen($annee))-1]=$anneeprecedent[(strlen($annee))-1]-1;
        for ($cptSemestre =2; $cptSemestre > 0; $cptSemestre--) {
          //  echo $dept . "_S" . $cptSemestre . "_" . $anneeprecedent . PHP_EOL;


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

    $contenu_json=json_encode($JSONEtudiant);


    $fichier=fopen($dept."_conseille.json","w+");
    fwrite($fichier,$contenu_json);
    fclose($fichier);





}


/**
 * @param $intituler_Fichier
 */
function modifcationJson($intituler_Fichier){
    global $JSONEtudiant;
    $json = file_get_contents($intituler_Fichier);
    $parsed_json = json_decode($json,true);

    if ($JSONEtudiant===null){
        $JSONEtudiant=$parsed_json;
    }else{
        foreach (array_keys($JSONEtudiant) as $etudiant) {
            if (in_array($etudiant,array_keys( $parsed_json))) {
                foreach (array_keys($parsed_json[$etudiant]["ue"]) as $jsonUE) {
                    if (in_array($jsonUE, array_keys($JSONEtudiant[$etudiant]["ue"])) === false) {
                        $JSONEtudiant[$etudiant]["ue"][$jsonUE] = $parsed_json[$etudiant]["ue"][$jsonUE];
                    } else {
                        $JSONEtudiant[$etudiant]["ue"][$jsonUE . "R"] = $parsed_json[$etudiant]["ue"][$jsonUE];

                    }

                }

            }
        }

    }
}


if ( count($argv)==4){
    salut($argv[1],$argv[2],$argv[3]);/// 1er argument : Departement , 2eme argument : Semestre , 3 eme argument: année
}
else
    salut($argv[1],$argv[2],$argv[3],$argv[4]);// 4 eme argument : le groupe



