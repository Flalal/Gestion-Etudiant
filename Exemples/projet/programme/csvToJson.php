<?php

/* Pour lancer le programme: 
 * 
 * php csvtojson.php
 * 		1er fichier: liste des Etudiants
 * 		2eme fichier: details des notes
 * 		3eme fichier: liste des Matieres
 * 		4eme fichier: decisions
 * 		Nom du fichier final 
 * */

//php csvToJson.php INFO2_S3_20162017_Liste_Etudiants.csv INFO2_S3_20162017_Note_detail_S3.csv INFO2_S3_20162017_Liste_Matiere.csv INFO2_S3_20162017_Décision.csv Liste_Final


require('Classes/PHPExcel.php');
require_once 'Classes/PHPExcel/IOFactory.php';

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
    $result['avatar'][]=$values[0].'.png';
    
    //pour eviter d'avoir un tableau comme resulat quand on en a qu'un
    //exemple: numero:[11655] =>numero: 11655
     array_walk($result, function(&$v){
		$v = (count($v) == 1) ? array_pop($v): $v;
     });
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

	//var_dump($commentaire);
	for ($cpt=0;$cpt<count($cours);$cpt++) {
		for($cpt2=0;$cpt2<count($tabMatiere);$cpt2++){
			for($cpt3=0;$cpt3<count($commentaire);$cpt3++){
				if(strpos($cours[$cpt],$tabMatiere[$cpt2][2])!==false){
					if (!empty($notes[$cpt])){
						
						foreach($commentaire[$cpt3] as $keys=>$values){
							//print $keys;
							foreach($values as $k=>$v){
								//print(gettype($keys));
								//print($v);
								$str=strval($keys);
								if(strpos($notes[0],$str)!==false){
									if(strpos($tabMatiere[$cpt2][4],$k)!==false){
										if(!empty($v))
											$out[$notes[0]]["UE"][$tabMatiere[$cpt2][4]]["commentaire"]=$v;
										else{
											$out[$notes[0]]["UE"][$tabMatiere[$cpt2][4]]["commentaire"]="valide";
										}
									}		
								}					
							}
						}
						$out[$notes[0]]["departement"]=$departement;
						//~ $out[$notes[0]]["UE"][$tabMatiere[$cpt2][4]]["commentaire"]="rien";
						$out[$notes[0]]["UE"][$tabMatiere[$cpt2][4]][$tabMatiere[$cpt2][2]]["intitule"]=$tabMatiere[$cpt2][2];
						$out[$notes[0]]["UE"][$tabMatiere[$cpt2][4]][$tabMatiere[$cpt2][2]]["coefficient"]=$tabMatiere[$cpt2][3];
						$out[$notes[0]]["UE"][$tabMatiere[$cpt2][4]][$tabMatiere[$cpt2][2]]["note"][]=$notes[$cpt];
					}
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

function array_combine3($keys, $keys2, $values){
	
    if(count($keys)==0){
		return false;
	}
 
	foreach($keys2 as $cpt2=>$cpt){
		$result[$values[0]][$keys2[$cpt2]]=$values[$cpt2];
	}
	
    array_walk($result, function(&$v){
		$v = (count($v) == 1) ? array_pop($v): $v;
    });
    
    return $result;
} 

function csvToJson($fname,$fname2,$fname3,$fname4,$name) {
	
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
	$comment=array_filter($comment);
	while($row4=fgetcsv($fi,"1024",",")){
		for($i=0;$i<1;$i++){
			if(!empty($row4[$i])){
				array_push($json4,array_combine3($index,$comment,$row4));
			}
		}
	}
    var_dump($json4);
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
	
	//var_dump($json4);
	// 1 : on ouvre le fichier
	$monfichier = fopen($name.'.json', 'w+');
	// 2 : on met en json et on ecris
	$ligne = json_encode($tableau);
	//$ligne2 = json_encode($json2);
	fwrite($monfichier,$ligne);
	// 3 : quand on a fini de l'utiliser, on ferme le fichier
	fclose($monfichier);
    
}

csvToJson($argv[1],$argv[2],$argv[3],$argv[4],$argv[5]);
?>
