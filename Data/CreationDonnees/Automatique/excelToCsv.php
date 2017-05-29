<?php

require('Classes/PHPExcel.php');
require_once 'Classes/PHPExcel/IOFactory.php';
 
function convertXLStoCSV($infile)
{
	$objPHPExcel = new PHPExcel();
 
	try {
		$inputFileType = PHPExcel_IOFactory::identify($infile);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objPHPExcel = $objReader->load($infile);
	}
	catch(Exception $e){
		die('[ERREUR catch]> "'.pathinfo($infile,PATHINFO_BASENAME).'": '.$e->getMessage());
	}
	
	$loadedSheetNames = $objPHPExcel->getSheetNames();
	
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
	
	foreach($loadedSheetNames as $sheetIndex => $loadedSheetName) {
		$objWriter->setSheetIndex($sheetIndex);
		$objWriter->setDelimiter(';');  
		$objWriter->save($loadedSheetName.'.csv');
		
	}	
	/*$objWriter->setSheetIndex(1);   
	$objWriter->setDelimiter(';');  
	$objWriter->save('testExportFile.csv');*/
}
convertXLStoCSV('Bilan S3.xlsx');
 
?>
