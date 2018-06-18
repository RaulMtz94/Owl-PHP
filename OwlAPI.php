
<?php
    class Ontologia{
        private $ontologia;
        function cargarOntologia($archivo){
            //abrimos el archivo en lectura 
            $fp = fopen($archivo,'r');
            //leemos el archivo
            $fread = fread($fp, filesize($archivo));
            //transformamos los saltos de línea en etiquetas <br> y lo almacenamos en el atributo ontologia
            //$this->ontologia = nl2br($fread);
            $this->ontologia = $fread;
        }
        function getClasses(){
            if($this->ontologia == null){ 
                echo "Primero debe cargar una ontología, llame al método cargarOntología(\$file)";
                return;
            }
            $ontology = $this->ontologia;
            if (strpos($this->ontologia, 'owl:Class') !== false) {
                $array_classes[]="";
                preg_match_all('/:[a-zA-Z]+[a-zA-Z0-9]*\s(rdf:type owl:Class)/', $ontology, $coincidencias);
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
        function getObjectProperties($class){
            if($this->ontologia == null){ 
                echo "Primero debe cargar una ontología, llame al método cargarOntología(\$file)";
                return;
            }
            $ontology = $this->ontologia;
            if (strpos($ontology, 'owl:ObjectProperty') !== false) {
                preg_match_all('/:[a-zA-Z]+[a-zA-Z0-9]*\s(rdf:type owl:ObjectProperty ;\n)[\sa-z:;.]*/', $ontology, $coincidencias);
                for($i=0; $i<count($coincidencias[0]); $i++){
                    $clase = trim($this->sustituir("rdf:type owl:ObjectProperty ;",",", $coincidencias[0][$i]),":");
                    $clase = $this->sustituir("rdfs:","",$clase);
                    $clase = $this->sustituir(";",",",$clase);
                    $existe = strpos($clase,"domain :".$class);
                    if($existe==true){
                        $clase = $this->sustituir("domain :","",$clase);
                        $clase = $this->sustituir("range :","",$clase); 
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
        function getDataProperties($class){
            if($this->ontologia == null){ 
                echo "Primero debe cargar una ontología, llame al método cargarOntología(\$file)";
                return;
            }
            $ontology = $this->ontologia;
            if (strpos($ontology, 'owl:DatatypeProperty') !== false) { 
                preg_match_all('/:[a-zA-Z]+[a-zA-Z0-9]*\s(rdf:type owl:DatatypeProperty ;\n)[\sa-zA-ZñÑáéíóúÁÉÍÓÚ:;.]*/', $ontology, $coincidencias); 
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
        function getIndividuals(){
            if($this->ontologia == null){ 
                echo "Primero debe cargar una ontología, llame al método cargarOntología(\$file)";
                return;
            }
            $ontology = $this->ontologia;
            if (strpos($ontology, 'owl:NamedIndividual') !== false) {
                $array_individual = array();
                preg_match_all('/:[a-zA-Z]+[a-zA-Z0-9]*\s(rdf:type owl:NamedIndividual ,\n)+[\sa-z0-9A-ZñÑáéíóúÁÉÍÓÚ"^:;.]*/', $ontology, $coincidencias);
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
            if($this->ontologia == null){ 
                echo "Primero debe cargar una ontología, llame al método cargarOntología(\$file)";
                return;
            }
            $ontology = $this->ontologia;
            if (strpos($ontology, 'owl:NamedIndividual') !== false) {
                preg_match_all('/:[a-zA-Z]+[a-zA-Z0-9]*\s(rdf:type owl:NamedIndividual ,\n)+[\sa-z0-9A-ZñÑáéíóúÁÉÍÓÚ"^:;.]*/', $ontology, $coincidencias);
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
