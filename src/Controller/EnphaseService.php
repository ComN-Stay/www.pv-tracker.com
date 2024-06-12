<?php

namespace App\Controller;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\SystemsRepository;
use App\Entity\Systems;

class EnphaseService extends AbstractController
{
    /**
     * https://api.enphaseenergy.com/oauth/authorize?response_type=code&client_id=9ad2e87f679728b3cd937f52c40e258c&redirect_uri=https://pv.comnstay.fr/enphase/init
     */
    private $userId;
    private $systemId;
    private $apiKey;
    private $clientSecret;
    private $redirectUri;
    private $clientId;
    private $authCode;
    private $accessToken;

    public function __construct()
    {
        // user credentials
        $this->userId = '4e444d314e54417a4f513d3d0a';
        $this->systemId = '4984291';
        $this->authCode = 'UwlqTG';
        $this->accessToken = "eyJhbGciOiJSUzI1NiJ9.eyJhcHBfdHlwZSI6InN5c3RlbSIsInVzZXJfbmFtZSI6IngudGV6emEzMUBmcmVlLmZyIiwiZW5sX2NpZCI6IiIsImVubF9wYXNzd29yZF9sYXN0X2NoYW5nZWQiOiIxNzA5NjQ1MzcwIiwiYXV0aG9yaXRpZXMiOlsiUk9MRV9VU0VSIl0sImNsaWVudF9pZCI6IjlhZDJlODdmNjc5NzI4YjNjZDkzN2Y1MmM0MGUyNThjIiwiYXVkIjpbIm9hdXRoMi1yZXNvdXJjZSJdLCJpc19pbnRlcm5hbF9hcHAiOmZhbHNlLCJzY29wZSI6WyJyZWFkIiwid3JpdGUiXSwiZXhwIjoxNzE4MjUzNjYxLCJlbmxfdWlkIjoiNDM1NTAzOSIsImFwcF9JZCI6IjE0MDk2MjQzNTkxODYiLCJqdGkiOiJlMTQzZDg1MC03NWFlLTQ4ZWItOWEzZS05YzM0Y2VkOTQ1NWQifQ.Q3Rdg_sPe5m6IH4_WygJQIkcYBdRx2WQC5l0Ss4EvYYvYB3TUHZhGKnZjuD19jXzHwXTQkrBtXptFhbw7PPpBduaxnHJQDw2tc-ILjrefu446sR8YkUfAhPm6fza-jMMIReM4NjAZ77hysaLjT7S7kuduAfUo3PECqm1bN4-nVY";
        // application cedentials
        $this->apiKey = 'e8746bf7a6c1877e111529d531fc6110';
        $this->clientSecret = '1b316feb27adf6448224c85941bd0097';
        $this->clientId = '9ad2e87f679728b3cd937f52c40e258c';
    }
    #[Route('/enphase/init', name: 'app_enphase_init')]
    public function init(
        Request $request, 
        EntityManagerInterface $entityManager,
        Systems $systems,
        SystemsRepository $systemsRepository
    ): void
    {
        dd($request);
        $redirectUri = 'https://pv.comnstay.fr/enphase/init';
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.enphaseenergy.com/oauth/token?grant_type=authorization_code&redirect_uri=' . $redirectUri . '&code=' . $this->authCode,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret)
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $datas = json_decode($response);
        dd($datas);
    }

    #[Route('/enphase/import', name: 'app_enphase_import')]
    public function index(): void
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.enphaseenergy.com/api/v4/systems/?key=' . $this->apiKey,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . $this->accessToken
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $datas = json_decode($response);
        dd($datas->systems[0]->system_id);
    }

}
/*
{
  eyJhbGciOiJSUzI1NiJ9.eyJhcHBfdHlwZSI6InN5c3RlbSIsInVzZXJfbmFtZSI6IngudGV6emEzMUBmcmVlLmZyIiwiZW5sX2NpZCI6IiIsImVubF9wYXNzd29yZF9sYXN0X2NoYW5nZWQiOiIxNzA5NjQ1MzcwIiwiYXV0aG9yaXRpZXMiOlsiUk9MRV9VU0VSIl0sImNsaWVudF9pZCI6IjlhZDJlODdmNjc5NzI4YjNjZDkzN2Y1MmM0MGUyNThjIiwiYXVkIjpbIm9hdXRoMi1yZXNvdXJjZSJdLCJpc19pbnRlcm5hbF9hcHAiOmZhbHNlLCJzY29wZSI6WyJyZWFkIiwid3JpdGUiXSwiYXRpIjoiZTE0M2Q4NTAtNzVhZS00OGViLTlhM2UtOWMzNGNlZDk0NTVkIiwiZXhwIjoxNzIwNzk3MDA3LCJlbmxfdWlkIjoiNDM1NTAzOSIsImFwcF9JZCI6IjE0MDk2MjQzNTkxODYiLCJqdGkiOiI2ZGU2Y2I3Ny02OWEyLTQzZDEtYTY2Mi1mM2UwMjU0NGZiZWMifQ.InYlfvJZ_Vf3ABOt_vnylxESLPokcFuCgRukaQHMgwTdmChvpGN011JOpYG0YeyqSKEYTZYw26wJ6o0K3AjaFQbd1zxUX7X5DtcrJyh6Ac1_wua5m_fSeDinDE38k_vlLMyAfz7RX66F4EHnrJulSd_39KHTarZGZFonddmERzQ
}*/