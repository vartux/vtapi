<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class EvolutionApiService
{
    private $apikey = "B6D711FCDE4D4FD5936544120E713976";
    //private $baseUrl = "http://85.190.242.172:8080";
    private $baseUrl = "https://evoapi.vtfotografia.com";

    private $requestOptions;
    private $requestParams;

    private $urls = [
        "getInstances" => "/instance/fetchInstances",
        "connect" => "/instance/connect/",
        "create" => "/instance/create",
        "disconnect" => "/instance/logout/",
        "delete" => "/instance/delete/",
        "sendSms" => "/message/sendText/",
        "sendMedia" => "/message/sendMedia/"
    ];

    private $instanceType;
    private $account_id;
    private $account_token;

    public function __construct()
    {
        $this->addRequestOption("apikey", $this->apikey);
    }

    /**
     * setters
     */
    public function setInstanceType($instacnceType)
    {
        $this->instanceType = $instacnceType;
    }

    public function setAccountId($accountId)
    {
        $this->account_id = $accountId;
    }

    public function setAccountToken($token)
    {
        $this->account_token = $token;
    }
    /**
     * End setters
     */


    /**
     * Agrega opcion al header de request
     */
    public function addRequestOption($key, $value)
    {
        $this->requestOptions[$key] = $value;
    }

    /**
     * Agrega opcion al body de request
     */
    public function addBodyOption($key, $value)
    {
        $this->requestParams[$key] = $value;
    }


    public function connect($url, $instance, $method = "get")
    {
        //var_dump( $this->urls[$url] . $instance);die;
        //var_dump($this->baseUrl . $this->urls[$url] . $instance);die;
        /*$optionsArray = [
            'apikey' =>  $this->apikey,
        ];

        if (isset($options)) {
            array_push($optionsArray, $options);
        }*/
        $rs = $this->baseUrl . $this->urls[$url] . $instance;
        $response = Http::withHeaders($this->requestOptions)->$method($rs, $this->requestParams);

        return $response;
    }

    /**
     * Obtiene las instancias de evolution api 
     */
    public function getInstances()
    {
        $response = $this->connect("getInstances", "");
        // Verifica si la solicitud fue exitosa (código de estado 2xx)
        if ($response->successful()) {
            // Obtiene los datos de la respuesta en formato JSON
            return $response->json();
        } else {
            // Maneja el caso en que la solicitud no sea exitosa
            return response()->json(['error' => 'Error al obtener datos desde la API'], $response->status());
        }
    }
    /**
     * metodo que general el QR para logearse en api desde WS
     */
    public function logIn($instance)
    {
        $response = $this->connect("connect", $instance);
        // Verifica si la solicitud fue exitosa (código de estado 2xx)
        if ($response->successful()) {
            // Obtiene los datos de la respuesta en formato JSON
            return $response->json();
        } else {
            // Maneja el caso en que la solicitud no sea exitosa
            return response()->json(['error' => 'Error al obtener datos desde la API'], $response->status());
        }
    }

    /**
     * metodo que crea la instancia para luego leer el codigo QR
     */
    public function create($instance)
    {
        $this->addBodyOption("instanceName", $instance); #addParams
        $this->addBodyOption("qrcode", true); #addParams

        if ($this->instanceType == 1) { #chatwootinstance
            $this->addBodyOption("chatwoot_account_id", $this->account_id); #addParams
            $this->addBodyOption("chatwoot_token", $this->account_token); #addParams
            $this->addBodyOption("chatwoot_url", "https://chat.pixelstudiove.com/"); #addParams
            $this->addBodyOption("chatwoot_sign_msg", false); #no firmar mensajes en chats
            $this->addBodyOption("chatwoot_reopen_conversation", true); #addParams
            $this->addBodyOption("chatwoot_conversation_pending", false); #addParams
        }

        $response = $this->connect("create", '', 'post');

        // Verifica si la solicitud fue exitosa (código de estado 2xx)
        if ($response->successful()) {
            // Obtiene los datos de la respuesta en formato JSON
            return $response->json();
        } else {
            // Maneja el caso en que la solicitud no sea exitosa
            return response()->json(['error' => 'Error al obtener datos desde la API'], $response->status());
        }
    }
    /**
     * metodo que envia mensaje
     */
    public function sendSms($data)
    {
        $this->addBodyOption("number", $data["phone"]); #addParams
        $this->addBodyOption("textMessage", array("text" => $data["message"])); #addParams

        $response = $this->connect("sendSms", $data["instanceName"], 'post');

        // Verifica si la solicitud fue exitosa (código de estado 2xx)
        if ($response->successful()) {
            // Obtiene los datos de la respuesta en formato JSON
            return $response->json();
        } else {
            // Maneja el caso en que la solicitud no sea exitosa
            return response()->json(['error' => 'Error al obtener datos desde la API'], $response->status());
        }
    }
    /**
     * metodo que envia media files
     *  Example:
     * "mediatype": "image",
     * "fileName": "vt_0514", //no es necesarioo si es image type
     * "caption": "vt_0514",
     * "media": "https://deportes.vtfotografia.com/storage/images/2024/MMV2024/VT_0008-0589.jpg"
     */
    public function sendMedia($data)
    {

        $this->addBodyOption("number", $data["phone"]); #addParams
        $this->addBodyOption("mediaMessage", $data["image"]); #addParams 
        $this->addBodyOption("options", array("delay" => 1)); #addParams

        $response = $this->connect("sendMedia", $data["instanceName"], 'post');
        var_dump($response->getBody()->getContents());
        // Verifica si la solicitud fue exitosa (código de estado 2xx)
        if ($response->successful()) {
            // Obtiene los datos de la respuesta en formato JSON
            return $response->json();
        } else {
            // Maneja el caso en que la solicitud no sea exitosa
            return response()->json(['error' => 'Error al obtener datos desde la API', "message" => $response->getBody()->getContents()], $response->status());
        }
    }
    /**
     * metodo logOut instance
     */
    public function logOut($instance)
    {
        $response = $this->connect("disconnect", $instance, "delete");
        //var_dump($response->body());die;
        if ($response->successful()) {
            // Obtiene los datos de la respuesta en formato JSON
            return $response->json();
        } else {
            // Maneja el caso en que la solicitud no sea exitosa
            return response()->json(['error' => 'Error al obtener datos desde la API'], $response->status());
        }
    }
    /**
     * metodo delete instance
     */
    public function delete($instance)
    {
        $response = $this->connect("delete", $instance, "delete");
        //var_dump($response->body());die;
        if ($response->successful()) {
            // Obtiene los datos de la respuesta en formato JSON
            return $response->json();
        } else {
            // Maneja el caso en que la solicitud no sea exitosa
            return response()->json(['error' => 'Error al obtener datos desde la API'], $response->status());
        }
    }
}
