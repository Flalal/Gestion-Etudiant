<?php

require('Classes/PHPExcel.php');
require_once 'Classes/PHPExcel/IOFactory.php';


date_default_timezone_set('Europe/Brussels') ;
$DEPARTEMENT="INFO";

//Permet de savoir si il y a des doublons (même nom, prenom, numero, ...) et detruit (unset)
//array_unique ne se compare qu'avec la chaîne
//Mais on veut comparer avec divers types de variables
function arrayUnique($array){

	foreach ($array as $k => $v) {
		
	   //print_r($array[$k]['Nom']."_".$array[$k]['Prénom']."\n");
		$arrayId=strtolower($array[$k]['nom']."_".$array[$k]['prenom']);
		$array[$arrayId]=$array[$k]; 
        foreach ($array as $k2 => $v2) {
            if (($k != $k2)) {
				unset($array[$k]);
            }
        }       
    }  
    return $array;
}

//Crée un tableau à partir de deux autres tableaux
//Crée un tableau, dont les clés sont les valeurs de keys, et les valeurs sont les valeurs de values
function array_combine2($keys, $values){
    $result = array();
    foreach ($keys as $i => $k) {
		$result[$k][] = $values[$i];
    }
	
	//pour les images
	$dir = opendir("img");
	while($element = readdir($dir)) {
		if($element != '.' && $element != '..') {
			$photo[] = $element;
		}
	}
	for($i=0;$i<count($photo);$i++){	
		$extension = explode('.', $photo[$i]);
		if($values[0]===$extension[0]){
				$result['avatar'][]=$values[0].'.'.$extension[1];
		}else{
			$num[]=$values[0];
		}
	}	
    $tabPasPhoto=array_unique($num);
    
    if(!empty($tabPasPhoto)){
		for($numero=0;$numero<count($tabPasPhoto);$numero++){
			echo("L'étudiant(e) n°".$tabPasPhoto[$numero]." n'a pas de photo\n");
		}
	}
    //pour eviter d'avoir un tableau comme resulat quand on en a qu'un
    //exemple: numero:[11655] =>numero: 11655
     array_walk($result, function(&$v){
		$v = (count($v) == 1) ? array_pop($v): $v;
     });
    return $result;
}  

function array_combine3($keys, $keys2, $values){
	if(count($keys)==0){
		return false;
	}
 
	foreach($keys2 as $cpt2=>$cpt){
		//print($values[$cpt2]);
		$result[$values[0]][$keys2[$cpt2]]=$values[$cpt2];
	}
	
    array_walk($result, function(&$v){
		$v = (count($v) == 1) ? array_pop($v): $v;
    });
    //var_dump($result);
    return $result;
} 



function ajoutNotes($cours,$notes,$semestre,$commentaire){
		if ((count($cours) === 0)){
		return false;
	}
	$tabMatiere=array();
	$departement="";
	$out=array();

	foreach($semestre as $var=>$gui){
		if (strpos($gui[0],"M")!==false){$tabMatiere[]=$gui;}
		if (strpos($gui[0],"Dé")!==false){$departement=$gui[1];}
	}


	for ($cpt=0;$cpt<count($cours);$cpt++) {
		for($cpt2=0;$cpt2<count($tabMatiere);$cpt2++){
			if(strpos($cours[$cpt],$tabMatiere[$cpt2][2])!==false){
				if (!empty($notes[$cpt])){
						$out[$notes[0]]["UE"][$tabMatiere[$cpt2][4]]["commentaire"]="";
						//pour ajout commentaire 
						
						foreach($commentaire[$cpt2] as $keys=>$values){
							foreach($values as $k=>$v){
								$str=strval($keys);
								
								if(strpos($notes[0],$str)!==false){
									if(!empty($k)){
										if(strpos($tabMatiere[$cpt2][4],$k)!==false){
											if(!empty($v))
												array_push($out[$notes[0]]["UE"][$tabMatiere[$cpt2][4]]["commentaire"],iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $v));
											else{
												$out[$notes[0]]["UE"][$tabMatiere[$cpt2][4]]["commentaire"]="valide";
											}
										}
									}		
								}			
							}
						}
						
						//fin ajout commentaire
						
						$out[$notes[0]]["departement"]=$departement;
						//~ $out[$notes[0]]["UE"][$tabMatiere[$cpt2][4]]["commentaire"]="rien";
						$out[$notes[0]]["UE"][$tabMatiere[$cpt2][4]][$tabMatiere[$cpt2][2]]["intitule"]=$tabMatiere[$cpt2][2];
						$out[$notes[0]]["UE"][$tabMatiere[$cpt2][4]][$tabMatiere[$cpt2][2]]["coefficient"]=$tabMatiere[$cpt2][3];
						$out[$notes[0]]["UE"][$tabMatiere[$cpt2][4]][$tabMatiere[$cpt2][2]]["note"][]=$notes[$cpt];
					}
				}

		}

	}
	//var_dump($out);
	return $out;
}

