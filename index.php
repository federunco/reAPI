<?php
    require "vendor/simple_html_dom.php";    
    $version = "1.0";
    $useragent = "SoAutomated/2019.1";
    $referer = "https://family.axioscloud.it/Secret/REDefault.aspx?Customer_ID=91032760067&Customer_Producer=Axios&Customer_Title=ISTITUTO ISTRUZIONE SUPERIORE&Customer_Name=I.T.I. A. SOBRERO&Customer_WebSite=https://family.sissiweb.it/Secret/REStart.aspx?Customer_ID=91032760067&Customer_Demo=False&Customer_Active=True&Customer_IDC=7.0.0&Customer_C=False&Customer_C64=False&Ticket=de1b087f96155adb5de301ccc940d28f&DBIDX=CLOUD_RE_03&Type=RE";
    
    $refererEncoded = str_replace(" ", "%20", $referer);
    $username = $_POST["username"];
    $password = $_POST["password"];

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
        //errore
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
        foreach ($objArgomenti as $giornata){
            $contenuti = $giornata->find("td");
            $data = $contenuti[0]->plaintext;
            $argomentiL = explode("<br>", $contenuti[1]->innerhtml);
            echo $data;
            print_r($argomentiL);
        }
        //AJAX

        $curl = curl_init("https://family.axioscloud.it/Secret/APP_Ajax_Get.aspx?Action=READ_COMUNICAZIONI_FAMILY&Others=0");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_REFERER, "https://family.axioscloud.it/Secret/RELogin.aspx"); 
        curl_setopt($curl, CURLOPT_COOKIE, "__AntiXsrfToken=" .  $antixsrf. "; ASP.NET_SessionId=" . $sessionId);
        $response = curl_exec($curl);
        curl_close($curl);

        $html = str_get_html($response);
        $objComunicazioni =  $html->find('#content-comunicazioni tbody')[0]->find('tr');
        $comunicazioni = array();
        foreach ($objComunicazioni as $comunicazione){
            $contenuti = $comunicazione->find("td");
            $commTemp["data"] = $contenuti[0]->plaintext;
            $commTemp["mittente"] = $contenuti[1]->plaintext;
            $commTemp["contenuto"] = $contenuti[2]->plaintext;      
            array_push($comunicazioni, $commTemp);      
        }

        
        

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

<html>
<head>
    <title>Registro Elettronico</title>
    <?php get_header(); ?>
    <main id="content">
        <div class="cardContainer">
            <div class="card usr">
                <div vcentr>
                    <img src="https://www.fnordware.com/superpng/pnggrad16rgb.png">
                <h1>Ciao <?php echo $nome . "!"?></h1>
                <h2 style="font-weight: initial;">Classe <?php echo "$classe $sezione - $corso";?></h2>
                </div>
            </div>
            
            <div class="card">
                <div vcentr>
                <h2>La tua media attuale</h2>
                <div grade mark="9" class="goodM"><span>9,5</span></div>
                <br>
                <a class="link" href="#" centered>
                    <span>Vai ai voti</span>
                    <span glyph style="font-size: 12px; margin-top: 4px; margin-left: 12px;">&#xE72A;</span>
                </a>
                </div>
            </div>
            <?php if (count($comunicazioni) > 0): ?>
            <div class="card">
                <div vcentr>
                <h2>Ultima comunicazione da <?php echo $comunicazioni[0]["mittente"]; ?></h2>
                <p>
                <?php echo substr($comunicazioni[0]["contenuto"], 0, 175) . "..."; ?>
                </p>
                <a class="link" href="#" centered>
                    <span>Visualizza altro</span>
                    <span glyph style="font-size: 12px; margin-top: 4px; margin-left: 12px;">&#xE72A;</span>
                </a>
                </div>
            </div>
            <?php else : ?>
            <div class="card">
                <div vcentr>
                <h2>Nessuna comunicazione disponibile</h2>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </main>
    <style>
    .reuser {
  display:grid;
  grid-template-rows: 50px 1fr;
  grid-template-columns: 100px, 1fr;
  grid-template-areas: "a b"
                       "a c";
  align-items: center;
}

.card h2 {
  font-size: 15px;
  min-height: 28px;
  font-weight: 600;
    margin-bottom: 16px;
    padding: 0;
}

.card {
  position: relative;
  outline: 2px solid transparent;
  border: 0;
  height: 360px;
  text-align: center;
  align-items: center;
  transform: translate3d(0,0,0);
    transition-property: box-shadow,transform;
    transition-duration: 400ms;
    transition-timing-function: cubic-bezier(.16,1,.29,.99);
    -ms-flex-positive: 1;
    flex-grow: 1;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,.05);
    background-color: #fff;
}

[grade]{
  display: inline-block;
  font-size: 75px;
  background-color: red;
  width: 170px;
  height: 170px;
  border-radius: 50%;
  text-align: center;
  vertical-align: middle;
  margin: 20px;
}

.cardContainer {
  margin-top: 70px;
  display: grid;
    grid-gap: 10px;
        grid-template-columns: repeat(auto-fill, minmax(230px, 1fr) ) ;
}

[grade] > span {
  position: relative;
  top: 50%;
  transform: translateY(-50%);
  text-align: center;
  display: block;
}

.goodM {
  color: black;
   background: linear-gradient(to right, #a8ff78, #78ffd6);
}

.goodM::before {
  content: "";
  background: linear-gradient(to right, #a8ff78, #78ffd6);
  width: 170px;
  height: 170px;
  display: block;
  border-radius: 50%;
  position: absolute;
  /* left: -10px; */
  filter: blur(10px);
}

.card:hover {
  transform: translate3d(0,-4px,0);
    box-shadow: 0 12px 30px 0 rgba(0,0,0,.2);
    transition-property: box-shadow,transform;
    transition-duration: 600ms;
    transition-timing-function: cubic-bezier(.16,1,.29,.99);
}

[centered]{
  display: inline;
  margin: 0px;
  font-weight: 800
}

.usr  img {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  box-shadow: 0px 10px 20px rgba(0,0,0,0.1)
}

[vcentr] {
    position: relative;
    top: 50%;
    transform: translateY(-50%);
}

.card h2 {
  margin-top: 0px;
}

.card h1 {
  font-size: 20px;
}
</style>
</body>
</html>