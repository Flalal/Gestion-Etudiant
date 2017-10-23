<?php


//Si il n'y a pas de paramtre ou plus de 1, on arrête le programme est on déclenche une erreur.
if($argc != 2){
	echo "Erreur paramètre : passer le semestre choisi ".PHP_EOL;
	exit(1);
}

//Vérifie si le parametre est un semestre sinon il declenche une erreur.
function checkParam($semestre){
	if(strcmp($semestre,"S1") == 0|| strcmp($semestre,"S2") == 0 || strcmp($semestre,"S3") == 0|| strcmp($semestre,"S4") == 0){
		return;
	}
	echo "Erreur : Le paramètre n'est pas un semestre (S1,S2,S3,S4)".PHP_EOL;
}

//Permet de verifier si le chemin vers ../<semestre>/<xls> existe, est si il existe il affiche l'interieur du repertoire.
function openDirectories($repertoire,$destination)
{
    checkParam($repertoire);
    $dir = "../" . $repertoire."/".$destination."/";
    echo $dir.PHP_EOL;

    // Ouvre un dossier bien connu, et liste tous les fichiers
    if (is_dir($dir)) {
        if ($dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {
                echo "fichier : $file : type : " . filetype($dir . $file) . "\n";
            }
            closedir($dh);
        }
    }
    return $dir;
}

//Récuperer les fichier csv dans le repertoire Admin/
function recoveryfileCSV(){
    $fileCSV = [];
    $chemin = ".";
    $fileCSV[] = $chemin;
    $dir = opendir($chemin);
    //Tant qu'il y a des fichier dans dir, on continue.
    while($file = readdir($dir)) {
        //si le fichier contient "csv" quelque part dans la string alors il entre dans le if
        if(stripos($file, ".csv") !== false ){
           echo $file.PHP_EOL;
           $fileCSV[] = $file;

        }
    }
    closedir($dir);
    return $fileCSV;
}

function readCSV(){
    $tab = recoveryfileCSV();
    $info = [];
    $infoperson = [];
    $compteur = 0;
    if (($handle = fopen($tab[1], "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
            $num = count($data);
            for ($c=0; $c < $num; $c++) {
                if($compteur == 0){
                    $info[] = $data[$c];
                } else {
                    $infoperson[$compteur] =$info[$c]."=>".$data[$c];
                }
            }
            $compteur++;
        }
        fclose($handle);
    }
    echo "********** Information **********".PHP_EOL;
    foreach ($info as $value){
        echo $value.PHP_EOL;
    }
    echo "********** Information Personne **********".PHP_EOL;
    foreach ($infoperson as $key=>$value){
        echo $key.")"." ".$value.PHP_EOL;
    }
}

openDirectories($argv[1],"xls");
readCSV();
