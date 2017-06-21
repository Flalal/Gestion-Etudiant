/**
 * Created by Frederic on 21/06/2017.
 */
/**
 * Created by Frederic on 13/04/2017.
 */


var index=0;

var promo;
var IntitulerClassement=null;

var intitulerprecedents=null;







/*
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
 */
function chargerFichier(idInputFile) {
    "use strict";
    var entree, fichier, fr;

    if (typeof window.FileReader !== "function") {
        alert("L’API file n’est pas encore supportée par votre navigateur.");
        return;
    }

    entree = document.getElementById(idInputFile);
    if (!entree.files[0]) {
        alert("S’il vous plaît sélectionnez un fichier avant de cliquer sur «Chargement».");
    } else {
        fichier = entree.files[0];
        fr = new FileReader();
        fr.onload = function () {
            var content= fr.result;
            decodeJson(content);
        };
        fr.readAsText(fichier);
    }
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
            lignes[i].style.display = "table";
        }
    }
}

function affichage_initial() {
    var lignes = document.getElementsByName("etudiant");
    var en_cours;

    for (en_cours = 0; en_cours < lignes.length; en_cours++)
    {
        lignes[en_cours].style.display = "table";
    }
}

function tri(a,b){
    return a - b;
}

function ajouterSemestreF(etudiantSemstre,intitulerSemestre,dept) {
    var annne=etudiantSemstre["annee"];
    var coeff=etudiantSemstre["coefficient"];
    var moyenne= etudiantSemstre["moyenne"];
    var moyennePromo= etudiantSemstre["moyennePromo"];
    var moyenneMin= etudiantSemstre["minimum"];
    var moyenneMax= etudiantSemstre["maximum"];
    var classement=etudiantSemstre["classement"];
    // argument:intituler Semestre, annee , coeff, moyenne , classement , moyennePromo,Moyenne Min et moyenne Max
    var sem=new Semestre(intitulerSemestre,annne,coeff,moyenne,classement,moyennePromo,moyenneMin,moyenneMax);
    for (var item in etudiantSemstre["UE"]){
       sem.ajouterUe(ajouterUEF(eval(etudiantSemstre["UE"][item]),item,dept));
    }
    return sem;

}
function ajouterUEF(etudiantUE,intitulerUE, dept ) {
    var annne=etudiantUE["annee"];
    var coeff=etudiantUE["coefficient"];
    var moyenne= etudiantUE["moyenne"];
    var moyennePromo= etudiantUE["moyennePromo"];
    var moyenneMin= etudiantUE["minimum"];
    var moyenneMax= etudiantUE["maximum"];
    var classement=etudiantUE["classement"];


    var UEclass=new ue(intitulerUE,annne,coeff,moyenne,classement,moyennePromo,moyenneMin,moyenneMax);
    if (etudiantUE["commentaire"]!=undefined){
        UEclass.ajouterCommentaire(etudiantUE["commentaire"]);
    }
    if(etudiantUE["TauxAbsent"]!=undefined){
        UEclass.setTauxAbsent(etudiantUE["TauxAbsent"]);
    }
    for (var item in etudiantUE["matieres"]){
        UEclass.ajouterMatiere(ajouterMatiereF(etudiantUE["matieres"][item],item))
    }

    return UEclass;
}
function ajouterMatiereF(etudiantMatiere,intitulerMatiere) {
    var coeff=etudiantMatiere["coefficient"];
    var moyenne= etudiantMatiere["moyenne"];
    var moyennePromo= etudiantMatiere["moyennePromo"];
    var moyenneMin= etudiantMatiere["minimum"];
    var moyenneMax= etudiantMatiere["maximum"];
    var classement=etudiantMatiere["classement"];
    var matiereclass=new Matiere(intitulerMatiere,intitulerMatiere,coeff,moyenne,classement,moyennePromo,moyenneMin,moyenneMax);

    if(etudiantMatiere["TauxAbsent"]!=undefined){
        matiereclass.setTauxAbsent(etudiantMatiere["TauxAbsent"]);
    }
    return matiereclass;


}



