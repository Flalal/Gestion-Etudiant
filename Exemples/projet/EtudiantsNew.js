/**
 * Created by Frederic on 20/06/2017.
 */
/**
 * Created by Frederic on 10/04/2017.
 */

var TabDepartement=['INFO','MMI','GEA','TC'];
function Promo() {
    this.etu=new Array;
    this.semetres=[0,0,0,0];
    this.annee=[0,0];
    this.ues =new Array;
    this.nbEtudiant=0;
    this.matieres=new Array;

    this.ajouterEtudiantCLA=function (etudiant) {
        if (typeof etudiant !== 'object')throw new Error("Type note invalide");
        this.etu.push(etudiant);
        this.nbEtudiant++;
        var ToutSemestres=etudiant.getToutSemestre();
        for (var se in ToutSemestres){
            if(ToutSemestres[se].getToutUE().length!=0) {
                if (ToutSemestres[se].getSemestre()>2){

                    this.annee[1]+=ToutSemestres[se].getMoyenneSem();
                }
                else {
                    this.annee[0] += ToutSemestres[se].getMoyenneSem();
                }

                this.semetres[ToutSemestres[se].getSemestre() - 1] += ToutSemestres[se].getMoyenneSem();
                var ToutUE=ToutSemestres[se].getToutUE();
                for (var ue in ToutUE ){
                    if (this.ues[ToutUE[ue].getIdUe()]!=undefined) {
                        this.ues[ToutUE[ue].getIdUe()] += ToutUE[ue].getMoyenneUE();
                    }
                    else{
                        this.ues[ToutUE[ue].getIdUe()]=ToutUE[ue].getMoyenneUE();
                    }
                    var ToutMatieres=ToutUE[ue].getToutMatiere();
                    for(var i in ToutMatieres) {
                        if (this.matieres[ToutMatieres[i].getIntitule()] != undefined) {
                            this.matieres[ToutMatieres[i].getIntitule()] += ToutMatieres[i].getMoyenne();
                        }
                        else {
                            this.matieres[ToutMatieres[i].getIntitule()] = ToutMatieres[i].getMoyenne();
                        }
                    }
                }
            }
        }
    };
    this.getToutLesEtudiants=function () {
        return this.etu;

    };


    this.getMoyDeptPromo=function (num) {
        return this.annee[num-1]/(this.nbEtudiant*2);

    };

    this.getMoySemPromo=function (num) {
        return this.semetres[num-1]/this.nbEtudiant;

    };
    this.getMoyenneUEPromo=function (num) {
        if (this.ues[num]==undefined)
            return null;
        else
            return this.ues[num]/this.nbEtudiant;

    };

    this.moyennePromoCours=function(cours){
        if(this.matieres[cours]==undefined)
            return null;
        else
            return this.matieres[cours]/this.nbEtudiant;
    };
    this.getnbEtudiants=function () {
        return this.nbEtudiant;
    };

}
function cleanArray(array) {// permet de surprimmer le doublon
    var i, j, len = array.length, out = [], obj = {};
    for (i = 0; i < len; i++) {
        var numero=array[i][1];
        if(obj[parseFloat(numero.toFixed(2))]==undefined)
            obj[parseFloat(numero.toFixed(2))] =array[i][0];
        else
            obj[parseFloat(numero.toFixed(2))] +="_"+ array[i][0];
    }
    for (j in obj) {
        out.push([obj[j],j]);
    }
    return out;
}


function compare(a, b) {
    if (a<b)
        return 1;
    if (a>b)
        return -1;
    // a doit être égal à b
    return 0;
}




