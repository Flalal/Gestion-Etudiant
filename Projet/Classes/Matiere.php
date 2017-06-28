<?php

/**
 * Created by PhpStorm.
 * User: hsu
 * Date: 28/06/17
 * Time: 11:57
 */
class Matiere {
    // Référence;Nom Module;Abréviation;Coefficient;UE;Semestre;Responsable
    public $reference = "";
    public $nom = "";
    public $abreviation  = "";
    public $coefficient = "";
    public $referenceUE = "";
    public $referenceSemestre = "";
    public $responsable="";
    public $note;
    function __construct($liste) {
        if (isset($liste["Référence"]))$this->reference = $liste["Référence"];
        if (isset($liste["Nom Module"]))$this->nom = $liste["Nom Module"];
        if (isset($liste["Abréviation"]))$this->abreviation = $liste["Abréviation"];
        if (isset($liste["Coefficient"]))$this->coefficient = $liste["Coefficient"];
        if (isset($liste["UE"]))$this->referenceUE = $liste["UE"];
        if (isset($liste["Semestre"]))$this->referenceSemestre = $liste["Semestre"];
        if (isset($liste["Responsable"]))$this->responsable = $liste["Responsable"];
        $this->note = -1;
    }
    function getReference () {
        return $this->reference;
    }
    function ajouterNote($note) {
        $this->note = $note;
    }

    function ajouterRapportMatiere ($rapport) {
        $this->minimum = $rapport["minimum"];
        $this->maximum = $rapport["maximum"];
        $this->moyennePromo = $rapport["moyennePromo"];
        $this->listeNotes = array();
        foreach ($rapport["listeNotes"] as $value) {
            $this->listeNotes[] = $value + 0;
        }
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
