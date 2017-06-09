/**
 * Created by Frederic on 07/06/2017.
 */
/// base donnée en fonction des etudiant


var fs = require('fs');
var parse = require('csv-parse');


var MongoClient = require("mongodb").MongoClient;
var MongoObjectID = require("mongodb").ObjectID;
var csvData=[];
var inputFile='programme/INFO2_S3_20162017_Liste_Etudiants.csv';


MongoClient.connect("mongodb://localhost/iut", function(error, db) {
    if (error) throw error;


/*
    var parser = parse({delimiter: ','}, function (error, data) {
        if (error) throw error;

        // when all countries are available,then process them
        // note: array element at index 0 contains the row of headers that we should skip
        data.forEach(function(line) {
            // create country object out of parsed fields
            csvData.push(line);

        });


        for(var cpt=2;cpt<csvData.length;cpt++){
            var etudiantobjet=etudiant(csvData[cpt][0],csvData[cpt][1],csvData[cpt][2],csvData[cpt][3],csvData[cpt][4],csvData[cpt][5],csvData[cpt][6]);
            db.collection("etudiants").insert(etudiantobjet, null, function (error, results) {
                if (error) throw error;

                console.log("Le document a bien été inséré");
            });

        }



    });

    fs.createReadStream(inputFile).pipe(parser);*/


    db.collection("etudiants").find().toArray(function (error, results) {
        if (error) throw error;
        results.forEach(function(i, obj) {
            console.log(
                "ID : "  +i._id.toString() + "\n" +// 53dfe7bbfd06f94c156ee96e
                "Nom : " + i.name + "\n"   +        // Adrian Shephard
                "Prenom : " + i.prenom  +"\n"+ i.dateNaissance// Half-Life: Opposing Force
            );
        });
    });
});

function etudiant(numero,Nom,Prenom,groupe,dateN,bac,lycee) {
   return{_id: numero, nom:Nom, prenom:Prenom , dateNaissance:dateN,groupe:groupe,bac:bac,EtablisementPrecedents:lycee}
}







// read the inputFile, feed the contents to the parser






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
