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

$acceptType=$_SERVER["HTTP_ACCEPT"];
$ret=explode("/",$acceptType);
//print_r($uri);
//echo "metodo-->".$metodo;

$requestData = file_get_contents('php://input');

// Determina il formato dei dati (JSON o XML)
if ($acceptType === 'application/json') {
    $requestData = json_decode($requestData, true);
} elseif ($acceptType === 'application/xml') {
    $requestData = simplexml_load_string($requestData);
} else {
    // Tipo di contenuto non valido
    http_response_code(415);
    echo 'Tipo di contenuto non valido';
    exit;
}


$risposta = null;
if ($metodo=="GET"){  
    $conn = new mysqli('localhost', 'root', '', 'codici_postali');
    if ($conn->connect_error) {
        die('Connessione fallita: ' . $conn->connect_error);
    }

    // Estrai il parametro dal percorso dell'URL
    $urlPath = explode('/', $_SERVER['REQUEST_URI']);
    $parametro = $urlPath[3] ?? null;
    $valore = $urlPath[4] ?? null;

    // Controllo sul secondo parametro e preparazione della query SQL
    if ($parametro === 'cap' && !empty($valore)) {
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
            $response[] = $row;
        }
    } else {
        $response = ['message' => 'Nessun risultato (database vuoto)'];
    }

    // Chiudi la connessione
    $conn->close();

    $risposta = $response;
}
if ($metodo=="POST"){
    echo "post";
    $urlPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if ($urlPath !== '/wsphp/ADD') {
        http_response_code(400);
        $risposta = ['status' => 'errore', 'message' => 'URL non valido'];
    }

    // Convalida i dati
    if (isset($requestData['cap']) && isset($requestData['comune'])) {
        // Inserisci i dati nel database
        $cap = $requestData['cap'];
        $comune = $requestData['comune'];

        // Crea una connessione al database
        $conn = new mysqli('localhost', 'root', '', 'codici_postali');

        // Controlla la connessione
        if ($conn->connect_error) {
            die('Connessione fallita: ' . $conn->connect_error);
        }

        // Query SQL per inserire i dati
        $query = "INSERT INTO comuni (cap, comune) VALUES ('$cap', '$comune')";

        // Esegui la query
        if ($conn->query($query) === TRUE) {
            // Imposta i dati di risposta
            $responseData = ['cap' => $cap, 'comune' => $comune];
        } else {
            $responseData = ['status' => 'errore', 'message' => 'Errore: ' . $query . '<br>' . $conn->error];
        }

        // Chiudi la connessione
        $conn->close();

        $risposta = $responseData;
    } else {
        // Dati non validi
        http_response_code(400);
        $risposta = ['status' => 'errore', 'message' => 'Dati non validi'];
    }
}
if ($metodo=="PUT"){
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $uriParts = explode( '/', $uri );

    // Verifica che il terzo parametro sia "EDIT" e che il quarto parametro sia presente
    if ($uriParts[3] !== 'edit' || !isset($uriParts[4])) {
        http_response_code(400);
        $risposta = ['status' => 'errore', 'message' => 'URL non valido'];
    }

    // Usa il quarto parametro come "codicePostale"
    $comune = $uriParts[4];

    // Convalida i dati
    if (isset($requestData['cap'])) {
        // Aggiorna i dati nel database
        $cap = $requestData['cap'];

        // Crea una connessione al database
        $conn = new mysqli('localhost', 'root', '', 'codici_postali');

        // Controlla la connessione
        if ($conn->connect_error) {
            die('Connessione fallita: ' . $conn->connect_error);
        }

        // Query SQL per aggiornare i dati
        $query = "UPDATE comuni SET cap = '$cap' WHERE comune = '$comune'";

        // Esegui la query
        if ($conn->query($query) === TRUE) {
            // Imposta i dati di risposta
            $responseData = ['cap' => $cap, 'comune' => $comune];
        } else {
            $responseData = ['status' => 'errore', 'message' => 'Errore: ' . $query . '<br>' . $conn->error];
        }

        // Chiudi la connessione
        $conn->close();

        $risposta = $responseData;
    } else {
        // Dati non validi
        http_response_code(400);
        $risposta = ['status' => 'errore', 'message' => 'Dati non validi'];
    }
}
if ($metodo=="DELETE"){
    // Ottieni l'URI e dividilo in parti
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $uriParts = explode( '/', $uri );

    // Verifica che il terzo parametro sia "DEL" e che il quarto parametro sia presente
    if ($uriParts[3] !== 'delete' || !isset($uriParts[4])) {
        http_response_code(400);
        $risposta = ['status' => 'errore', 'message' => 'URL non valido'];
    }

    // Usa il quarto parametro come "codicePostale"
    $comune = $uriParts[4];

    // Crea una connessione al database
    $conn = new mysqli('localhost', 'root', '', 'codici_postali');

    // Controlla la connessione
    if ($conn->connect_error) {
        die('Connessione fallita: ' . $conn->connect_error);
    }

    // Query SQL per eliminare i dati
    $query = "DELETE FROM comuni WHERE comune = '$comune'";

    // Esegui la query
    if ($conn->query($query) === TRUE) {
        // Imposta i dati di risposta
        $responseData = ['status' => 'successo', 'message' => 'Dato eliminato con successo'];
    } else {
        $responseData = ['status' => 'errore', 'message' => 'Errore: ' . $query . '<br>' . $conn->error];
    }

    // Chiudi la connessione
    $conn->close();

    $risposta = $responseData;
}
// Funzione per convertire un array in formato XML
function xml_encode($data) {
    $xml = new SimpleXMLElement('<root/>');
    array_to_xml($data, $xml);
    return $xml->asXML();
}

// Funzione ricorsiva per convertire un array in formato XML
function array_to_xml($data, &$xml) {
    foreach ($data as $key => $value) {
        if (is_array($value)) {
            if (is_numeric($key)) {
                $key = 'item' . $key;
            }
            $subnode = $xml->addChild($key);
            array_to_xml($value, $subnode);
        } else {
            $xml->addChild("$key", htmlspecialchars("$value"));
        }
    }
}
// Formatta la risposta in base al tipo di contenuto accettato
if ($acceptType === 'application/json') {
    header('Content-Type: application/json');
    echo json_encode($risposta);
} elseif ($acceptType === 'application/xml') {
    header('Content-Type: application/xml');
    echo xml_encode($risposta); 
} else {
    // Tipo di contenuto non accettato
    http_response_code(406);
    echo 'Tipo di contenuto non accettato';
    exit;
}
}

?>