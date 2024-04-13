<?php

/*header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");*/

/*foreach($_SERVER as $chiave=>$valore){
    echo $chiave."-->".$valore."\n<br>";
}*/

$metodo=$_SERVER["REQUEST_METHOD"];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );

if(!isset($_SERVER["CONTENT_TYPE"])){
    echo "
    <html>
        <head>
            <title>Codici postali</title>
        </head>
        <body>
            <h1>Codici postali</h1>
            <p>Il servizio restituisce il codice postale di una località</p>
        </body>
    </html>";
}
else{
$ct=$_SERVER["CONTENT_TYPE"];
$type=explode("/",$ct);

$retct=$_SERVER["HTTP_ACCEPT"];
$ret=explode("/",$retct);
//echo $type[1];
//print_r($uri);
//echo "metodo-->".$metodo;

if ($metodo=="GET"){  
    $conn = new mysqli('localhost', 'root', '', 'codici_postali');
    if ($conn->connect_error) {
        die('Connessione fallita: ' . $conn->connect_error);
    }

    // Estrai il parametro dal percorso dell'URL
    $urlPath = explode('/', $_SERVER['REQUEST_URI']);
    $parametro = $urlPath[2] ?? null;
    $valore = $urlPath[3] ?? null;

    // Controllo sul secondo parametro e preparazione della query SQL
    if ($parametro === 'CAP' && !empty($valore)) {
        $valore = $conn->real_escape_string($valore); // Prevenire SQL Injection
        $query = "SELECT comune FROM comuni WHERE cap = '$valore'";
    } elseif ($parametro === 'comune' && !empty($valore)) {
        $valore = $conn->real_escape_string($valore); // Prevenire SQL Injection
        $query = "SELECT cap FROM comuni WHERE comune = '$valore'";
    } elseif (empty($parametro)) {
        $query = "SELECT * FROM comuni";
    } else {
        die('Errore: URL non valido');
    }

    // Esegui la query
    $result = $conn->query($query);

    $response = [];

    if ($result->num_rows > 0) {
        // Stampa i dati
        while ($row = $result->fetch_assoc()) {
            echo $row;
        }
    } else {
        echo 'Nessun risultato (database vuoto)';
    }

    // Chiudi la connessione
    $conn->close();
}
if ($metodo=="POST"){
    echo "post\n";
    //recupera i dati dall'header
   $body=file_get_contents('php://input');
   // echo $bodys
   
   //converte in array associativo
    if ($type[1]=="json"){
        $data = json_decode($body,true);
    }
    if ($type[1]=="xml"){
        $xml = simplexml_load_string($body);
        $json = json_encode($xml);
        $data = json_decode($json, true);
    }
    
    //elabora i dati o interagisce con il database
    $data["valore"]+=2000;
    
    //settaggio dei campi dell'header
    header("Content-Type: ".$retct);    
    //restituisce i dati convertiti nel formato richiesto
    if ($ret[1]=="json"){
        echo json_encode($data);
    }
    if ($ret[1]=="xml"){
        $xml = new SimpleXMLElement('<root/>');
        array_walk_recursive($data, array ($xml, 'addChild'));    
        echo $xml->asXML();
        //alternativa
        $r='<?xml version="1.0"?><rec><nome>'.$data["nome"].'</nome><valore>'.$data["valore"].'</valore></rec>';
    }
}
if ($metodo=="PUT"){
    echo "put";
    //codice di risposta
    http_response_code(404);
}
if ($metodo=="DELETE"){
    echo "delete";
    http_response_code(404);
}
}

?>