function ajouterEtudiant (etudiant) {

    var root = document.getElementById("liste_des_étudiants");
    var modal = document.getElementById('listemodal');
    var titre=document.getElementById('titreTableau');
    var coef=document.getElementById('titreCoefficient');
    var moyennePromo=document.getElementById('moyennePromo');

    var keys = ["numero", "nom", "prenom", "departement", "dateNaissance", "bac","groupe", "semestres"];
    var bac;
    var dateN;
    var dept;

    var nom;
    var prenom;
    var numero;
    var groupe;

    var tmpClassement=new Array;
    var tabSem=new Array;

// creation des ue et du semestre




    // intituler du tableau
    var intituler2 = " <th >Num</th> <th  >Nom</th> <th >Prénom</th><th>Clas</th> <th>Groupe</th>";
    var coef2="<th></th><th class='"+CLASSBOOSTRAP+"'></th><th class='"+CLASSBOOSTRAP+"'></th><th class='"+CLASSBOOSTRAP+"'></th> <th></th>";
    var moyPromo2="<th class='"+CLASSBOOSTRAP+"'></th><th class='"+CLASSBOOSTRAP+"'></th><th class='"+CLASSBOOSTRAP+"'></th><th class='"+CLASSBOOSTRAP+"'></th> <th></th>";
    var moyenne = "";
    var details="";
    var redoubler=false;
    var plusieurs="";

    var commentaire="";


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
        if(i == 6) {groupe=eval("etudiant."+keys[i]);}

        var attribut = "etudiant." + keys[i];

        if (i == 7) {

            for (var j in eval(attribut)) {
                tabSem.push(ajouterSemestreF(eval(attribut+"."+j),j,dept));

            }
            console.log(tabSem);





            // permet de afficher correctement le tableau du plus récent
          tabSem = tabSem.sort(function (a,b) {
                if(a.getSemestre()>b.getSemestre())
                    return -1;
                if (a.getSemestre()<b.getSemestre())
                    return 1;
                else
                    return 0;

                
            });


            for (var j in tabSem) {
                if (tabSem[j].getToutUE().length != 0) {
                    intituler2+="<th id='intituler_"+tabSem[j].getSemestre()+"' onclick='classementAccueil("+tabSem[j].getSemestre()+")'>"+tabSem[j].getSemestre()+"</th>";
                    moyPromo2+="<th >"+tabSem[j].getMoyennePromoS()+"</th>";
                    moyenne+=moyenneCouleur(tabSem[j].getMoyenneSem(),tabSem[j].getSemestre());
                    coef2+="<th class='"+CLASSBOOSTRAP+"' >"+tabSem[j].getCoefficientSem()+"</th>";

                    for (var tmpUe in tabSem[j].getToutUE()) {
                        if(tabSem[j].getToutUE()[tmpUe].getRedoubler()==false) {
                            intituler2 += "<th id='intituler_" + tabSem[j].getToutUE()[tmpUe].getIdUe() + "' onclick='classementAccueil(" + tabSem[j].getToutUE()[tmpUe].getIdUe() + ")'>" + tabSem[j].getToutUE()[tmpUe].getIdUe() + "</th>";
                            moyenne+=moyenneCouleur(tabSem[j].getToutUE()[tmpUe].getMoyenneUE(),tabSem[j].getToutUE()[tmpUe].getIdUe());
                        }
                        moyPromo2+="<th>"+tabSem[j].getToutUE()[tmpUe].getMoyennePromoUE()+"</th>";
                        coef2+="<th class='"+CLASSBOOSTRAP+"'>"+tabSem[j].getToutUE()[tmpUe].getCoefficientUE()+"</th>";
                        details+=afficheDetails(tabSem[j].getToutUE()[tmpUe],tabSem[j].getToutUE()[tmpUe].getCommentaire(),numero);

                    }

                }
            }

        }


    }

    var test=new Etudiant(numero,nom,prenom,dept,groupe,dateN,bac);

    for (var cpt = 0; cpt < tabSem.length; cpt+2) {
        plusieurs+=tableauSeparer(tabSem[cpt],tabSem[cpt+1],numero,dept);
    }
    for (var cpt = 0; cpt < tabSem.length; cpt++) {
      test.ajouterSemestre(tabSem[cpt]);
    }
    promo.ajouterEtudiantCLA(test);
    titre.innerHTML=intituler2+"<th class='"+CLASSBOOSTRAP+"'>detail</th>";
    coef.innerHTML=coef2+"<th class='"+CLASSBOOSTRAP+"'></th>";
    moyennePromo.innerHTML=moyPromo2+"<th class='"+CLASSBOOSTRAP+"'></th>";

    root.innerHTML+="<tr class='etudiant' name='etudiant' ><td name='numero' class='"+CLASSBOOSTRAP+"'>" + numero
        + "</td> <td name='nom' class='"+CLASSBOOSTRAP+"' >" + nom
        + "</td><td name='prenom' class='"+CLASSBOOSTRAP+"'>" +prenom + "</td> " +
        "<td id='classementFinal"+numero+"' class='"+CLASSBOOSTRAP+"' >"+"</td>"+
        "<td  > "+test.getGroupe() +"</td>"+

        moyenne+
        "<td ><button class='btn btn-success' data-toggle=\"modal\" data-target='.bs-example-modal-lg"+ index+"'>+</button> </td></tr>";


    modal.innerHTML+= "<div class='modal fade bs-example-modal-lg" + index + "' tabindex='-1' role='dialog' aria-labelledby='myLargeModalLabel'>"+
        "<div class='modal-dialog modal-lg' role='document'>" +
        "<div class='modal-content'>" +
        "<div class='container'>" +
        "<h1 class='text-center'>" +
        test.getNom() + " " + test.getPrenom() +" "+ test.getGroupe()+
        "</h1>" +
        "<img  src='img/"+ test.avatar+"' width='130px' height='130px' style='float: right;' alt='"+test.avatar +"'/>"+
        "<p> infomation complementaire: </p>"+
        "<ul> <li> Diplôme : "+ test.getBac()+"</li>"+
        "<li> Date de naissance "+ test.getDateNaissance()+"</li></ul>"+plusieurs+



        details+


        "</div>"+

        "</div>"+
        "</div>"+
        "</div>";

    index++;
    var cpt3=0;


}

