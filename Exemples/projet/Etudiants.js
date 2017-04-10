/**
 * Created by Frederic on 10/04/2017.
 */

function Etudiant (numero, nom, prenom ) {

    if (arguments.length < 3  ) throw new Error("Nombre arguments insuffisants");
    this.nom = nom.toLowerCase();
    this.prenom = prenom.toLowerCase();
    this.listematieres=new Array();

    if (0 < numero )
        this.numero = numero;
    else this.numero = -1;

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
    }

    this.getMatieres= function () {
        return this.listematieres;

    }

    this.ajouterMatiere=function(nom,ab,coeff){
        this.listematieres.push(new Matiere(nom,ab,coeff));
    }
    this.getUneMatiere=function (nom) {
        for(var i in this.listematieres){
            if (i.getIntitule()== nom){
                return i;
            }
        }
        return null;

    }
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