<?php

require('Classes/PHPExcel.php');
require_once 'Classes/PHPExcel/IOFactory.php';

function convertXLStoCSV($infile)
{
	$objPHPExcel = new PHPExcel();
 
	$chemin=explode("/",$infile)[0];
	//mkdir($chemin."/csv",0700);
	
	try {
		$inputFileType = PHPExcel_IOFactory::identify($infile);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objPHPExcel = $objReader->load($infile);
	}
	catch(Exception $e){
		die('[ERROR catch]> "'.pathinfo($infile,PATHINFO_BASENAME).'": '.$e->getMessage());
	}
	
	$loadedSheetNames = $objPHPExcel->getSheetNames();
	
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
	
	/*$date=date("Y");
	$departement="*";*/
	
	//~ $objWriter->setSheetIndex(0);
	//~ $departement=$objPHPExcel->getActiveSheet()->getCell('B67')->getValue();
	//~ $date=$objPHPExcel->getActiveSheet()->getCell('B68')->getValue();
	//~ $date2=$objPHPExcel->getActiveSheet()->getCell('B69')->getValue();
	//~ $semestre=$objPHPExcel->getActiveSheet()->getCell('B70')->getValue();

	$column = 'A'; //la colonne A
	$lastRow = $objPHPExcel->getActiveSheet()->getHighestRow();
	for ($row = 1; $row <= $lastRow; $row++){ 
		// Pour chaque ligne jusqu'a la dernière on récupère la cellule
		$cell = $objPHPExcel->getActiveSheet()->getCell($column.$row);
		if($cell->getValue()=='Année'){
		   $departement=$objPHPExcel->getActiveSheet()->getCell('B'.$row)->getValue();
		}
		if($cell->getValue()=='Date début'){
			$date=$objPHPExcel->getActiveSheet()->getCell('B'.$row)->getValue();
		}
		if($cell->getValue()=='Date de fin'){
			$date2=$objPHPExcel->getActiveSheet()->getCell('B'.$row)->getValue();
		}
		if($cell->getValue()=='Semestre'){
			$semestre=$objPHPExcel->getActiveSheet()->getCell('B'.$row)->getValue();
		}
	}
	echo $departement." ".$date." ".$semestre;
	$date3=explode("/",$date);
	$date4=explode("/",$date2);

	foreach($loadedSheetNames as $sheetIndex => $loadedSheetName) {
		$objWriter->setSheetIndex($sheetIndex);
		$objWriter->setDelimiter(',');
		$objWriter->setEnclosure('');
		$objWriter->save($chemin."/S3/".$departement.'_'.$semestre.'_'.$date3[2].$date4[2].'_'.$loadedSheetName.'.csv');		
	}
	/*$objWriter->setSheetIndex(1);   
	$objWriter->setDelimiter(';');  
	$objWriter->save('testExportFile.csv');*/
}
convertXLStoCSV('20162017/S3/Bilan_INFO_S3_20162017.xls');
 
?>