function somme(chiffre1,chiffre2){
    total=chiffre1+chiffre2;
    return total;
}

function tableauSeparer(name1,name2,numero,departement){

    var tmpTab="";
    var info;

    var classementTab="";
    var intitulerTab="";
    var coeffTab="";
    var moyenneTab="";
    var tabPromo="";

    var total="";
    var totalCoef="";
    var totalMoyenne="";
    var totalClassement="";
    var totalMoyPromo="";

    var moyenneMoins="";
    var moyennePlus="";

    var tabPMS=new Array();
    var tabResuS=new Array();
    var tabPMU=new Array();
    var tabResuU=new Array();
    if (name1==undefined && name2==undefined)
        return "";

    if((name1.getToutUE().length == 0) && (name2.getToutUE().length == 0))
        return"";
    if(name1.getSemestre()>2)
        info=2;
    else
        info=1;
    if (name1.getToutUE().length != 0) {
        total+="<th>"+departement+info+"</th>";
        tabPromo+="<td ></td>";
        classementTab+="<td ></td>";

        totalCoef+="<td>"+somme(name1.getCoefficientSem(),name2.getCoefficientSem())+"</td>";
        totalMoyenne+="<td>"+((somme(name1.getMoyenneSem(),name2.getMoyenneSem()))/2).toFixed(2)+"</td>";
        totalMoyPromo+="<td name='PDepart"+info+"'> </td>";
        totalClassement+="<td>"+"****"+"</td>";

        intitulerTab+="<th>"+name1.getSemestre()+"</th>";
        tabPromo+="<td>"+name1.getMoyennePromoS()+"</td>";
        moyenneTab+=moyenneCouleur(name1.getMoyenneSem(),"");

        coeffTab+="<td >"+name1.getCoefficientSem()+"</td>";
        classementTab+="<td >"+name1.getClassementS()+"</td>";

        ///pour moyenne semestre min/max
        tabPMS.push(name1.getMoyenneSem());
        tabResuS=tabPMS.sort(tri);
        moyenneMoins+="<td name=>"+"</td>";
        moyennePlus+="<td name='MP"+name1.getSemestre()+"'>"+tabResuS[tabResuS.length-1].toFixed(2)+"</td>";

        for (var tmpUe in name1.getToutUE()) {

            intitulerTab+="<th><a href='#"+name1.getToutUE()[tmpUe].getIdUe()+numero+"'><i class='icon icon-sign-out icon-lg'></i>"+name1.getToutUE()[tmpUe].getIdUe()+"</a></th>";
            moyenneTab+=moyenneCouleur(name1.getToutUE()[tmpUe].getMoyenneUE(),"");

            tabPromo+="<td  >"+name1.getToutUE()[tmpUe].getMoyennePromoUE()+"</td>";
            coeffTab+="<td >"+name1.getToutUE()[tmpUe].getCoefficientUE()+"</td>";
            classementTab+="<td >"+name1.getToutUE()[tmpUe].getClassementUE()+"</td>";

            tabPMU.push(name1.getToutUE()[tmpUe].getMoyenneUE());
            tabResuU=tabPMU.sort(tri);
            moyenneMoins+="<td name='MM"+name1.getToutUE()[tmpUe].getIdUe()+"'>"+tabResuU[0].toFixed(2)+"</td>";
            moyennePlus+="<td name='MP"+name1.getToutUE()[tmpUe].getIdUe()+"'>"+tabResuU[tabResuU.length-1].toFixed(2)+"</td>";

        }

    }
    if (name2.getToutUE().length != 0) {

        intitulerTab+="<th>"+name2.getSemestre()+"</th>";
        tabPromo+="<td >"+name2.getMoyennePromoS()+"</td>";
        moyenneTab+=moyenneCouleur(name2.getMoyenneSem(),"");

        coeffTab+="<td >"+name2.getCoefficientSem()+"</td>";
        classementTab+="<td>"+name2.getClassementS()+"</td>";

        ///pour moyenne semestre min/max
        tabPMS.push(name2.getMoyenneSem());
        tabResuS=tabPMS.sort(tri);
        moyenneMoins+="<td name='MM"+name2.getSemestre()+"'>"+tabResuS[0].toFixed(2)+"</td>";
        moyennePlus+="<td name='MP"+name2.getSemestre()+"'>"+tabResuS[tabResuS.length-1].toFixed(2)+"</td>";

        for (var tmpUe in name2.getToutUE()) {
            intitulerTab+="<th><a href='#"+name2.getToutUE()[tmpUe].getIdUe()+numero+"'><i class='icon icon-sign-out icon-lg'></i>"+name2.getToutUE()[tmpUe].getIdUe()+"</a></th>";
            moyenneTab+=moyenneCouleur(name2.getToutUE()[tmpUe].getMoyenneUE(),"");

            tabPromo+="<td >"+name2.getToutUE()[tmpUe].getMoyennePromoUE()+"</td>";
            coeffTab+="<td >"+name2.getToutUE()[tmpUe].getCoefficientUE()+"</td>";
            classementTab+="<td>"+name2.getToutUE()[tmpUe].getClassementUE()+"</td>";

            tabPMU.push(name2.getToutUE()[tmpUe].getMoyenneUE());
            tabResuU=tabPMU.sort(tri);
            moyenneMoins+="<td name='MM"+name2.getToutUE()[tmpUe].getIdUe()+"'>"+tabResuU[0].toFixed(2)+"</td>";
            moyennePlus+="<td name='MP"+name2.getToutUE()[tmpUe].getIdUe()+"'>"+tabResuU[tabResuU.length-1].toFixed(2)+"</td>";

        }


    }

    //coeffTab+=name.getCoefficientSem();
    //moyenneTab+=moyenneCouleur(name.getMoyenneSem(),name.getSemestre());
    //if((nbr>2 || nbr>31) && !(nbr>10 && nbr<30)){

    tmpTab= "<h3>"+total+"</h3>"+
        "<table class='table table-bordered table-striped'>" +

        "<thead>" +"<th></th>" +
        total+intitulerTab +
        "</tr>"+
        "</thead>"+
        "<tbody>" +

        "<tr id='Coeff"+index+"'>  <td>Coefficent</td>"+totalCoef+coeffTab+"</tr>"+
        "<tr id='Moyenne"+index+"'>  <td>Moyenne</td>"+totalMoyenne+moyenneTab+"</tr>"+
        "<tr name='MoyennePromo'>  <td>Moyenne Promo</td>"+ tabPromo+" </tr>"+
        "<tr id='Classement"+index+"'>  <td>Classement</td>"+ classementTab+"</tr>"+
        "<tr name='MoyenneMoins'><td>Moyenne Moins<td>"+
        moyenneMoins+
        "</tr>"+
        "<tr name='MoyennePlus'><td>Moyenne Plus<td>"+
        moyennePlus+
        "</tr>"+
        "</tbody>"+
        "</table>";
    return tmpTab;
}

