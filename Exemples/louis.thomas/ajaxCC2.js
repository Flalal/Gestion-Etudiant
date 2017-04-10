function fetchJSONFile(path, callback) {
    var httpRequest = new XMLHttpRequest();
    httpRequest.onreadystatechange = function() {
        if (httpRequest.readyState === 4) {
            if (httpRequest.status === 200) {
                var data = JSON.parse(httpRequest.responseText);
                if (callback) callback(data);
            }
        }
    };
    httpRequest.open('GET', path);
    httpRequest.send(); 
}


function execute() {
	
	fetchJSONFile('listeIPI1.json', function(data){
		
		//~ console.log(data);
		
		// récupere les elements interressants
		var liste = document.getElementById('liste des étudiants');
		var modal = document.getElementById('listemodal');
		
		// Vide les différentes DIV
		while (liste.hasChildNodes()) {
			liste.removeChild(liste.firstChild);
		}
		while (modal.hasChildNodes()) {
			modal.removeChild(modal.firstChild);
		}
		
		
			
		var index= 0;
		for (x in data) {
			//~ console.log(x);
			//~ console.log(data[x]);
			
			//Ajoute chaque étudiant dans la table
			liste.innerHTML+="<tr class='etudiant' data-toggle='modal' data-target='.bs-example-modal-lg" + index + "'><td>" + data[x].numero + "</td><td>" + data[x].nom + "</td><td>" + data[x].prenom + "</td></tr>";
			
			// valeur html
			var rowue41 = "";
			var rowue42 = "";
			
			// moyenne de chaque UE
			var MoyUe41 = 0;
			var MoyUe42 = 0;
			
			// Somme des coefs par UE
			var cpt1 = 0;
			var cpt2 = 0;
			
			// Parcourt l'ue1 de l'etudiant x
			for (a in data[x].ue41) {
				//~ console.log(data[x].ue41[a].intitule);
				rowue41 += "<tr><td>" + data[x].ue41[a].intitule + "</td><td>" + data[x].ue41[a].coefficient + "</td><td>" + data[x].ue41[a].notes[0] + "</td></tr>";
				MoyUe41 += data[x].ue41[a].notes[0] * data[x].ue41[a].coefficient; // somme des calculs (notes * coefs)
				cpt1+= data[x].ue41[a].coefficient; // somme des coefs
			}
			// calcul moy ue41
			MoyUe41 = MoyUe41 / cpt1;
			
			// Parcourt l'ue2 de l'etudiant x
			for (a in data[x].ue42) {
				//~ console.log(data[x].ue42[a].intitule);
				rowue42 += "<tr><td>" + data[x].ue42[a].intitule + "</td><td>" + data[x].ue42[a].coefficient + "</td><td>" + data[x].ue42[a].notes[0] + "</td></tr>";
				MoyUe42 += data[x].ue42[a].notes[0] * data[x].ue42[a].coefficient; // somme des calculs (notes * coefs)
				cpt2+= data[x].ue42[a].coefficient; // somme des coefs
			}
			// calcul moy ue42
			MoyUe42 = MoyUe42 / cpt2;
			
			//~ console.log(MoyUe42);
			

			
			
			
			modal.innerHTML+= "<div class='modal fade bs-example-modal-lg" + index + "' tabindex='-1' role='dialog' aria-labelledby='myLargeModalLabel'>"+
			  "<div class='modal-dialog modal-lg' role='document'>" +
				"<div class='modal-content'>" +
					"<div class='container'>" +
					"<h1 class='text-center'>" +
					 data[x].nom + " " + data[x].prenom +
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
						"<th>" + (((cpt1*MoyUe41)+(cpt2*MoyUe42))/(cpt2+cpt1)).toFixed(2) + "</th>" +
					"</tr>" +
				   "</tbody>"+
				  "</table>"+
					
				  "</div>"+
				  
				"</div>"+
			  "</div>"+
			"</div>"
			
		
			index++;
		}
		
	});

}

function initButton() {
    var bouton = document.getElementById('button');
    initEventHandlers(bouton, 'click', function() { execute();});
}    
function initEventHandlers(element, event, fx) {
    if (element.addEventListener)
        element.addEventListener(event, fx, false);
    else if (element.attachEvent)
        element.attachEvent('on' + event, fx);
} 
initEventHandlers(window, 'load', initButton);
