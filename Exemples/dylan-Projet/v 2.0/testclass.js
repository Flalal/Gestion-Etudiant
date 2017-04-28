'use strict';

var index=0;
var moyPromoUE41=0;
var moyPromoUE42=0;
var moyPromoG=0;

function createXhrObject(){
    if (window.XMLHttpRequest)
        return new XMLHttpRequest();

    if (window.ActiveXObject){
        var names = [ "Msxml2.XMLHTTP", "Microsoft.XMLHTTP",
            "Msxml2.XMLHTTP.6.0", "Msxml2.XMLHTTP.3.0"];
        for(var i in names){
            try{ return new ActiveXObject(names[i]); }
            catch(e){}
        }
    }
    window.alert("pas de prise en charge de XMLHTTPRequest.");
    return null; // non supporte
}

function rechercher(){
    var mot = document.getElementById("recherche").value;
    var nom = document.getElementsByName("nom");
    var prenom = document.getElementsByName("prenom");
    var lignes = document.getElementsByName("etudiant");

    for(var i = 0; i<nom.length; i++){
        if((nom[i].innerHTML + prenom[i].innerHTML).toUpperCase().indexOf(mot.toUpperCase()) == -1){
            lignes[i].style.display = "none";
        }
        else{
            lignes[i].style.display = "table-row";
        }
    }
}

function affichage_initial() {
    var lignes = document.getElementsByName("etudiant");
    var en_cours;

    for (en_cours = 0; en_cours < lignes.length; en_cours++)
    {
        lignes[en_cours].style.display = "table-row";
    }
}

function ajouterEtudiant (etudiant,num) {
    var root = document.getElementById("liste des étudiants");
    var modal = document.getElementById('listemodal');
    var photo = document.getElementById('mettrePhoto');
    var modal2 = document.getElementById('diplome');

    var keys =  ["numero","nom","prenom","ue","departement","dateNaissance","bac"];
    var bac;
    var dateN;
    var dept;

    var nom;
    var prenom;
    var numero;
// creation des ue et du semestre
    var semestre=new Semestre(4,2017);
    var ue41class=new ue(41);
    var ue42class=new ue(42);

    var rowue41 = "";
    var rowue42 = "";
    // moyenne Ue

    // Somme des coefs par UE

    var ue41 = false;
    var ue42 = false;

    for (var i in keys) {

        if(i == 0){numero=eval("etudiant."+keys[i]);}
        if(i == 1) {nom=eval("etudiant."+keys[i]);}
        if(i == 2) {prenom=eval("etudiant."+keys[i]);}
        if(i == 4) {dept=eval("etudiant."+keys[i]);}
        if(i == 5) {dateN=eval("etudiant."+keys[i]);}
        if(i == 6) {bac=eval("etudiant."+keys[i]);}
        //if(i == 7) {avatar=eval("etudiant."+keys[i]);}

        var attribut = "etudiant."+keys[i];

        if(i == 3) {
            for (var x in eval(attribut)) {
                if (x == 'ue41') {


                    var moyenne = 0;
                    var matieres = ["Pweb2", "AdmSR", "ProgMobile", "ProgRep", "CompInfo", "Projet"];
                    for (var j in matieres) {
                        var attribut2 = attribut + "." + x + "." + matieres[j];
                        var matiere=new Matiere(matieres[j],matieres[j],eval(attribut2 + ".coefficient"));
                        var notes = eval(attribut2 + ".notes");
                        for (var n in notes) {
                            matiere.ajouterNotes(notes[n]);

                        }
                        ue41class.ajouterMatiere(matiere);

                        rowue41 += "<tr><td>" + matiere.getIntitule() + "</td><td>" + matiere.getCoefficient() + "</td><td>" + matiere.getMoyenne() + "</td></tr>";


                    }

                    semestre.ajouterUe(ue41class);
                    if (ue41class.getMoyenneUE() >= 8)
                        ue41 = true;

                }
                else if (x == "ue42") {
                    var matieres = ["Atelier", "RO", "COM", "Ang"];
                    var total_coeff = 0;
                    for (var j in matieres) {
                        var attribut2 = attribut + "." + x + "." + matieres[j];
                        var matiere=new Matiere(matieres[j],matieres[j],eval(attribut2 + ".coefficient"))
                        var mat = eval(attribut2);
                        var notes = eval(attribut2 + ".notes");
                        for (var n in notes) {
                            matiere.ajouterNotes(notes[n]);
                        }
                        ue42class.ajouterMatiere(matiere);
                        rowue42 += "<tr><td>" + matiere.getIntitule() + "</td><td>" + matiere.getCoefficient() + "</td><td>" + matiere.getMoyenne() + "</td></tr>";

                    }
                    semestre.ajouterUe(ue42class);
                    if (ue42class.getMoyenneUE() >= 8)
                        ue42 = true;


                }
            }
        }


    }

    var test=new Etudiant(numero,nom,prenom,dept,dateN,bac);
    test.ajouterSemestre(semestre);

    moyPromoG+=semestre.getMoyenneSem();
    moyPromoUE41+=ue41class.getMoyenneUE();
    moyPromoUE42+=ue42class.getMoyenneUE();

    root.innerHTML+="<tr class='etudiant' name='etudiant' ><td name='numero'>" + numero
        + "</td> <td name='nom'>" + nom
        + "</td><td name='prenom'>" +prenom + "</td> "
        +"<td ><button class='btn btn-success' data-toggle='modal' data-target='.bs-example-modal-sm" + index + "'>Photo étudiant</button></td>"
        +"<td><button class='btn btn-success' data-toggle=\"modal\" data-target='.bs-example-modal-lg"+ index+"'>Fiche étudiant</button> </td>";


	photo.innerHTML+="<div class='modal fade bs-example-modal-sm" + index + "' tabindex='-1' role='dialog' aria-labelledby='mySmallModalLabel'>"+ 
				"<div class='modal-dialog modal-sm' role='document'>"+
	   				" <div class='modal-content'>"+
	    					 "<img c src='img/"+ test.avatar+"' width='130px' height='150px' style='float: right;' alt='"+test.avatar +"'/>"+
         "<ul> <li>"+test.getNom() + " " + test.getPrenom()+ " </li><li>Dipl: " +test.getBac()+"</li><li>Né le: " +test.getDateNaissance()+"</li></ul>"+
					"</div>"+
				" </div>"+
			" </div>";

    modal.innerHTML+= "<div class='modal fade bs-example-modal-lg" + index + "' tabindex='-1' role='dialog' aria-labelledby='myLargeModalLabel'>"+
        "<div class='modal-dialog modal-lg' role='document'>" +
        "<div class='modal-content'>" +
        "<div class='container'>" +
        "<h1 class='text-center'>" +
         test.getNom() + " " + test.getPrenom() +
        "</h1>" +
            "<img c src='img/"+ test.avatar+"' width='130px' height='130px' style='float: right;' alt='"+test.avatar +"'/>"+
        "<p> infomation complementaire: </p>"+
        "<ul> <li> Diplôme : "+ test.getBac()+"</li>"+
        "<li> Date de naissance "+ test.getDateNaissance()+"</li></ul>"+
        "<h2>UE41</h2>" +

        "<table class='table'>" +
        "<thead>" +
        "<tr>" +
        "<th>Matière</th>" +
        "<th>Coef</th>" +
        "<th>Note</th>" +
        "</tr>" +
        "</thead>" +
        "<tbody>" +
        rowue41 +
        "</tbody>"+
        "</table>"+


        "<h2>UE42</h2>" +

        "<table class='table'>" +
        "<thead>" +
        "<tr>" +
        "<th>Matière</th>" +
        "<th>Coef</th>" +
        "<th>Note</th>" +
        "</tr>" +
        "</thead>" +
        "<tbody>" +
        rowue42 +
        "</tbody>"+
        "</table>"+

        "<h2>Moyenne</h2>" +

        "<table class='table'>" +
        "<thead>" +
        "<tr>" +
        "<th>UE</th>" +
        "<th>Coef</th>" +
        "<th>Résultat</th>" +
        "<th>Résultat de la Promo</th>" +
        "</tr>" +
        "</thead>" +
        "<tbody>" +
        "<tr>" +
        "<td>UE41</td>" +
        "<td>" + ue41class.getCoefficientUE()+ "</td>" +
        "<td>" + ue41class.getMoyenneUE().toFixed(2) + "</td>" +
        "<td name='promoUe41'> </td>" +
        "</tr>" +
        "<tr>" +
        "<td>UE42</td>" +
        "<td>" + ue42class.getCoefficientUE()+ "</td>" +
        "<td>" + ue42class.getMoyenneUE().toFixed(2) + "</td>" +
        "<td name='promoUe42'> </td>" +
        "</tr>" +
        "<tr>" +
        "<th>Genérale</th>" +
        "<th>" + semestre.getCoefficientSem()+ "</th>" +
        "<th>" + semestre.getMoyenneSem().toFixed(2)+ "</th>" +
        "<th name='PromoG'></th>" +
        "</tr>" +
        "</tbody>"+
        "</table>"+

        "</div>"+

        "</div>"+
        "</div>"+
        "</div>";


    index++;
    affichePromo();
}


