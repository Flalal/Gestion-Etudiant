/**
 * Created by Frederic on 07/06/2017.
 */
/
/// base donnée en fonction des etudiant


var fs = require('fs');
var parse = require('csv-parse');



function etudiant(numero,nom,prenom,dateN,departement,groupe,bac,lycee) {
    this._id=numero;
    this.nom=nom;
    this.prenom=prenom;
    this.dateNaissance=dateN;
    this.departatement=departement;
    this.groupe=groupe;
    this.bac=bac;
    this.ancienneEtablisement =lycee;
}
var csvData=[];
var inputFile='programme/INFO2_S3_20162017_Note_detail_S3.csv';

var parser = parse({delimiter: ','}, function (error, data) {
    if (error) throw error;

    // when all countries are available,then process them
    // note: array element at index 0 contains the row of headers that we should skip
    data.forEach(function(line) {
        // create country object out of parsed fields
        csvData.push(line);

    });

    return csvData;



});



// read the inputFile, feed the contents to the parser
var tmp =fs.createReadStream(inputFile).pipe(parser);

console.log(tmp);




/*
var fs = require('fs');
var parse = require('csv-parse');

var csvData=[];
fs.createReadStream("programme" + "/INFO2_S3_20162017_Note_detail_S3.csv").pipe(parse({delimiter: ','}))
    .on('data', function(csvrow) {
        //do something with csvrow
        csvData.push("salut");
       //console.log(csvrow);
    });

csvData.push("salut");
console.log(csvData);
*/

/*
/// connextion à la base donne mongo
var MongoClient = require("mongodb").MongoClient;
var MongoObjectID = require("mongodb").ObjectID;



MongoClient.connect("mongodb://localhost/iut", function(error, db) {
    if (error) throw error;


    var objNew = { _id :"1234567890", name: "GLaDOS", game: "Portal", prenom:"toto"};

    db.collection("etudiants").insert(objNew, null, function (error, results) {
        if (error) throw error;

        console.log("Le document a bien été inséré");
    });

    db.collection("etudiants").find().toArray(function (error, results) {
        if (error) throw error;
        results.forEach(function(i, obj) {
            console.log(
                "ID : "  +i._id.toString() + "\n" +// 53dfe7bbfd06f94c156ee96e
            "Nom : " + i.name + "\n"   +        // Adrian Shephard
            "Jeu : " + i.game   +"\n"+ i.prenom// Half-Life: Opposing Force
            );
        });
    });
});*/