function fusionner($tab1,$tableau2){
	$out=array();

	for($tmp=0;$tmp<count($tab1);$tmp++){
		if (array_keys($tableau2[$tmp])[0]==$tab1[$tmp]["numero"]){
			$out[]=array_merge($tab1[$tmp], $tableau2[$tmp][array_keys($tableau2[$tmp])[0]]);
		}

	}

	return $out;
}

function convertXLStoCSV($infile)
{
	$objPHPExcel = new PHPExcel();
 
	$depart=explode("/",$infile)[0];
	$annee=explode("/",$infile)[1];
	$dossier=explode("/",$infile)[2];
	
	if(!file_exists($depart."/".$annee."/".$dossier."/csv")){
		mkdir($depart."/".$annee."/".$dossier."/csv",0700);
	}	else{
		error_log("le dossier $depart/$annee/$dossier/csv existe déjà ");
	}
	
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
	
	$column = 'A'; //la colonne A
	$lastRow = $objPHPExcel->getActiveSheet()->getHighestRow();
	for ($row = 1; $row <= $lastRow; $row++){ 
		// Pour chaque ligne jusqu'a la dernière on récupère la cellule
		$cell = $objPHPExcel->getActiveSheet()->getCell($column.$row);
		if($cell->getValue()=='Département'){
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

	$date3=explode("/",$date);
	$date4=explode("/",$date2);

	$fichier=array();
	
	foreach($loadedSheetNames as $sheetIndex => $loadedSheetName){
		$objWriter->setSheetIndex($sheetIndex);
		$objWriter->setDelimiter(',');
		$objWriter->setEnclosure('');
		$objWriter->save($depart."/".$annee."/".$dossier."/csv/".$departement.'_'.$semestre.'_'.$date3[2].$date4[2].'_'.$loadedSheetName.'.csv');
		if($loadedSheetName=="Liste_Matiere" || $loadedSheetName=="Liste_Etudiants"|| $loadedSheetName=="Décision"||strpos($loadedSheetName,"Note_detail_S")!== false){
			$fichier[]=$depart."/".$annee."/".$dossier."/csv/".$departement.'_'.$semestre.'_'.$date3[2].$date4[2].'_'.$loadedSheetName.'.csv';
		}
	}
	
	csvToJson($fichier[1],$fichier[3],$fichier[0],$fichier[2]);
	/*$objWriter->setSheetIndex(1);   
	$objWriter->setDelimiter(';');  
	$objWriter->save('testExportFile.csv');*/
}

function csvToJson($fname,$fname2,$fname3,$fname4) {
	
    if(!($fp = fopen($fname, 'r'))) { // ouverture de la liste etudiants
        die("*** [ERREUR]> Probleme d'ouverture fichier $fname ***\n");
    }
    if(!($fd = fopen($fname2, 'r'))) { // ouverture Détails des notes
        die("*** [ERREUR]> Probleme d'ouverture fichier $fname2 ***\n");
    }
    if(!($fa = fopen($fname3, 'r'))) { // ouveture liste matiere
        die("*** [ERREUR]> Probleme d'ouverture fichier $fname3 ***\n");
    }
    if(!($fi = fopen($fname4, 'r'))) { // ouverture de la décision
        die("*** [ERREUR]> Probleme d'ouverture fichier $fname4 ***\n");
    }
   
    $chemin=explode('/',$fname);
    if(!file_exists($chemin[0]."/".$chemin[1]."/".$chemin[2]."/json")){
		mkdir($chemin[0]."/".$chemin[1]."/".$chemin[2]."/json",0700);
	}
	else{
		error_log("le dossier $chemin[0]/$chemin[1]/$chemin[2]/json existe déjà ");
	}
	
    $nameFile=explode('_',$chemin[4]);
	
    $key = fgetcsv($fp,"1024",",");
    $cours = fgetcsv($fd,"1024",",");
    $ranger = fgetcsv($fa,"1024",",");
    $commentaire = fgetcsv($fi,"1024",",");
	$comment = fgetcsv($fi,"1024",",");
	$cle=array();
	foreach($key as $k=>$v){
		$c=strtolower($v);
		setlocale(LC_CTYPE, 'fr_FR.UTF-8');
		array_push($cle ,iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $c));
	} 
    $json = array();// recuperer les donnée dans liste étudiants
    while ($row= fgetcsv($fp,"1024",",")) {
		for($i=0;$i<1;$i++){
			if(!empty($row[$i])){
				$json[]= array_combine2($cle, $row);
			}
		}
    }
    
    $json3=array();///recuperer les donnée dans Matiere
    while($row3=fgetcsv($fa,"1024",",")){
		$json3[]=$row3;
	}
	
	$index=array();
	foreach($commentaire as $k=>$v){
		$c=strtolower($v);
		setlocale(LC_CTYPE, 'fr_FR.UTF-8');
		array_push($index,iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $c));	
	}	
	
	$json4=array();// décision
	
	
	while($row4=fgetcsv($fi,"1024",",")){
		for($i=0;$i<1;$i++){
			if(!empty($row4[$i])){
				$json4[]=array_combine3($index,$comment,$row4);
				//print_r($row4);
			}
		}
	}
    //var_dump($json4);
    $json2=array();
    while($row2=fgetcsv($fd,"1024",",")){
		for($j=0;$j<1;$j++){
			if(!empty($row2[$j])){
				$rowLigne=(array_filter($row2));
				$json2[]=ajoutNotes($cours,$rowLigne,$json3,$json4);
			}
		}
	}

	$tab=fusionner($json,$json2);    

    $tableau=arrayUnique($tab);

    fclose($fp);
    fclose($fd);

	// 1 : on ouvre le fichier
	$monfichier = fopen($chemin[0]."/".$chemin[1]."/".$chemin[2]."/json/".$nameFile[0].'_'.$nameFile[1].'_'.$chemin[1].'.json', 'w+');
	// 2 : on met en json et on ecris dossier json
	$ligne = json_encode($tableau);
	fwrite($monfichier,$ligne);	
	// 3 : quand on a fini de l'utiliser, on ferme le fichier
	fclose($monfichier);
    
}

