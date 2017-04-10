'use strict';

var pweb = [];
var admsr = [];
var pm = [];
var pr = [];
var ci = [];
var projet = [];
var atelier = [];
var ro= [];
var com= [];
var anglais = [];

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


function afficherNotesUE41(id){
    var notes = "Pweb2 : " + pweb[id] + "\nAdmSR : " + admsr[id] + "\nProgMobile : " + pm[id] + "\nProgRep : " + pr[id] + "\nCompInfo : " + ci[id] + "\nProjet : " + projet[id];
    alert(notes);
}

function afficherNotesUE42(id){
    var notes = "Atelier :" + atelier[id]+"\nRO : " + ro[id]+"\nCOM :" + com[id]+"\nAnglais :" + anglais[id];
    alert(notes);
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
    var nodeEtudiant = document.createElement("tr");
    nodeEtudiant.setAttribute("name","etudiant");
    var keys =  ["numero","nom","prenom","ue41","ue42"];

    var moyenneTotal = 0;
    var ue41 = false;
    var ue42 = false;

    for (var i in keys) {
        console.log(i);
        var element = document.createElement("td");

        if(i == 0){
            element.setAttribute("name","numero");
        }
        if(i == 1)
            element.setAttribute("name","nom");
        if(i == 2)
            element.setAttribute("name","prenom");

        var attribut = "etudiant."+keys[i];
        element.innerHTML = eval(attribut);
        nodeEtudiant.appendChild(element);

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
                if(j == 0){
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
                }

                var coefficient = eval(attribut2 + ".coefficient");
                total_coeff += coefficient;
                moyenne += (total * coefficient);
            }
            moyenne /= total_coeff;
            if(moyenne >= 8)
                ue41 = true;
            moyenneTotal += moyenne;
            initEventHandlers(element,"click",function(){afficherNotesUE41(num);});
            element.innerHTML = moyenne.toFixed(2);
            nodeEtudiant.appendChild(element);
        }

        if(i == 4){
            var moyenne = 0;
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
                if(j == 0){
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
                }
                var coefficient = eval(attribut2 + ".coefficient");
                total_coeff += coefficient;
                moyenne += (total * coefficient);
            }
            moyenne /= total_coeff;
            if(moyenne >= 8)
                ue42 = true;
            moyenneTotal += moyenne;
            initEventHandlers(element,"click",function(){afficherNotesUE42(num);});
            element.innerHTML = moyenne.toFixed(2);
            nodeEtudiant.appendChild(element);
        }
    }
    var element = document.createElement("td");
    var moyenne = (moyenneTotal / 2).toFixed(2);
    element.innerHTML = moyenne;
    nodeEtudiant.appendChild(element);

    if(moyenne >= 10 && ue41 && ue42){
        var element = document.createElement("td");
        element.innerHTML = "Oui";
        element.style.color = "green";
        nodeEtudiant.appendChild(element);
    }else{
        var element = document.createElement("td");
        element.innerHTML = "Non";
        element.style.color = "red";
        nodeEtudiant.appendChild(element);
    }
    root.appendChild(nodeEtudiant);
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
    initEventHandlers(bouton, 'click', function() { submitForm("ipi2.json"); } );
}

function initEventHandlers(element, event, fx) {
    if (element.addEventListener)
        element.addEventListener(event, fx, false);
    else if (element.attachEvent)
        element.attachEvent('on' + event, fx);
}

initEventHandlers(window, 'load', submitForm("ipi2.json"));