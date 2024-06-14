<?php

namespace App\Controller;

use App\Entity\Energy;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\SystemsRepository;
use App\Entity\Systems;
use App\Repository\EnergyRepository;

class EnphaseService extends AbstractController
{
    /**
     * https://api.enphaseenergy.com/oauth/authorize?response_type=code&client_id=9ad2e87f679728b3cd937f52c40e258c&redirect_uri=https://pv.comnstay.fr/enphase/init
     */
    private $apiKey;
    private $clientSecret;
    private $clientId;

    public function __construct()
    {
        // application cedentials
        $this->apiKey = 'e8746bf7a6c1877e111529d531fc6110';
        $this->clientSecret = '1b316feb27adf6448224c85941bd0097';
        $this->clientId = '9ad2e87f679728b3cd937f52c40e258c';
    }
    #[Route('/enphase/init', name: 'app_enphase_init')]
    public function init(
        Request $request, 
        EntityManagerInterface $entityManager,
        Security $security
    ): Response
    {
        $redirectUri = 'https://pv.comnstay.fr/enphase/init';
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.enphaseenergy.com/oauth/token?grant_type=authorization_code&redirect_uri=' . $redirectUri . '&code=' . $request->get('code'),
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
        
        $system = new Systems;
        $system->setName('Enphase');
        $system->setToken($datas->access_token);
        $system->setRefreshToken($datas->refresh_token);
        $system->setFkUser($security->getUser());
        $entityManager->persist($system);
        $entityManager->flush();
        
        return $this->redirectToRoute('app_enphase_system', ['token' => $datas->access_token]);
    }

    #[Route('/enphase/system/{token}', name: 'app_enphase_system')]
    public function system(
        $token,
        EntityManagerInterface $entityManager,
        SystemsRepository $systemsRepository
    ): Response
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
                'Authorization: Bearer ' . $token
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $datas = json_decode($response);
        $system = $systemsRepository->findOneBy(['token' => $token]);
        $system->setSystemId($datas->systems[0]->system_id);
        $entityManager->persist($system);
        $entityManager->flush();

        $this->addFlash('success', ['title' => 'Système OK !', 'text' => 'Votre système Enphase à bien été créé et paramétré !']);

        return $this->redirectToRoute('app_account');
    }

    #[Route('/enphase/import/{date?}', name: 'app_enphase_import')]
    public function import(
        Request $request,
        EntityManagerInterface $entityManager,
        SystemsRepository $systemsRepository,
        EnergyRepository $energyRepository,
        Security $security
    ): Response
    {
        $system = $systemsRepository->findOneBy(['fk_user' => $security->getUser()]);
        
        $dateStart = $request->query->get('date');
        $url = 'https://api.enphaseenergy.com/api/v4/systems/' . $system->getSystemId() . '/energy_lifetime?key=' . $this->apiKey;
        if($dateStart !== null && $dateStart != '') {
            $url .= '&start_date=' . $dateStart;
        }
        
        // récupération de la production
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $system->getToken()
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $datas = json_decode($response);

        if(isset($datas->code) && $datas->code == 401) {
            return $this->redirectToRoute('app_enphase_renew', ['callback' => 'app_enphase_import']);
        }

        $date = \DateTime::createFromFormat('Y-m-d', $datas->start_date);
        foreach($datas->production as $production) {
            $energy = new Energy;
            $energy->setDate($date);
            $energy->setProduction($production/1000);
            $energy->setImport(0);
            $energy->setFkUser($security->getUser());
            $entityManager->persist($energy);
            $entityManager->flush();
            $date = $date->modify('+1 day');
        }

        // récupération de la revente
        $url = 'https://api.enphaseenergy.com/api/v4/systems/' . $system->getSystemId() . '/energy_export_lifetime?key=' . $this->apiKey;
        if($dateStart !== null && $dateStart != '') {
            $url .= '&start_date=' . $dateStart;
        }

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $system->getToken()
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $datas = json_decode($response);
        $date = \DateTime::createFromFormat('Y-m-d', $datas->start_date);
        foreach($datas->export as $export) {
            $energy = $energyRepository->findOneBy(['date' => $date, 'fk_user' => $security->getUser()]);
            $energy->setExport($export/1000);
            $energy->setSelf($energy->getProduction() - ($export/1000));
            $entityManager->persist($energy);
            $entityManager->flush();
            $date = $date->modify('+1 day');
        }

        $this->addFlash('success', ['title' => 'Import OK !', 'text' => 'L\'import de vos données a bien été effectué !']);

        return $this->redirectToRoute('app_account');
    }

    #[Route('/enphase/renew/{callback}', name: 'app_enphase_renew')]
    public function renewToken(
        $callback,
        EntityManagerInterface $entityManager,
        SystemsRepository $systemsRepository,
        Security $security
    ): Response
    {
        $system = $systemsRepository->findOneBy(['fk_user' => $security->getUser()]);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.enphaseenergy.com/oauth/token?grant_type=refresh_token&refresh_token=' . $system->getRefreshToken(),
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
        $system->setToken($datas->access_token);
        $system->setRefreshToken($datas->refresh_token);
        $system->setFkUser($security->getUser());
        $entityManager->persist($system);
        $entityManager->flush();
        
        return $this->redirectToRoute($callback);
    }

}