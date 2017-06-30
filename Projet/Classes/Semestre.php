<?php

/**
 * Created by PhpStorm.
 * User: hsu
 * Date: 28/06/17
 * Time: 11:59
 */
class Semestre {
    public $code = "";
    public $note = "";
    public $promo = "";
    public $departement = "";
    public $anneeDebut;
    public $anneeFin;
    public $UE = "";
    public $tabNotes;
    function __construct($code,$promo, $departement,$anneeDebut,$anneeFin) {
        $this->code = $code;
        $this->promo = $promo;
        $this->departement =  $departement;
        $this->anneeDebut = $anneeDebut;
        $this->anneeFin = $anneeFin;
        $this->note = 0.0;
        $this->UE = array();
        $this->tabNotes = array();
    }

    function setNote ($note) {
        $this->note = $note;
    }
    function ajouterUE( $ue) {
        $this->UE[$ue->getDesignation()] = $ue;
    }

    function rechercherUE ($matiere) {
        foreach ($this->UE as $key => $refUE) {
            if ($refUE->contient($matiere)===true) {
                echo $matiere." est dans ".$key.PHP_EOL;
                return $refUE;
            }
        }
        return null;
    }

    function calculerRang ($note) {
        $rang = 0;
        while ($rang<$this->effectifs ){
            if ($this->listeNotes[$rang]<=$note) return $rang+1;
            $rang++;
        }
        return $rang;
    }
    function miseAJourSemestreEtudiant ($tabNotes) {
        $refSemestre = "M ".$this->code; // A faire : on suppose que c'est toujours "M S2"
        $this->moyenne = $tabNotes[$refSemestre];
        $position  = $this->calculerRang ($this->moyenne);
        $this->classement = $position."/".$this->effectifs;

        // mise à jour note UE
        foreach ($tabNotes as $key => $value){
            $keys = explode(" ", $key);
            if (count($keys)>1) {
                $refUE = $keys[1];
                if (stripos($refUE, "UE")!==false) {
                    if (!isset ($this->UE[$refUE])) {
                        throw new Exception ($refUE." n'existe pas");
                    } else {
                        $this->UE[$refUE]->miseAJourUEEtudiant($value);
                    }
                }
            }
        }
        // Mise à jour Note Matière
        echo "recherche matiere ...".PHP_EOL;
        foreach ($tabNotes as $key => $value){
            $keys = explode(" ", $key);
            if (count($keys)>1) {
                $refMat = $keys[0];
                // echo $refMat.PHP_EOL;
                $refUE = $this->rechercherUE($refMat);
                if ($refUE != null) {
                    $refUE->ajouterNotesDansMatiere($refMat, $value);
                }
            }
        }

    }

    function ajouterRapportSemestre ($rapport) {

        //  Rapport sur le semestre
        $refSemestre = "M ".$this->code; // A faire : on suppose que c'est toujours "M S2"
        $rapSemestre = $rapport[$refSemestre];
        $this->minimum = $rapSemestre["minimum"];
        $this->maximum = $rapSemestre["maximum"];
        $this->moyennePromo = $rapSemestre["moyennePromo"];
        $this->listeNotes = array();
        foreach ($rapSemestre["listeNotes"] as $value) {
            $this->listeNotes[] = $value + 0;
        }
        $this->effectifs = count($this->listeNotes);

        // Rapport sur les UE
        foreach ($rapport as $key => $value){
            $keys = explode(" ", $key);
            if (count($keys)>1) {
                $refUE = $keys[1];
                if (stripos($refUE, "UE")!==false) {
                    if (!isset ($this->UE[$refUE])) {
                        throw new Exception ($refUE." n'existe pas");
                    } else {
                        $this->UE[$refUE]->ajouterRapportUE($value);
                    }
                }
            }
        }

        // Rapport sur les matières
        echo "recherche matiere ...".PHP_EOL;
        foreach ($rapport as $key => $value){
            $keys = explode(" ", $key);
            if (count($keys)>1) {
                $refMat = $keys[0];
                // echo $refMat.PHP_EOL;
                $refUE = $this->rechercherUE($refMat);
                if ($refUE != null) {
                   $refUE->ajouterRapportMatiere($refMat, $value);
                }
            }
        }


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

    function ajouterNotesDansUE($UE,$matiere,$note) {
        if (isset($this->UE[$UE])) {
            $this->UE[$UE]->ajouterNotesDansMatiere($matiere, $note);
        } else new Exception ("UE ".$UE. " n'exsite pas : ");
    }

    function __clone() {
        $newUE = array();
        foreach ($this->UE as $key => $value) $newUE[$key]= clone $value;
        $this->UE = $newUE;
        /* if (isset($this->listeNotes)) {
            $liste = array();;
            foreach ($this->listeNotes as $value) $liste[]= $value;
            $this->listeNotes = $liste;
        }*/
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