function classementAccueil(cours){
    //alert(cours);

    var toutEtudiant=promo.getToutLesEtudiants();
    for(var tmp in toutEtudiant){
        var doc=document.getElementById('classementFinal'+toutEtudiant[tmp].getNumero());
        doc.innerHTML=promo.Promoclassement(cours,toutEtudiant[tmp].getNumero())+ "/"+promo.getnbEtudiants();
    }
    trier(cours);
}

function trier (intituler) {
    var mot = document.getElementById("recherche");

    if ( intitulerprecedents!=null){
        var intitulerhtmlpred= document.getElementById("intituler_"+intitulerprecedents);
        intitulerhtmlpred.style.backgroundColor="";
    }
    var intitulerhtml= document.getElementById("intituler_"+intituler);
    intitulerhtml.style.backgroundColor="red";
    intitulerprecedents=intituler;
    mot.value = "";
    var TdModifer = document.getElementsByName(intituler);
    var lignes = document.getElementsByName("etudiant");
    var en_cours, plus_petit, j, temp;

    for (en_cours = 0; en_cours < TdModifer.length - 1; en_cours++)
    {
        plus_petit = en_cours;
        for (j = en_cours + 1; j < TdModifer.length; j++){

            if (TdModifer[j].innerHTML< eval(TdModifer[plus_petit].innerHTML))
                plus_petit = j;
        }
        /*  temp = TdModifer[en_cours];
         TdModifer[en_cours] = TdModifer[plus_petit];
         TdModifer[plus_petit] = temp;*/

        lignes[en_cours].style.display = "table";
        temp = lignes[en_cours].innerHTML;
        lignes[en_cours].innerHTML = lignes[plus_petit].innerHTML;
        lignes[plus_petit].innerHTML = temp;
    }

}


