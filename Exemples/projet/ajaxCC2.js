'use strict';


var index=0;


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


    var keys =  ["numero","nom","prenom","ue41","ue42"];

    var nom="";
    var prenom="";
    var numero;

    var rowue41 = "";
    var rowue42 = "";
    // moyenne Ue
    var MoyUe41 = 0;
    var MoyUe42 = 0;

    // Somme des coefs par UE
    var cpt1 = 0;
    var cpt2 = 0;


    var moyenneTotal = 0;
    var ue41 = false;
    var ue42 = false;

    for (var i in keys) {

        if(i == 0){
            numero=eval("etudiant."+keys[i]);
        }
        if(i == 1) {

            nom=eval("etudiant."+keys[i]);
        }
        if(i == 2) {
            prenom=eval("etudiant."+keys[i]);

        }

        var attribut = "etudiant."+keys[i];



        if(i == 3){
            var moyenne = 0;
            var matieres = ["Pweb2","AdmSR","ProgMobile","ProgRep","CompInfo","Projet"];
            var total_coeff = 0;
            for(var j in matieres){
                var attribut2 = attribut + "." + matieres[j];
                var notes = eval(attribut2 + ".notes");
                var total = 0;
                for(var n in notes){
                    total += notes[n];
                }
                total /= notes.length;

                rowue41 += "<tr><td>" +matieres[j] + "</td><td>" + eval(attribut2 + ".coefficient") + "</td><td>" + total + "</td></tr>";
               /* if(j == 0){
                    pweb.push(total);
                }
                if(j == 1){
                    admsr.push(total);
                }
                if(j == 2){
                    pm.push(total);
                }
                if(j == 3){
                    pr.push(total);
                }
                if(j == 4){
                    ci.push(total);
                }
                if(j == 5){
                    projet.push(total);
                }*/

                var coefficient = eval(attribut2 + ".coefficient");
                total_coeff += coefficient;
                MoyUe41 += (total * coefficient);


            }
            cpt1=total_coeff;
            MoyUe41 /= total_coeff;
            if(MoyUe41 >= 8)
                ue41 = true;
            moyenneTotal += MoyUe41;

        }

        if(i == 4){
            var matieres = ["Atelier","RO","COM","Ang"];
            var total_coeff = 0;
            for(var j in matieres){
                var attribut2 = attribut + "." + matieres[j];
                var mat = eval(attribut2);
                var notes = eval(attribut2 + ".notes");
                var total = 0;
                for(var n in notes){
                    total += notes[n];
                }
                total /= notes.length;
                rowue42 += "<tr><td>" +matieres[j] + "</td><td>" + eval(attribut2 + ".coefficient") + "</td><td>" + total + "</td></tr>";

              /*  if(j == 0){
                    atelier.push(total);
                }
                if(j == 1){
                    ro.push(total);
                }
                if(j == 2){
                    com.push(total);
                }
                if(j == 3){
                    anglais.push(total);
                }*/
                var coefficient = eval(attribut2 + ".coefficient");
                total_coeff += coefficient;
                MoyUe42 += (total * coefficient);
            }
            cpt2=total_coeff;
            MoyUe42 /= total_coeff;
            if(MoyUe42 >= 8)
                ue42 = true;
            moyenneTotal += MoyUe42;
        }


    }
    root.innerHTML+="<tr class='etudiant' name='etudiantx' data-toggle='modal' data-target='.bs-example-modal-lg" + index + "'><td name='numero'>" + numero + "</td><td name='nom'>" + nom + "</td><td name='prenom'>" +prenom + "</td></tr>";
    documen

    modal.innerHTML+= "<div class='modal fade bs-example-modal-lg" + index + "' tabindex='-1' role='dialog' aria-labelledby='myLargeModalLabel'>"+
        "<div class='modal-dialog modal-lg' role='document'>" +
        "<div class='modal-content'>" +
        "<div class='container'>" +
        "<h1 class='text-center'>" +
        nom + " " + prenom +
        "</h1>" +
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
        "</tr>" +
        "</thead>" +
        "<tbody>" +
        "<tr>" +
        "<td>UE41</td>" +
        "<td>" + cpt1 + "</td>" +
        "<td>" + MoyUe41.toFixed(2) + "</td>" +
        "</tr>" +
        "<tr>" +
        "<td>UE42</td>" +
        "<td>" + cpt2 + "</td>" +
        "<td>" + MoyUe42.toFixed(2) + "</td>" +
        "</tr>" +
        "<tr>" +
        "<th>Genérale</th>" +
        "<th>" + (cpt2+cpt1) + "</th>" +
        "<th>" + (moyenneTotal/2).toFixed(2)+ "</th>" +
        "</tr>" +
        "</tbody>"+
        "</table>"+

        "</div>"+

        "</div>"+
        "</div>"+
        "</div>";


    index++;
}


function viderListe () {
    var rootListe = document.getElementById("liste des étudiants");
    rootListe.innerHTML = "";
}

function decodeJson(text) {
    var liste = JSON.parse(text);
    viderListe();
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
    initEventHandlers(bouton, 'click', function() { submitForm("listeIPI1.json"); } );
}

function initEventHandlers(element, event, fx) {
    if (element.addEventListener)
        element.addEventListener(event, fx, false);
    else if (element.attachEvent)
        element.attachEvent('on' + event, fx);
}

initEventHandlers(window, 'load', submitForm("listeIPI1.json"));