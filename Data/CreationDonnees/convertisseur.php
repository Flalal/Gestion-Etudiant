<?php
/*
 * Convertisseur CSV en JSON
 */

/*
 * lecture du fichier csv
 * */

//fichier csv
$feed='FeuilleDeNotes.ods';

$keys = array();
$newArray = array();

//fonction qui converti csv en un tableau associative
function csvToArray($file, $delimiter) { 
  if (($handle = fopen($file, 'r')) !== FALSE) { 
    $i = 0; 
    ///ffgetcsv() analyse la ligne qu'il lit et recherche les champs CSV, qu'il va retourner dans un tableau les contenant
    ///1: pointeur sur un fichier, 2: taille=>plus grande que la grande ligne, 3: le separateur (un seul caractére), 4:le caractére d'encadrement du texte (un seul caractére), 5: le caractére d'echapement (un seul caractere)=> par defaut c'est antislash
    while (($lineArray = fgetcsv($handle, 4000, $delimiter, '"')) !== FALSE) { 
      for ($j = 0; $j < count($lineArray); $j++) { 
        $arr[$i][$j] = $lineArray[$j];
        print_r("ligne ".$j." ".$lineArray[$j]);
      } 
      $i++; 
    } 
    fclose($handle); 
  } 
  return $arr; 
} 

$data = csvToArray($feed, ',');

//Définis le nombre d'éléments (au moins 1 car je declare la première ligne)
$count = count($data) - 1;
  
//utilise la premiée ligne pour les noms
//array_shift: extrait la premiére valeur d'un tableau et la retourne, en la supprimant et en déplaçant tous les éléments vers le bas
//Toutes les clés numériques seront modifiées pour commencer à zéro
$labels = array_shift($data);  
foreach ($labels as $label) {
  $keys[] = $label;
}

//Ajouts des identifiants, pour plus tard
$keys[] = 'id';
for ($i = 0; $i < $count; $i++) {
  $data[$i][] = $i;
}

$keyCount = count($keys);

$min=min($keyCount,$count);

//tous ensemble 
for ($j = 0; $j <$min; $j++) {
	print_r("[nombre element]> ".$count);
	print_r("[tableau]> ".$keyCount);
    $d[$keys] = $data[$j];
    
    /*
     * $d=$keys+$data[$j];
     * */
    //keys et data doivent avoir la même taille
	 //$d = array_combine($keys, $data[$j]);
	  $newArray[$j] = $d;
}

echo json_encode($newArray);

/*
 * ecriture dans le fichier liste.json 
 * */
 
$contenu_json=json_encode($newArray);

// Nom du fichier à créer
$nom_du_fichier = 'liste.json';

// Ouverture du fichier
// w+ permet si le fichier pas créer, de le créer
$fichier = fopen($nom_du_fichier, 'w+');

if($fichier==false){
	die("La crétion du fichier a échoué");
}

// Ecriture dans le fichier
fwrite($fichier, $contenu_json);

// Fermeture du fichier
fclose($fichier);

?>
