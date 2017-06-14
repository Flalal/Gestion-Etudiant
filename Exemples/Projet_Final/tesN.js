/**
 * Created by Frederic on 13/04/2017.
 */


var index=0;

var promo;
var info=3;






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

function ajouterEtudiant (etudiant) {


    var root = document.getElementById("liste_des_étudiants");
    var modal = document.getElementById('listemodal');
    var titre=document.getElementById('titreTableau');
    var coef=document.getElementById('titreCoefficient');
    var moyennePromo=document.getElementById('moyennePromo');

    var keys = ["numero", "nom", "prenom", "departement", "dateNaissance", "bac", "ue"];
    var bac;
    var dateN;
    var dept;

    var nom;
    var prenom;
    var numero;

// creation des ue et du semestre
    var tabUe = new Array();
    var tabSem = new Array();
    for (var cpt = 0; cpt < 4; cpt++) {
        tabSem.push(new Semestre(cpt+1, 2017));
    }



    // intituler du tableau
    var intituler2 = " <th class='"+CLASSBOOSTRAP+"' onclick='trier(\"numero\")'>Num</th> <th class='"+CLASSBOOSTRAP+"' onclick='trier(\"nom\")' >Nom</th> <th class='"+CLASSBOOSTRAP+"' onclick='trier(\"prenomx\")'>Prénom</th><th class='"+CLASSBOOSTRAP+"'>Clas</th>";
    var coef2="<th class='"+CLASSBOOSTRAP+"'></th><th class='"+CLASSBOOSTRAP+"'></th><th class='"+CLASSBOOSTRAP+"'></th><th class='"+CLASSBOOSTRAP+"'></th>";
    var moyPromo2="<th class='"+CLASSBOOSTRAP+"'></th><th class='"+CLASSBOOSTRAP+"'></th><th class='"+CLASSBOOSTRAP+"'></th><th class='"+CLASSBOOSTRAP+"'></th>";
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
        //if(i == 7) {avatar=eval("etudiant."+keys[i]);}

        var attribut = "etudiant." + keys[i];

        if (i == 6) {

            for (var x in UEDEPT) {/// parcours la constant qui reference tout les ue de chaque département;
                for (var j in eval(attribut)) {// parcours dans les ue
                    var Redoublement=false;
                    var intutitulerUE ="";
                    if(j.indexOf("R")!=-1) {
                        intutitulerUE = j.slice(0, -1);
                        Redoublement=true;
                        redoubler=true;
                    }
                    else
                        intutitulerUE=j;



                    if (UEDEPT[x][0] == (dept + "_" + intutitulerUE)) {//condition si ue existe  dans le json
                        var numSem = parseInt(j.slice(2)[0]) - 1;
                        var numue = tabUe.push(new ue(j.slice(2),eval(attribut + "." + j+".annee")));
                        if(eval(attribut + "." + j+".TauxAbsent")!=undefined){
                            tabUe[numue-1].setTauxAbsent(eval(attribut + "." + j+".TauxAbsent"));
                        }
                        tabUe[numue-1].setRedoubler(Redoublement);
                        if(eval(attribut + "." + j+".commentaire")!=undefined){
							tabUe[numue-1].ajouterCommentaire(eval(attribut + "." + j+".commentaire"));
						}

                        //console.log(eval(attribut + "." + j+".commentaire"));
                        for (var matiere in UEDEPT[x][1]) {
                            var attribut2 = attribut + "." + j + "." + UEDEPT[x][1][matiere];
                            var matiereclass = new Matiere(UEDEPT[x][1][matiere], UEDEPT[x][1][matiere], eval(attribut2 + ".coefficient"));
                            var notes = eval(attribut2 + ".notes");
                            for (var n in notes) {
                                matiereclass.ajouterNotes(notes[n]);

                            }
                            if(eval(attribut2+".TauxAbsent")!=undefined){
                                matiereclass.setTauxAbsent(eval(attribut2+".TauxAbsent"))
                            }

                            tabUe[numue - 1].ajouterMatiere(matiereclass);

                        }

                        tabSem[numSem].ajouterUe(tabUe[numue - 1]);
                    }

                }
            }

            // permet de afficher correctement le tableau du plus récent
            tabSem.reverse();
            for (var j in tabSem) {
                if (tabSem[j].getToutUE().length != 0) {
                    if (j<2) {
                        if (intituler2.indexOf(dept+"2")==-1) {
                            intituler2 += "<th >" + dept + "2</th>";
                         //   intituler += "<th >" + dept + "2</th>";
                            moyenne += "<td></td>";
                            moyPromo2 += "<th >" + "</th>";
                            coef2 += "<th > </th>";
                        }
                    }
                    else{
                        if (intituler2.indexOf(dept+"1")==-1) {
                            intituler2 += "<th >" + dept + "1</th>";
                           // intituler += "<th >" + dept + "1</th>";
                            moyenne += "<td></td>";
                            moyPromo2 += "<th >" + "</th>";
                            coef2 += "<th > </th>";
                        }
                    }
                    intituler2+="<th class='"+CLASSBOOSTRAP+"' onclick='classementAccueil("+tabSem[j].getSemestre()+")'>S"+tabSem[j].getSemestre()+"</th>";
                    moyPromo2+="<th class='"+CLASSBOOSTRAP+"' name='PS"+tabSem[j].getSemestre()+"'>"+"</th>";
                    moyenne+=moyenneCouleur(tabSem[j].getMoyenneSem(),tabSem[j].getSemestre());
                    coef2+="<th class='"+CLASSBOOSTRAP+"' >"+tabSem[j].getCoefficientSem()+"</th>";

                    for (var tmpUe in tabSem[j].getToutUE()) {
                        if(tabSem[j].getToutUE()[tmpUe].getRedoubler()==false) {
                            intituler2 += "<th class='" + CLASSBOOSTRAP + "' onclick='classementAccueil(" + tabSem[j].getToutUE()[tmpUe].getIdUe() + ")'>Ue" + tabSem[j].getToutUE()[tmpUe].getIdUe() + "</th>";
                            moyenne+=moyenneCouleur(tabSem[j].getToutUE()[tmpUe].getMoyenneUE(),tabSem[j].getToutUE()[tmpUe].getIdUe());
                        }
                        moyPromo2+="<th class='"+CLASSBOOSTRAP+"'  name='PUe"+tabSem[j].getToutUE()[tmpUe].getIdUe()+"'>"+"</th>";
                        coef2+="<th class='"+CLASSBOOSTRAP+"'>"+tabSem[j].getToutUE()[tmpUe].getCoefficientUE()+"</th>";
                        details+=afficheDetails(tabSem[j].getToutUE()[tmpUe],tabSem[j].getToutUE()[tmpUe].getCommentaire(),numero);

                    }

                }
            }

        }


    }

    var test=new Etudiant(numero,nom,prenom,dept,dateN,bac);
    plusieurs+=tableauSeparer(tabSem[0],tabSem[1],numero);
    plusieurs+=tableauSeparer(tabSem[2],tabSem[3],numero);
    for (var cpt = 0; cpt < 4; cpt++) {
       test.ajouterSemestre(tabSem[cpt]);
    }
    promo.ajouterEtudiantCLA(test);
    promo.Promoclassement(42);

    if(redoubler){
        prenom+="*";
    }

	titre.innerHTML=intituler2+"<th class='"+CLASSBOOSTRAP+"'>detail</th>";
	coef.innerHTML=coef2+"<th class='"+CLASSBOOSTRAP+"'></th>";
	moyennePromo.innerHTML=moyPromo2+"<th class='"+CLASSBOOSTRAP+"'></th>";

    root.innerHTML+="<tr class='etudiant' name='etudiant' ><td name='numero' class='"+CLASSBOOSTRAP+"'>" + numero
        + "</td> <td name='nom' class='"+CLASSBOOSTRAP+"' >" + nom
        + "</td><td name='prenom' class='"+CLASSBOOSTRAP+"'>" +prenom + "</td> " +
         "<td id='classementFinal"+numero+"' class='"+CLASSBOOSTRAP+"' >"+"</td>"+
            moyenne+
        "<td ><button class='btn btn-success' data-toggle=\"modal\" data-target='.bs-example-modal-lg"+ index+"'>+</button> </td></tr>";


    modal.innerHTML+= "<div class='modal fade bs-example-modal-lg" + index + "' tabindex='-1' role='dialog' aria-labelledby='myLargeModalLabel'>"+
        "<div class='modal-dialog modal-lg' role='document'>" +
        "<div class='modal-content'>" +
        "<div class='container'>" +
        "<h1 class='text-center'>" +
        test.getNom() + " " + test.getPrenom() +
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
    for (var j in tabSem) {
        if (tabSem[j].getToutUE().length != 0) {
            var SemPromo=document.getElementsByName("PS"+tabSem[j].getSemestre());
            for (var cptSem=0;cptSem<SemPromo.length;cptSem++){
                SemPromo[cptSem].innerHTML=(promo.getMoySemPromo(tabSem[j].getSemestre())).toFixed(2);
                var ToutEtudiants=promo.getToutLesEtudiants();
                for (var etu in ToutEtudiants){
                    var SemClass=document.getElementById("CS"+tabSem[j].getSemestre()+ToutEtudiants[etu].getNumero());
                    SemClass.innerHTML=promo.Promoclassement(tabSem[j].getSemestre(),ToutEtudiants[etu].getNumero())+"/"+promo.getnbEtudiants();
                    couleurColonne(tabSem[j].getSemestre());
                   /*var fin=document.getElementById("classementFinal"+ToutEtudiants[etu].getNumero());
                    if(null!=fin)fin.innerHTML=promo.Promoclassement(tabSem[j].getSemestre(),ToutEtudiants[etu].getNumero())+ "/"+promo.getnbEtudiants();
					*/
                }
            }
            for (var tmpUe in tabSem[j].getToutUE()) {
                var UePromo=document.getElementsByName("PUe"+tabSem[j].getToutUE()[tmpUe].getIdUe());
                // console.log(promo.getMoyenneUEPromo(tabSem[j].getToutUE()[tmpUe].getIdUe()));
                for (var cptUe=0;cptUe<UePromo.length;cptUe++){
                    UePromo[cptUe].innerHTML=(promo.getMoyenneUEPromo(tabSem[j].getToutUE()[tmpUe].getIdUe())).toFixed(2);
                    var ToutEtudiants=promo.getToutLesEtudiants();
                    for (var etu in ToutEtudiants){
                        var SemClass=document.getElementById("CUe"+tabSem[j].getToutUE()[tmpUe].getIdUe()+ToutEtudiants[etu].getNumero());
                        SemClass.innerHTML=promo.Promoclassement(tabSem[j].getToutUE()[tmpUe].getIdUe(),ToutEtudiants[etu].getNumero())+"/"+promo.getnbEtudiants();
                        couleurColonne(tabSem[j].getToutUE()[tmpUe].getIdUe());
                    }
                }
                for(var matiere2 in tabSem[j].getToutUE()[tmpUe].getToutMatiere()){
                    var tabMatiere=document.getElementsByName(tabSem[j].getToutUE()[tmpUe].getToutMatiere()[matiere2].getIntitule());
                    for(var moyenne2=0 ;moyenne2< tabMatiere.length ;moyenne2++){
                        tabMatiere[moyenne2].innerHTML=(promo.moyennePromoCours(tabSem[j].getToutUE()[tmpUe].getToutMatiere()[matiere2].getIntitule()).toFixed(2));

                    }
                }

            }

        }
    }


}

