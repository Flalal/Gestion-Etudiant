/**
 * Created by Frederic on 13/04/2017.
 */
'use strict';


var index=0;

var promo;


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

    var keys = ["numero", "nom", "prenom", "departement", "dateNaissance", "bac", "ue"];
    var bac;
    var dateN;
    var dept;

    var nom;
    var prenom;
    var numero;
// creation des ue et du semestre
    var semestre = new Semestre(4, 2017);
    var tabUe = new Array();
    var tabSem = new Array();
    for (var cpt = 0; cpt < 4; cpt++) {
        tabSem.push(new Semestre(cpt+1, 2017));
    }



    // intituler du tableau
    var promoTab="";
    var intituler = "";
    var moyenne = "";
    var coeff = "";
    var classement="";
    var nbUE = 0;

    var ue41 = false;
    var ue42 = false;

    for (var i in keys) {

        if (i == 0) {
            numero = eval("etudiant." + keys[i]);
        }
        if (i == 1) {
            nom = eval("etudiant." + keys[i]);
        }
        if (i == 2) {
            prenom = eval("etudiant." + keys[i]);
        }
        if (i == 3) {
            dept = eval("etudiant." + keys[i]);
        }
        if (i == 4) {
            dateN = eval("etudiant." + keys[i]);
        }
        if (i == 5) {
            bac = eval("etudiant." + keys[i]);
        }
        //if(i == 7) {avatar=eval("etudiant."+keys[i]);}

        var attribut = "etudiant." + keys[i];

        if (i == 6) {

            for (var x in UEDEPT) {/// parcours la constant qui reference tout les ue de chaque département;
                for (var j in eval(attribut)) {// parcours dans les ue
                    if (UEDEPT[x][0] == (dept + "_" + j)) {//condition si ue existe  dans le json
                        var numSem = parseInt(j.slice(2)[0]) - 1;
                        var numue = tabUe.push(new ue(parseInt(j.slice(2))));
                        nbUE++;
                        for (var matiere in UEDEPT[x][1]) {
                            var attribut2 = attribut + "." + j + "." + UEDEPT[x][1][matiere];
                            var matiereclass = new Matiere(UEDEPT[x][1][matiere], UEDEPT[x][1][matiere], eval(attribut2 + ".coefficient"));
                            var notes = eval(attribut2 + ".notes");
                            for (var n in notes) {
                                matiereclass.ajouterNotes(notes[n]);

                            }
                            nbUE += numue;
                            tabUe[numue - 1].ajouterMatiere(matiereclass);

                        }

                        semestre.ajouterUe(tabUe[numue - 1]);
                        tabSem[numSem].ajouterUe(tabUe[numue - 1]);
                    }
                }
            }
            tabSem.reverse();
            for (var j in tabSem) {
                if (tabSem[j].getToutUE().length != 0) {
                    intituler+="<td>S"+tabSem[j].getSemestre()+"</td>";
                    promoTab+="<td name='PS"+tabSem[j].getSemestre()+"'></td>";
                    moyenne+="<td>"+tabSem[j].getMoyenneSem().toFixed(2)+"</td>";
                    coeff+="<td>"+tabSem[j].getCoefficientSem()+"</td>";
                    classement+="<td id='CS"+tabSem[j].getSemestre()+numero+"'></td>";
                    for (var tmpUe in tabSem[j].getToutUE()) {
                        intituler+="<td>Ue"+tabSem[j].getToutUE()[tmpUe].getIdUe()+"</td>";
                        promoTab+="<td name='PUe"+tabSem[j].getToutUE()[tmpUe].getIdUe()+"'></td>"
                        moyenne+="<td>"+tabSem[j].getToutUE()[tmpUe].getMoyenneUE().toFixed(2)+"</td>";
                        coeff+="<td>"+tabSem[j].getToutUE()[tmpUe].getCoefficientUE()+"</td>";
                        classement+="<td id='CUe"+tabSem[j].getSemestre()+numero+"'></td>";



                    }

                }
            }


        }


    }


    var test=new Etudiant(numero,nom,prenom,dept,dateN,bac);
    for (var cpt = 0; cpt < 4; cpt++) {
       test.ajouterSemestre(tabSem[cpt]);
    }
    promo.ajouterEtudiant(test);




    root.innerHTML+="<tr class='etudiant' name='etudiant' ><td name='numero'>" + numero
        + "</td> <td name='nom'>" + nom
        + "</td><td name='prenom'>" +prenom + "</td> "
        +"<td ><button class='btn btn-success' data-toggle='modal' data-target='.bs-example-modal-sm" + index + "'>Photo étudiant</button></td><td><button class='btn btn-success' data-toggle=\"modal\" data-target='.bs-example-modal-lg"+ index+"'>Fiche étudiant</button> </td></tr>";

    photo.innerHTML+="<div class='modal fade bs-example-modal-sm" + index + "' tabindex='-1' role='dialog' aria-labelledby='mySmallModalLabel'>"+
        "<div class='modal-dialog modal-sm' role='document'>"+
        " <div class='modal-content'>"+
        "<img c src='img/"+ test.avatar+"' width='130px' height='130px' style='float: right;' alt='"+test.avatar +"'/>"+
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

        "<table class='table'>" +
        "<thead>" +
        "<tr id='TittreTableau'>" +
        "<th></th>" +
            intituler +
        "</tr>" +
        "</thead>" +
        "<tbody>" +
        "<tr id='Coeff"+index+"'>  <td>Coefficent</td>"+coeff+"</tr>"+
        "<tr id='Moyenne"+index+"'>  <td>Moyenne</td> "+
            moyenne+
        "</tr>"+
        "<tr name='MoyennePromo'>  <td>Moyenne Promo</td> "+
            promoTab +
        "</tr>"+
        "<tr id='Classement"+index+"'>  <td>Classement</td>"+
            classement+
        "</tr>"+
        "<tr name='TaillePromo'>  <td>Taille Promo</td> <td name='nombreEtu'></td></tr>"+
        "</tbody>"+
        "</table>"+

        "</tbody>"+
        "</table>"+

        "</div>"+

        "</div>"+
        "</div>"+
        "</div>";


    index++;

    for (var j in tabSem) {
        if (tabSem[j].getToutUE().length != 0) {
           var SemPromo=document.getElementsByName("PS"+tabSem[j].getSemestre());
           for (var cptSem=0;cptSem<SemPromo.length;cptSem++){
               SemPromo[cptSem].innerHTML=(promo.getMoySemPromo(tabSem[j].getSemestre())).toFixed(2);
               var ToutEtudiants=promo.getToutLesEtudiants();
               for (var etu in ToutEtudiants){
                   var SemClass=document.getElementById("CS"+tabSem[j].getSemestre()+numero);
                   SemClass.innerHTML=promo.Promoclassement(1,numero);
                   console.log("");
               }
           }
           for (var tmpUe in tabSem[j].getToutUE()) {
                var UePromo=document.getElementsByName("PUe"+tabSem[j].getToutUE()[tmpUe].getIdUe());
              // console.log(promo.getMoyenneUEPromo(tabSem[j].getToutUE()[tmpUe].getIdUe()));
               for (var cptUe=0;cptUe<UePromo.length;cptUe++){
                   UePromo[cptUe].innerHTML=(promo.getMoyenneUEPromo(tabSem[j].getToutUE()[tmpUe].getIdUe())).toFixed(2);
               }

           }

        }
    }
    var NbEtudiant=document.getElementsByName("nombreEtu");
    for (var cptNbEtu=0;cptNbEtu<NbEtudiant.length;cptNbEtu++){
        NbEtudiant[cptNbEtu].innerHTML=promo.getnbEtudiants();
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
    promo=new Promo();
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
