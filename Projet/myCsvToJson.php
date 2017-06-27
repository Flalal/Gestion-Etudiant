<?php
/**
 * Created by PhpStorm.
 * User: hsu
 * Date: 27/06/17
 * Time: 18:09
 */

require('Classes/PHPExcel.php');
require ('Classes/PHPExcel/IOFactory.php');
define("MARQUEUR","*");
define("DEPARTEMENT","INFO");
define("SEMESTRE", "SEMESTRE"); // S1, S2, S3, S4
define("ANNEE", "ANNEE"); // format 1617
define("LISTE",     "Liste_Etudiants_SEMESTRE_ANNEE.csv");
define("MATIERES",     "Liste_Matières_SEMESTRE_ANNEE.csv");
define("LES_SEMESTRES", "S0 S1 S2 S3 S4 S5 S6");
define("CSV_DELIMITEUR",";");


class Etudiant {
/*
"20112": {"numero":"20112",
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
class Matiere {
    // Référence;Nom Module;Abréviation;Coefficient;UE;Semestre;Responsable
    public $reference = "";
    public $nom = "";
    public $abreviation  = "";
    public $coefficient = "";
    public $referenceUE = "";
    public $referenceSemestre = "";
    public $responsable="";
    function __construct($liste) {
        if (isset($liste["Référence"]))$this->reference = $liste["Référence"];
        if (isset($liste["Nom Module"]))$this->nom = $liste["Nom Module"];
        if (isset($liste["Abréviation"]))$this->abreviation = $liste["Abréviation"];
        if (isset($liste["Coefficient"]))$this->coefficient = $liste["Coefficient"];
        if (isset($liste["UE"]))$this->referenceUE = $liste["UE"];
        if (isset($liste["Semestre"]))$this->referenceSemestre = $liste["Semestre"];
        if (isset($liste["Responsable"]))$this->responsable = $liste["Responsable"];
        $this->notes = array();
    }
    function getReference () {
        return $this->reference;
    }
    function ajouterNote($note) {
        $this->notes[] = $note;
    }
    function getCoefficient () {
        return $this->coefficient;
    }
    public function __toString() {
        $res = $this->reference."-";
        $res .= $this->nom."-";
        $res .= $this->abreviation."-";
        $res .= $this->coefficient."-";
        $res .= $this->referenceUE."-";
        $res .= $this->referenceSemestre."-";
        $res .= $this->responsable.PHP_EOL;
        return $res;
    }
}

class UE {
    public $designation = "";
    public $nom = "";
    public $coefficient  = "";
    public $note = "";
    public $matieres = "";
    function __construct($liste) {
        if (isset($liste["Désignation"]))$this->designation = $liste["Désignation"];
        if (isset($liste["Nom"]))$this->nom = $liste["Nom"];
        if (isset($liste["Coefficient"]))$this->coefficient = $liste["Coefficient"];
        $this->note = 0.0;
        $this->matieres = array();
    }
    function ajouterMatiere($matiere) {
        $this->matieres[$matiere->getReference()] = $matiere;
    }

    function  getDesignation () {
        return $this->designation;
    }

    function validerCoefficients () {
        $sommeCoefficients = 0;
        foreach ($this->matieres as $key => $matiere) {
            $sommeCoefficients += $matiere->getCoefficient();

        }
        if ($sommeCoefficients != $this->coefficient){
            echo "xxxxx Erreur Coefficients : ".$this->designation."=>".$this->coefficient." à la place de ".$sommeCoefficients.PHP_EOL;
            return false;
        }
        return true;
    }

    public function __toString() {
        $res = "Désignation : ".$this->designation.PHP_EOL;
        $res .= "Nom :".$this->nom.PHP_EOL;
        $res .= "Coefficient :".$this->coefficient.PHP_EOL;
        if (count($this->matieres) >0) {
            $res .= "=== Liste des Matières : ".PHP_EOL;
            foreach ($this->matieres as $key => $value) {
                $res.=$value;
            }
        }
        if ($this->note > 0) $res .= "Note :".$this->note.PHP_EOL;
        return $res;
    }
}

class Semestre {
    public $code = "";
    public $note = "";
    public $promo = "";
    public $departement = "";
    public $anneeDebut;
    public $anneeFin;
    public $UE = "";
    function __construct($code,$promo, $departement,$anneeDebut,$anneeFin) {
        $this->code = $code;
        $this->promo = $promo;
        $this->departement =  $departement;
        $this->anneeDebut = $anneeDebut;
        $this->anneeFin = $anneeFin;
        $this->note = 0.0;
        $this->UE = array();
    }
    function ajouterUE( $ue) {
        $this->UE[$ue->getDesignation()] = $ue;
    }
    function ajouterMatiereDansUE ($liste) {
        // Référence;Nom Module;Abréviation;Coefficient;UE;Semestre;Responsable
        // M3101;Principes des systèmes d'exploitation ;SE-3;2.5;UE31;S3;Roussel
        $designationUE = $liste["UE"];
        if (isset($this->UE[$designationUE])) {
            $this->UE[$designationUE]->ajouterMatiere(new Matiere($liste));
        } else {
            $liste = "";
            foreach ($this->UE as $key => $value) $liste.=$key.",";
            throw  new Exception ("UE ".$designationUE. " n'exsite pas : ". $liste);
        }
    }

    function getUE ($index) {
        if($index < 0 || $index >= count($this->UE)) return null;
        return $this->UE[$index];
    }

    function validerUECoefficient() {
        $valide = true;
        foreach ($this->UE as $keys => $ue) {
            if (! $ue->validerCoefficients()) {
                $valide = false;
            }
        }
        return $valide;
    }
    public function __toString() {
        $res = "Departement :".$this->departement.PHP_EOL;
        $res .= "Semestre :".$this->code.PHP_EOL;
        $res .= "Année :".$this->promo.PHP_EOL;
        $res .= "Date de début :".$this->anneeDebut.PHP_EOL;
        $res .= "Date de Fin :".$this->anneeFin.PHP_EOL;
        if (count($this->UE) >0) {
            $res .= "=== Liste des UE : ".PHP_EOL;
            foreach ($this->UE as $key => $value) {
                $res.=$value.PHP_EOL;
            }
        }
        if ($this->note > 0) $res .= "Note :".$this->note.PHP_EOL;
        return $res;
    }
}


function rechercherTag ($f, $tag) {
    $existe = false;
    while ($row = fgetcsv($f,"1024",CSV_DELIMITEUR)) {
        //  echo $row[0].PHP_EOL;
        if ($row[0] == MARQUEUR) {
            // echo MARQUEUR. "trouve".PHP_EOL;
            if (strcasecmp ($row[1],$tag)==0 ) {
                $existe = true;
                break;
            }
        }
    }
    return $existe;

}


function lirePromotion ($file) {
    // Numéro;Nom;Prénom;Groupe;Date;Bac;Lycée/etablissement
    $f = fopen($file, 'r');
    // echo LISTE_UES. "trouvée";
    $tabIntitules= fgetcsv($f,"1024",CSV_DELIMITEUR);
    $nbIntitules = count($tabIntitules);
    $promotion = array();
    while ($data = fgetcsv($f,"1024",CSV_DELIMITEUR)) {
        //  echo "data=".$data;
        $liste = array();
        if (strlen(trim($data[0]))>0) { // si la ligne n'est pas vide
            for ($i = 0; $i < $nbIntitules; $i++) $liste[$tabIntitules[$i]] = trim($data[$i]);
            $numeroEtudiant = $data[0];
            $promotion[$numeroEtudiant]= new Etudiant($liste);
        } else {
            echo "Pas de numero étudiant : ";
            print_r($data);
        }

    }
    fclose($f);
    return $promotion;
}

function initialiserPromotion ($file) {
    if (!($f = fopen($file, 'r'))) {
        die("** Probleme d'ouverture Fichier promotion : ".$file." **");
    }
    fclose($f);
    return  lirePromotion ($file);
}


function lireInfoSemestre ($file) {
    /*
    Département;INFO;;;;;
    Année;INFO2;;;;;
    Date de début;01/09/2016;;;;;
    Date de fin ;31/06/2017;;;;;
    Semestre;S3;;;;;
    */
    define("INFORMATIONS", "Informations générales");
    $f = fopen($file, 'r');
    if (! rechercherTag($f,INFORMATIONS)) throw new Exception ("Pas de ". INFORMATIONS);
    //  echo INFORMATIONS. "trouvée";
    $cpt = 0;
    $ligneNonTraitee = "";
    while ($data = fgetcsv($f,"1024",CSV_DELIMITEUR)) {
        if (strlen(trim($data[0]))==0) break; // ligne vide rencontrée;
        if(strcasecmp($data[0],"Département")==0) {
            $departement= $data[1];
            $cpt++;
        } else if(strcasecmp($data[0],"Année")==0) { $promo = $data[1] ; $cpt++;
        } else if(strcasecmp($data[0],"Date de Début")==0) { $anneeDebut = $data[1] ; $cpt++;
        } else if(strcasecmp($data[0],"Date de Fin")==0) { $anneeFin = $data[1] ; $cpt++;
        } else if(strcasecmp($data[0],"Semestre")==0) { $codeSemestre = $data[1] ; $cpt++;
        } else {
            $ligneNonTraitee.=trim($data[1])." ";
        }
    }
    fclose($f);
    $ligneNonTraitee = trim($ligneNonTraitee);
    if (strlen($ligneNonTraitee)>0) {
        echo "Attention - Traitement InfoSemestre : ".$ligneNonTraitee." : informations non traitées".PHP_EOL;
    }
    if ($cpt != 5) {
        echo $departement."".$promo." ".$anneeDebut." ".$anneeFin." ".$codeSemestre.PHP_EOL;
        throw new Exception ("Nombre Arguements insuffisant ".$cpt) ;
    }
    return new Semestre ($codeSemestre,$promo, $departement,$anneeDebut,$anneeFin);
}



