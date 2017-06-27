<?php

require('Classes/PHPExcel.php');
require_once 'Classes/PHPExcel/IOFactory.php';

define("CSV_DELIMITEUR",",");
date_default_timezone_set('Europe/Brussels') ;
$DEPARTEMENT="INFO";

$tabPromo=array();
$tabMin=array();
$tabMax=array();
$nbEtudiants=0;


//Permet de savoir si il y a des doublons (même nom, prenom, numero, ...) et detruit (unset)
//array_unique ne se compare qu'avec la chaîne
//Mais on veut comparer avec divers types de variables
function arrayUnique($array){

	foreach ($array as $k => $v) {
		$arrayId=strtolower($array[$k]['numero']);
		$array[$arrayId]=$array[$k];
		foreach ($array as $k2 => $v2) {
			if (($k != $k2)) {
				unset($array[$k]);
			}
		}
	}
	$out['liste']=$array;
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

//Pour la liste des etudiants 
function ajoutNotes($cours,$notes,$semestre,$commentaire){
	if ((count($cours) === 0)){
		return false;
	}
	$tabMatiere=array();
	global $tabPromo;
	global $tabMax;
	global $tabMin;
	$departement="";
	$out=array();

	//pour avoir les matieres
	//et le department pour l'etudiant
	foreach($semestre as $var=>$gui){
		if (strpos($gui[0],"M")!==false){$tabMatiere[]=$gui;}
		if (strpos($gui[0],"O")!==false){$tabMatiere[]=$gui;}
		if (strpos($gui[0],"Se")!==false){$tabSemestre[]=$gui;}
		if (strpos($gui[0],"Dé")!==false){$departement=$gui[1];}
		if (strpos($gui[0],"UE")!==false){$UE[]=$gui;}
	}
// parcours du semestre
	for ($cpt=0;$cpt<count($cours);$cpt++) {
		if (!empty($notes[$cpt])){
			if(strpos($cours[$cpt],$tabSemestre[0][1])!==false) {

				if (in_array("moyenne", array_keys($tabPromo[$notes[0]]["semestre"][$tabSemestre[0][1]])) === false) {
					$tabPromo["semestre"][$tabSemestre[0][1]]["moyenne"] = floatval(number_format($notes[$cpt], 2));
					$tabMin["semestre"][$tabSemestre[0][1]]["moyenne"]= floatval(number_format($notes[$cpt], 2));
					var_dump("SAlut je suis la valeurs min".floatval(number_format($notes[$cpt], 2)));
					$tabMax["semestre"][$tabSemestre[0][1]]["moyenne"] = floatval(number_format($notes[$cpt], 2));
				} else {
					$tabPromo["semestre"][$tabSemestre[0][1]]["moyenne"] += floatval(number_format($notes[$cpt], 4));

					if($tabMin["semestre"][$tabSemestre[0][1]]["moyenne"]>floatval(number_format($notes[$cpt], 2))){
						$tabMin["semestre"][$tabSemestre[0][1]]["moyenne"] = floatval(number_format($notes[$cpt], 2));
					}
					var_dump("SAlut je suis la valeurs min".floatval(number_format($notes[$cpt], 2)));
					if($tabMax["semestre"][$tabSemestre[0][1]]["moyenne"] < floatval(number_format($notes[$cpt], 2))){
						$tabMax["semestre"][$tabSemestre[0][1]]["moyenne"] = floatval(number_format($notes[$cpt], 2));
					}
				}

				$out[$notes[0]]["semestre"][$tabSemestre[0][1]]["classement"]=intval($notes[5]);
				$out[$notes[0]]["semestre"][$tabSemestre[0][1]]["moyenne"]=floatval(number_format($notes[$cpt]));
				$out[$notes[0]]["semestre"][$tabSemestre[0][1]]["annee"]=(date('Y')-1)."-".date('Y');
				$out[$notes[0]]["semestre"][$tabSemestre[0][1]]["moyennePromo"]="??";
			}
		}

	}

	for ($cpt=0;$cpt<count($cours);$cpt++) {
		for($cpt2=0;$cpt2<count($UE);$cpt2++){
			if(strpos($cours[$cpt],$UE[$cpt2][0])!==false){
				if (!empty($notes[$cpt])) {
					if (in_array("moyenne", array_keys($tabPromo[$notes[0]]["semestre"][$tabSemestre[0][1]]["UE"][$UE[$cpt2][0]])) === false) {
						$tabPromo["semestre"][$tabSemestre[0][1]]["UE"][$UE[$cpt2][0]]["moyenne"] = floatval(number_format($notes[$cpt], 2));
						$tabMin["semestre"][$tabSemestre[0][1]]["UE"][$UE[$cpt2][0]]["moyenne"] = floatval(number_format($notes[$cpt], 2));
						$tabMax["semestre"][$tabSemestre[0][1]]["UE"][$UE[$cpt2][0]]["moyenne"] = floatval(number_format($notes[$cpt], 2));
					} else {
						$tabPromo["semestre"][$tabSemestre[0][1]]["UE"][$UE[$cpt2][0]]["moyenne"] += floatval(number_format($notes[$cpt], 2));
						if ($tabMax["semestre"][$tabSemestre[0][1]]["UE"][$UE[$cpt2][0]]["moyenne"] < floatval(number_format($notes[$cpt], 2))) {
							$tabMax["semestre"][$tabSemestre[0][1]]["UE"][$UE[$cpt2][0]]["moyenne"] = floatval(number_format($notes[$cpt], 2));

						}
						if ($tabMin["semestre"][$tabSemestre[0][1]]["UE"][$UE[$cpt2][0]]["moyenne"] > floatval(number_format($notes[$cpt], 2))) {
							$tabMin["semestre"][$tabSemestre[0][1]]["UE"][$UE[$cpt2][0]]["moyenne"] = floatval(number_format($notes[$cpt], 2));

						}
					}
					$out[$notes[0]]["semestre"][$tabSemestre[0][1]]["UE"][$UE[$cpt2][0]]["classement"] = intval($notes[5]);
					$out[$notes[0]]["semestre"][$tabSemestre[0][1]]["UE"][$UE[$cpt2][0]]["moyenne"] = floatval(number_format($notes[$cpt], 2));
					$out[$notes[0]]["semestre"][$tabSemestre[0][1]]["UE"][$UE[$cpt2][0]]["annee"] = (date('Y') - 1) . "-" . date('Y');
					$out[$notes[0]]["semestre"][$tabSemestre[0][1]]["UE"][$UE[$cpt2][0]]["moyennePromo"] = "??";
				}

			}
		}
	}
	// pacours note matiere
	for ($cpt=0;$cpt<count($cours);$cpt++) {
		for($cpt2=0;$cpt2<count($tabMatiere);$cpt2++){
			if(strpos($cours[$cpt],$tabMatiere[$cpt2][2])!==false){
				if (!empty($notes[$cpt])){
					for($index=0;$index<count($tabSemestre);$index++){
						if(!empty($tabSemestre[$index])){
							//ajout pour le semestre le coef, classement, moyenne
							if (in_array("coefficient",array_keys($out[$notes[0]]["semestre"][$tabSemestre[$index][1]]))===false){
								$out[$notes[0]]["semestre"][$tabSemestre[$index][1]]["coefficient"]=floatval($tabMatiere[$cpt2][3]);
							}else {define("CSV_DELIMITEUR",";");
								$out[$notes[0]]["semestre"][$tabSemestre[$index][1]]["coefficient"] += floatval($tabMatiere[$cpt2][3]);
							}



							//ajout pour le UE le coef, classement, moyenne
							if (in_array("coefficient",array_keys($out[$notes[0]]["semestre"][$tabSemestre[$index][1]]["UE"][$tabMatiere[$cpt2][4]]))===false){
								$out[$notes[0]]["semestre"][$tabSemestre[$index][1]]["UE"][$tabMatiere[$cpt2][4]]["coefficient"]=floatval($tabMatiere[$cpt2][3]);
							}else {
								$out[$notes[0]]["semestre"][$tabSemestre[$index][1]]["UE"][$tabMatiere[$cpt2][4]]["coefficient"] += floatval($tabMatiere[$cpt2][3]);
							}




							//pour les cours l'abriviation, coeff et la note
							//taux d'absent est 0, je l'ai pas gerer 
							$out[$notes[0]]["semestre"][$tabSemestre[$index][1]]["UE"][$tabMatiere[$cpt2][4]]["matieres"][$tabMatiere[$cpt2][2]]["intitule"]=$tabMatiere[$cpt2][2];
							$out[$notes[0]]["semestre"][$tabSemestre[$index][1]]["UE"][$tabMatiere[$cpt2][4]]["matieres"][$tabMatiere[$cpt2][2]]["coefficient"]=floatval($tabMatiere[$cpt2][3]);
							$out[$notes[0]]["semestre"][$tabSemestre[$index][1]]["UE"][$tabMatiere[$cpt2][4]]["matieres"][$tabMatiere[$cpt2][2]]["moyenne"]=floatval($notes[$cpt]);
							$out[$notes[0]]["semestre"][$tabSemestre[$index][1]]["UE"][$tabMatiere[$cpt2][4]]["matieres"][$tabMatiere[$cpt2][2]]["TauxAbsent"]=0;
							$out[$notes[0]]["semestre"][$tabSemestre[$index][1]]["UE"][$tabMatiere[$cpt2][4]]["matieres"][$tabMatiere[$cpt2][2]]["moyennePromo"]="??";

							if ( in_array("moyenne",array_keys($tabPromo[$notes[0]]["semestre"][$tabSemestre[$index][1]]["UE"][$tabMatiere[$cpt2][4]]["matieres"][$tabMatiere[$cpt2][2]]))===false){
								$tabPromo["semestre"][$tabSemestre[$index][1]]["UE"][$tabMatiere[$cpt2][4]]["matieres"][$tabMatiere[$cpt2][2]]["moyenne"]=floatval(number_format($notes[$cpt],2));
								$tabMin["semestre"][$tabSemestre[$index][1]]["UE"][$tabMatiere[$cpt2][4]]["matieres"][$tabMatiere[$cpt2][2]]["moyenne"]=floatval(number_format($notes[$cpt],2));
								$tabMax["semestre"][$tabSemestre[$index][1]]["UE"][$tabMatiere[$cpt2][4]]["matieres"][$tabMatiere[$cpt2][2]]["moyenne"]=floatval(number_format($notes[$cpt],2));
							}else{
								$tabPromo["semestre"][$tabSemestre[$index][1]]["UE"][$tabMatiere[$cpt2][4]]["matieres"][$tabMatiere[$cpt2][2]]["moyenne"]+=floatval(number_format($notes[$cpt],2));
								if ($tabMax["semestre"][$tabSemestre[$index][1]]["UE"][$tabMatiere[$cpt2][4]]["matieres"][$tabMatiere[$cpt2][2]]["moyenne"]<floatval(number_format($notes[$cpt],2))){
									$tabMax["semestre"][$tabSemestre[$index][1]]["UE"][$tabMatiere[$cpt2][4]]["matieres"][$tabMatiere[$cpt2][2]]["moyenne"]=floatval(number_format($notes[$cpt],2));
								}
								if($tabMin["semestre"][$tabSemestre[$index][1]]["UE"][$tabMatiere[$cpt2][4]]["matieres"][$tabMatiere[$cpt2][2]]["moyenne"]>floatval(number_format($notes[$cpt],2))){
									$tabMin["semestre"][$tabSemestre[$index][1]]["UE"][$tabMatiere[$cpt2][4]]["matieres"][$tabMatiere[$cpt2][2]]["moyenne"]=floatval(number_format($notes[$cpt],2));
								}
							}
							//pour ajouter commentaire 
							foreach($commentaire[$cpt2] as $keys=>$values){
								foreach($values as $k=>$v){
									//ajout pour le semestre le commentaire 
									$out[$notes[0]]["semestre"][$tabSemestre[$index][1]]["commentaire"]=$v;
									//si il a un commentaire dans le tableau, on met se commentaire
									//pour les UE
									if(!empty($k)){
										$out[$notes[0]]["semestre"][$tabSemestre[$index][1]]["UE"][$k]["commentaire"]= $v;
									}
								}
							}	//mkdir($chemin."/csv",0700);
						}
						//fin ajout commentaire

						//ajout le departement pour l'etudiant
						$out[$notes[0]]["departement"]=$departement;
					}
				}
			}
		}
	}


	return $out;
}

//cela permet de fusionner la totaliter des données selon le numéro de l'etudiant
function fusionner($tab1,$tableau2){
	$out=array();
	for($tmp=0;$tmp<count($tab1);$tmp++){
		if (array_keys($tableau2[$tmp])[0]==$tab1[$tmp]["numero"]){
			$out[]=array_merge($tab1[$tmp], $tableau2[$tmp][array_keys($tableau2[$tmp])[0]]);
		}

	}

	return $out;
}

function fusion($departement,$liste){
	$out=array();

	$out=array_merge($departement, $liste);

	return $out;
}
function ajoutermoyennePromoMinMax($ListeNotes){
	$out=array();


}

function moyenneTableau($tableau,$nombreEtu){
	return array_sum($tableau)/$nombreEtu;
}

//Pour le details de la promotion 
function ajoutPromo($cle,$information,$semestre){
	$out=array();

	foreach($semestre as $var=>$gui){
		if (strpos($gui[0],"M")!==false){$tabMatiere[]=$gui;}
		if (strpos($gui[0],"Se")!==false){$tabSemestre[]=$gui;}
		if (strpos($gui[0],"Dé")!==false){$departement=$gui[1];}
	}

	//Prendre les toutes les informations
	//enlever les 5 derniers lignes puis que c'est des infos pour le departement (département, année, date de début, date de fin, semestre)
	$effectif=(count($information)-5);

	for($cpt=0;$cpt<count($cle);$cpt++){
		for($cpt2=0;$cpt2<count($tabMatiere);$cpt2++){

			if(strpos($cle[$cpt],$tabMatiere[$cpt2][2])!==false){

				if(!empty($information[$cpt])){
					//ajoute le departement
					$out['Departement']=$departement;

					for($index=0;$index<count($tabSemestre);$index++){

						//pour le semestre
						$out["Promo"]["Semestre"][$tabSemestre[$index][1]]["maximum"]="??";
						$out["Promo"]["Semestre"][$tabSemestre[$index][1]]["minimun"]="??";
						$out["Promo"]["Semestre"][$tabSemestre[$index][1]]["coefficient"]+=floatval($tabMatiere[$cpt2][3]);
						$out["Promo"]["Semestre"][$tabSemestre[$index][1]]["moyennePromo"]="??";
						$out["Promo"]["Semestre"][$tabSemestre[$index][1]]["effectifs"]=$effectif;

						//pour chaque UE					
						$out["Promo"]["Semestre"][$tabSemestre[$index][1]]["UE"][$tabMatiere[$cpt2][4]]["maximum"]="??";
						$out["Promo"]["Semestre"][$tabSemestre[$index][1]]["UE"][$tabMatiere[$cpt2][4]]["minimun"]="??";
						$out["Promo"]["Semestre"][$tabSemestre[$index][1]]["UE"][$tabMatiere[$cpt2][4]]["coefficient"]+=floatval($tabMatiere[$cpt2][3]);
						$out["Promo"]["Semestre"][$tabSemestre[$index][1]]["UE"][$tabMatiere[$cpt2][4]]["moyennePromo"]="??";

						//Pour chaque cours 
						//floatval => permet de mettre en float et de ne pas garder en string
						$out["Promo"]["Semestre"][$tabSemestre[$index][1]]["UE"][$tabMatiere[$cpt2][4]]["matieres"][$tabMatiere[$cpt2][2]]["intitule"]=$tabMatiere[$cpt2][2];
						$out["Promo"]["Semestre"][$tabSemestre[$index][1]]["UE"][$tabMatiere[$cpt2][4]]["matieres"][$tabMatiere[$cpt2][2]]["coefficient"]=floatval($tabMatiere[$cpt2][3]);
						$out["Promo"]["Semestre"][$tabSemestre[$index][1]]["UE"][$tabMatiere[$cpt2][4]]["matieres"][$tabMatiere[$cpt2][2]]["maximum"]=floatval(max($information[$cpt]));
						$out["Promo"]["Semestre"][$tabSemestre[$index][1]]["UE"][$tabMatiere[$cpt2][4]]["matieres"][$tabMatiere[$cpt2][2]]["minimum"]=floatval(min($information[$cpt]));
						$out["Promo"]["Semestre"][$tabSemestre[$index][1]]["UE"][$tabMatiere[$cpt2][4]]["matieres"][$tabMatiere[$cpt2][2]]["moyennePromo"]="??";


					}
				}
			}
		}
	}
	//var_dump($out);
	return $out;
}



function convertXLStoCSV($infile)
{
	$objPHPExcel = new PHPExcel();

	print_r($infile);
	return;

	//comme infile est le fichier depart et qu'il est dans un dossier, alors je decoupe les données
	//$depart est le departement
	//$dossier par exemple le dossier INFO_S3_20162017
	$depart=explode("/",$infile)[0];
	$annee=explode("/",$infile)[1];
	$dossier=explode("/",$infile)[2];

	if(!file_exists($depart."/".$annee."/".$dossier."/csv")){
		mkdir($depart."/".$annee."/".$dossier."/csv",0700);
	}else{
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


	echo "Je suis dans converXLStoCSV ".$infile.PHP_EOL;


	$loadedSheetNames = $objPHPExcel->getSheetNames();

	print_r($loadedSheetNames);

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');

	//cette partie permet d'avoir dans le fichier bilan, les données
	//comme tout les excel se ressemble, les données tel que le département, Semestre,... sont tout en bas du fichier
	//exemple: 
	//avant il y a les etudiants 
	//colonne A(61): Département colonne B(61): INFO
	//colonne A(62): Date début colonne B(62): 12/05/2017

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
		if($cell->getValue()=='Date de fin '){
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
		//j'ai besoin de ses 4 fichiers pour lancer csvToJson (function un peu plus bas)
		//pour le fichier Absence
		//$loadedSheetName=="Absences"

		/*puis mettre $fichier[]
		 * en concervant l'ordre a laquelle on les mets en parametr, quand on appelle csvToJson
		 * 1 fichier:la liste etudiants / 2 fichier: Détails des notes / 3 fichier: liste matiere / 4 fichier: décision
		 * */

		if($loadedSheetName=="Liste_Matiere" || $loadedSheetName=="Liste_Etudiants"|| $loadedSheetName=="Décision"||strpos($loadedSheetName,"Note_detail_S")!== false){
			$fichier[]=$depart."/".$annee."/".$dossier."/csv/".$departement.'_'.$semestre.'_'.$date3[2].$date4[2].'_'.$loadedSheetName.'.csv';
		}
	}
	echo "fin conversion".PHP_EOL;
	print_r($fichier);


	$ListeMatieres = $fichier[0];
	$ListeEtudiants = $fichier[1];
	$Decisions = $fichier[2];
	$BilanDetaille = $fichier[3];



	csvToJson($ListeEtudiants,$BilanDetaille,$ListeMatieres,$Decisions);

}

class Etudiant {
	/*     "20112": {"numero":"20112",
"nom":"c'estqui",
"prenom":"personne",
"departement":"INFO",

"informations":{
"dateNaissance":"06/03/1998",
"Lycee":"je ne sais pas",
"bac":"S_2015"
},
"groupe":"IPI",
"avatar":"20112.png"
*/
	public $numero="";
	public $nom="";
	public $prenom="";
	public $departement = "INFO";
	public $groupe = "";
	public $avatar="anonyme.jgp";
	public $informations;


	function __construct($liste) {
		$this->informations = array();
        foreach ($liste as $key => $value) {
             if (strcasecmp ($key,"Numéro")==0) {
				 $this->numero = $value;
				 $this->avatar = $value.".png";
			 } else if (strcasecmp($key,"Nom")==0) $this->nom = $value;
			 else if (strcasecmp($key,"Prénom")==0) $this->prenom = $value;
			 else if (strcasecmp($key,"Groupe")==0) $this->groupe = $value;
			 else  $this->informations[$key] =  $value;
		}
	}
	public function __toString() {
		return $this->numero+":"+$this->nom;
	}
}

function csvToJson($fileListeEtudiants,$fileBilanDetaille,$fileListeMatieres,$fileDecisions) {

	global $tabPromo;
	global $nbEtudiants;
	global $tabMax;
	global $tabMin;


	if(!($descFileEtudiants = fopen($fileListeEtudiants, 'r'))) { // ouverture de la liste etudiants
		die("*** [ERREUR]> Probleme d'ouverture du fichier $fileListeEtudiants ***\n");
	}
	if(!($descFileBilan = fopen($fileBilanDetaille, 'r'))) { // ouverture Détails des notes
		die("*** [ERREUR]> Probleme d'ouverture du fichier $fileBilanDetaille ***\n");
	}
	if(!($descFileMatieres = fopen($fileListeMatieres, 'r'))) { // ouveture liste matiere
		die("*** [ERREUR]> Probleme d'ouverture du fichier $fileListeMatieres ***\n");
	}
	if(!($descFileDecisions = fopen($fileDecisions, 'r'))) { // ouverture de la décision
		die("*** [ERREUR]> Probleme d'ouverture du fichier $fileDecisions ***\n");
	}

	$chemin=explode('/',$fileListeEtudiants);
	$repertoireDestination = $chemin[0]."/".$chemin[1]."/".$chemin[2]."/json/";
	if(!file_exists($repertoireDestination)){
		mkdir($repertoireDestination,0700);
	}


	$nameFile=explode('_',$chemin[4]);

	///////******  liste etudiants

	$listeProprietesEtudiants = fgetcsv($descFileEtudiants,"1024",CSV_DELIMITEUR);

	$listeEtudiants = array();// recuperer les donnée dans liste étudiants
	while ($row= fgetcsv($descFileEtudiants,"1024",CSV_DELIMITEUR)) {
		if(!empty($row[0])){
			$nbEtudiants++;
			$etudiant = array();
			for ($i=0;$i<count($listeProprietesEtudiants);$i++) $etudiant[$listeProprietesEtudiants[$i]]=$row[$i];
			$listeEtudiants[$row[0]] = new Etudiant($etudiant);
		}
	}
	echo "...Liste Etudiants : ".$nbEtudiants.PHP_EOL;

	echo json_encode($listeEtudiants, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES| JSON_UNESCAPED_UNICODE );
    // print_r($json["20150986"]);
	//echo "...cles ...".PHP_EOL;
	print_r($listeProprietesEtudiants);

	return;




	$ranger = fgetcsv($descFileMatieres,"1024",CSV_DELIMITEUR);
	$json3=array();///recuperer les donnée dans Matiere
	while($row3=fgetcsv($descFileMatieres,"1024",CSV_DELIMITEUR)){
		$json3[]=$row3;
	}


	$comment = fgetcsv($descFileDecisions,"1024",CSV_DELIMITEUR);


	$commentaire = fgetcsv($descFileDecisions,"1024",CSV_DELIMITEUR);
	$index=array();
	foreach($commentaire as $k=>$v){
		$c=strtolower($v);
		setlocale(LC_CTYPE, 'fr_FR.UTF-8');
		array_push($index,iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $c));
	}

	$json4=array();// décision
	while($row4=fgetcsv($descFileDecisions,"1024",CSV_DELIMITEUR)){
		for($i=0;$i<1;$i++){
			if(!empty($row4[$i])){
				$json4[]=array_combine3($index,$comment,$row4);
			}
		}
	}

	$cours = fgetcsv($descFileBilan,"1024",CSV_DELIMITEUR);
	$json2=array();
	$promo=array();
	while($row2=fgetcsv($descFileBilan,"1024",CSV_DELIMITEUR)){
		for($j=0;$j<1;$j++){
			if(!empty($row2[$j])){
				$rowLigne=(array_filter($row2));
				$json2[]=ajoutNotes($cours,$rowLigne,$json3,$json4);
				$ligne[]=$rowLigne;
			}
		}

	}
	$promo=ajoutPromo($cours,$ligne,$json3);
	$tab=fusionner($listeEtudiants,$json2);

	$tableau=arrayUnique($tab);

	fclose($descFileEtudiants);
	fclose($descFileBilan);

	var_dump($tabMax);
	$date="";
	foreach (explode("-",$chemin[1]) as $item){
		$date=$date.$item;
	}

	// 1 : on ouvre le fichier
	$monfichier = fopen($repertoireDestination.$nameFile[0].'_'.$nameFile[1].'_'.$date.'.json', 'w+');
	// 2 : on met en json et on ecris dossier json
	$tableauGeneral=fusion($promo,$tableau);
	$information = json_encode($tableauGeneral,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
	fwrite($monfichier,$information);

	//permet de separer les infos (json) de le liste (json)

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

	//option IPI ou PEL
	//$option2 la date

	//si les 2 options sont null alors nous cherchons le fichier xls pour le mettre en csv
	if ($option===null && $option2===null){
		$fichier_Bilan="$DEPARTEMENT/".(date("Y")-1)."-".date('Y')."/".$DEPARTEMENT."_".$semestre."_".(date("Y")-1).date('Y')."/excel/"."Bilan_".$DEPARTEMENT."_".$semestre."_".(date("Y")-1).date('Y').".xls";
		if (validationFichier($fichier_Bilan)===true) {
			convertXLStoCSV($fichier_Bilan);
		}
	}

	//si l'option a etait choisi
	elseif ($option!==null && $option2===null){
		//l'erreur est humaine, mais on verifie si ce n'est pas une date, et nous cherchons le fichier xls pour le mettre en csv
		if (validateDate($option,'Y')===false){
			$fichier_Bilan="$DEPARTEMENT/".(date('Y')-1)."-".date('Y')."/".$DEPARTEMENT."_".$semestre."_".(date('Y')-1).date('Y')."/excel/"."Bilan_".$DEPARTEMENT."_".$semestre."_".$option.".xls";

			if (validationFichier($fichier_Bilan)===true) {
				convertXLStoCSV($fichier_Bilan);
			}

		}
		else{
			//aù au sinon, nous allons chercher le fichier coorespondant au IPI ou PEL
			$d = DateTime::createFromFormat('Y', $option);
			$date=$d->format("Y");
			$fichier_Bilan="$DEPARTEMENT/".($date-1).$date."/".$DEPARTEMENT."_".$semestre."_".($date-1).$date."/excel/"."Bilan_".$DEPARTEMENT."_".$semestre."_".($date-1).$date.".xls";
			if (validationFichier($fichier_Bilan)===true) {
				convertXLStoCSV($fichier_Bilan);
			}
		}

	}
	//si les 2 options sont pas null
	elseif ($option!==null && $option2!==null){
		//on verifie si la premiere option n'est pas une date et que la deuxieme oui
		if (validateDate($option,'Y')==false && validateDate($option2,'Y')==true){
			$d = DateTime::createFromFormat('Y', $option2);
			$date=$d->format("Y");
			$fichier_Bilan="$DEPARTEMENT/".($date-1).$date."/".$DEPARTEMENT."_".$semestre."_".($date-1).$date."/excel/"."Bilan_".$DEPARTEMENT."_".$semestre."_".$option."_".($date-1).$date.".xls";
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
$option=null;//IPI ou PEL
$date=null;

// groupe si il y a des lite spécifique date passer en parametre si elle n'existe c'est la date courante
if( count($argv)>=3){
	$option=$argv[2];
}

// groupe si il y a des lite spécifique date passer en parametre si elle n'existe c'est la date courante
if( count($argv)>=4){
	$date=$argv[3];
}


chercherFichier($semestre,$option,$date);
?>
