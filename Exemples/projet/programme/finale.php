<?php
/**
 * Created by PhpStorm.
 * User: Frederic
 * Date: 09/06/2017
 * Time: 14:38
 */

$JSONEtudiant=null;
$DEPARTEMENT="INFO";

date_default_timezone_set('Europe/Brussels') ;


function validationFichier($fichier){
    if (file_exists($fichier)===false){
        error_log("le fichier ou dossier '$fichier' n'exite pas ");
       return false;
    }
    return true;

}



function salut($semestre,$annee,$groupe=null ){
    global $DEPARTEMENT;
    global $JSONEtudiant;

    validationFichier($DEPARTEMENT);
    validationFichier($DEPARTEMENT.'/'.($annee-1).'-'.$annee);








    if ($semestre[1]>2) {
        for ($cptSemestre = $semestre[1]; $cptSemestre > 2; $cptSemestre--) {
            if ($cptSemestre==4){
                $intituleFichier=$DEPARTEMENT.'/'.($annee-1).'-'.$annee.'/'.$DEPARTEMENT . "_S" . $cptSemestre . "_".$groupe.'_' .($annee-1).$annee."/json/".$DEPARTEMENT . "_S" . $cptSemestre . "_" . ($annee-1).$annee.".json";
            }else
            $intituleFichier=$DEPARTEMENT.'/'.($annee-1).'-'.$annee.'/'.$DEPARTEMENT . "_S" . $cptSemestre . "_" .($annee-1).$annee."/json/".$DEPARTEMENT . "_S" . $cptSemestre . "_" .($annee-1).$annee.".json";
            if (validationFichier($intituleFichier)){
               // echo $intituleFichier.PHP_EOL;
                modifcationJson($intituleFichier);

            }


        }
        //// anner -1
        $anneeprecedent=$annee-1;
        for ($cptSemestre = $semestre[1]; $cptSemestre > 0; $cptSemestre--) {
           // echo $DEPARTEMENT . "_S" . $cptSemestre . "_" . ($anneeprecedent-1).$anneeprecedent. PHP_EOL;
            $intituleFichier=$DEPARTEMENT.'/'.($annee-1).'-'.$annee.'/'.$DEPARTEMENT . "_S" . $cptSemestre . "_" . ($anneeprecedent-1).$anneeprecedent."/json/".$DEPARTEMENT . "_S" . $cptSemestre . "_" . ($anneeprecedent-1).$anneeprecedent.".json";
            if (validationFichier($intituleFichier)){
                // echo $intituleFichier.PHP_EOL;
                modifcationJson($intituleFichier);

            }
        }
        // année -2
        $anneeprecedent=$anneeprecedent-1;
        for ($cptSemestre =2; $cptSemestre > 0; $cptSemestre--) {
          //  echo $DEPARTEMENT . "_S" . $cptSemestre . "_" . $anneeprecedent . PHP_EOL;

            $intituleFichier=$DEPARTEMENT.'/'.($annee-1).'-'.$annee.'/'.$DEPARTEMENT . "_S" . $cptSemestre . "_" .($anneeprecedent-1).$anneeprecedent."/json/".$DEPARTEMENT . "_S" . $cptSemestre . "_" . ($anneeprecedent-1).$anneeprecedent.".json";
            if (validationFichier($intituleFichier)){
                // echo $intituleFichier.PHP_EOL;
                modifcationJson($intituleFichier);

            }

        }
    }
    else{

        for ($cptSemestre = $semestre[1]; $cptSemestre > 0; $cptSemestre--) {
            $intituleFichier=$DEPARTEMENT.'/'.($annee-1).'-'.$annee.'/'.$DEPARTEMENT . "_S" . $cptSemestre . "_" .($annee-1).$annee."/json/".$DEPARTEMENT . "_S" . $cptSemestre . "_" .($annee-1). $annee.".json";
            if (validationFichier($intituleFichier)) {
                // echo $intituleFichier.PHP_EOL;
                modifcationJson($intituleFichier);
            }

        }
        $anneeprecedent=$annee-1;

        for ($cptSemestre = $semestre[1]; $cptSemestre > 0; $cptSemestre--) {
            $intituleFichier=$DEPARTEMENT.'/'.($annee-1).'-'.$annee.'/'.$DEPARTEMENT . "_S" . $cptSemestre . "_" . ($anneeprecedent-1).$anneeprecedent."/json/".$DEPARTEMENT . "_S" . $cptSemestre . "_" .($anneeprecedent-1). $anneeprecedent.".json";
            if (validationFichier($intituleFichier)){
                // echo $intituleFichier.PHP_EOL;
                modifcationJson($intituleFichier);

            }
        }
    }

    $contenu_json=json_encode($JSONEtudiant);


    $fichier=fopen($DEPARTEMENT."_conseille.json","w+");
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

function validateDate($date, $format = 'Y-m-d H:i:s')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}



function chercherFichier($semestre,$option=null,$option2=null){
    global $DEPARTEMENT;
    if ($option===null && $option2===null){
        salut($semestre,date('Y'));

    }


    elseif ($option!==null && $option2===null){
        if (validateDate($option,'Y')===false){
            salut($semestre,date('Y'),$option);



        }
        else{
            $d = DateTime::createFromFormat('Y', $option);
            $date=$d->format("Y");

            salut($semestre,$date);



        }

    }
    elseif ($option!==null && $option2!==null){
        if (validateDate($option,'Y')==false && validateDate($option2,'Y')==true){
            $d = DateTime::createFromFormat('Y', $option2);
            $date=$d->format("Y");
            salut($semestre,$date,$option);


        }else{
            error_log("erreur sur les arguments Semestre groupe et Année ");
        }

    }


}



if( count($argv)<2){
    error_log("manque l'argument du semestre");
    exit(1);
}
$semestre=$argv[1];//Semestre
$option=null;
$date=null;
if( count($argv)>=3){
    $option=$argv[2];// groupe si il y a des lite spécifique date passer en paramettre si elle n'existe c'est la date courante

}


if( count($argv)>=4){
    $date=$argv[3];// groupe si il y a des lite spécifique date passer en paramettre si elle n'existe c'est la date courante
}

chercherFichier($semestre,$option,$date);


