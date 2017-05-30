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
        if (count($row)==1)
            $matieresCSV[] =  explode(";",$row[0]);
        if (count($row)==2)
            $matieresCSV[] =  explode(";",$row[0].$row[1]);
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
    $nbSheet=0;

    foreach ($matieresCSV as $donnes){
        if ($donnes[0]!=null) {
            if ($donnes[0][0] === "M") {
                $matieres[] = $donnes;
            }
            else if  ($donnes[0][0]==="U"){
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
        $sheet->getColumnDimension(chr(65+$cpt))->setAutoSize(true);
    }
    $nbSheet++;

    for ($cpt=0;$cpt<count($matieresCSV);$cpt++){
        for ($cpt2=0;$cpt2<count($matieresCSV[$cpt]);$cpt2++) {

            $sheet->setCellValue(chr(65 + $cpt2) . ($cpt + 2),$matieresCSV[$cpt][$cpt2]);
        }
    }

    // creation de la liste des etudiants
   $sheet2 = $objPHPExcel->createSheet($nbSheet);

    for ($cpt=0;$cpt<count($intilueretu[0]);$cpt++){

        $sheet2->setCellValue(chr(65+$cpt)."1" , $intilueretu[0][$cpt]);
        $sheet2->getStyle(chr(65+$cpt)."1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet2->setTitle("Liste Etudiants");
        $sheet2->getColumnDimension(chr(65+$cpt))->setAutoSize(true);
    }

    for ($cpt=0;$cpt<count($etudiant);$cpt++){
        for ($cpt2=0;$cpt2<count($etudiant[$cpt]);$cpt2++) {
            $sheet2->setCellValue(chr(65 + $cpt2) . ($cpt + 2),$etudiant[$cpt][$cpt2]);
        }
    }
    $nbSheet++;



    // creation décision
    $sheet3 = $objPHPExcel->createSheet($nbSheet);
    for ($cpt=0;$cpt<4;$cpt++){

        $sheet3->setCellValue(chr(65+$cpt)."1" , $intilueretu[0][$cpt]);
        $sheet3->getStyle(chr(65+$cpt)."1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet3->setTitle("Décision");
        $sheet3->getColumnDimension(chr(65+$cpt))->setAutoSize(true);
    }
    $sheet3->setCellValue("E1" ,"Décions");
    $sheet3->setCellValue("I1" ,"Commentaire");
    $sheet3->getColumnDimension("I")->setAutoSize(true);

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


    for ($cptautre=0;$cptautre<count($autre);$cptautre++){
        for ($cpt2=0;$cpt2<count($autre[$cptautre]);$cpt2++) {

            $sheet3->setCellValue(chr(65 + $cpt2) . ($cptautre + count($etudiant) + 5),$autre[$cptautre][$cpt2]);

        }
    }
    $nbSheet++;
/// creation absentece
    $sheet4 = $objPHPExcel->createSheet($nbSheet);

    for ($cpt=0;$cpt<4;$cpt++){

        $sheet4->setCellValue(chr(65+$cpt)."1" , $intilueretu[0][$cpt]);
        $sheet4->getStyle(chr(65+$cpt)."1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet4->setTitle("Absences");
        $sheet4->getColumnDimension(chr(65+$cpt))->setAutoSize(true);
    }
    $sheet4->setCellValue("E1" ,"ABI");
    $sheet4->getStyle("E1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    for ($cptue=0;$cptue<count($UE);$cptue++){
        $sheet4->setCellValue(chr(70+($cptue*2))."1" ,$UE[$cptue][0]);
        $sheet4->getStyle(chr(70+($cptue*2))."1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet4->getStyle(chr(70+($cptue*2))."2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet4->getStyle(chr(70+($cptue*2)+1)."2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet4->mergeCells(chr(70+($cptue*2))."1:".chr(70+($cptue*2)+1)."1");
        $sheet4->setCellValue(chr(70+($cptue*2))."2" ,'J');
        $sheet4->setCellValue("D2" ,'Seuil');
        $sheet4->setCellValue("E2" ,0.11);
        $sheet4->setCellValue(chr(70+($cptue*2)+1)."2" ,'NJ');


    }
    for ($cpt=0;$cpt<count($etudiant);$cpt++) {
        for ($cpt2 = 0; $cpt2 < 4; $cpt2++) {

            $sheet4->setCellValue(chr(65 + $cpt2) . ($cpt + 2), $etudiant[$cpt][$cpt2]);
        }
    }
    for ($cptautre=0;$cptautre<count($autre);$cptautre++){
        for ($cpt2=0;$cpt2<count($autre[$cptautre]);$cpt2++) {

            $sheet4->setCellValue(chr(65 + $cpt2) . ($cptautre + count($etudiant) + 5),$autre[$cptautre][$cpt2]);

        }
    }
    $nbSheet++;


    ///Note detailler


    $sheet = $objPHPExcel->createSheet($nbSheet);

    for ($cpt=0;$cpt<4;$cpt++){
        $sheet->setCellValue(chr(65+$cpt)."1" , $intilueretu[0][$cpt]);
        $sheet->getStyle(chr(65+$cpt)."1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->setTitle("Note detail".$matieres[0][5]);
        $sheet->getColumnDimension(chr(65+$cpt))->setAutoSize(true);
    }
    $sheet->setCellValue("E1" , "M ".$matieres[0][5]);
    $sheet->setCellValue("F1" , "Rang");

    for ($cptue=0;$cptue<count($UE);$cptue++){
        $tmp=explode(" ",$UE[$cptue][1]);
        $intituler="";
        foreach ($tmp as $mot){
            $intituler=$intituler.$mot[0];
        }

        $sheet->setCellValue(chr(71+$cptue)."1" , $intituler." ".$UE[$cptue][0]);
        $sheet->getColumnDimension(chr(71+$cptue))->setAutoSize(true);
        $sheet->setCellValue(chr(71+$cptue)."2" , $UE[$cptue][2]);
    }
    for ($cptMatiere=0;$cptMatiere<count($matieres);$cptMatiere++){


        $sheet->setCellValue(chr(65+6+count($UE)+$cptMatiere)."1" , $matieres[$cptMatiere][0]." ".$matieres[$cptMatiere][2]);
        $sheet->getColumnDimension(chr(65+6+$cptMatiere+count($UE)))->setAutoSize(true);
        $sheet->setCellValue(chr(65+6+count($UE)+$cptMatiere)."2" , $matieres[$cptMatiere][3]);


    }

    for ($cpt=0;$cpt<count($etudiant);$cpt++) {
        for ($cpt2 = 0; $cpt2 < 4; $cpt2++) {

            $sheet->setCellValue(chr(65 + $cpt2) . ($cpt + 2), $etudiant[$cpt][$cpt2]);
        }
    }
    for ($cptautre=0;$cptautre<count($autre);$cptautre++){
        for ($cpt2=0;$cpt2<count($autre[$cptautre]);$cpt2++) {

            $sheet->setCellValue(chr(65 + $cpt2) . ($cptautre + count($etudiant) + 5),$autre[$cptautre][$cpt2]);

        }
    }
    $nbSheet++;






    /// page des matiere et fichier xls par matiere
    for ($cptMatiere=0;$cptMatiere<count($matieres);$cptMatiere++){
        $excelMatiere=new PHPExcel();
        $sheet=$objPHPExcel->createSheet($nbSheet+$cptMatiere);
        $sheetMatiere=$excelMatiere->getActiveSheet();

        for ($cpt=0;$cpt<4;$cpt++){

            $sheet->setCellValue(chr(65+$cpt)."1" , $intilueretu[0][$cpt]);
            $sheet->getColumnDimension(chr(65+$cpt))->setAutoSize(true);
            $sheetMatiere->getColumnDimension(chr(65+$cpt))->setAutoSize(true);
            $sheet->getStyle(chr(65+$cpt)."1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $sheet->setTitle($matieres[$cptMatiere][2]);
            $sheetMatiere->setCellValue(chr(65+$cpt)."1" , $intilueretu[0][$cpt]);
            $sheetMatiere->getStyle(chr(65+$cpt)."1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $sheetMatiere->setTitle($matieres[$cptMatiere][2]);
        }
        $sheet->setCellValue("E1" ,"Moyenne");
        $sheet->setCellValue("F1" ,"Rang");
        $sheetMatiere->setCellValue("E1" ,"Moyenne");
        $sheetMatiere->setCellValue("F1" ,"Rang");
        for ($cpt=0;$cpt<count($etudiant);$cpt++){
            for ($cpt2=0;$cpt2<4;$cpt2++) {

                $sheet->setCellValue(chr(65 + $cpt2) . ($cpt + 2),$etudiant[$cpt][$cpt2]);
                $sheetMatiere->setCellValue(chr(65 + $cpt2) . ($cpt + 2),$etudiant[$cpt][$cpt2]);
            }
        }


        for ($cptautre=0;$cptautre<count($autre);$cptautre++){
            for ($cpt2=0;$cpt2<count($autre[$cptautre]);$cpt2++) {

                $sheet->setCellValue(chr(65 + $cpt2) . ($cptautre + count($etudiant) + 4),$autre[$cptautre][$cpt2]);
                $sheetMatiere->setCellValue(chr(65 + $cpt2) . ($cptautre + count($etudiant)+4),$autre[$cptautre][$cpt2]);

            }
        }
        $sheet->setCellValue("A". ( count($etudiant)+4+count($autre)),"Resposable");
        $sheet->setCellValue("B". ( count($etudiant)+4+count($autre)),$matieres[$cptMatiere][6]);
        $sheetMatiere->setCellValue("A". ( count($etudiant)+4+count($autre)),"Responsable");
        $sheetMatiere->setCellValue("B". ( count($etudiant)+4+count($autre)),$matieres[$cptMatiere][6]);


        $objWriterMatiere = PHPExcel_IOFactory::createWriter($excelMatiere, 'Excel2007');
        $objWriterMatiere->save($matieres[$cptMatiere][2].'.xls');

    }




    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('bilan.xls');



}




convertCSVtoXLS($inputFileName1,$inputFileName);