function somme(chiffre1,chiffre2){
    total=chiffre1+chiffre2;
    return total;
}

function tableauSeparer(name1,name2,numero){
    if((name1.getToutUE().length == 0) && (name2.getToutUE().length == 0))
        return"";

    var tmpTab="";

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
    if (name1.getToutUE().length != 0) {

    }

    info--;
    if (name1.getToutUE().length != 0) {
        total+="<th>Info"+info+"</th>";
        tabPromo+="<td name='class"+info+"'></td>";
        classementTab+="<td ></td>";

        totalCoef+="<td>"+somme(name1.getCoefficientSem(),name2.getCoefficientSem())+"</td>";
        totalMoyenne+="<td>"+"**"+"</td>";
        totalMoyPromo+="<td>"+"***"+"</td>";
        totalClassement+="<td>"+"****"+"</td>";

        intitulerTab+="<th>S"+name1.getSemestre()+"</th>";
        tabPromo+="<td name='PS"+name1.getSemestre()+"'></td>";
        moyenneTab+=moyenneCouleur(name1.getMoyenneSem(),"");

        coeffTab+="<td >"+name1.getCoefficientSem()+"</td>";
        classementTab+="<td id='CS"+name1.getSemestre()+numero+"'></td>";

        ///pour moyenne semestre min/max
        tabPMS.push(name1.getMoyenneSem());
        tabResuS=tabPMS.sort(tri);
        moyenneMoins+="<td name='MM"+name1.getSemestre()+"'>"+tabResuS[0].toFixed(2)+"</td>";
        moyennePlus+="<td name='MP"+name1.getSemestre()+"'>"+tabResuS[tabResuS.length-1].toFixed(2)+"</td>";

        for (var tmpUe in name1.getToutUE()) {

            intitulerTab+="<th><a href='#"+name1.getToutUE()[tmpUe].getIdUe()+numero+"'><i class='icon icon-sign-out icon-lg'></i>Ue"+name1.getToutUE()[tmpUe].getIdUe()+"</a></th>";
            moyenneTab+=moyenneCouleur(name1.getToutUE()[tmpUe].getMoyenneUE(),"");

            tabPromo+="<td  name='PUe"+name1.getToutUE()[tmpUe].getIdUe()+"'></td>";
            coeffTab+="<td >"+name1.getToutUE()[tmpUe].getCoefficientUE()+"</td>";
            classementTab+="<td id='CUe"+name1.getToutUE()[tmpUe].getIdUe()+numero+"'></td>";

            tabPMU.push(name1.getToutUE()[tmpUe].getMoyenneUE());
            tabResuU=tabPMU.sort(tri);
            moyenneMoins+="<td name='MM"+name1.getToutUE()[tmpUe].getIdUe()+"'>"+tabResuU[0].toFixed(2)+"</td>";
            moyennePlus+="<td name='MP"+name1.getToutUE()[tmpUe].getIdUe()+"'>"+tabResuU[tabResuU.length-1].toFixed(2)+"</td>";

        }

    }
    if (name2.getToutUE().length != 0) {

        intitulerTab+="<th>S"+name2.getSemestre()+"</th>";
        tabPromo+="<td name='PS"+name2.getSemestre()+"'></td>";
        moyenneTab+=moyenneCouleur(name2.getMoyenneSem(),"");

        coeffTab+="<td >"+name2.getCoefficientSem()+"</td>";
        classementTab+="<td id='CS"+name2.getSemestre()+numero+"'></td>";

        ///pour moyenne semestre min/max
        tabPMS.push(name2.getMoyenneSem());
        tabResuS=tabPMS.sort(tri);
        moyenneMoins+="<td name='MM"+name2.getSemestre()+"'>"+tabResuS[0].toFixed(2)+"</td>";
        moyennePlus+="<td name='MP"+name2.getSemestre()+"'>"+tabResuS[tabResuS.length-1].toFixed(2)+"</td>";

        for (var tmpUe in name2.getToutUE()) {
            intitulerTab+="<th><a href='#"+name2.getToutUE()[tmpUe].getIdUe()+numero+"'><i class='icon icon-sign-out icon-lg'></i>Ue"+name2.getToutUE()[tmpUe].getIdUe()+"</a></th>";
            moyenneTab+=moyenneCouleur(name2.getToutUE()[tmpUe].getMoyenneUE(),"");

            tabPromo+="<td  name='PUe"+name2.getToutUE()[tmpUe].getIdUe()+"'></td>";
            coeffTab+="<td >"+name2.getToutUE()[tmpUe].getCoefficientUE()+"</td>";
            classementTab+="<td id='CUe"+name2.getToutUE()[tmpUe].getIdUe()+numero+"'></td>";

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
    if (info<2)
        info=3;
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
    mot.value = "";
    var TdModifer = document.getElementsByName(intituler);
    var lignes = document.getElementsByName("etudiant");
    var en_cours, plus_petit, j, temp;

    for (en_cours = 0; en_cours < TdModifer.length - 1; en_cours++)
    {
        plus_petit = en_cours;
        for (j = en_cours + 1; j < TdModifer.length; j++){
            if (TdModifer[j].innerHTML < TdModifer[plus_petit].innerHTML)
                plus_petit = j;
        }
        temp = TdModifer[en_cours];
        TdModifer[en_cours] = TdModifer[plus_petit];
        TdModifer[plus_petit] = temp;

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

    var mat="<th>UE"+ue.getIdUe()+"</th>";
    var moye=moyenneCouleur(ue.getMoyenneUE(),"");
    var coef="<td>"+ue.getCoefficientUE() +"</td>";
    var tab="<td></td>";
    var commentaire=comments;


    for(var tmpmati in ue.getToutMatiere()){
        mat+="<th>"+ue.getToutMatiere()[tmpmati].getIntitule()+"</th>";
        moye+=moyenneCouleur(ue.getToutMatiere()[tmpmati].getMoyenne(),"");
        coef+='<td>'+ue.getToutMatiere()[tmpmati].getCoefficient()+'</td>';
        tab+="<td name='"+ue.getToutMatiere()[tmpmati].getIntitule()+"'></td>";

    }


    var table=
        "<h2 id='"+ue.getIdUe()+numero+"' >UE"+ue.getIdUe() +" \ Commentaire: "+ commentaire+"</h2>"+
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
    var i =0;
    for (var id in liste)  {
        ajouterEtudiant(liste[id],i);
        i++;
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