function affichePromo() {
    var tmp=document.getElementsByName('promoUe41');

    for(var i=0;i<tmp.length;i++)
        tmp[i].innerHTML=(moyPromoUE41/index).toFixed(2);

    var tmp2=document.getElementsByName('promoUe42');
    for(var j=0;j<tmp2.length;j++)
        tmp2[j].innerHTML=(moyPromoUE42/index).toFixed(2);

    var tmp3=document.getElementsByName('PromoG');
    for(var  x=0;x<tmp3.length;x++) {
        tmp3[x].innerHTML = (moyPromoG / index).toFixed(2);
    }
}

function viderListe () {
    var rootListe = document.getElementById("liste des étudiants");
    rootListe.innerHTML = "";
}

function decodeJson(text) {
    var liste = JSON.parse(text);
    viderListe();
    index=0;
    moyPromoUE41=0;
    moyPromoUE42=0;
    moyPromoG=0;

    var i =0;
    for (var id in liste)  {
        ajouterEtudiant(liste[id],i);
        i++;
    }
}

function submitForm(file) {
    var req = createXhrObject();
    if (null == req) return ;
    req.onreadystatechange = function() {
        if(req.readyState == 4){
            if(req.status == 200){
                decodeJson(req.responseText);
            } else {
                alert("Error: returned status code " + req.status + " " + req.statusText);
            }
        }
    };
    req.open("GET", file, true);
    req.send(null);
} // submitForm

function initButton() {
    var bouton = document.getElementById('button');
    initEventHandlers(bouton, 'click', function() { submitForm("liste.json"); } );
}

function initEventHandlers(element, event, fx) {
    if (element.addEventListener)
        element.addEventListener(event, fx, false);
    else if (element.attachEvent)
        element.attachEvent('on' + event, fx);
}

initEventHandlers(window, 'load', submitForm("liste.json"));/**
 * Created by Frederic on 11/04/2017.
 */