<?php
/**
 * Created by PhpStorm.
 * User: hsu
 * Date: 27/06/17
 * Time: 18:09
 */

require('Classes/PHPExcel.php');
require ('Classes/PHPExcel/IOFactory.php');
require ('Classes/Etudiant.php');
require ('Classes/Matiere.php');
require ('Classes/UE.php');
require ('Classes/Semestre.php');


define("MARQUEUR","*");
define("DEPARTEMENT","INFO");
define("SEMESTRE", "SEMESTRE"); // S1, S2, S3, S4
define("ANNEE", "ANNEE"); // format 1617
define("LISTE",     "Liste_Etudiants_SEMESTRE_ANNEE.csv");
define("MATIERES",     "Liste_Matières_SEMESTRE_ANNEE.csv");
define("BILAN","Bilan_INFO_SEMESTRE_ANNEE.xls");;
define("LES_SEMESTRES", "S0 S1 S2 S3 S4 S5 S6");
define("CSV_DELIMITEUR",";");


function rechercherTag ($f, $tag) {
    $existe = false;
    while ($row = fgetcsv($f,"1024",CSV_DELIMITEUR)) {
        //  echo $row[0].PHP_EOL;
        if ($row[0] == MARQUEUR) {
            // echo MARQUEUR. "trouve".PHP_EOL;
            if (strcasecmp ($row[1],$tag)==0 ) {
                $existe = true;
                break;
            }
        }
    }
    return $existe;

}


function lirePromotion ($file) {
    // Numéro;Nom;Prénom;Groupe;Date;Bac;Lycée/etablissement
    $f = fopen($file, 'r');
    // echo LISTE_UES. "trouvée";
    $tabIntitules= fgetcsv($f,"1024",CSV_DELIMITEUR);
    $nbIntitules = count($tabIntitules);
    $promotion = array();
    while ($data = fgetcsv($f,"1024",CSV_DELIMITEUR)) {
        //  echo "data=".$data;
        $liste = array();
        if (strlen(trim($data[0]))>0) { // si la ligne n'est pas vide
            for ($i = 0; $i < $nbIntitules; $i++) $liste[$tabIntitules[$i]] = trim($data[$i]);
            $numeroEtudiant = $data[0];
            $promotion[$numeroEtudiant]= new Etudiant($liste);
        } else {
            echo "Pas de numero étudiant : ";
            print_r($data);
        }

    }
    fclose($f);
    return $promotion;
}

function initialiserPromotion ($file) {
    if (!($f = fopen($file, 'r'))) {
        die("** Probleme d'ouverture Fichier promotion : ".$file." **");
    }
    fclose($f);
    return  lirePromotion ($file);
}


function lireInfoSemestre ($file) {
    /*
    Département;INFO;;;;;
    Année;INFO2;;;;;
    Date de début;01/09/2016;;;;;
    Date de fin ;31/06/2017;;;;;
    Semestre;S3;;;;;
    */
    define("INFORMATIONS", "Informations générales");
    $f = fopen($file, 'r');
    if (! rechercherTag($f,INFORMATIONS)) throw new Exception ("Pas de ". INFORMATIONS);
    //  echo INFORMATIONS. "trouvée";
    $cpt = 0;
    $ligneNonTraitee = "";
    while ($data = fgetcsv($f,"1024",CSV_DELIMITEUR)) {
        if (strlen(trim($data[0]))==0) break; // ligne vide rencontrée;
        if(strcasecmp($data[0],"Département")==0) {
            $departement= $data[1];
            $cpt++;
        } else if(strcasecmp($data[0],"Année")==0) { $promo = $data[1] ; $cpt++;
        } else if(strcasecmp($data[0],"Date de Début")==0) { $anneeDebut = $data[1] ; $cpt++;
        } else if(strcasecmp($data[0],"Date de Fin")==0) { $anneeFin = $data[1] ; $cpt++;
        } else if(strcasecmp($data[0],"Semestre")==0) { $codeSemestre = $data[1] ; $cpt++;
        } else {
            $ligneNonTraitee.=trim($data[1])." ";
        }
    }
    fclose($f);
    $ligneNonTraitee = trim($ligneNonTraitee);
    if (strlen($ligneNonTraitee)>0) {
        echo "Attention - Traitement InfoSemestre : ".$ligneNonTraitee." : informations non traitées".PHP_EOL;
    }
    if ($cpt != 5) {
        echo $departement."".$promo." ".$anneeDebut." ".$anneeFin." ".$codeSemestre.PHP_EOL;
        throw new Exception ("Nombre Arguements insuffisant ".$cpt) ;
    }
    return new Semestre ($codeSemestre,$promo, $departement,$anneeDebut,$anneeFin);
}

