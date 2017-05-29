<?php

require('Classes/PHPExcel.php');
require_once 'Classes/PHPExcel/IOFactory.php';

function csvToJson($fname) {

    if (!($fp = fopen($fname, 'r'))) {
        die("** Probleme d'ouverture **");
    }
    
    $key = fgetcsv($fp,"1024",",");
    
    $json = array();
    while ($row = fgetcsv($fp,"1024",",")) {
        $json[] = array_combine($key, $row);
    }
    
    fclose($fp);
    
    /*		ecrire dans fichier
     * 
	// 1 : on ouvre le fichier
	$monfichier = fopen('liste.json', 'r+');

	// 2 : on met en json et on ecris
	$ligne = json_encode($json);
	fputs($ligne);
	* 
	// 3 : quand on a fini de l'utiliser, on ferme le fichier
	fclose($monfichier);
     * */
    
    return json_encode($json);
}

$file="Liste_Info2_1617.csv";
echo(csvToJson($file));
?>
