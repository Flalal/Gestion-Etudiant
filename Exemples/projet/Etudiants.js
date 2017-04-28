/**
 * Created by Frederic on 10/04/2017.
 */

var TabDepartement=['INFO','MMI','GEA','TC'];
function Promo() {
    this.etu=new Array;
    this.semetres=[0,0,0,0];
    this.ues =new Array;
    this.nbEtudiant=0;
    this.matieres=new Array;

    this.ajouterEtudiant=function (etudiant) {
        if (typeof etudiant !== 'object')throw new Error("Type note invalide");
        this.etu.push(etudiant);
        this.nbEtudiant++;
        var ToutSemestres=etudiant.getToutSemestre();
        for (var se in ToutSemestres){
            if(ToutSemestres[se].getToutUE().length!=0) {
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

    this.Promoclassement=function (intituler,numeroEtu) {
        var dut=new Array;
        for (var tmpetu in this.etu ){
            dut[tmpetu]=new Array();
            var ToutSemestres=this.etu[tmpetu].getToutSemestre();
            for (var se in ToutSemestres) {
                if (ToutSemestres[se].getToutUE().length != 0) {
                    if (ToutSemestres[se].getSemestre() == intituler) {
                        dut[tmpetu][0]=this.etu[tmpetu].getNumero();
                        dut[tmpetu][1] = ToutSemestres[se].getMoyenneSem();
                        break;
                    }

                    var ToutUE=ToutSemestres[se].getToutUE();
                    for (var ue in ToutUE ) {
                        if (ToutUE[ue].getIdUe()==intituler){
                            dut[tmpetu][0]=this.etu[tmpetu].getNumero();
                            dut[tmpetu][1] = ToutUE[ue].getMoyenneUE();
                            break;
                        }
                        var ToutMatiere=ToutUE[ue].getToutMatiere();
                        for( var matiere in ToutMatiere){
                            if(ToutMatiere[matiere].getIntitule()+ToutUE[ue].getIdUe()==intituler){
                                dut[tmpetu][0]=this.etu[tmpetu].getNumero();
                                dut[tmpetu][1]=ToutMatiere[matiere].getMoyenne();

                            }
                        }

                    }

                }
            }


        }

        for(var tmpdut in dut ){
            for (var tmpdut2=0; tmpdut2<dut.length-1;tmpdut2++){
                if (dut[tmpdut2][1]<dut[tmpdut2+1][1]){
                    var tmp=dut[tmpdut2+1];
                    dut[tmpdut2+1]=dut[tmpdut2];
                    dut[tmpdut2]=tmp;

                }
            }
        }

        for(var tmpdut3=0; tmpdut3<dut.length;tmpdut3++ ){
            if (dut[tmpdut3][0]==numeroEtu)
                return tmpdut3+1;
        }




    };








}
function compare(a, b) {
    if (a<b)
    return 1;
    if (a>b)
    return -1;
    // a doit être égal à b
    return 0;
}




function Etudiant (numero, nom, prenom, dept,dateN,bac) {

    if (arguments.length < 6) throw new Error("Nombre arguments insuffisants");
    this.nom = nom.toLowerCase();
    this.prenom = prenom.toLowerCase();
    var tmp=false;
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



function Semestre(num,annee) {
    if (arguments.length < 2 ) throw new Error("Nombre arguments insuffisants");
    if (typeof num !== 'number' && typeof annee !== 'number') throw new Error("Type note invalide");
    this.id=num;
    this.annee=annee;
    this.UE=new Array();
    this.moyenneSem=0;
    this.coefficientSem=0;
    this.SemestreValde=true;

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
       if (! uejenesaispas.ValidationUe()){
           this.SemestreValde=false;
       }
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
    this.getMoyenneSem=function () {
        return this.moyenneSem/this.UE.length;

    };
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
    this.tauxAbsent=0;
    this.coefficientUE=0;
    this.valideAbs=true;


    this.ajouterMatiere=function (matiere) {// function ajouter une matiere et permet de calculer la moyenne de ue et coeff
        if (typeof matiere !== 'object')throw new Error("Type note invalide");
        this.matieres.push(matiere);
        this.moyenneUE+=matiere.getCoefficient()*matiere.getMoyenne();
        this.coefficientUE+=matiere.getCoefficient();
        if(matiere.getTauxAbsent()>=10)
            this.valideAbs=false;
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
    this.tauxAbsent=0;

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
    };
    this.getTauxAbsent=function () {
        return this.tauxAbsent;
    };
    this.setTauxAbsent=function (taux) {
        if (typeof taux !== 'number')throw new Error("Type note invalide");
        this.tauxAbsent=taux;

    };

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