function validateDate($date, $format = 'Y-m-d H:i:s')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}

function validationFichier($fichierMatiere){
    if(file_exists($fichierMatiere)==false){
        error_log("Erreur le fichier ' ".$fichierMatiere."' n'exite pas dans le repertoire, le nom du fichier ne correspond pas ");
        exit(1);
    }

    return true;
}

function chercherFichier($semestre,$option=null,$option2=null){
    global $DEPARTEMENT;
    if ($option===null && $option2===null){
        $fichier_Bilan="$DEPARTEMENT/".(date("Y")-1)."-".date('Y')."/".$DEPARTEMENT."_".$semestre."_".(date("Y")-1).date('Y')."/excel/"."Bilan_".$DEPARTEMENT."_".$semestre."_".(date("Y")-1).date('Y').".xls";
        if (validationFichier($fichier_Bilan)===true) {
            convertXLStoCSV($fichier_Bilan);
        }    
    }
    
    elseif ($option!==null && $option2===null){
        if (validateDate($option,'Y')===false){
            $fichier_Bilan="$DEPARTEMENT/".(date("Y")-1)."_".date('Y')."/".$DEPARTEMENT."_".$semestre."_".(date("y")-1).date('y')."/xls/"."Bilan_".$semestre."_".$option."_".(date("Y")-1).date('Y').".xls";
            
            if (validationFichier($fichier_Bilan)===true) {
                convertXLStoCSV($fichier_Bilan);
            }

        }
        else{
            $d = DateTime::createFromFormat('Y', $option);
            $date=$d->format("Y");
            $fichier_Bilan="$DEPARTEMENT/".($date-1).$date."/".$DEPARTEMENT."_".$semestre."_".($date-1).$date."/xls/"."Bilan_".$semestre."_".($date-1).$date.".xls";
            if (validationFichier($fichier_Bilan)===true) {
                convertXLStoCSV($fichier_Bilan);
            }
        }

    }
    
    elseif ($option!==null && $option2!==null){
        if (validateDate($option,'Y')==false && validateDate($option2,'Y')==true){
            $d = DateTime::createFromFormat('Y', $option2);
            $date=$d->format("Y");
			$fichier_Bilan="$DEPARTEMENT/".($date-1).$date."/".$DEPARTEMENT."_".$semestre."_".($date-1).$date."/excel/"."Bilan_".$semestre."_".$option."_".($date-1).$date.".xls";
            if (validationFichier($fichier_Bilan)===true) {
                convertXLStoCSV($fichier_Bilan);
            }


        }else{
            error_log("erreur sur le nombre arguments Semestre groupe et Année ");
        }

    }
    
}

if( count($argv)<2){
    error_log("manque l'argument du semestre");
    exit(1);
}
$semestre=$argv[1];//Semestre
$option=null;
$date=null;
if( count($argv)>=3){
    $option=$argv[2];// groupe si il y a des lite spécifique date passer en paramettre si elle n'existe c'est la date courante
}
if( count($argv)>=4){
    $date=$argv[3];// groupe si il y a des lite spécifique date passer en paramettre si elle n'existe c'est la date courante}//convertCSVtoXLS($inputFileName1,$inputFileName);
}

chercherFichier($semestre,$option,$date);
?>