function lireListeUE ($file, $semestre) {
    /*
     * Désignation;Nom;Coefficient;;;;
     *
     *  UE31;Informatique Avancé;12;;;;
     */
    define("LISTE_UES", "Liste des UE");
    $f = fopen($file, 'r');
    if (! rechercherTag($f,LISTE_UES)) throw new Exception ("Pas de ". LISTE_UES);
    // echo LISTE_UES. "trouvée";

    $tabIntitules= fgetcsv($f,"1024",CSV_DELIMITEUR);
    $nbIntitules = count($tabIntitules);
    while ($data = fgetcsv($f,"1024",CSV_DELIMITEUR)) {
        //  echo "data=".$data;
        $liste = array();
        if (strlen(trim($data[0]))==0) break;
        for ($i = 0; $i < $nbIntitules; $i++) $liste[$tabIntitules[$i]]= trim($data[$i]);
        $semestre->ajouterUE(new UE($liste));
    }
    fclose($f);
}

function lireListeMatieres($file,$semestre) {
    // *;Liste des Matières;;;;;
    // Référence;Nom Module;Abréviation;Coefficient;UE;Semestre;Responsable
    // M3101;Principes des systèmes d'exploitation ;SE-3;2.5;UE31;S3;Roussel

    define("LISTE_MATIERES", "Liste des Matières");
    $f = fopen($file, 'r');
    if (! rechercherTag($f,LISTE_MATIERES)) throw new Exception ("Pas de ". LISTE_MATIERES);
    // echo LISTE_UES. "trouvée";
    $tabIntitules= fgetcsv($f,"1024",CSV_DELIMITEUR);
    $nbIntitules = count($tabIntitules);
    while ($data = fgetcsv($f,"1024",CSV_DELIMITEUR)) {
        //  echo "data=".$data;
        $liste = array();
        if (strlen(trim($data[0]))==0) break;
        for ($i = 0; $i < $nbIntitules; $i++) $liste[$tabIntitules[$i]]= trim($data[$i]);
        $semestre->ajouterMatiereDansUE($liste);
    }
    fclose($f);
}


