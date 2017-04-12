/**
 * Created by Frederic on 10/04/2017.
 */


var TabDepartement=['INFO','MMI','GEA','TC'];

function Etudiant (numero, nom, prenom, dept,dateN,bac) {

    if (arguments.length < 6) throw new Error("Nombre arguments insuffisants");
    this.nom = nom.toLowerCase();
    this.prenom = prenom.toLowerCase();
    var tmp=false;
    this.redoubler=false;
    this.semestres=new Array();
    for (var i in TabDepartement){
        if (dept==TabDepartement[i])
            tmp=true;
    }

    if (!tmp)throw  new Error("problement sur nom du département");
    this.departatement=dept;

    this.avatar=this.prenom+"_"+this.nom+".jpg";
    this.bac=bac;
    this.dateNaissance=new Date(dateN);





    if (0 < numero )
        this.numero = numero;
    else this.numero = -1;


    this.getAvatar=function () {
        return this.avatar;

    };

    this.getDateNaissance=function () {
      return  this.dateNaissance.toLocaleDateString();

    };

    this.getDepartement=function () {
      return this.departatement;
    };

    this.getBac=function () {
        return this.bac;
    };
    this.setRedoubler=function () {
        this.redoubler=true;

    };
    this.getRedoubler=function () {
        return this.redoubler;

    };




    this.getNom = function () {
        return this.nom;
    };

    this.getPrenom = function () {
        return this.prenom;
    };

    this.getNumero = function () {
        return this.numero;
    };
    this.toString = function () {
        return this.numero+" "+this.nom.toUpperCase() + " " +this.prenom;
    };

    this.ajouterSemestre=function (semestre) {
        if (typeof semestre !== 'object')throw new Error("Type note invalide");
        this.semestres.push(semestre);
    };

    this.getToutSemestre=function () {
        return this.semestres;
    };
    this.getUnSemestre=function(numeroduSemestre){
        for (var i in this.semestres){
            if (this.semestres[i].getSemestre()==numeroduSemestre)
                return this.UE[i];
        }throw Error("le semestre que vous demander n'existe pas")

    };








}



function Semestre(num,annee) {
    if (arguments.length < 2 ) throw new Error("Nombre arguments insuffisants");
    if (typeof num !== 'number' && typeof annee !== 'number') throw new Error("Type note invalide");
    this.id=num;
    this.annee=annee
    this.UE=new Array();
    this.moyenneSem=0;
    this.coefficientSem=0;

    this.getAnnee=function () {
        return this.annee;
    };
    this.getSemestre=function () {
        return this.id;

    };

    this.ajouterUe=function (uejenesaispas) {
        if (typeof uejenesaispas  !== 'object')throw new Error("Type note invalide");
        this.UE.push(uejenesaispas);
       this.moyenneSem+= uejenesaispas.getMoyenneUE();
       this.coefficientSem+=uejenesaispas.getCoefficientUE();
    };


    this.getToutUE=function () {
       return this.UE;
    };

    this.getUnUe=function (quelUe) {
        for (var i in this.UE){
            if (this.UE[i].getIdUe()==quelUe)
                return this.UE[i];
        }throw Error("Ue que vous aviez demander n'existe pas")
    };
    this.getMoyenneSem=function () {
        return this.moyenneSem/this.UE.length;

    }
    this.getCoefficientSem=function () {
        return this.coefficientSem;

    }










}


function ue (identifiant) {

    if (arguments.length < 1  ) throw new Error("Nombre arguments insuffisants");
    if (typeof identifiant !== 'number') throw new Error("Type note invalide");
    this.id=identifiant;
    this.matieres=new Array();
    this.moyenneUE=0;
    this.coefficientUE=0;


    this.ajouterMatiere=function (matiere) {
        if (typeof matiere !== 'object')throw new Error("Type note invalide");
        this.matieres.push(matiere);
        this.moyenneUE+=matiere.getCoefficient()*matiere.getMoyenne();
        this.coefficientUE+=matiere.getCoefficient();
    };

    this.getIdUe=function () {
        return "Ue"+this.id;

    };

    this.getToutMatiere=function () {
        return this.matieres;

    };

    this.getUneMatiere=function (nomMatiere) {
        for (var i in this.matieres){
            if(this.matieres[i].getIntitule()==nomMatiere)
                return this.matieres[i] ;
        }throw Error("la matiere n'exite pas ")

    };

    this.getCoefficientUE=function () {
        return this.coefficientUE;

    }

    this.getMoyenneUE=function () {
        return this.moyenneUE/this.coefficientUE;

    };


}


/**
 * Created by Frederic on 10/04/2017.
 */
function Matiere (intitule, abreviation, coefficient) {
    if (arguments.length < 3  ) throw new Error("Nombre arguments insuffisants");
    if (coefficient < 0  ) throw new Error("Coefficient négatif");
    this.intitule = intitule.toLowerCase();
    this.abreviation = abreviation.toLowerCase();
    this.coefficient = coefficient;
    this.listeNotes = new Array();
    this.somme = 0.0;

    this.getCoefficient = function () {
        return this.coefficient;
    };

    this.getAbreviation = function () {
        return this.abreviation;
    };

    this.getIntitule = function () {
        return this.intitule;
    };

    this.getNombreNotes = function () {
        return this.listeNotes.length;
    }

    this.ajouterNotes = function (note) {
        if (typeof note !== 'number') throw new Error("Type note invalide");
        if (note  < 0 || note > 20 ) throw new  Error("Valeur note invalide");
        this.listeNotes.push(note);
        this.somme += note;
    };

    this.toString = function () {
        var out = "";
        out+= this.intitule + " ("+this.abreviation + ":"+this.coefficient +"): ";
        var first = true;
        for (var i in this.listeNotes) {
            if (! first ) {
                out += ", ";
            } else first = false;
            out += this.listeNotes[i];
        }
        return out;
    };

    this.getMoyenne = function ()  {
        var nbNotes = this.listeNotes.length;
        if (nbNotes <= 0) return 0;
        return this.somme/nbNotes;
    }
}