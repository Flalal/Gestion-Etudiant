<?php

require('Classes/PHPExcel.php');
require_once 'Classes/PHPExcel/IOFactory.php';
 
function convertXLStoCSV($infile)
{
	$objPHPExcel = new PHPExcel();

	$chemin=explode("/",$infile)[0];

	mkdir($chemin."/csv",0700);
 
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
	
	$objWriter->setSheetIndex(0);
	$departement=$objPHPExcel->getActiveSheet()->getCell('B67')->getValue();
	$date=$objPHPExcel->getActiveSheet()->getCell('B68')->getValue();
	$date2=$objPHPExcel->getActiveSheet()->getCell('B69')->getValue();
	$semestre=$objPHPExcel->getActiveSheet()->getCell('B70')->getValue();

	$date3=explode("/",$date);
	$date4=explode("/",$date2);

	foreach($loadedSheetNames as $sheetIndex => $loadedSheetName) {
		$objWriter->setSheetIndex($sheetIndex);
		$objWriter->setDelimiter(',');
		$objWriter->setEnclosure('');
		$objWriter->save($chemin."/csv/".$departement.'_'.$semestre.'_'.$date3[2].$date4[2].'_'.$loadedSheetName.'.csv');
	}	
	/*$objWriter->setSheetIndex(1);   
	$objWriter->setDelimiter(';');  
	$objWriter->save('testExportFile.csv');*/
}


convertXLStoCSV('INFO_S3_20162017/excel/Bilan_INFO_S3_20162017.xls');



?>
