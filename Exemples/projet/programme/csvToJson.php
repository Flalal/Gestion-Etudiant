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
		$arrayId=strtolower($array[$k]['nom']."_".$array[$k]['prnom']);
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

function array_combine3($keys, $keys2, $values){
	
    if(count($keys)!=0 && count($keys)>count($values)){
		return false;
	}
    
    $result = array();
	
    foreach ($keys as $i => $k) {
			
		if(!empty($values[$i])){
			$result[$k][] = $values[$i];
			if($result[$k]===$result['Décions']){
				foreach($keys2 as $lm => $fao){
					if(is_string($fao) && $fao!==''){
						$result[$k][$fao][]=$values[$i];
					}else{
						
					}
				}
			}else{
				//break;
			}
			
		}else{
			unset($result[$k]);
		}

    } 
    
     array_walk($result, function(&$v){
		$v = (count($v) == 1) ? array_pop($v): $v;
     });
    return $result;
} 

function count_words($string) {
    $string= str_replace("&#039;", "'", $string);
    $t= array(' ', "\t", '=', '+', '-', '*', '/', '\\', ',', '.', ';', ':', '[', ']', '{', '}', '(', ')', '<', '>', '&', '%', '$', '@', '#', '^', '!', '?', '~'); //separateurs
    $string= str_replace($t, " ", $string);
    $string= trim(preg_replace("/\s +/", " ", $string));
    $num= 0;
    if (my_strlen($string)>0) {
        $word_array= explode(" ", $string);
        $num= count($word_array);
    }
    return $num;
}
function my_strlen($s) {
    return mb_strlen($s, "UTF-8");
}

function ajoutNotes($cours,$notes,$semestre,$commentaire){
	if ((count($cours) === 0)){
		return false;
	}


	$tabMatiere=array();
	
	$out=array();

	foreach($semestre as $var=>$gui){
		if (strpos($gui[0],"M")!==false){$tabMatiere[]=$gui;}
	}

	for ($cpt=0;$cpt<count($cours);$cpt++) {
		for($cpt2=0;$cpt2<count($tabMatiere);$cpt2++){
			if(strpos($cours[$cpt],$tabMatiere[$cpt2][2])!==false){
				if (!empty($notes[$cpt])){
					$out[$notes[0]]["UE"][$tabMatiere[$cpt2][4]]["Commentaire"]="rien";
					$out[$notes[0]]["UE"][$tabMatiere[$cpt2][4]][$tabMatiere[$cpt2][2]]["intitule"]=$tabMatiere[$cpt2][2];
					$out[$notes[0]]["UE"][$tabMatiere[$cpt2][4]][$tabMatiere[$cpt2][2]]["coefficient"]=$tabMatiere[$cpt2][3];
					$out[$notes[0]]["UE"][$tabMatiere[$cpt2][4]][$tabMatiere[$cpt2][2]]["note"][]=$notes[$cpt];
				}
			}

		}

	}
	return $out;








	
	/*foreach ($cours as $i => $k){
		if(count_words($k)>1){
			$decoupage=explode(" ",$k);

			if(empty($notes[$i])){
				unset($out[$decoupage[1]]);
			}else{
				for($var=0;$var<count($tableau);$var++) {
					for ($nb = 0; $nb < count($tabSemestre); $nb++) {
						//print_r($tabSemestre[$nb]." ".$tableau[$var]."\n");

						if (($tableau[$var] === $tabSemestre[$nb])) {

							for ($nbr = 0; $nbr < count($tableauMatiere); $nbr++) {
								for ($axe = 0; $axe < count($tabMatiere); $axe++) {
									$out[$tableau[$var]]['Commentaire'] = "rien";
									if ($tabMatiere[$axe] === $tableauMatiere[$nbr]) {
										//print_r(" ".$tabMatiere[$axe]." ".$tableauMatiere[$nbr]);
										//echo $c[2][1]." ".$c[1][1]." ".$c[0][1]."\n";
										$nom = $decoupage[1];
										$out[$tableau[$var]][$nom]['intitule'] = $nom;
										for ($ta = 0; $ta < count($tableauCoeff); $ta++) {
											$out[$tableau[$var]][$nom]['coefficient'] = $tableauCoeff[$ta];
										}

										for ($t = 0; $t < 3; $t++) {
											for ($ligne = 0; $ligne < count($decoupage[1]); $ligne++) {
												$out[$tableau[$var]][$nom]['note'] = $notes[$i];
											}
										}

									}
								}

							}

						}
					}
				}}
			} else{
			if(empty($notes[$i]) ){
				unset($out[$k]);
			}else{
				$out[$k][] = $notes[$i];
			}
		}
		
    }
	//print_r($out);
    array_walk($out, function(&$v){
		$v = (count($v) == 1) ? array_pop($v): $v;
    });
    return $out;*/
}


function fusionner($tab1,$tableau2){
	$out=array();
	var_dump($tab1);
	for($tmp=0;$tmp<count($tab1);$tmp++){
		if (array_keys($tableau2[$tmp])[0]==$tab1[$tmp]["numro"]){
			$out[]=array_merge($tab1[$tmp], $tableau2[$tmp][array_keys($tableau2[$tmp])[0]]);
		}

	}


	return $out;
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
	while($row4=fgetcsv($fi,"1024",",")){
		for($i=0;$i<1;$i++){
			if(!empty($row4[$i])){
				$json4[]=array_combine3($index,$comment,$row4);
			}
		}
	}

    
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
	//~ foreach($tab as $k=>$v){
		//~ sleep(1);
		//~ print_r($v);
		//~ sleep(1);
	//~ }
    $tableau=arrayUnique($tab);

    fclose($fp);
    fclose($fd);

	
	// 1 : on ouvre le fichier
	$monfichier = fopen($name.'.json', 'w+');
	// 2 : on met en json et on ecris
	$ligne = json_encode($tab);
	//$ligne2 = json_encode($json2);
	fwrite($monfichier,$ligne);
	// 3 : quand on a fini de l'utiliser, on ferme le fichier
	fclose($monfichier);
    
}

csvToJson($argv[1],$argv[2],$argv[3],$argv[4],$argv[5]);
?>