function lireListeUE ($file, $semestre) {
    /*
     * Désignation;Nom;Coefficient;;;;
     *
     *  UE31;Informatique Avancé;12;;;;
     */
    define("LISTE_UES", "Liste des UE");
    $f = fopen($file, 'r');
    if (! rechercherTag($f,LISTE_UES)) throw new Exception ("Pas de ". LISTE_UES);
    // echo LISTE_UES. "trouvée";

    $tabIntitules= fgetcsv($f,"1024",CSV_DELIMITEUR);
    $nbIntitules = count($tabIntitules);
    while ($data = fgetcsv($f,"1024",CSV_DELIMITEUR)) {
        //  echo "data=".$data;
        $liste = array();
        if (strlen(trim($data[0]))==0) break;
        for ($i = 0; $i < $nbIntitules; $i++) $liste[$tabIntitules[$i]]= trim($data[$i]);
        $semestre->ajouterUE(new UE($liste));
    }
    fclose($f);
}

function lireListeMatieres($file,$semestre) {
    // *;Liste des Matières;;;;;
    // Référence;Nom Module;Abréviation;Coefficient;UE;Semestre;Responsable
    // M3101;Principes des systèmes d'exploitation ;SE-3;2.5;UE31;S3;Roussel

    define("LISTE_MATIERES", "Liste des Matières");
    $f = fopen($file, 'r');
    if (! rechercherTag($f,LISTE_MATIERES)) throw new Exception ("Pas de ". LISTE_MATIERES);
    // echo LISTE_UES. "trouvée";
    $tabIntitules= fgetcsv($f,"1024",CSV_DELIMITEUR);
    $nbIntitules = count($tabIntitules);
    while ($data = fgetcsv($f,"1024",CSV_DELIMITEUR)) {
        //  echo "data=".$data;
        $liste = array();
        if (strlen(trim($data[0]))==0) break;
        for ($i = 0; $i < $nbIntitules; $i++) $liste[$tabIntitules[$i]]= trim($data[$i]);
        $semestre->ajouterMatiereDansUE($liste);
    }
    fclose($f);
}


function initialiserSemestre ($file) {
    // Numéro;Nom;Prénom;Groupe;Date;Bac;Lycée/etablissement
    if (!($f = fopen($file, 'r'))) {
        die("** Probleme d'ouverture Fichier Semestre : ".$file." **");
    }
    fclose($f);
    $semestre = lireInfoSemestre($file);
    lireListeUE($file,$semestre);
    lireListeMatieres($file,$semestre);
    if (! $semestre->validerUECoefficient() ) throw new Exception ("Coeffcients non valide");
    return  $semestre;
}


function convertXLStoCSV($file, $repDestination, $prefixe) {
    $objPHPExcel = new PHPExcel();

    //comme infile est le fichier depart et qu'il est dans un dossier, alors je decoupe les données
    //$depart est le departement
    //$dossier par exemple le dossier INFO_S3_20162017
    echo "Je suis dans converXLStoCSV ".$file.PHP_EOL;

    if(!file_exists($repDestination)){
        mkdir($repDestination,0700);
    }

    try {
        $inputFileType = PHPExcel_IOFactory::identify($file);
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel = $objReader->load($file);
    }
    catch(Exception $e){
        die('[ERROR catch]> "'.pathinfo($file,PATHINFO_BASENAME).'": '.$e->getMessage());
    }

    $loadedSheetNames = $objPHPExcel->getSheetNames();

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');

    //cette partie permet d'avoir dans le fichier bilan, les données
    //comme tout les excel se ressemble, les données tel que le département, Semestre,... sont tout en bas du fichier
    //exemple:
    //avant il y a les etudiants
    //colonne A(61): Département colonne B(61): INFO
    //colonne A(62): Date début colonne B(62): 12/05/2017

    $column = 'A'; //la colonne A
    $lastRow = $objPHPExcel->getActiveSheet()->getHighestRow();
    for ($row = 1; $row <= $lastRow; $row++){
        // Pour chaque ligne jusqu'a la dernière on récupère la cellule
        $cell = $objPHPExcel->getActiveSheet()->getCell($column.$row);
        if($cell->getValue()=='Département'){
            $departement=$objPHPExcel->getActiveSheet()->getCell('B'.$row)->getValue();
        }
        if($cell->getValue()=='Date début'){
            $date=$objPHPExcel->getActiveSheet()->getCell('B'.$row)->getValue();
        }
        if($cell->getValue()=='Date de fin '){
            $date2=$objPHPExcel->getActiveSheet()->getCell('B'.$row)->getValue();
        }
        if($cell->getValue()=='Semestre'){
            $semestre=$objPHPExcel->getActiveSheet()->getCell('B'.$row)->getValue();
        }
    }

    $fichiers=array();

    foreach($loadedSheetNames as $sheetIndex => $loadedSheetName){
        $objWriter->setSheetIndex($sheetIndex);
        $objWriter->setDelimiter(CSV_DELIMITEUR);
        $objWriter->setEnclosure('');
        $destinationFile = $repDestination."/".$prefixe.'_'.$loadedSheetName.'.csv';
        $objWriter->save($destinationFile);
        //j'ai besoin de ses 4 fichiers pour lancer csvToJson (function un peu plus bas)
        //pour le fichier Absence
        //$loadedSheetName=="Absences"

        /*puis mettre $fichier[]
         * en concervant l'ordre a laquelle on les mets en parametr, quand on appelle csvToJson
         * 1 fichier:la liste etudiants / 2 fichier: Détails des notes / 3 fichier: liste matiere / 4 fichier: décision
         * */

        if($loadedSheetName=="Liste_Matiere" || $loadedSheetName=="Liste_Etudiants"|| $loadedSheetName=="Décision"||strpos($loadedSheetName,"Note_detail_S")!== false){
            $fichiers[$loadedSheetName]=$destinationFile;
        }
    }
    echo "fin conversion".PHP_EOL;
    print_r($fichiers);

    return $fichiers;

//    csvToJson($ListeEtudiants,$BilanDetaille,$ListeMatieres,$Decisions);

}//

