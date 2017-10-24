<?php
//tableau pour stocker les données des fichiers CSV
$infoEtu = [];
$info_person = [];
$infoMat = [];
$info_matiere = [];
$ue = [];
$departement = [];

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
            while (($file = readdir($dh)) !== false) {
                echo "fichier : $file : type : " . filetype($dir . $file) . "\n";
            }
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
                    if($data[0] == ""){
                        $compteur++;
                        break;
                    } elseif (stripos($data[0],"UE") !== false){
                        $ue[$data[0]][] = $data[$c];
                    } elseif (stripos($data[0],"M3") !== false ) {
                        $info_matiere[$compteur][$infoMat[$c]] = $data[$c];
                    } else{
                        $departement[$data[0]][] = $data[$c];
                    }
                }
            }
            $compteur++;
        }
        fclose($handle);
    }
}

openDirectories($argv[1], "xls");
$csv = recoveryfileCSV();
readCSVEtudiant();
readCSVMatieres();