  'use strict';

$(document).ready(function(){

    $('[data-toggle="popover"]').popover({ html : true });

});

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

function ajouterEtudiant (etudiant) {
    var root = document.getElementById("liste des étudiants");
    var nodeEtudiant = document.createElement("tr");
    var keys =  ["numero","nom","prenom"];
    for (var i in keys) {
        var element = document.createElement("td");
        var attribut = "etudiant."+keys[i];
        element.innerHTML = eval(attribut);
        nodeEtudiant.appendChild(element);

    }

    // UE41

    var MatieresUE41 =  ["Pweb2","AdmSR","ProgMobile","ProgRep","CompInfo","Projet"];
    var resMatiere = 0;
    var element2 = document.createElement("td");
    var ttcoef=0;

    var details = "";

    for(var j in MatieresUE41)
    {

        var notesUE41 = "etudiant.ue41."+MatieresUE41[j]+".notes";
        var coeffUE41 = "etudiant.ue41."+MatieresUE41[j]+".coefficient";
        ttcoef += parseFloat(eval(coeffUE41));
        resMatiere += parseFloat(eval(notesUE41))*parseFloat(eval(coeffUE41));
        details += "<b>" + MatieresUE41[j] + "</b>" + " : " + Math.round(  (parseFloat(eval(notesUE41)))  *100)/100 + " <br /> ";

    }

    var bouton = document.createElement("button");
    bouton.setAttribute("type","text");
    bouton.setAttribute("data-toggle","popover");
    bouton.setAttribute("title","Détail des notes");
    bouton.setAttribute("data-content",details);
    bouton.setAttribute("data-placement","left");
    var value = Math.round(  (resMatiere / ttcoef)  *100)/100;
    bouton.innerHTML = value;
    if(value<8){
        bouton.setAttribute("class","btn btn-danger btn-block");
    }
    else if(value<10){
        bouton.setAttribute("class","btn btn-warning btn-block");
    }
    else
        bouton.setAttribute("class","btn btn-success btn-block");

    element2.appendChild(bouton);

    nodeEtudiant.appendChild(element2);


    // UE42

    var MatieresUE42 =  ["Atelier","RO","COM","Ang"];
    var resMatiere2 = 0;
    var element3 = document.createElement("td");
    var ttcoef2=0;
    var details2 = "";

    for(var k in MatieresUE42)
    {
        var notesUE42 = "etudiant.ue42."+MatieresUE42[k]+".notes";
        var coeffUE42 = "etudiant.ue42."+MatieresUE42[k]+".coefficient";
        ttcoef2 += parseFloat(eval(coeffUE42));
        resMatiere2 += parseFloat(eval(notesUE42))*parseFloat(eval(coeffUE42));
        details2 += "<b>" + MatieresUE42[k] + "</b>" + " : " + Math.round(  (parseFloat(eval(notesUE42)))  *100)/100 + " <br /> ";

    }

    var bouton2 = document.createElement("button");
    bouton2.setAttribute("type","text");
    bouton2.setAttribute("data-toggle","popover");
    bouton2.setAttribute("title","Détail des notes");
    bouton2.setAttribute("data-content",details2);
    bouton2.setAttribute("data-placement","left");
    var value2 = Math.round(  (resMatiere2 / ttcoef2)  *100)/100;
    bouton2.innerHTML = value2;
    if(value2<8){
        bouton.setAttribute("class","btn btn-danger btn-block");
    }
    else if(value2<10){
        bouton2.setAttribute("class","btn btn-warning btn-block");
    }
    else
        bouton2.setAttribute("class","btn btn-success btn-block");

    element3.appendChild(bouton2);

    nodeEtudiant.appendChild(element3);


    root.appendChild(nodeEtudiant);
}


function viderListe () {
    var rootListe = document.getElementById("liste des étudiants");
    rootListe.innerHTML = "";
}

function decodeJson(text) {
    var liste = JSON.parse(text);
    viderListe();
    for (var id in liste)  {
        ajouterEtudiant(liste[id]);
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


    var bouton = document.getElementById('button');
    bouton.parentNode.removeChild(bouton);
    submitForm("ipi2.json");
}

initEventHandlers(window, 'load', initButton);
