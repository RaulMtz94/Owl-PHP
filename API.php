<?php
class Ontology{
    public $ontology;
            public function Ontology($url){
                //Validar que la ruta exista
                if(file_exists($url) === FALSE){
                exit("El archivo ingresado: $url no existe");
                }
                    $fp = fopen($url,'r');
                
                //Validar que el archivo tenga un formato correcto
                    $fread = fread($fp, filesize($url));
                    
                //Asignación del archivo a la variable $ontology
                if($fread === TRUE){
                    $this->ontology = $fread;
                }

                if($fread ===FALSE){
                    exit("Falló la apertura del flujo al URL");
                }
                $this->ontology = $fread;
            }
            
    //RENAME GetAllClass
    function getClasses(){

            if (strpos($this->ontology, 'owl:Class') !== false) {
                $array_classes[]="";
                preg_match_all('/:[a-zA-Z]+[a-zA-Z0-9]*\s(rdf:type owl:Class)/', $this->ontology, $coincidencias);
                for($i=0; $i<count($coincidencias[0]); $i++){ 
                    //Quitar rdf:typ... dejar sólo la clase y agregarla al arreglo
                    $array_classes[$i] = trim(substr($coincidencias[0][$i], 0, strpos($coincidencias[0][$i], "rdf:type owl:Class")),":");
                }
                return $array_classes;
            }else{
                //Retorna nullo si la ontología no tiene clases
                return null;
            }
        } 