function moyenneCouleur(moyenne,names){
    var tmp="";
    if(moyenne<10){
        if(moyenne<8){
            tmp="<td  name='"+names+"' class='"+CLASSBOOSTRAP+"' style='color: red'>"+moyenne.toFixed(2)+"</td>";
        }
        else{
            tmp="<td name='"+names+"'  class='"+CLASSBOOSTRAP+"'  style='color: orange'>"+moyenne.toFixed(2)+"</td>";
        }
    }
    else{
        tmp="<td name='"+names+"'  class='"+CLASSBOOSTRAP+"' style='color: green'>"+moyenne.toFixed(2)+"</td>";
    }

    return tmp;

}

function afficheDetails(ue,comments,numero){

    var mat="<th>"+ue.getIdUe()+"</th>";
    var moye=moyenneCouleur(ue.getMoyenneUE(),"");
    var coef="<td>"+ue.getCoefficientUE() +"</td>";
    var tab="<td></td>";
    var commentaire=comments;


    for(var tmpmati in ue.getToutMatiere()){
        mat+="<th>"+ue.getToutMatiere()[tmpmati].getIntitule()+"</th>";
        moye+=moyenneCouleur(ue.getToutMatiere()[tmpmati].getMoyenne(),"");
        coef+='<td>'+ue.getToutMatiere()[tmpmati].getCoefficient()+'</td>';
        tab+="<td >"+ue.getToutMatiere()[tmpmati].getMoyennePromoM()+"</td>";

    }


    var table=
        "<h2 id='"+ue.getIdUe()+numero+"' >"+ue.getIdUe() +" : "+ commentaire+"</h2>"+
        "<table class='table table-bordered table-striped'>" +
        "<thead>" +
        "<tr>" +
        "<th></th>" + mat+
        "</tr>" +
        "</thead>" +
        "<tbody>" +
        "<tr>  <td>Coefficent</td>"+coef+"</tr>"+
        "<tr>  <td>Moyenne</td> "+

        moye+
        "</tr>"+
        "<tr> <td>Moyenne Promo</td>"+tab+"</tr>"+
        "</tbody>" +
        "</table>";

    return table;
}
function couleurColonne(ue){

    var tmp=document.getElementsByName("PS"+ue);
    var cpt;
    for(var i=0;i<tmp.length;i++){
        cpt=ue;
        //tmp[i].style.backgroundColor = "RGB(255,255,0)";
    }

    var tmp=document.getElementsByName(ue);

    for(var i=0;i<tmp.length;i++){
        if(ue==cpt){
            if(!(i%2==0)){
                tmp[i].style.backgroundColor = "RGBA(255,255,0,0.50)";

            }else{
                tmp[i].style.backgroundColor = "RGBA(255,255,0,0.80)";
            }
        }else{
            if(!(i%2==0)){
                tmp[i].style.backgroundColor = "RGBA(72,201,176,0.50)";
            }else{
                tmp[i].style.backgroundColor = "RGBA(72,201,176,0.80)";
            }
        }
    }

    return tmp;
}




