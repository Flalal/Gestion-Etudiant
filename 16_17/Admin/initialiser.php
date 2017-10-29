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
function recoveryfileCSV()
{
    $fileCSV = [];
    $chemin = ".";
    $fileCSV[] = $chemin;
    $dir = opendir($chemin);
    //Tant qu'il y a des fichier dans dir, on continue.
    while ($file = readdir($dir)) {
        //si le fichier contient "csv" quelque part dans la string alors il entre dans la condition
        if (stripos($file, "csv") !== false) {
            $fileCSV[] = $file;

        }
    }
    closedir($dir);
    return $fileCSV;
}

//Ecriture dans infoEtu: Numéro,Nom,... puis dans info_person: les elements de chaque personne
function readCSVEtudiant()
{
    global $infoEtu, $info_person;
    $tab = recoveryfileCSV();
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
    global $infoMat, $info_matiere, $ue, $departement;
    $tab = recoveryfileCSV();
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
    //A faire !

    if(strcmp($abreviation[count($abreviation) - 1], "Bilan") == 0){
        echo "TEST";
        fileBilan($feuille);
    }
    $writer = PHPExcel_IOFactory::createWriter($classeur, 'Excel2007');
    $writer->save($chemin . '_Info2_S3_1617.xlsx');
}

function browseArrayMatiere($chemin)
{
    global $info_matiere;
    for ($compteur = 1; $compteur < count($info_matiere); $compteur++) {
        creationFilesXLS($chemin . $info_matiere[$compteur]["Abréviation"]);
    }
    creationFilesXLS($chemin . "Bilan");
    creationFilesXLS($chemin . "Absences");
}

//A faire !
function fileBilan($feuille){
    echo "Passage dans fileBilan";
    global $ue;
    $compteur = 0;
    foreach ($ue as $value){
        $feuille->setCellValueByColumnAndRow(6+$compteur , 1 , $value[0]);
        $feuille->setCellValueByColumnAndRow(6+$compteur , 2 , $value[2]);
        $compteur++;
    }
}

function writeInfoPersonFilesXLS($feuille)
{
    global $infoEtu, $info_person;
    $colonne = 0;
    for ($attributs = 0; $attributs < 4; $attributs++) {
        $feuille->setCellValueByColumnAndRow($colonne++, 1, $infoEtu[$attributs]);
    }
    $ligne = 2;
    for ($compteur = 1; $compteur < count($info_person); $compteur++) {
        $feuille->setCellValueByColumnAndRow(0, $ligne, $info_person[$compteur]["Numéro"]);
        $feuille->setCellValueByColumnAndRow(1, $ligne, $info_person[$compteur]["Nom"]);
        $feuille->setCellValueByColumnAndRow(2, $ligne, $info_person[$compteur]["Prénom"]);
        $feuille->setCellValueByColumnAndRow(3, $ligne, $info_person[$compteur]["Groupe"]);
        $ligne++;
    }
}


readCSVEtudiant();
readCSVMatieres();
openDirectories($argv[1], "xls");