function Etudiant (numero, nom, prenom, dept,groupe,dateN,bac) {

    if (arguments.length < 7) throw new Error("Nombre arguments insuffisants");
    this.nom = nom.toLowerCase();
    this.prenom = prenom.toLowerCase();
    var tmp=false;
    this.groupe=groupe;
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
    this.numero = numero;



    this.getAvatar=function () {
        return this.avatar;

    };

    this.getGroupe=function () {
        return this.groupe;
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



function Semestre(num,annee,coefficient,moyenne,classement,moyennePromo,min,max) {
    if (arguments.length < 8 ) throw new Error("Nombre arguments insuffisants");
    this.id=num;
    this.annee=annee;
    this.UE=new Array();
    this.coefficientSem=coefficient;
    this.SemestreValde=true;
    this.classement=classement;

    if (verifierNote(moyennePromo) && verifierNote(moyenne) && verifierNote(min) && verifierNote(max)) {
        this.moyenneMax=max;
        this.moyenneMin=min;
        this.moyenneSem=moyenne;
        this.moyennePromo = moyennePromo;
    }

    this.getAnnee=function () {
        return this.annee;
    };
    this.getSemestre=function () {
        return this.id;

    };

    this.ajouterUe=function (uejenesaispas) {

        if (typeof uejenesaispas  !== 'object')throw new Error("Type note invalide");
        this.UE.push(uejenesaispas);
    };

    this.validationSemestre=function () {
        return (this.SemestreValde && (this.moyenneSem/this.UE.length) >=10);
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
    this.getMoyennePromoS=function () {
        return this.moyennePromo;

    };
    this.getMoyenneMaxS=function () {
        return this.moyenneMax;
    };
    this.getMoyenneMinS=function () {
        return this.moyenneMin;
    };
    this.getMoyenneSem=function () {
        return this.moyenneSem;

    };
    this.getCoefficientSem=function () {
        return this.coefficientSem;

    };
    this.getClassementS=function () {
        return this.classement

    };










}


function ue (identifiant,annee, coefficient,moyenne,classement,moyennePromo,min,max) {

    if (arguments.length < 8  ) throw new Error("Nombre arguments insuffisants");
    this.id=identifiant;
    this.matieres=new Array();
    this.tauxAbsent=0;
    this.coefficientUE=coefficient;
    this.valideAbs=true;
    this.commentaire="";
    this.annee=annee;
    this.redoubler=false;
    this.classement=classement;

    if (verifierNote(moyennePromo) && verifierNote(moyenne) && verifierNote(min) && verifierNote(max)) {
        this.moyenneMax=max;
        this.moyenneMin=min;
        this.moyenneUE=moyenne;
        this.moyennePromo = moyennePromo;
    }

    this.ajouterCommentaire=function(comment){
        if(typeof comment!='string')throw new Error("Type commentaire invalide");
        this.commentaire=comment;
    };
    this.getCommentaire=function(){
        return this.commentaire;
    };
    this.setRedoubler=function (valeur) {

        this.redoubler=valeur;

    };
    this.getRedoubler=function () {
        return this.redoubler;
    };

    this.ajouterMatiere=function (matiere) {// function ajouter une matiere et permet de calculer la moyenne de ue et coeff
        if (typeof matiere !== 'object')throw new Error("Type note invalide");
        this.matieres.push(matiere);

    };

    /**
     * @return {boolean}
     */
    this.ValidationUe=function () {

        return this.valideAbs && (this.moyenneUE/this.coefficientUE)>=8 && this.tauxAbsent<10;

    } ;

    this.getIdUe=function () {
        return this.id;

    };

    this.getTauxAbsent=function () {
        return this.tauxAbsent;
    };
    this.setTauxAbsent=function (taux) {
        if (typeof taux !== 'number')throw new Error("Type note invalide");
        this.tauxAbsent=taux;

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

    };
    this.getMoyennePromoUE=function () {
        return this.moyennePromo;

    };
    this.getClassementUE=function () {
        return this.classement

    };
    this.getMoyenneMaxUE=function () {
        return this.moyenneMax;
    };
    this.getMoyenneMinUE=function () {
        return this.moyenneMin;
    };

    this.getMoyenneUE=function () {
        return this.moyenneUE;

    };


}
function verifierNote(note) {
    if (typeof note !== 'number') {
        throw new Error("Type note invalide");
        return false
    }
    if (note  <= 0 || note >= 20 ) {
        throw new  Error("Valeur note invalide");
        return false;
    }


    return true

}

/**
 * Created by Frederic on 10/04/2017.
 */
function Matiere (intitule, abreviation, coefficient,moyenne,classement,moyennePromo,min,max) {
    if (arguments.length < 6) throw new Error("Nombre arguments insuffisants");
    if (coefficient < 0  ) throw new Error("Coefficient négatif");
    if (verifierNote(moyennePromo) && verifierNote(moyenne) && verifierNote(min) && verifierNote(max)) {
        this.moyenneMax=max;
        this.moyenneMin=min;
        this.moyenneMatiere=moyenne;
        this.moyennePromoM = moyennePromo;
    }
    this.intitule = intitule;
    this.abreviation = abreviation;
    this.coefficient = coefficient;
    this.tauxAbsent=0;
    this.classement=classement;


    this.getClassementM=function () {
        return this.classement

    };
    this.getCoefficient = function () {
        return this.coefficient;
    };


    this.getAbreviation = function () {
        return this.abreviation;
    };

    this.getIntitule = function () {
        return this.intitule;
    };


    this.getTauxAbsent=function () {
        return this.tauxAbsent;
    };
    this.setTauxAbsent=function (taux) {
        if (typeof taux !== 'number')throw new Error("Type note invalide");
        this.tauxAbsent=taux;

    };
    this.getMoyennePromoM=function () {
        return this.moyennePromoM;

    };
    this.getMoyenneMaxM=function () {
        return this.moyenneMax;
    };
    this.getMoyenneMinM=function () {
        return this.moyenneMin;
    };

    this.getMoyenne = function ()  {
        return this.moyenneMatiere;
    }
}