function moyenneArray ($tab) {
  $dimension = count($tab);
  if ($dimension==0) return 0;
  return array_sum($tab)/$dimension;
}

function extraireRapportTableau ($tab) {
    $rapport = array ();
    arsort($tab,SORT_NUMERIC);
    $dimension = count($tab);
    $rapport["listeNotes"] = array();
    foreach ($tab as $value)$rapport["listeNotes"][] = $value;
    $rapport["minimum"]= $rapport["listeNotes"][$dimension-1];
    $rapport["maximum"] = $rapport["listeNotes"][0];
    $rapport["moyennePromo"] = moyenneArray($tab);
    return $rapport;
}

function  extraireRapportSemestre  ($fileNotes,$semestre,$promotion) {
    if (!file_exists($fileNotes)){
        throw new Exception ("Fichier inexistant : ".$fileNotes);
    }
    $f = fopen($fileNotes, 'r');
    $tabIntitules= fgetcsv($f,"1024",CSV_DELIMITEUR);

    $tabValues = array();
    foreach ($tabIntitules as $value) {
        $tabValues[$value]= array();
    }

    $nbIntitules = count($tabIntitules);
    while ($data = fgetcsv($f,"1024",CSV_DELIMITEUR)) {
        //  echo "data=".$data;
        $liste = array();
        $attributColonne1 = $data[0];
        if (strlen(trim($attributColonne1))>0) {
            if (is_numeric($attributColonne1)) { // doit être numerique
                for ($i = 0; $i < $nbIntitules; $i++) $liste[$tabIntitules[$i]] = trim($data[$i]);
                foreach ($liste as $key => $value) {
                    if (is_numeric($value))$tabValues[$key][] = $value;
                }
            }
        }
    }

    $rapportSemestre = array ();
    foreach ($tabIntitules as $key) {
        if (count($tabValues[$key])>0) {
            // echo $key." ";
            $rapportSemestre[$key]= extraireRapportTableau($tabValues[$key]);
        }
    }

    // echo json_encode($rapportSemestre["M3303 PPP-3"],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES| JSON_UNESCAPED_UNICODE);
    echo count($rapportSemestre);
    return $rapportSemestre;
}