function initialiserSemestre ($file) {
    // Numéro;Nom;Prénom;Groupe;Date;Bac;Lycée/etablissement
    if (!($f = fopen($file, 'r'))) {
        die("** Probleme d'ouverture Fichier Semestre : ".$file." **");
    }
    fclose($f);
    $semestre = lireInfoSemestre($file);
    lireListeUE($file,$semestre);
    lireListeMatieres($file,$semestre);
    if (! $semestre->validerUECoefficient() ) throw new Exception ("Coeffcients non valide");
    return  $semestre;
}


function convertXLStoCSV($infile) {
    $objPHPExcel = new PHPExcel();

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



//    csvToJson($ListeEtudiants,$BilanDetaille,$ListeMatieres,$Decisions);

}


function createCSVToJson($anneeLong, $nomSemestre, $groupe = null) {
/*    [0] => INFO/2016-2017/INFO_S3_20162017/csv/INFO_S3_20162017_Liste_Matiere.csv
    [1] => INFO/2016-2017/INFO_S3_20162017/csv/INFO_S3_20162017_Liste_Etudiants.csv
    [2] => INFO/2016-2017/INFO_S3_20162017/csv/INFO_S3_20162017_Décision.csv
    [3] => INFO/2016-2017/INFO_S3_20162017/csv/INFO_S3_20162017_Note_detail_S3.csv
*/

//    $repertoire = DEPARTEMENT."/".$anneeLong."/Admin/";
    $repertoireSource = DEPARTEMENT."/".$anneeLong."/Admin/";
    $repertoireDestination = DEPARTEMENT."/".$anneeLong."/csv/";
    echo "Répertoire destination : ".$repertoireDestination.PHP_EOL;

    if (!is_dir($repertoireSource)) throw new Exception ("Repertoire source inexistant : ".$repertoireSource);

    if (!is_dir($repertoireDestination)) mkdir($repertoireDestination,0755,true); // création récursive


    if (isset($groupe)) $nomSemestre = $nomSemestre."_".$groupe;

// Lecture Semestre

    $matieres = str_replace(SEMESTRE, $nomSemestre, MATIERES);
    $matieres = str_replace(ANNEE, $anneeLong, $matieres);
    $semestreFile = $repertoireSource.$matieres;
    $semestre = initialiserSemestre($semestreFile);
    echo json_encode($semestre,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES| JSON_UNESCAPED_UNICODE);


    $liste = str_replace (SEMESTRE, $nomSemestre, LISTE);
    $liste = str_replace (ANNEE, $anneeLong, $liste);
    $listeFile = $repertoireSource.$liste;
    $promotion= initialiserPromotion($listeFile);
   /* foreach ($promotion as $key => $value) {
        echo $key.": \n".$value.PHP_EOL;
    }*/
    echo json_encode($promotion,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES| JSON_UNESCAPED_UNICODE);
}

