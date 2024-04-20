<!DOCTYPE html>
<html>
    <head>
        <title>Client Rest</title>
        <link rel="stylesheet" href="stile.css">
    </head>
    <body style="font-family: Arial;">
        <h1>Codici postali</h1>
        <div>
            <h2>GET</h2>
            <input type="text" id="c_get">
            <button onclick="getA('cap')">ottieni cap</button>
            <button onclick="getA('comune')">ottieni comune</button>
            <button onclick="get()">ottieni tutto</button>
            <br><br>
            <table id="tabella" border="1"></table>
        </div>
        <div style="background:white">
            <h2>POST</h2>
            <input type="text" id="cap_post" placeholder="cap">
            <input type="text" id="comune_post" placeholder="comune">
            <button onclick="post()">aggiungi comune</button>
            <p id="risultato_post"></p>
        </div>
        <div>
            <h2>PUT</h2>
            <input type="text" id="cap_put" placeholder="cap">
            <input type="text" id="comune_put" placeholder="comune">
            <button onclick="put()">modifica comune</button>
            <p id="risultato_put"></p>
        </div>
        <div style="background:white">
            <h2>DELETE</h2>
            <input type="text" id="comune_delete" placeholder="comune">
            <button onclick="del()">elimina comune</button>
            <p id="risultato_del"></p>
        </div>
    </body>
    <script>
        function getA(a){
            var c = document.getElementById("c_get").value;
            var url = "http://localhost/codiciPostali/api.php/"+ a + "/" + c;
            var xhr = new XMLHttpRequest();
            xhr.open("GET", url, true);
            xhr.setRequestHeader("Content-Type", "application/json");
            xhr.setRequestHeader("Accept", "application/json");
            xhr.onreadystatechange = function(){
                if(xhr.readyState == 4 && xhr.status == 200){
                    //document.getElementById("tabella").innerHTML = xhr.responseText;
                    risposta = JSON.parse(xhr.responseText);
                    console.log(risposta);
                    if(a == "cap"){
                        creaTabellaA(risposta);
                    }else if(a == "comune"){
                        creaTabellaB(risposta);
                    }
                }
            }
            xhr.send();
        }
        function get(){
            var url = "http://localhost/codiciPostali/api.php";
            var xhr = new XMLHttpRequest();
            xhr.open("GET", url, true);
            xhr.setRequestHeader("Content-Type", "application/json");
            xhr.setRequestHeader("Accept", "application/json");
            xhr.onreadystatechange = function(){
                if(xhr.readyState == 4 && xhr.status == 200){
                    //document.getElementById("paragrafo").innerHTML = xhr.responseText;
                    risposta = JSON.parse(xhr.responseText);
                    console.log(risposta);
                    creaTabella(risposta);
                }
            }
            xhr.send();
        }
        function creaTabella(dati){
            var tabella = document.getElementById("tabella");
            tabella.innerHTML = "<tr><th>Cap</th><th>Comune</th></tr>";
            for(var i = 0; i < dati.length; i++){
                tabella.innerHTML += "<tr><td>" + dati[i].cap + "</td><td>"
                + dati[i].comune + "</td></tr>";
            }
        }
        function creaTabellaA(dati){
            var tabella = document.getElementById("tabella");
            tabella.innerHTML = "<tr><th>Cap</th><th>Comune</th></tr>";
            for(var i = 0; i < dati.length; i++){
                tabella.innerHTML += "<tr><td>" + document.getElementById("c_get").value + "</td><td>"
                + dati[i].comune + "</td></tr>";
            }
        }
        function creaTabellaB(dati){
            var tabella = document.getElementById("tabella");
            tabella.innerHTML = "<tr><th>Cap</th><th>Comune</th></tr>";
            for(var i = 0; i < dati.length; i++){
                tabella.innerHTML += "<tr><td>" + dati[i].cap + "</td><td>"
                + document.getElementById("c_get").value + "</td></tr>";
            }
        }
        function post(){
            var cap = document.getElementById("cap_post").value;
            var comune = document.getElementById("comune_post").value;
            var url = "http://localhost/codiciPostali/api.php";
            var xhr = new XMLHttpRequest();
            xhr.open("POST", url, true);
            xhr.setRequestHeader("Content-Type", "application/json");
            xhr.setRequestHeader("Accept", "application/json");
            xhr.onreadystatechange = function(){
                if(xhr.readyState == 4 && xhr.status == 200){
                    risposta = (xhr.responseText);
                    document.getElementById("risultato_post").innerHTML = risposta;
                }
            }
            xhr.send(JSON.stringify({"cap": cap, "comune": comune}));
        }
        function put(){
            var cap = document.getElementById("cap_put").value;
            var comune = document.getElementById("comune_put").value;
            var url = "http://localhost/codiciPostali/api.php/edit/" + comune;
            var xhr = new XMLHttpRequest();
            xhr.open("PUT", url, true);
            xhr.setRequestHeader("Content-Type", "application/json");
            xhr.setRequestHeader("Accept", "application/json");
            xhr.onreadystatechange = function(){
                if(xhr.readyState == 4 && xhr.status == 200){
                    risposta = (xhr.responseText);
                    document.getElementById("risultato_put").innerHTML = risposta;
                }
            }
            xhr.send(JSON.stringify({"cap": cap, "comune": comune}));
        }
        function del(){
            var comune = document.getElementById("comune_delete").value;
            var url = "http://localhost/codiciPostali/api.php/delete/" + comune;
            var xhr = new XMLHttpRequest();
            xhr.open("DELETE", url, true);
            xhr.setRequestHeader("Content-Type", "application/json");
            xhr.setRequestHeader("Accept", "application/json");
            xhr.onreadystatechange = function(){
                if(xhr.readyState == 4 && xhr.status == 200){
                    risposta = (xhr.responseText);
                    document.getElementById("risultato_del").innerHTML = risposta;
                }
            }
            xhr.send(JSON.stringify({"comune": comune}));
        }
    </script>
</html>