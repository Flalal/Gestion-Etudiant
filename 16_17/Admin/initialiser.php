<?php

require('Classes/PHPExcel.php');
require_once 'Classes/PHPExcel/IOFactory.php';

//tableau pour stocker les données des fichiers CSV
$infoEtu = [];
$info_person = [];
$infoMat = [];
$info_matiere = [];
$ue = [];
$departement = [];
$semestre = $argv[1];


//Si il n'y a pas de paramtre ou plus de 1, on arrête le programme est on déclenche une erreur.
if ($argc != 2) {
    echo "Erreur paramètre : passer le semestre choisi " . PHP_EOL;
    exit(1);
}

//Vérifie si le parametre est un semestre sinon il declenche une erreur.
function checkParam($semestre)
{
    if (strcmp($semestre, "S1") == 0 || strcmp($semestre, "S2") == 0 || strcmp($semestre, "S3") == 0 || strcmp($semestre, "S4") == 0) {
        return;
    }
    echo "Erreur : Le paramètre n'est pas un semestre (S1,S2,S3,S4)" . PHP_EOL;
    exit(1);
}

//Permet de verifier si le chemin vers ../<semestre>/<xls> existe, est si il existe il affiche l'interieur du repertoire.
function openDirectories($repertoire, $destination)
{
    checkParam($repertoire);
    $dir = "../" . $repertoire . "/" . $destination . "/";
    echo $dir . PHP_EOL;

    // Ouvre un dossier bien connu, et liste tous les fichiers
    if (is_dir($dir)) {
        if ($dh = opendir($dir)) {
            browseArrayMatiere($dir);
            closedir($dh);
        }
    } else {
        echo "Erreur : Le dossier n'existe pas";
    }
    return $dir;
}

//Récuperer les fichier csv dans le repertoire Admin/
function recoveryfileCSV($param)
{
    $fileCSV = [];
    $chemin = ".";
    $fileCSV[] = $chemin;
    $dir = opendir($chemin);
    //Tant qu'il y a des fichier dans dir, on continue.
    while ($file = readdir($dir)) {
        //si le fichier contient "csv" quelque part dans la string alors il entre dans la condition
        if(stripos($file, $param) !== false && stripos($file, "csv") !== false) {
            $fileCSV[] = $file;
        }
    }
    closedir($dir);

    if(count($fileCSV) != 3){
        echo "Erreur : Problème fichier CSV".PHP_EOL;
        exit(1);
    }
    return $fileCSV;
}