function getPeriodeUniversitaire () {
    $annee = date("y");
    $anneeLong = date("Y");
    $mois = date("m");
    if ($mois > 7 ) { // on est au début de l'année
        $annee = $annee.($annee + 1)	;
        $anneeLong = $anneeLong."-".($anneeLong + 1) ;
    } else {
        $annee = ($annee-1).$annee;
        $anneeLong = ($anneeLong-1)."-".$anneeLong  ;
    }
    return $anneeLong;
}

$nbArguments = count($argv);
echo $nbArguments.PHP_EOL;
if($nbArguments < 2) {
    echo "Usage : il faut au moins un argument semestre : (".LES_SEMESTRES.")".PHP_EOL;
    return ;
} else if ($nbArguments > 1) {
    $anneeLong = getPeriodeUniversitaire();
    $semestre = strtoupper($argv[1]);
    if (strpos(LES_SEMESTRES,$semestre)===false) {
        echo "Semestre non défini : ".$semestre, " (".LES_SEMESTRES.")".PHP_EOL;
        exit -1;
    }
    if ($nbArguments < 3)  createCSVToJson($anneeLong,$semestre);
    else  {
        $groupe = $argv[2];
        createCSVToJson($anneeLong,$semestre,$groupe);
    }

}



echo "=====================================".PHP_EOL;
echo "Fin programme".PHP_EOL;
