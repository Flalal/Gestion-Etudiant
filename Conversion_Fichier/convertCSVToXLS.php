<?php

/*Ce programme permet de capture tout les fichier csv afin de les convertir en xls.*/
require('Classes/PHPExcel.php');
require_once 'Classes/PHPExcel/IOFactory.php';

$objReader = PHPExcel_IOFactory::createReader('CSV');

// If the files uses a delimiter other than a comma (e.g. a tab), then tell the reader
$objReader->setDelimiter(",");
// If the files uses an encoding other than UTF-8 or ASCII, then tell the reader
$objReader->setInputEncoding('UTF-8');

//on ouvre le repertoire courant est on l'affecte a la variable dir.
$dir = opendir(".");
//Tant qu'il y a des fichier dans dir, on continue.
while($file = readdir($dir)) {
	//si le fichier contient "csv" quelque part dans la string alors il entre dans le if
	if(stripos($file, ".csv") !== false ){
		$objPHPExcel = $objReader->load($file);
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$newName = substr($file, 0,strlen($file)-3)."xls";
		$objWriter->save($newName);
		
	}
}
closedir($dir);




