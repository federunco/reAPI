<?php
    require "./vendor/simple_html_dom.php";
    header('Content-Type: application/json');
    $version = "1.0";
    $useragent = $_SERVER['HTTP_USER_AGENT'];
    $referer = "https://family.axioscloud.it/Secret/REDefault.aspx?Customer_ID=91032760067&Customer_Producer=Axios&Customer_Title=ISTITUTO ISTRUZIONE SUPERIORE&Customer_Name=I.T.I. A. SOBRERO&Customer_WebSite=https://family.sissiweb.it/Secret/REStart.aspx?Customer_ID=91032760067&Customer_Demo=False&Customer_Active=True&Customer_IDC=7.0.0&Customer_C=False&Customer_C64=False&Ticket=de1b087f96155adb5de301ccc940d28f&DBIDX=CLOUD_RE_03&Type=RE";
    
    $refererEncoded = str_replace(" ", "%20", $referer);
    $username = $_GET["uname"];
    $password = $_GET["password"];

    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => $refererEncoded,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_HEADER => 1,
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => array(
        "Accept: */*",
        "Accept-Encoding: gzip, deflate",
        "Connection: keep-alive",
        "Host: family.axioscloud.it",
        "Referer: $referer",
        "User-Agent: $useragent"
      ),
    ));
    
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $response, $matches);
    $cookies = array();
    foreach ($matches[1] as $item) {
        parse_str($item, $cookie);
        $cookies = array_merge($cookies, $cookie);
    }

    $curl = curl_init();
    $sessionId = $cookies['ASP_NET_SessionId'];
    $antixsrf = $cookies["__AntiXsrfToken"];

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://family.axioscloud.it/Secret/RELogin.aspx",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HEADER => 1,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "Accept: */*",
            "Accept-Encoding: gzip, deflate",
            "Connection: keep-alive",
            "Cookie: ASP.NET_SessionId=$sessionId; __AntiXsrfToken=$antixsrf",
            "Host: family.axioscloud.it",
            "Referer: $referer",
            "User-Agent: $useragent"
        ),
    ));
    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    $curl = curl_init();

    preg_match_all('/<input\s+(?:[^"\'>]+|"[^"]*"|\'[^\']*\')*name=("[^"]+"|\'[^\']+\'|[^<>\s]+)/i', $response, $postRELoginData['name']); //Ottieni i name dagli input
    preg_match_all('/<input\s+(?:[^"\'>]+|"[^"]*"|\'[^\']*\')*value=("[^"]+"|\'[^\']+\'|[^<>\s]+)/i', $response, $postRELoginData['value']); //Ottieni i value dagli input

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://family.axioscloud.it/Secret/RELogin.aspx",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_HEADER => 1,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_HTTPHEADER => array(
            "Accept: */*",
            "Accept-Encoding: gzip, deflate",
            "Connection: keep-alive",
            "Cookie: ASP.NET_SessionId=$sessionId; __AntiXsrfToken=$antixsrf",
            "Host: family.axioscloud.it",
            "Referer: https://family.axioscloud.it/Secret/REDefault.aspx?Customer_ID=91032760067&Customer_Producer=Axios&Customer_Title=ISTITUTO ISTRUZIONE SUPERIORE&Customer_Name=I.T.I. A. SOBRERO&Customer_WebSite=https://family.sissiweb.it/Secret/REStart.aspx?Customer_ID=91032760067&Customer_Demo=False&Customer_Active=True&Customer_IDC=7.0.0&Customer_C=False&Customer_C64=False&Ticket=de1b087f96155adb5de301ccc940d28f&DBIDX=CLOUD_RE_03&Type=RE",
            "User-Agent: $useragent"
        ),
        CURLOPT_POSTFIELDS => array(
            trim($postRELoginData['name'][1][0], "\"") => trim($postRELoginData['value'][1][0], "\""),
            trim($postRELoginData['name'][1][1], "\"") => trim($postRELoginData['value'][1][1], "\""),
            trim($postRELoginData['name'][1][2], "\"") => trim($postRELoginData['value'][1][2], "\""),
            'txtUser' => $username,
            'txtPassword' => $password,
            'btnLogin' => "Accedi",
        )
    ));
    $response = curl_exec($curl);
    curl_close($curl);

    if (strpos($response, "Utente non presente o password errata")){
        $finalResponse = [
            "version" => $version,
            "authenticationHeader" => $authHead, 
            "status"  => array(
                "code" => 1,
                "description" => "Utente non presente o password errata"
            ),
        ];
        http_response_code(401);
        die(json_encode($finalResponse));
    } else {
        $curl = curl_init();
        $sessionId = $cookies['ASP_NET_SessionId'];
        $antixsrf = $cookies["__AntiXsrfToken"];
        
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://family.axioscloud.it/Secret/REFamily.aspx",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_HTTPHEADER => array(
                "Accept: */*",
                "Accept-Encoding: gzip, deflate",
                "Connection: keep-alive",
                "Cookie: ASP.NET_SessionId=$sessionId; __AntiXsrfToken=$antixsrf",
                "Host: family.axioscloud.it",
                "Referer: https://family.axioscloud.it/Secret/RELogin.aspx",
                "User-Agent: $useragent"
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);

        $html = str_get_html($response);

        $periodoAttuale = $html->find('#ContentPlaceHolderMenu_ddlFT option[selected]')[0]->value;
        $annoScolastico = $html->find('#ContentPlaceHolderMenu_ddlAnno option[selected]')[0]->value;
        $dataSelezionata = $html->find('#ContentPlaceHolderBody_txtDataSelezionataCAL')[0]->value;
        $functionSelected = $html->find('#ContentPlaceHolderBody_txtFunctionSelected')[0]->value;
        $alunnoSelezionato = $html->find('#ContentPlaceHolderBody_txtAluSelected')[0]->value;
        $table = $html->find('.TableMasterFamily')[0]; //Trova la tabella con la classe TableMasterFamily
        foreach ($table->children() as $key => $value) { //ogni figlio
            $aluninfo = $value->children()[0]->onclick; //del figlio seleziona il primo campo (img Genere) e ottieni l'attributo onclick
            $alunnoSelezionato = explode("\"", $aluninfo)[5]; 
        }
        if ($periodoAttuale == "FT01") $descPeriodo = "Primo Quadrimestre";
        if ($periodoAttuale == "FT02") $descPeriodo = "Secondo Quadrimestre";
        if ($periodoAttuale == "FT03") $descPeriodo = "Giudizi sospesi";

        $response = requestREFamily($response, "Curriculum");
        $html = str_get_html($response);
        $objAnnoCorrente =  $html->find('#curTab tr')[1]->find('td');
        $classe = $objAnnoCorrente[3]->plaintext;
        $sezione = $objAnnoCorrente[4]->plaintext;
        $corso = mb_convert_case($objAnnoCorrente[2]->plaintext, MB_CASE_TITLE, "UTF-8");

        $response = requestREFamily($response, "Anagrafico");
        $html = str_get_html($response);
        $objAnagrafica =  $html->find('#content-comunicazioni tr')[2]->find('.value');
        $cognome = mb_convert_case($objAnagrafica[0]->plaintext, MB_CASE_TITLE, "UTF-8");
        $nome = mb_convert_case($objAnagrafica[1]->plaintext, MB_CASE_TITLE, "UTF-8");
        $professori = array();
        $response = requestREFamily($response, "MD");
        $html = str_get_html($response);
        $objProfessori =  $html->find('#ddlDocente option');
        foreach ($objProfessori as $professore){
            $fullText = $professore->plaintext;
            $fullText = str_replace("(", "", $fullText);
            $fullText = str_replace(")", "", $fullText);
            $arr = explode(" - ", $fullText, 2);
            $finalProfessore["docente"] = $arr[0];
            $finalProfessore["materie"] = explode(", ", $arr[1]);
            array_push($professori, $finalProfessore);
        }

        array_shift($professori);

        $response = requestREFamily($response, "REC");
        $html = str_get_html($response);
        $objArgomenti =  $html->find('#content-comunicazioni tbody')[0]->find('tr');
        $reClasse = array();
        $compiti = array();
        foreach ($objArgomenti as $giornata){
            $contenuti = $giornata->find("td");
            $data = $contenuti[0]->plaintext;
            $listaArgomenti = explode(PHP_EOL, $contenuti[1]->plaintext);
            $listaCompiti = explode(PHP_EOL, $contenuti[2]->plaintext);
            $argomenti = array();
            foreach($listaArgomenti as $arg){
                $argobj = explode(": ", $arg);
                $argomento["materia"] = $argobj[0];
                $argomento["descrizione"] = $argobj[1];
                if (strlen($argobj[1]) > 0) array_push($argomenti, $argomento);
            }
            foreach($listaCompiti as $arg){
                $argobj = explode(": ", $arg);
                $compito["materia"] = $argobj[0];
                $compito["compito"] = addslashes($argobj[1]);
                $compito["data"] = $data;
                if (strlen($argobj[0]) > 0) array_push($compiti, $compito);
            }
            $argGiornata["data"] = $data;
            $argGiornata["argomenti"] = $argomenti;
            array_push($reClasse, $argGiornata);
        }

        $response = requestREFamily($response, "RED");
        $html = str_get_html($response);
        $objVoti =  $html->find('table.table tbody')[0]->find('tr');
        $reDocente = array();
        foreach ($objVoti as $voto){
            $contenuti = $voto->find("td");
            $votoTemp["data"] = $contenuti[0]->plaintext;
            $votoTemp["materia"] = mb_convert_case($contenuti[1]->plaintext, MB_CASE_TITLE, "UTF-8");
            $votoTemp["tipologia"]  = $contenuti[2]->plaintext;
            $votoTemp["voto"] = trim($contenuti[3]->plaintext);
            $votoTemp["commento"] = addslashes($contenuti[5]->plaintext);
            $votoTemp["docente"] = $contenuti[6]->plaintext;
            if (strlen($votoTemp["voto"]) > 0 && is_numeric(str_replace(",", ".", $votoTemp["voto"]))) array_push($reDocente, $votoTemp);
        }
        $response = requestREFamily($response, "Assenze");
        $html = str_get_html($response);
        $objAssenzeNG = $html->find('.table-responsive tbody')[0]->find('tr');
        $objAssenzeG = $html->find('.table-responsive tbody')[1]->find('tr');
        $assenzeNG = array();
        $assenzeG = array();
        foreach ($objAssenzeNG as $assenzaN){
        	$contenuti = $assenzaN->find("td");
        	$temp1 = explode('(', $contenuti[1]->plaintext)[1];
        	$motivazione = explode(')', $temp1)[0];
        	$temp2 = explode('[', $contenuti[1]->plaintext)[1];
        	$orario = explode(']', $temp2)[0];
            $assenzaNT["data"] = $contenuti[0]->plaintext;
            $assenzaNT["motivazione"] = $motivazione;
            $assenzaNT["tipologia"] = explode(" ", $contenuti[1]->plaintext)[0];
            $assenzaNT["orario"] = $orario;
 			array_push($assenzeNG, $assenzaNT);
        }
        
        foreach ($objAssenzeG as $assenzaG){
            $contenuti = $assenzaG->find("td");
        	$temp1 = explode('(', $contenuti[1]->plaintext)[1];
        	$motivazione = explode(')', $temp1)[0];
        	$temp2 = explode('[', $contenuti[1]->plaintext)[1];
        	$orario = explode(']', $temp2)[0];
            $assenzaGT["data"] = $contenuti[0]->plaintext;
            $assenzaGT["motivazione"] = $motivazione;
            $assenzaGT["tipologia"] = explode(" ", $contenuti[1]->plaintext)[0];
            $assenzaGT["orario"] = $orario;
 			array_push($assenzeG, $assenzaGT);
        }
        $curl = curl_init("https://family.axioscloud.it/Secret/APP_Ajax_Get.aspx?Action=READ_COMUNICAZIONI_FAMILY&Others=0");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_REFERER, "https://family.axioscloud.it/Secret/RELogin.aspx"); 
        curl_setopt($curl, CURLOPT_COOKIE, "__AntiXsrfToken=" .  $antixsrf. "; ASP.NET_SessionId=" . $sessionId);
        $response = curl_exec($curl);
        curl_close($curl);

        $html = str_get_html($response);
        $objComunicazioni =  $html->find('#content-comunicazioni tbody')[0]->find('tr');
        $comunicazioni =array();
        foreach ($objComunicazioni as $comunicazione){
            $contenuti = $comunicazione->find("td");
            $commTemp["data"] = $contenuti[0]->plaintext;
            $commTemp["mittente"] = $contenuti[1]->plaintext;
            $commTemp["contenuto"] = addslashes($contenuti[2]->plaintext);      
            array_push($comunicazioni, $commTemp);      
        }
        
        $utenteHead["matricola"] = $username;
        $utenteHead["nome"] = $nome;
        $utenteHead["cognome"] = $cognome;
        $utenteHead["classe"] = $classe;
        $utenteHead["sezione"] = $sezione;
        $utenteHead["corso"] = $corso;
        $utenteHead["periodo"] = $descPeriodo;

        $authHead["antixsrf"] = $antixsrf;
        $authHead["asp_sessionID"] = $sessionId;
        $finalResponse = [
            "version" => "$version",
            "authenticationHeader" => $authHead, //xsrf asp session
            "status"  => array(
                "code" => 0,
                "description" => "Login effettuato con successo"
            ), // errore ?
            "user" => $utenteHead, // dati utente
            "docenti" => $professori,
            "regclasse" => $reClasse,
            "comunicazioni" => $comunicazioni,
            "voti" => $reDocente,
            "compiti" => $compiti,
            "assenze" => array(
            	"nongiustificate" => $assenzeNG,
                "giustificate" => $assenzeG,
            ),
        ];
        http_response_code(200);
        $tempString = json_encode($finalResponse, JSON_HEX_APOS|JSON_HEX_QUOT);
        //echo html_entity_decode($tempString, ENT_QUOTES);
        echo str_replace("&quot;", '\"', $tempString);
        
    }

    function requestREFamily($response, $event){
        preg_match_all('/<input\s+(?:[^"\'>]+|"[^"]*"|\'[^\']*\')*name=("[^"]+"|\'[^\']+\'|[^<>\s]+)/i', $response, $postREData['name']); //Ottieni i name dagli input
        preg_match_all('/<input\s+(?:[^"\'>]+|"[^"]*"|\'[^\']*\')*value=("[^"]+"|\'[^\']+\'|[^<>\s]+)/i', $response, $postREData['value']); //Ottieni i value dagli input

        $postreq = array(
            '__EVENTTARGET' => 'FAMILY',
            '__EVENTARGUMENT' => $event,
            trim($postREData['name'][1][0], "\"") => trim($postREData['value'][1][0], "\""),
            trim($postREData['name'][1][1], "\"") => trim($postREData['value'][1][1], "\""),
            trim($postREData['name'][1][2], "\"") => trim($postREData['value'][1][2], "\""),
            'ctl00$ContentPlaceHolderMenu$ddlAnno' => $GLOBALS['annoScolastico'],
            'ctl00$ContentPlaceHolderMenu$ddlFT' => $GLOBALS['periodoAttuale'],
            'ctl00$ContentPlaceHolderBody$txtDataSelezionataCAL' => $GLOBALS['dataSelezionata'],
            'ctl00$ContentPlaceHolderBody$txtFunctionSelected' => $GLOBALS['functionSelected'],
            'ctl00$ContentPlaceHolderBody$txtAluSelected' => $GLOBALS['alunnoSelezionato'],
            'ctl00$ContentPlaceHolderBody$txtIDAluSelected' => 0
        );
        $antixsrf = $GLOBALS['antixsrf'];
        $sessionId = $GLOBALS['sessionId'];
        $useragent = $GLOBALS['useragent'];

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://family.axioscloud.it/Secret/REFamily.aspx",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => array(
                "Accept: */*",
                "Accept-Encoding: gzip, deflate",
                "Connection: keep-alive",
                "Cookie: ASP.NET_SessionId=$sessionId; __AntiXsrfToken=$antixsrf",
                "Host: family.axioscloud.it",
                "Referer: https://family.axioscloud.it/Secret/REFamily.aspx",
                "User-Agent: $useragent"
            ),
            CURLOPT_POSTFIELDS => $postreq
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }
?>

