<?php
require_once '../utils/HttpClient.php';
require_once '../models/SolarEdge.php';

class SolarEdgeService {
    private $solarEdge;
    private $httpClient;

    public function __construct() {
        $this->solarEdge = new SolarEdge();
        $this->httpClient = new HttpClient();
    }

    public function getPoweDashboard($siteId, $dia, $fechaHoy = null, $fechaFin = null, $periodDuration = null, $billingCycle = null) {
        if($fechaFin == null){
        // Obtener la fecha de hoy a las 23:59:59
        $fechaFin = new DateTime('today 23:59:59');
        }

        // Convertirla a timestamp en milisegundos
        $timestampMs = $fechaFin->getTimestamp() * 1000;
        switch($dia){
            case "DAY":
                $url = $this->solarEdge->getUrl() . "solaredge-apigw/api/site/$siteId/powerDashboardChart?chartField=$dia&foldUp=true&activeTab=0&fieldId=$siteId&endDate=$timestampMs&perserveTabsData=true;";
                break;
            case "WEEK":
                $url = $this->solarEdge->getUrl() . "solaredge-apigw/api/site/$siteId/powerDashboardChart?chartField=$dia&foldUp=true&activeTab=1&fieldId=$siteId&endDate=$timestampMs&perserveTabsData=true;";
                break;
            case "MONTH":
                $url = $this->solarEdge->getUrl() . "solaredge-apigw/api/site/$siteId/powerDashboardChart?chartField=$dia&foldUp=true&activeTab=2&fieldId=$siteId&endDate=$timestampMs&perserveTabsData=true;";
                break;
            case "YEAR":
                $url = $this->solarEdge->getUrl() . "solaredge-apigw/api/site/$siteId/powerDashboardChart?chartField=$dia&foldUp=true&activeTab=4&fieldId=$siteId&endDate=$timestampMs&perserveTabsData=true;";
                break;
            case "CUSTOM":
                if($fechaFin != null && $fechaHoy != null && $periodDuration != null && $billingCycle != null){
                $url = $this->solarEdge->getUrl() . "solaredge-apigw/api/site/$siteId/customEnergyDashboardChart?chartField=$dia&foldUp=true&timeUnit=MONTH&siteId=$siteId&billingCycle=$billingCycle&periodDuration=$periodDuration&startTime=$fechaHoy&endTime=$fechaFin";
                }else{
                    return "error en los datos obligatorios no pueden ser nulos";
                }
                break;
            default:
                return "error ";
                break;
            }
        try {
            $response = $this->httpClient->get($url);
            return $response;
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function getSiteDetails($siteId) {
        $url = $this->solarEdge->getUrl() . "site/$siteId/details?api_key=" . $this->solarEdge->getApiKey();
        try {
            $response = $this->httpClient->get($url);
            return $response;
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
    //Método que recoje todas las plantas
    public function getAllPlants() {
        $url = $this->solarEdge->getUrl() . "sites/list?api_key=" . $this->solarEdge->getApiKey();
        try {
            $response = $this->httpClient->get($url);
            return json_decode($response, true);
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
     // Método para obtener los datos de energía del sitio en un rango de fechas
     public function getSiteEnergy($siteId, $startDate, $endDate) {
        $url = $this->solarEdge->getUrl() . "site/$siteId/energy";
        $url .= "?timeUnit=DAY&startDate=$startDate&endDate=$endDate&api_key=" . $this->solarEdge->getApiKey();

        try {
            $response = $this->httpClient->get($url);
            return json_decode($response, true);
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

     // Método para obtener los datos de energía en intervalos de cuarto de hora
     public function getQuarterHourlyEnergy($siteId, $startDate, $endDate) {
        $url = $this->solarEdge->getUrl() . "site/$siteId/energy";
        $url .= "?timeUnit=QUARTER_OF_AN_HOUR&startDate=$startDate&endDate=$endDate&api_key=" . $this->solarEdge->getApiKey();

        try {
            $response = $this->httpClient->get($url);
            return json_decode($response, true);
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
    // Método para obtener los datos de energía diaria
    public function getDailyEnergy($siteId, $startDate, $endDate) {
        $url = $this->solarEdge->getUrl() . "site/$siteId/energy";
        $url .= "?timeUnit=DAY&startDate=$startDate&endDate=$endDate&api_key=" . $this->solarEdge->getApiKey();

        try {
            $response = $this->httpClient->get($url);
            return json_decode($response, true);
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
    // Método para obtener los datos de energía diaria para un mes completo
    public function getMonthlyDailyEnergy($siteId, $startDate, $endDate) {
        $url = $this->solarEdge->getUrl() . "site/$siteId/energy";
        $url .= "?timeUnit=DAY&startDate=$startDate&endDate=$endDate&api_key=" . $this->solarEdge->getApiKey();

        try {
            $response = $this->httpClient->get($url);
            return json_decode($response, true);
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
    // Método para obtener los datos de energía anual
    public function getYearlyEnergy($siteId, $startDate, $endDate) {
        $url = $this->solarEdge->getUrl() . "site/$siteId/energy";
        $url .= "?timeUnit=YEAR&startDate=$startDate&endDate=$endDate&api_key=" . $this->solarEdge->getApiKey();

        try {
            $response = $this->httpClient->get($url);
            return json_decode($response, true);
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
     // Método para obtener los beneficios ambientales
     public function getEnvironmentalBenefits($siteId, $systemUnits) {
        $url = $this->solarEdge->getUrl() . "site/$siteId/envBenefits";
        $url .= "?systemUnits=$systemUnits&api_key=" . $this->solarEdge->getApiKey();

        try {
            $response = $this->httpClient->get($url);
            return json_decode($response, true);
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
     // Método para obtener los beneficios ambientales en unidades métricas
     public function getEnvironmentalBenefitsMetrics($siteId) {
        $url = $this->solarEdge->getUrl() . "site/$siteId/envBenefits";
        $url .= "?systemUnits=Metrics&api_key=" . $this->solarEdge->getApiKey();

        try {
            $response = $this->httpClient->get($url);
            return json_decode($response, true);
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
     // Método para obtener los detalles de energía del consumo mensual
     public function getEnergyDetailsConsumption($siteId, $startTime, $endTime) {
        $url = $this->solarEdge->getUrl() . "site/$siteId/energyDetails";
        $url .= "?meters=CONSUMPTION&timeUnit=MONTH&startTime=" . urlencode($startTime) . "&endTime=" . urlencode($endTime);
        $url .= "&api_key=" . $this->solarEdge->getApiKey();

        try {
            $response = $this->httpClient->get($url);
            return json_decode($response, true);
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
    // Método para obtener los detalles de energía del consumo anual
    public function getEnergyDetailsConsumptionYear($siteId, $startTime, $endTime) {
        $url = $this->solarEdge->getUrl() . "site/$siteId/energyDetails";
        $url .= "?meters=CONSUMPTION&timeUnit=YEAR&startTime=" . urlencode($startTime) . "&endTime=" . urlencode($endTime);
        $url .= "&api_key=" . $this->solarEdge->getApiKey();

        try {
            $response = $this->httpClient->get($url);
            return json_decode($response, true);
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
    // Método para obtener los detalles de energía del consumo diario
    public function getEnergyDetailsConsumptionDay($siteId, $startTime, $endTime) {
        $url = $this->solarEdge->getUrl() . "site/$siteId/energyDetails";
        $url .= "?meters=CONSUMPTION&timeUnit=DAY&startTime=" . urlencode($startTime) . "&endTime=" . urlencode($endTime);
        $url .= "&api_key=" . $this->solarEdge->getApiKey();

        try {
            $response = $this->httpClient->get($url);
            return json_decode($response, true);
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
    // Método para obtener los detalles de potencia del consumo
    public function getPowerDetailsConsumption($siteId, $startTime, $endTime) {
        $url = $this->solarEdge->getUrl() . "site/$siteId/powerDetails";
        $url .= "?meters=CONSUMPTION&startTime=" . urlencode($startTime) . "&endTime=" . urlencode($endTime);
        $url .= "&api_key=" . $this->solarEdge->getApiKey();

        try {
            $response = $this->httpClient->get($url);
            return json_decode($response, true);
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
     // Método para obtener los datos de almacenamiento
     public function getStorageData($siteId, $startTime, $endTime) {
        $url = $this->solarEdge->getUrl() . "site/$siteId/storageData";
        $url .= "?startTime=" . urlencode($startTime) . "&endTime=" . urlencode($endTime);
        $url .= "&api_key=" . $this->solarEdge->getApiKey();

        try {
            $response = $this->httpClient->get($url);
            return json_decode($response, true);
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
    // Método para obtener el flujo de energía actual
    public function getCurrentPowerFlow($siteId) {
        $url = $this->solarEdge->getUrl() . "site/$siteId/currentPowerFlow";
        $url .= "?api_key=" . $this->solarEdge->getApiKey();

        try {
            $response = $this->httpClient->get($url);
            return json_decode($response, true);
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
    // Método para obtener los datos de sensores
    public function getSensorsData($siteId, $startDate, $endDate) {
        $url = $this->solarEdge->getUrl() . "site/$siteId/sensors";
        $url .= "?startDate=" . urlencode($startDate) . "&endDate=" . urlencode($endDate);
        $url .= "&api_key=" . $this->solarEdge->getApiKey();

        try {
            $response = $this->httpClient->get($url);
            return json_decode($response, true);
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
    // Método para obtener los datos de sensores con un rango de fechas extendido
    public function getSensorsDataExtended($siteId, $startDate, $endDate) {
        $url = $this->solarEdge->getUrl() . "site/$siteId/sensors";
        $url .= "?startDate=" . urlencode($startDate) . "&endDate=" . urlencode($endDate);
        $url .= "&api_key=" . $this->solarEdge->getApiKey();

        try {
            $response = $this->httpClient->get($url);
            return json_decode($response, true);
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
     // Método para obtener los datos de sensores con un rango de fechas desde el 1 hasta el 30 de octubre
     public function getSensorsDataForMonth($siteId, $startDate, $endDate) {
        $url = $this->solarEdge->getUrl() . "site/$siteId/sensors";
        $url .= "?startDate=" . urlencode($startDate) . "&endDate=" . urlencode($endDate);
        $url .= "&api_key=" . $this->solarEdge->getApiKey();

        try {
            $response = $this->httpClient->get($url);
            return json_decode($response, true);
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
?>