//Ecriture dans infoEtu: Numéro,Nom,... puis dans info_person: les elements de chaque personne
function readCSVEtudiant()
{
    global $infoEtu, $info_person,$semestre;
    $tab = recoveryfileCSV($semestre);
    $compteur = 0;
    if (($handle = fopen($tab[1], "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
            $num = count($data);
            for ($c = 0; $c < $num; $c++) {
                if ($compteur == 0) {
                    $infoEtu[] = $data[$c];
                } else {
                    $info_person[$compteur][$infoEtu[$c]] = $data[$c];
                }
            }
            $compteur++;
        }
        fclose($handle);
    }
}

//Ecriture dans infoMat: Référence,Nom Module,... puis dans info_matiere: les elements de chaque matiere
function readCSVMatieres()
{
    global $infoMat, $info_matiere, $ue, $departement,$semestre;
    $tab = recoveryfileCSV($semestre);
    $compteur = 0;
    if (($handle = fopen($tab[2], "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
            $num = count($data);
            for ($c = 0; $c < $num; $c++) {
                if ($compteur == 0) {
                    $infoMat[] = $data[$c];
                } else {
                    if ($data[0] == "") {
                        $compteur++;
                        break;
                    } elseif (stripos($data[0], "UE") !== false) {
                        $ue[$data[0]][] = $data[$c];
                    } elseif (stripos($data[0], "M3") !== false) {
                        $info_matiere[$compteur][$infoMat[$c]] = $data[$c];
                    } else {
                        $departement[$data[0]][] = $data[$c];
                    }
                }
            }
            $compteur++;
        }
        fclose($handle);
    }
}

//Création des fichiers excel.
function creationFilesXLS($chemin)
{
    $abreviation = explode("/", $chemin);

    // création des objets de base et initialisation des informations d'entête
    $classeur = new PHPExcel;
    $classeur->getProperties()->setCreator("Bastien Cornu");
    $classeur->setActiveSheetIndex(0);
    $feuille = $classeur->getActiveSheet();

    // ajout des données dans la feuille de calcul
    $feuille->setTitle($abreviation[count($abreviation) - 1]);
    writeInfoPersonFilesXLS($feuille);

    if(strcmp($abreviation[count($abreviation) - 1], "Absences") != 0 && strcmp($abreviation[count($abreviation) - 1], "Bilan") != 0){
        fileMatiere($feuille,$abreviation[count($abreviation) - 1]);
    }
    if(strcmp($abreviation[count($abreviation) - 1], "Bilan") == 0){
        fileBilan($feuille);
    }
    $writer = PHPExcel_IOFactory::createWriter($classeur, 'Excel2007');
    $writer->save($chemin . '_Info2_S3_1617.xlsx');
}

//Fonction qui va boucler en appelant la fonction qui créer les fichiers excel en lui donnant le nom de chaque abréviation.
function browseArrayMatiere($chemin)
{
    global $info_matiere;
    for ($compteur = 1; $compteur < count($info_matiere); $compteur++) {
        creationFilesXLS($chemin . $info_matiere[$compteur]["Abréviation"]);
    }
    creationFilesXLS($chemin . "Bilan");
    creationFilesXLS($chemin . "Absences");
}

//Ecriture des informations dans le fichier Bilan...xlsx
function fileBilan($feuille){
    global $ue, $info_matiere, $info_person, $departement;

    $compteur = 0;
    //Les UE dans le fichier Bilan
    foreach ($ue as $value){
        $feuille->setCellValueByColumnAndRow(6+$compteur , 1 , $value[0]);
        $feuille->setCellValueByColumnAndRow(6+$compteur , 2 , $value[2]);
        $compteur++;
    }

    //Les Matieres dans le fichier Bilan
    for($cpt = 0; $cpt < count($info_matiere); $cpt++){
        $feuille->setCellValueByColumnAndRow(9+$cpt, 1 , $info_matiere[$cpt]["Référence"]." ".$info_matiere[$cpt]["Abréviation"]);
        $feuille->setCellValueByColumnAndRow(9+$cpt, 2 , $info_matiere[$cpt]["Coefficient"]);
    }


    //La Moyenne dans le fichier Bilan
    $feuille->setCellValueByColumnAndRow(4 , 1 , "M S3");
    $somme = 0;
    foreach ($ue as $value) {
        $somme +=$value[2];
    }
    $feuille->setCellValueByColumnAndRow(4, 2, $somme);


    //Le rang dans le fichier Bilan
    /*$feuille->setCellValueByColumnAndRow(5 , 1 , "Rang");
    for ($i = 0; s$i < count($info_person); $i++){
        $feuille->setCellValueByColumnAndRow(5 , ($i+2) , "=RANG(E".($i+2).";\$E\$".(3).":\$E$".(count($info_person)).")");
    }*/


}

//Ecriture des informations dans tous les fichier Matiere.
function fileMatiere($feuille,$abreviation)
{
    global $info_person, $departement, $info_matiere, $infoEtu;
    $ligne = count($info_person)+1+count($departement)+1;

    $compteur = 0;

    echo $abreviation.PHP_EOL;

    for($cpt = 0; $cpt < count($info_matiere); $cpt++){
        if(strcmp($info_matiere[$cpt]["Abréviation"],$abreviation) == 0){
            $compteur = $cpt;
        }
    }

    $feuille->setCellValueByColumnAndRow(4 , 1 , "Moyenne");
    $feuille->setCellValueByColumnAndRow(5 , 1 , "Rang");

    $feuille->setCellValueByColumnAndRow(0 , $ligne , "Code");
    $feuille->setCellValueByColumnAndRow(1 , $ligne , $info_matiere[$compteur]["Référence"]);

    $ligne++;
    $feuille->setCellValueByColumnAndRow(0 , $ligne , "Intitulé");
    $feuille->setCellValueByColumnAndRow(1 , $ligne , $info_matiere[$compteur]["Nom Module"]);

    $ligne++;
    $feuille->setCellValueByColumnAndRow(0 , $ligne , "Coefficient");
    $feuille->setCellValueByColumnAndRow(1 , $ligne , $info_matiere[$compteur]["Coefficient"]);

    $ligne++;
    $feuille->setCellValueByColumnAndRow(0 , $ligne , "Responsable");

    $ligne++;
    $feuille->setCellValueByColumnAndRow(0 , $ligne , "Moyenne");
    $feuille->setCellValueByColumnAndRow(1 , $ligne , "=MOYENNE(E3:E62)");

    $ligne++;
    $feuille->setCellValueByColumnAndRow(0 , $ligne , "Max");
    $feuille->setCellValueByColumnAndRow(1 , $ligne , "=MAX(E3:E62)");

    $ligne++;
    $feuille->setCellValueByColumnAndRow(0 , $ligne , "Min");
    $feuille->setCellValueByColumnAndRow(1 , $ligne , "=MIN(E3:E62)");
}

//Ecriture des Numero, Nom, Prenom et Groupe dans les fichiers excel.
function writeInfoPersonFilesXLS($feuille)
{
    global $infoEtu, $info_person, $departement;
    $colonne = 0;
    //Ecriture des Numero, Nom, Prenom et Groupe
    for ($attributs = 0; $attributs < 4; $attributs++) {
        $feuille->setCellValueByColumnAndRow($colonne++, 1, $infoEtu[$attributs]);
    }
    $ligne = 2;
    //Ecriture des Numero, Nom, Prenom et Groupe pour chaque étudiants.
    for ($compteur = 1; $compteur < count($info_person); $compteur++) {
        $feuille->setCellValueByColumnAndRow(0, $ligne, $info_person[$compteur]["Numéro"]);
        $feuille->setCellValueByColumnAndRow(1, $ligne, $info_person[$compteur]["Nom"]);
        $feuille->setCellValueByColumnAndRow(2, $ligne, $info_person[$compteur]["Prénom"]);
        $feuille->setCellValueByColumnAndRow(3, $ligne, $info_person[$compteur]["Groupe"]);
        $ligne++;
    }
    $compteur = 0;
    //Ecriture du Département, Date, Semestre, ...
    foreach ($departement as $value){
        $feuille->setCellValueByColumnAndRow(0 , (count($info_person)+2)+$compteur  , $value[0]);
        $feuille->setCellValueByColumnAndRow(1 , (count($info_person)+2)+$compteur , $value[1]);
        $compteur++;
    }
}


readCSVEtudiant();
readCSVMatieres();
openDirectories($argv[1], "xls");


