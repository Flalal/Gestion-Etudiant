Premiérement aller dans le dossier programme, ensuite (si le(s) dossier(s) du departement et/ou liste_annee pas créer =>) Créer le dossier du departement en majuscule (ex: INFO) vide et/ou un autre dossier Liste_annee (avec a l'interieur les listes des etduiants et matieres .csv).
Puis lancer depuis le dossier programme csvtoxls.php, grâce à la commande suivante: 
	php csvtoxls.php (string $semestre[, string $option1[,int $date]])

***********************
* semestre
* 	La valeur du semestre en cours
* option1
* 	Pour le semestre 4
* date
*	l'année scolaire de septembre à juin
* 
* Ce qui est obligatoire c'est le semestre, le reste est optionnel 
* 
* Exemple:
*	php csvtoxls.php S1
*	php csvtoxls.php S2 20162017
*	php csvtoxls.php S4 IPI
*	php csvtoxls.php S4 IPI 20162017
***********************

Envoyer le/les fichier(s) excel notes aux enseignants responsables de matière (qui se trouve dans le fichier [DEPARTEMENT]/[ANNÉE]/[DEPARTEMENT_SEMESTRE_ANNÉE]/excel). Ensuite les enseignants renvoient ce fichier remplie a la fin de semestre à le/la secrétaire qui n'a plus qu'à faire un copier-coller les notes dans les onglets correspondant dans le fichier Bilan (qui se trouve dans le fichier [DEPARTEMENT]/[ANNÉE]/[DEPARTEMENT_SEMESTRE_ANNÉE]/excel). Revenir dans le dossier programme, lancer excelToJson.php, avec la commande suivante: 
	php excelToJson.php (string $semestre[, string $option1[,int $date]])

***********************
* semestre
* 	La valeur du semestre en cours
* option1
* 	Pour le semestre 4
* date
*	l'année scolaire de septembre à juin
* 
* Ce qui est obligatoire c'est le semestre, le reste est optionnel 
* 
* Exemple:
*	php excelToJson.php S1
*	php excelToJson.php S3 20162017
*	php excelToJson.php S4 IPI
*	php excelToJson.php S4 IPI 20162017
*************************

Et enfin lancer finale.php, grâce à la commande suivante: 
	php finale.php (string $semestre[, string $option1[,int $date]])

***********************
* semestre
* 	La valeur du semestre en cours 
* option1
* 	Pour le semestre 4
* date
*	l'année scolaire de septembre à juin
* 
* Ce qui est obligatoire c'est le semestre, le reste est optionnel 
*  
* Exemple:
*	php finale.php S1
* 	php finale.php S2 20162017
*	php finale.php S4 IPI
*	php finale.php S4 IPI 20162017
*************************

Le jour du jury:
Aller dans le dossier projet. Puis lancer index.html, appuyez sur le bouton "Parcourir", Prendre le fichier que vous avez créer, grâce finale.php puis appuyez sur le bouton "Affiche-moi le contenu du fichier" et laissez-vous guider






************************
* Ce qui nous manque:
*Verification des coefficient des UE lors de la creation du excel
*Moyenne promo, moyenne max et moyenne min lors de la creation du json
*Classement de la creation du json
*Reunir tous les informations des années precedentes (json)
* Ce qui reste a ameliorer:
*Sur le site, generaliser les tableaux
*Manque d'Optimisation dans les cellules de A à Z (excel)
*Optimisation lors de la crétion des notes (json)
*Ligne de couleur à la premiere du page du site (fonctionne pas lorsque classement change)
************************