        function getClass($class){

            if (strpos($this->ontology, 'owl:ObjectProperty') !== false) {
                $datos = null;
                $rango="";
                $nombre="";
                $dominio="";
                $tipo="";
                preg_match_all('/:[a-zA-Z]+[a-zA-Z0-9]*\s(rdf:type owl:ObjectProperty ;)[\sa-z:;.]*/', $this->ontology, $coincidencias);
                for($i=0; $i<count($coincidencias[0]); $i++){
                    $clase = trim($this->sustituir("rdf:type owl:ObjectProperty ;",",", $coincidencias[0][$i]),":");
                    $clase = $this->sustituir("rdfs:","",$clase);
                    $clase = $this->sustituir(";",",",$clase);
                    $clase1=$clase;
                    $existe = strpos($clase,"range :".$class);
                    
                   
                    $existe2 = strpos($clase,"domain :".$class);
                    
                    if ($existe==true){
                        $clase = $this->sustituir("domain :","",$clase1);
                        $clase = $this->sustituir("range xsd:","",$clase1); 
                        $clase = $this->sustituir(".","",$clase1); 
                        $clase = $this->sustituir(" ","",$clase1);  
                        $clase = $this->sustituir("\n","",$clase1);  
                        list($name2, $class2, $datatype2) = explode(",",$clase);
                        $rango = $rango. $name2;
                    }
                    if ($existe2==true){
                        $clase = $this->sustituir("domain :","",$clase1);
                        $clase = $this->sustituir("range xsd:","",$clase1); 
                        $clase = $this->sustituir(".","",$clase1); 
                        $clase = $this->sustituir(" ","",$clase1);  
                        $clase = $this->sustituir("\n","",$clase1);  
                        list($name, $class, $datatype) = explode(",",$clase);
                        $dominio = $dominio. $name;
                    }
                   

                }
                preg_match_all('/:[a-zA-Z]+[a-zA-Z0-9]*\s(rdf:type owl:DatatypeProperty ;)[\sa-zA-ZñÑáéíóúÁÉÍÓÚ:;.]*/', $this->ontology, $coincidencias); 
                for($i=0; $i<count($coincidencias[0]); $i++){
                    $clase = trim($this->sustituir("rdf:type owl:DatatypeProperty ;",",", $coincidencias[0][$i]),":");
                    $clase = $this->sustituir("rdfs:","",$clase);
                    $clase = $this->sustituir(";",",",$clase);
                    $clasebuscar= explode(":",$class);
                    
                    $existe3 = strpos($clase,"domain :".trim($clasebuscar[1]));
                    if($existe3==true){
                       
                        $clase = $this->sustituir("domain :","",$clase);
                        $clase = $this->sustituir("range xsd:","",$clase); 
                        $clase = $this->sustituir(".","",$clase); 
                        $clase = $this->sustituir(" ","",$clase);  
                        $clase = $this->sustituir("\n","",$clase);  
                        list($name3, $class3, $datatype3) = explode(",",$clase);
                        $nombre =  $name3;
                        $tipo = $datatype3;
                    }
                }
                $datos = array(
                    "nombre" => $nombre,
                    "tipo" => $tipo,
                    "rango" => $rango,
                    "dominio" =>  $dominio
                ); 
 
                return $datos;
            } 
            return null;
        }
        function getDataProperties($class){
        
            if (strpos($this->ontology, 'owl:DatatypeProperty') !== false) { 
                preg_match_all('/:[a-zA-Z]+[a-zA-Z0-9]*\s(rdf:type owl:DatatypeProperty ;)[\sa-zA-ZñÑáéíóúÁÉÍÓÚ:;.]*/', $this->ontology, $coincidencias); 
                for($i=0; $i<count($coincidencias[0]); $i++){
                    $clase = trim($this->sustituir("rdf:type owl:DatatypeProperty ;",",", $coincidencias[0][$i]),":");
                    $clase = $this->sustituir("rdfs:","",$clase);
                    $clase = $this->sustituir(";",",",$clase);
                    $existe = strpos($clase,"domain :".$class);
                    if($existe==true){
                        $clase = $this->sustituir("domain :","",$clase);
                        $clase = $this->sustituir("range xsd:","",$clase); 
                        $clase = $this->sustituir(".","",$clase); 
                        $clase = $this->sustituir(" ","",$clase);  
                        $clase = $this->sustituir("\n","",$clase);  
                        list($name, $class, $datatype) = explode(",",$clase);
                        $datos = array(
                            "name" => $name,
                            "domain" =>  $class,
                            "range" => $datatype
                        ); 
                    }
                }
                return $datos;
            } 
            return null;
        }
        function getAllIndividuals(){
            if (strpos($this->ontology, 'owl:NamedIndividual') !== false) {
                $array_individual = array();
                preg_match_all('/:[a-zA-Z]+[a-zA-Z0-9]*\s(rdf:type owl:NamedIndividual ,)+[\sa-z0-9A-ZñÑáéíóúÁÉÍÓÚ"^:;.]*/', $this->ontology, $coincidencias);
                for($i=0; $i<count($coincidencias[0]); $i++){
                    $clase = trim($this->sustituir("rdf:type owl:DatatypeProperty ;",",", $coincidencias[0][$i]),":");
                    $clase = $this->sustituir("rdfs:","",$clase);
                    $clase = $this->sustituir("range xsd:",",tipo:",$clase);
                    $clase = $this->sustituir(";","",$clase);
                    $array_individual[$i]= $clase;
                }
            } 
            return $array_individual;
        }
        function getIndividual($individual){
            if (strpos($this->ontology, 'owl:NamedIndividual') !== false) {
                preg_match_all('/:[a-zA-Z]+[a-zA-Z0-9]*\s(rdf:type owl:NamedIndividual ,)+[\sa-z0-9A-ZñÑáéíóúÁÉÍÓÚ"^:;.]*/', $this->ontology, $coincidencias);
                for($i=0; $i<count($coincidencias[0]); $i++){
                    $clase = trim($this->sustituir("rdf:type owl:DatatypeProperty ;",",", $coincidencias[0][$i]),":");
                    $clase = $this->sustituir("rdfs:","",$clase);
                    $clase = $this->sustituir("range xsd:",",tipo:",$clase); 
                    $clase = $this->sustituir("\n"," ", $clase); 
                    //echo "$clase<br><br><br>";
                    $temporal = array(); 
                    $temporal = explode(";", $clase); 
                    if(strpos(" ".$temporal[0], $individual)){
                        $name = $this->sustituir(" rdf:type owl:NamedIndividual ,","", $temporal[0]);
                        $name = $this->sustituir(" ","", $name);
                        list($valor, $name) = explode(":", $name);

                        $datos = array(
                            $name => $valor
                        ); 

                        for($i=1; $i<count($temporal)-1; $i++){
                            $item = trim($this->sustituir("             ", "", $temporal[$i])); 
                            $item = $this->sustituir(":", "", $item); 
                            list($clave, $valor) = explode(" ", $item);
                            $datos[$clave] = $valor;
                        }
                        $ultimoItem = $temporal[count($temporal)-1];
                        $ultimoItem = trim($this->sustituir("             ", "", $ultimoItem)); 

                        if(strpos($ultimoItem, "\"")){
                            list($clave, $valor, $datatype) = explode("\"",$ultimoItem);
                        }else{
                            list($clave, $valor) = explode(" ",$ultimoItem); 
                        }
                        
                        $clave = $this->sustituir(":","", $clave);
                        $datos[$clave] = $valor;
                        
                        return $datos;
                    } 
                }
            } 
           
        }

        // Métodos para manipulación de cadenas para uso de la propia API.
        function before($t, $inthat){
            return substr($inthat, 0, strpos($inthat, $t));
        }
        function sustituir($original,$sustituir,$cadena){
            return str_replace($original, $sustituir, $cadena);
        }
    } 
?>