function createCSVToJson($anneeLong, $nomSemestre, $groupe = null) {
/*    [0] => INFO/2016-2017/INFO_S3_20162017/csv/INFO_S3_20162017_Liste_Matiere.csv
    [1] => INFO/2016-2017/INFO_S3_20162017/csv/INFO_S3_20162017_Liste_Etudiants.csv
    [2] => INFO/2016-2017/INFO_S3_20162017/csv/INFO_S3_20162017_Décision.csv
    [3] => INFO/2016-2017/INFO_S3_20162017/csv/INFO_S3_20162017_Note_detail_S3.csv
*/

//    $repertoire = DEPARTEMENT."/".$anneeLong."/Admin/";
    $repertoireAdmin = DEPARTEMENT."/".$anneeLong."/Admin/";
    $repertoireDestinationCSV = DEPARTEMENT."/".$anneeLong."/".$nomSemestre."/csv/";
    $repertoireSourceExcel = DEPARTEMENT."/".$anneeLong."/".$nomSemestre."/excel/";

    echo "Répertoire destination CSV: ".$repertoireDestinationCSV.PHP_EOL;

    if (!is_dir($repertoireAdmin)) throw new Exception ("Repertoire source inexistant : ".$repertoireAdmin);
    if (!is_dir($repertoireSourceExcel)) throw new Exception ("Repertoire source inexistant : ".$repertoireSourceExcel);
    if (!is_dir($repertoireDestinationCSV)) mkdir($repertoireDestinationCSV,0755,true); // création récursive


    if (isset($groupe)) $nomSemestre = $nomSemestre."_".$groupe;

// Lecture Semestre

    $matieres = str_replace(SEMESTRE, $nomSemestre, MATIERES);
    $matieres = str_replace(ANNEE, $anneeLong, $matieres);
    $semestreFile = $repertoireAdmin.$matieres;
    $semestre = initialiserSemestre($semestreFile);
    echo json_encode($semestre,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES| JSON_UNESCAPED_UNICODE);


    $liste = str_replace (SEMESTRE, $nomSemestre, LISTE);
    $liste = str_replace (ANNEE, $anneeLong, $liste);
    $listeFile = $repertoireAdmin.$liste;
    $promotion= initialiserPromotion($listeFile);
   /* foreach ($promotion as $key => $value) {
        echo $key.": \n".$value.PHP_EOL;
    }*/
    echo json_encode($promotion,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES| JSON_UNESCAPED_UNICODE);

    $bilan = str_replace(SEMESTRE, $nomSemestre, BILAN);
    $bilan = str_replace(ANNEE, $anneeLong, $bilan);
    $bilanFile = $repertoireSourceExcel.$bilan;
    $prefixe = DEPARTEMENT."_".$nomSemestre."_".$anneeLong;
    $tabCSVFile = convertXLStoCSV($bilanFile, $repertoireDestinationCSV,$prefixe);

    print_r($tabCSVFile);
    foreach ($tabCSVFile as $key => $value) {
        if ( stripos($key, "Décision") !== false) $fileDecisions = $value;
        else if ( stripos($key, "Note_detail") !== false) $fileNotes = $value;


    }
    echo  $fileDecisions."+".$fileNotes.PHP_EOL;
   /* $semestreEtudiant = clone $semestre;
    $semestreEtudiant->setNote (10);
    $semestre->setNote (15);
    $semestreEtudiant->ajouterNotesDansUE("UE33","M3303",10);
    $semestre->ajouterNotesDansUE("UE33","M3303",15);
    echo "-----------------------------------------".PHP_EOL;
    echo json_encode($semestre,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES| JSON_UNESCAPED_UNICODE);
    echo "-----------------------------------------".PHP_EOL;
    echo json_encode($semestreEtudiant,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES| JSON_UNESCAPED_UNICODE);

*/
    $rapportSemestre = extraireRapportSemestre ($fileNotes,$semestre,$promotion);
    $semestre->ajouterRapportSemestre ($rapportSemestre);

    // echo json_encode($semestre,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES| JSON_UNESCAPED_UNICODE);

    echo PHP_EOL;

    foreach ($rapportSemestre as $key=>$value) {
        echo "(".$key.")";
    }
    echo PHP_EOL;
}

function getPeriodeUniversitaire () {
    $annee = date("y");
    $anneeLong = date("Y");
    $mois = date("m");
    if ($mois > 7 ) { // on est au début de l'année
        $annee = $annee.($annee + 1);
        $anneeLong = $anneeLong."-".($anneeLong + 1) ;
    } else {
        $annee = ($annee-1).$annee;
        $anneeLong = ($anneeLong-1)."-".$anneeLong  ;
    }
    return $anneeLong;
}

$nbArguments = count($argv);
echo $nbArguments.PHP_EOL;
if($nbArguments < 2) {
    echo "Usage : il faut au moins un argument semestre : (".LES_SEMESTRES.")".PHP_EOL;
    return ;
} else if ($nbArguments > 1) {
    $anneeLong = getPeriodeUniversitaire();
    $semestre = strtoupper($argv[1]);
    if (strpos(LES_SEMESTRES,$semestre)===false) {
        echo "Semestre non défini : ".$semestre, " (".LES_SEMESTRES.")".PHP_EOL;
        exit -1;
    }
    if ($nbArguments < 3)  createCSVToJson($anneeLong,$semestre);
    else  {
        $groupe = $argv[2];
        createCSVToJson($anneeLong,$semestre,$groupe);
    }

}



echo "=====================================".PHP_EOL;
echo "Fin programme".PHP_EOL;

