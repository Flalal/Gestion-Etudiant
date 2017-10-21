<?php

if($argc != 2){
	echo "Erreur paramètre : passer le semestre choisi ".PHP_EOL;
	exit(1);
}

function checkParam($semestre){
	if(strcmp($semestre,"S1") == 0|| strcmp($semestre,"S2") == 0 || strcmp($semestre,"S3") == 0|| strcmp($semestre,"S4") == 0){
		return;
	}
	echo "Erreur : Le paramètre n'est pas un semestre (S1,S2,S3,S4)".PHP_EOL;
}

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

function recoveryfileCSV(){
    $dir = opendir(".");
//Tant qu'il y a des fichier dans dir, on continue.
    while($file = readdir($dir)) {
        //si le fichier contient "csv" quelque part dans la string alors il entre dans le if
        if(stripos($file, ".csv") !== false ){
           echo $file.PHP_EOL;

        }
    }
    closedir($dir);
}

openDirectories($argv[1],"xls");
recoveryfileCSV();