function viderListe () {
    var rootListe = document.getElementById("liste_des_étudiants");
    rootListe.innerHTML = "";
}


function decodeJson(text) {
    var liste = JSON.parse(text);
    viderListe();
    index=0;
    promo=new Promo();
    for (var id in liste.Liste)  {
        ajouterEtudiant(liste.Liste[id])
    }
}


function submitForm(file) {

    /*    var req = createXhrObject();
     if (null == req) return;
     req.onreadystatechange = function () {
     if (req.readyState == 4) {
     if (req.status == 200) {
     console.log(req.responseText);
     decodeJson(req.responseText);
     } else {
     alert("Error: returned status code " + req.status + " " + req.statusText);
     }
     }
     };
     req.open("GET", file, true);
     req.send(null);*/
    promo=new Promo();
    viderListe();
    index=0;
    $.getJSON( file, function(obj) {
        $.each(obj, function(key, value) {
            console.log(value);
            ajouterEtudiant(value);
        });
    });

} // submitForm

function initButton() {
    var bouton = document.getElementById('boutonCharger');
    initEventHandlers(bouton,"click",function(){chargerFichier("fichierEntre");});
}

function initEventHandlers(element, event, fx) {
    if (element.addEventListener)
        element.addEventListener(event, fx, false);
    else if (element.attachEvent)
        element.attachEvent('on' + event, fx);
}

initEventHandlers(window, 'load', initButton);
/**
 * Created by Frederic on 11/04/2017.
 */
