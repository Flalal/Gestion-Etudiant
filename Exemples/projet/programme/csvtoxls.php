<?php
/**
 * Created by PhpStorm.
 * User: Frederic
 * Date: 22/05/2017
 * Time: 13:20
 */
require('Classes/PHPExcel.php');

require ('Classes/PHPExcel/IOFactory.php');


/*require('Classes/PHPExcel-1.8.php');
require_once 'Classes/PHPExcel-1.8/IOFactory.php';*/

$inputFileName = 'Liste_Info2_1617.csv';
$inputFileName1 = 'Liste_Matières_Info2_S3_1617.csv';



function convertCSVtoXLS($file,$file2)
{
    //$objPHPExcel = new PHPExcel();


    $matieresCSV = array();
    $etudiant = array();

    if (!($fp = fopen($file, 'r'))) {
        die("** Probleme d'ouverture **");
    }
    if (!($fp2 = fopen($file2, 'r'))) {
        die("** Probleme d'ouverture **");
    }


    $intilerMatier []=explode(";",fgetcsv($fp,"1024")[0]);
    $intilerEtu []= explode(";",fgetcsv($fp2,"1024")[0]);



    while ($row = fgetcsv($fp,"1024")) {
        $matieresCSV[] =  explode(";",$row[0]);
    }
    while ($row = fgetcsv($fp2,"1024")) {
        $etudiant[] = explode(";",$row[0]);
    }


    fclose($fp);
    fclose($fp2);

    creation($matieresCSV,$etudiant,$intilerEtu,$intilerMatier);







}

function creation($matieresCSV,$etudiant ,$intilueretu,$intiluerMatier){
    $matieres=array();
    $UE=array();
    $autre=array();

    foreach ($matieresCSV as $donnes){
        if ($donnes[0]!=null) {
            if ($donnes[0][0] === "M") {
                $matieres[] = $donnes;
            }
            if ($donnes[0][0]==="U"){
                $UE[]=$donnes;
            }
            else{
                $autre[]=$donnes;
            }
        }
    }


    $objPHPExcel = new PHPExcel();

    $sheet = $objPHPExcel->getActiveSheet();

    /// creation de la liste des matière

    for ($cpt=0;$cpt<count($intiluerMatier[0]);$cpt++){

        $sheet->setCellValue(chr(65+$cpt)."1" , $intiluerMatier[0][$cpt]);
        $sheet->getStyle(chr(65+$cpt)."1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->setTitle("Liste Matiere");
    }

    for ($cpt=0;$cpt<count($matieresCSV);$cpt++){
        for ($cpt2=0;$cpt2<count($matieresCSV[$cpt]);$cpt2++) {

            $sheet->setCellValue(chr(65 + $cpt2) . ($cpt + 2),$matieresCSV[$cpt][$cpt2]);
        }
    }

    // creation de la liste des etudiants
   $sheet2 = $objPHPExcel->createSheet(1);
    for ($cpt=0;$cpt<count($intilueretu[0]);$cpt++){

        $sheet2->setCellValue(chr(65+$cpt)."1" , $intilueretu[0][$cpt]);
        $sheet2->getStyle(chr(65+$cpt)."1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet2->setTitle("Liste Etudiants");
    }

    for ($cpt=0;$cpt<count($etudiant);$cpt++){
        for ($cpt2=0;$cpt2<count($etudiant[$cpt]);$cpt2++) {
            $sheet2->setCellValue(chr(65 + $cpt2) . ($cpt + 2),$etudiant[$cpt][$cpt2]);
        }
    }



    // creation décision
    $sheet3 = $objPHPExcel->createSheet(2);
    for ($cpt=0;$cpt<4;$cpt++){

        $sheet3->setCellValue(chr(65+$cpt)."1" , $intilueretu[0][$cpt]);
        $sheet3->getStyle(chr(65+$cpt)."1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet3->setTitle("Décision");
    }
    $sheet3->setCellValue("E1" ,"Décions");
    $sheet3->setCellValue("I1" ,"Commentaire");
    $sheet3->getColumnDimension('I')->setWidth(50);

    $sheet3->getStyle("E1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $sheet3->mergeCells('E1:H1');
    for ($cptue=0;$cptue<count($UE);$cptue++){
        $sheet3->setCellValue("E2" ,$matieresCSV[0][5]);
        $sheet3->setCellValue(chr(70+$cptue)."2" ,$UE[$cptue][0]);


    }
    for ($cpt=0;$cpt<count($etudiant);$cpt++){
        for ($cpt2=0;$cpt2<4;$cpt2++) {

            $sheet3->setCellValue(chr(65 + $cpt2) . ($cpt + 2),$etudiant[$cpt][$cpt2]);
        }
    }




    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('bilan.xls');



}




convertCSVtoXLS($inputFileName1,$inputFileName);



