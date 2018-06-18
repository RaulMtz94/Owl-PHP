<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <?php include "./API.php"?>
    <title>Probando API ontología en PHP</title>
</head>
<body>
    <?php
        $ontologia = new Ontology("carreras.owl");
        //$ontologia->cargarOntologia("carrera.owl");
        echo "############################## CLASES ################################<br>";
        print_r($ontologia->getClasses());
        echo "<br><br>########################## PROPIEDADES DE CLASSE ###########################<br>";
        echo "<br>PRUEBA CON CARRERA<br>";
        print_r($ontologia->getClass("carrera"));
        echo "<br>PRUEBA CON MATERIA<br>";
        print_r($ontologia->getClass("materia"));
        echo "<br>############################## INDIVIDUOS ################################<br>";
        print_r($ontologia->getAllIndividuals());
       

        echo "<br><br>################### INDIVIDUALS ESTUDIANTE1 ##########################<br>";
        print_r($ontologia->getIndividual("estudiante1"));
        echo "<br><br>##################### INDIVIDUALS CARRERA1 ###########################<br>";
        print_r($ontologia->getIndividual("carrera1")); 
        echo "<br><br>###################### INDIVIDUALS GRUPO1#######################<br>";
        print_r($ontologia->getIndividual("grupo1")); 
        echo "<br><br>######################### PRUEBA FINAL #########################<br>";
        
        //El arreglo $estudiante1 recibirá las propiedades del individuo "estudiante1"
        $estudiante1 = $ontologia->getIndividual("estudiante1");
        print_r($estudiante1);
        
        $materia1 = $ontologia->getIndividual("materia1");
        $carrera1 = $ontologia->getIndividual("carrera1");
        $grupo1 = $ontologia->getIndividual("grupo1");
/*
        // Imprimir todos los campos del individuo
        foreach($estudiante1 as $campo){
            $clave = array_keys($estudiante1, $campo); 
            echo "$clave[0] = $campo <br>";
        }
*/
        //Imprimir los campos con un "Join"
        echo "estudiante: ".$estudiante1['estudiante'];
        echo "<br>nombre: ".$estudiante1['nombreEstudiante '];
        echo "<br>cursa: ".$materia1['nombreMateria '];
        echo "<br>pertenece: ".$grupo1['grupoID'];
    
        $nombre="name";
        $texto="asdas";

        $lins = file("carreras.owl");

        $LINE_TO_WRITE = count($lins);
        $lins[126] = "Texto desde PHP";
        
        // Escribimos de nuevo el texto en el archivo (versión BRUTUS)
        $fh = fopen("carreras.owl", 'wt'); //** Atención en el modo
        fwrite($fh, implode('', $lins));
        fclose($fh);

    ?>
</body>
</html>