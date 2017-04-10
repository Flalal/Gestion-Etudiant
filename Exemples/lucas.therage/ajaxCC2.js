'use strict';
var x = 1;

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
    var keys =  ["numero","nom","prenom", "ue41", "ue42"];
    var keysUE1 = ["Pweb2", "AdmSR", "ProgMobile", "ProgRep", "CompInfo", "Projet"];
    var keysUE2 = ["Atelier", "RO", "COM", "Ang"];
    var keysAUTRE = ["coefficient", "notes"];
    var coef = 0;
    var noteUE41 = 0;
    var noteUE42 = 0;
    var sommeUE41 = 0;
    var sommeUE42 = 0;
    var sommeCoef=0;
    var moyUE41=0;
    var moyUE42=0;
    for (var i in keys) {
        if(i<3){
            var element = document.createElement("td");
            var attribut = "etudiant."+keys[i];
            element.innerHTML = eval(attribut);
            nodeEtudiant.appendChild(element);
        }
        else if(i==3) {
            for (var j in keysUE1) {

                coef = eval("etudiant." + keys[i] + "." + keysUE1[j] + "." + keysAUTRE[0]);
                sommeCoef += coef;
                //alert(coef);


                noteUE41 = eval("etudiant." + keys[i] + "." + keysUE1[j] + "." + keysAUTRE[1]);
                //alert(note[0]);
                for (var l = 0; l < noteUE41.length; l++) {
                    sommeUE41 += noteUE41[l] * coef;
                }
                sommeUE41 = (sommeUE41 / noteUE41.length);

            }
            moyUE41 = (sommeUE41/sommeCoef);
            var element = document.createElement("td");
            var attribut = moyUE41.toFixed(2);
            element.innerHTML = eval(attribut);
            couleurStyle(moyUE41, element);
            nodeEtudiant.appendChild(element);
            sommeCoef = 0;
        }
        else if(i==4){

            for (var j in keysUE2){

                coef = eval("etudiant."+keys[i]+"."+keysUE2[j]+"."+keysAUTRE[0]);
                sommeCoef+=coef;
                //alert(coef);


                noteUE42 = eval("etudiant."+keys[i]+"."+keysUE2[j]+"."+keysAUTRE[1]);
                //alert(note[0]);
                for(var l=0; l<noteUE42.length; l++){
                    sommeUE42 += noteUE42[l] * coef;
                }
                sommeUE42 = (sommeUE42/noteUE42.length);
            }

            moyUE42 = (sommeUE42/sommeCoef);;
            var element = document.createElement("td");
            var attribut = moyUE42.toFixed(2);
            element.innerHTML = eval(attribut);
            couleurStyle(moyUE42, element);
            nodeEtudiant.appendChild(element);
            sommeCoef = 0;

            var elementMoy = document.createElement("td");
            var attributMoy = ((moyUE41 + moyUE42)/2).toFixed(2);
            elementMoy.innerHTML = eval(attributMoy);
            couleurStyleMoyenne(moyUE41, moyUE42, elementMoy);
            nodeEtudiant.appendChild(elementMoy);
        }
    }

    var bouton = document.createElement("button");
    bouton.setAttribute("id", x);
    x++;
    bouton.innerHTML = "+";

    initEventHandlers(bouton, 'click', function () {
        alert(alertCustom(etudiant));
    });
    nodeEtudiant.appendChild(bouton);
    root.appendChild(nodeEtudiant);
}
function alertCustom(etudiant) {
    var stringFin = "";
    var keys =  ["numero","nom","prenom", "ue41", "ue42"];
    var keysUE1 = ["Pweb2", "AdmSR", "ProgMobile", "ProgRep", "CompInfo", "Projet"];
    var keysUE2 = ["Atelier", "RO", "COM", "Ang"];

    var nom = eval("etudiant."+keys[1]);
    var prenom = eval("etudiant."+keys[2]);
    var tabMatNote = [];
    var tabMatNote2 = [];

    for (var i in keysUE1){
        tabMatNote.push(" " + keysUE1[i] + " = " + eval("etudiant." + keys[3] + "." + keysUE1[i] + ".notes"));
    }

    for (var i in keysUE2){
        tabMatNote2.push(" " + keysUE2[i] + " = " + eval("etudiant." + keys[4] + "." + keysUE2[i] + ".notes"));
    }

    stringFin = nom + " " + prenom + "\n" + "\n" + "UE41 : " + tabMatNote + "\n" + "\n" + "UE42 : " + tabMatNote2;
    return stringFin;


}
function couleurStyle(moy, elt) {

    if(moy<10){
        if(moy<8){
            elt.style.color = "#FA5858";
        }
        else{
            elt.style.color = "#FAAC58";
        }

    }
    else if(moy>10){
        elt.style.color = "#01DF74";
    }
}

function couleurStyleMoyenne(moy1, moy2, elt) {

    if((moy1 > 8) && (moy2 > 8) && ((moy1 + moy2)/2 >= 10)){
        elt.style.color = "#01DF74";
    }
    else {
        elt.style.color = "#FA5858";
    }
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
    viderListe();
    submitForm("ipi2.json");
    bouton.parentNode.removeChild(bouton);
}

function initEventHandlers(element, event, fx) {
    if (element.addEventListener)
        element.addEventListener(event, fx, false);
    else if (element.attachEvent)
        element.attachEvent('on' + event, fx);
}

initEventHandlers(window, 'load', initButton);
