<?php
class VictronEnergy{
    private $url;
    private $api_key;
    private $id_access_token;
    private $id_installation;

    //definimos el constructor de la clase
    public function __construct()
    {
        $this->url = 'https://vrmapi.victronenergy.com/v2/';
        $this->api_key = 'Token 61ffd7dc0f459c485f527f1056d9492e6418e58adf74cd34be9ce768ac6fe576';
        $this->id_access_token = 2160468;
        $this->id_installation = 58178;
    }

    //definimos el getter y setter
    public function getUrl(){
        return $this->url;
    }
    public function setUrl($url){
        $this->url = $url;
    }
    public function getApiKey(){
        return $this->api_key;
    }
    public function setApiKey($api_key){
        $this->api_key = $api_key;
    }
    public function getIdAccessToken(){
        return $this->id_access_token;
    }
    public function setIdAccessToken($id_access_token){
        $this->id_access_token = $id_access_token;
    }
    public function getIdInstallation(){
        return $this->id_installation;
    }
    public function setIdInstallation($id_installation){
        $this->id_installation = $id_installation;
    }
    //Documentacion API https://vrm-api-docs.victronenergy.com/#/
}

?>