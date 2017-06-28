<?php

/**
 * Created by PhpStorm.
 * User: hsu
 * Date: 28/06/17
 * Time: 11:58
 */
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

    function ajouterNotesDansMatiere ($matiere,$note) {
        if (isset($this->matieres[$matiere])) {
            $this->matieres[$matiere]->ajouterNote($note);
        } else throw new Exception ($matiere."n'existe pas");
    }

    function ajouterRapportUE ($rapport) {
        $this->minimum = $rapport["minimum"];
        $this->maximum = $rapport["maximum"];
        $this->moyennePromo = $rapport["moyennePromo"];
        $this->listeNotes = array();
        foreach ($rapport["listeNotes"] as $value) {
            $this->listeNotes[] = $value + 0;
        }
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

    function __clone() {
        $newMatieres = array();
        foreach ($this->matieres as $key => $value) {
            $newMatieres[$key]= clone $value;
        }
        $this->matieres = $newMatieres;
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