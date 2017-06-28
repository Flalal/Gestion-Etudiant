<?php

/**
 * Created by PhpStorm.
 * User: hsu
 * Date: 28/06/17
 * Time: 11:55
 */
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