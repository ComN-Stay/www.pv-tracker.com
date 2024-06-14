<?php

#############################################

# php bin/console doctrine:fixtures:load --purge-with-truncate --purger=mysql_purger

#############################################

namespace App\DataFixtures;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Entity\Teams;
use App\Entity\Genders;
use App\Entity\Countries;
use App\Entity\Users;
use App\Entity\UserStatuses;

class AppFixtures extends Fixture
{
    private $passwordHasher;
    private $output;
    
    public function __construct(
        UserPasswordHasherInterface $passwordHasher, 
    )
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $output = new ConsoleOutput();
        $this->output = $output;

        $this->countriesFixtures($manager);
        $this->gendersFixtures($manager);
        $this->statusFixtures($manager);
        $this->teamsFixtures($manager);
        $this->usersFixtures($manager);

        $this->output->writeln('');
        $this->output->writeln('<error>###########################</error>');
        $this->output->writeln('<error># Done ! fixtures loaded. #</error>');
        $this->output->writeln('<error>###########################</error>');
    }

    private function countriesFixtures($manager)
    {
        $this->output->writeln('');
        $this->output->writeln('<comment>   ></comment><info> Start loading Countries fixtures ...</info>');
        $countries = $this->setCountries();
        
        foreach($countries as $object) {
            $country = new Countries;
            $country->setName($object['name']);
            $country->setIsoCode($object['iso_code']);
            $manager->persist($country);
        }
        $manager->flush();
        
        $this->output->writeln('<question>   > Countries fixtures loaded ...   </question>');
    }

    private function gendersFixtures($manager)
    {
        $this->output->writeln('');
        $this->output->writeln('<comment>   ></comment><info> Start loading Genders fixtures ...</info>');

        $gender1 = new Genders;
        $gender1->setName('Madame');
        $gender1->setShortName('Mme');
        $manager->persist($gender1);

        $gender2 = new Genders;
        $gender2->setName('Monsieur');
        $gender2->setShortName('M.');
        $manager->persist($gender2);

        $gender3 = new Genders;
        $gender3->setName('Non défini');
        $gender3->setShortName('X');
        $manager->persist($gender3);

        $manager->flush();
        
        $this->output->writeln('<question>   > Genders fixtures loaded ...   </question>');
    }

    protected function statusFixtures($manager): void 
    {
        $this->output->writeln('<info>Loading Status fixtures ...</info>');

        $statuses = ['Actif', 'Inactif'];
        $i = 1;
        foreach($statuses as $status) {
            $st[$i] = new UserStatuses;
            $st[$i]->setName($status);
            $manager->persist($st[$i]);
            $i++;
        }
        $manager->flush();
        $this->output->writeln('<info>Status fixtures loaded</info>');
    }

    protected function teamsFixtures($manager): void
    {
        $this->output->writeln('');
        $this->output->writeln('<comment>   ></comment><info> Start loading Teams fixtures ...</info>');

        $team = new Teams;
        $team->setEmail('xavier.tezza@comnstay.fr');
        $hashedPassword = $this->passwordHasher->hashPassword(
            $team,
            'ComNStay@2021'
        );
        $team->setPassword($hashedPassword);
        $team->setRoles(['ROLE_SUPER_ADMIN']);
        $team->setFkGender($this->getReferencedObject(Genders::class, 2, $manager));
        $team->setFirstname('Xavier');
        $team->setLastname('Tezza');
        $team->setFkStatus($this->getReferencedObject(UserStatuses::class, 1, $manager));
        $manager->persist($team);

        $manager->flush();
        $this->output->writeln('<question>   > Teams fixtures loaded ...   </question>');
    }

    protected function usersFixtures($manager): void
    {
        $this->output->writeln('');
        $this->output->writeln('<comment>   ></comment><info> Start loading Users fixtures ...</info>');

        $user = new Users;
        $user->setEmail('x.tezza31@free.fr');
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            'ComNStay@2021'
        );
        $user->setPassword($hashedPassword);
        $user->setRoles(['ROLE_CLIENT']);
        $user->setFkGender($this->getReferencedObject(Genders::class, 2, $manager));
        $user->setFirstname('Xavier');
        $user->setLastname('Tezza');
        $user->setAddress('183 ruedu petit pastellié');
        $user->setZipCode('31660');
        $user->setTown('Bessières');
        $user->setMobile('06 16 94 06 53');
        $user->setFkCountry($this->getReferencedObject(Countries::class, 75, $manager));
        $user->setFkStatus($this->getReferencedObject(UserStatuses::class, 1, $manager));
        $manager->persist($user);

        $manager->flush();
        $this->output->writeln('<question>   > Teams fixtures loaded ...   </question>');
    }

    #######################################################################################

    protected function getReferencedObject(string $className, int $id, object $manager) {
        return $manager->find($className, $id);
    }

    protected function getRandomReference(string $className, object $manager) {
        $list = $manager->getRepository($className)->findAll();
        return $list[array_rand($list)];
    }

    protected function setCountries()
    {
        return array(
            array('id' => '1','name' => 'Afghanistan','iso_code' => 'AF'),
            array('id' => '2','name' => 'Albanie','iso_code' => 'AL'),
            array('id' => '3','name' => 'Antarctique','iso_code' => 'AQ'),
            array('id' => '4','name' => 'Algérie','iso_code' => 'DZ'),
            array('id' => '5','name' => 'Samoa Américaines','iso_code' => 'AS'),
            array('id' => '6','name' => 'Andorre','iso_code' => 'AD'),
            array('id' => '7','name' => 'Angola','iso_code' => 'AO'),
            array('id' => '8','name' => 'Antigua-et-Barbuda','iso_code' => 'AG'),
            array('id' => '9','name' => 'Azerbaïdjan','iso_code' => 'AZ'),
            array('id' => '10','name' => 'Argentine','iso_code' => 'AR'),
            array('id' => '11','name' => 'Australie','iso_code' => 'AU'),
            array('id' => '12','name' => 'Autriche','iso_code' => 'AT'),
            array('id' => '13','name' => 'Bahamas','iso_code' => 'BS'),
            array('id' => '14','name' => 'Bahreïn','iso_code' => 'BH'),
            array('id' => '15','name' => 'Bangladesh','iso_code' => 'BD'),
            array('id' => '16','name' => 'Arménie','iso_code' => 'AM'),
            array('id' => '17','name' => 'Barbade','iso_code' => 'BB'),
            array('id' => '18','name' => 'Belgique','iso_code' => 'BE'),
            array('id' => '19','name' => 'Bermudes','iso_code' => 'BM'),
            array('id' => '20','name' => 'Bhoutan','iso_code' => 'BT'),
            array('id' => '21','name' => 'Bolivie','iso_code' => 'BO'),
            array('id' => '22','name' => 'Bosnie-Herzégovine','iso_code' => 'BA'),
            array('id' => '23','name' => 'Botswana','iso_code' => 'BW'),
            array('id' => '24','name' => 'Île Bouvet','iso_code' => 'BV'),
            array('id' => '25','name' => 'Brésil','iso_code' => 'BR'),
            array('id' => '26','name' => 'Belize','iso_code' => 'BZ'),
            array('id' => '27','name' => 'Territoire Britannique de l\'Océan Indien','iso_code' => 'IO'),
            array('id' => '28','name' => 'Îles Salomon','iso_code' => 'SB'),
            array('id' => '29','name' => 'Îles Vierges Britanniques','iso_code' => 'VG'),
            array('id' => '30','name' => 'Brunéi Darussalam','iso_code' => 'BN'),
            array('id' => '31','name' => 'Bulgarie','iso_code' => 'BG'),
            array('id' => '32','name' => 'Myanmar','iso_code' => 'MM'),
            array('id' => '33','name' => 'Burundi','iso_code' => 'BI'),
            array('id' => '34','name' => 'Bélarus','iso_code' => 'BY'),
            array('id' => '35','name' => 'Cambodge','iso_code' => 'KH'),
            array('id' => '36','name' => 'Cameroun','iso_code' => 'CM'),
            array('id' => '37','name' => 'Canada','iso_code' => 'CA'),
            array('id' => '38','name' => 'Cap-vert','iso_code' => 'CV'),
            array('id' => '39','name' => 'Îles Caïmanes','iso_code' => 'KY'),
            array('id' => '40','name' => 'République Centrafricaine','iso_code' => 'CF'),
            array('id' => '41','name' => 'Sri Lanka','iso_code' => 'LK'),
            array('id' => '42','name' => 'Tchad','iso_code' => 'TD'),
            array('id' => '43','name' => 'Chili','iso_code' => 'CL'),
            array('id' => '44','name' => 'Chine','iso_code' => 'CN'),
            array('id' => '45','name' => 'Taïwan','iso_code' => 'TW'),
            array('id' => '46','name' => 'Île Christmas','iso_code' => 'CX'),
            array('id' => '47','name' => 'Îles Cocos (Keeling)','iso_code' => 'CC'),
            array('id' => '48','name' => 'Colombie','iso_code' => 'CO'),
            array('id' => '49','name' => 'Comores','iso_code' => 'KM'),
            array('id' => '50','name' => 'Mayotte','iso_code' => 'YT'),
            array('id' => '51','name' => 'République du Congo','iso_code' => 'CG'),
            array('id' => '52','name' => 'République Démocratique du Congo','iso_code' => 'CD'),
            array('id' => '53','name' => 'Îles Cook','iso_code' => 'CK'),
            array('id' => '54','name' => 'Costa Rica','iso_code' => 'CR'),
            array('id' => '55','name' => 'Croatie','iso_code' => 'HR'),
            array('id' => '56','name' => 'Cuba','iso_code' => 'CU'),
            array('id' => '57','name' => 'Chypre','iso_code' => 'CY'),
            array('id' => '58','name' => 'République Tchèque','iso_code' => 'CZ'),
            array('id' => '59','name' => 'Bénin','iso_code' => 'BJ'),
            array('id' => '60','name' => 'Danemark','iso_code' => 'DK'),
            array('id' => '61','name' => 'Dominique','iso_code' => 'DM'),
            array('id' => '62','name' => 'République Dominicaine','iso_code' => 'DO'),
            array('id' => '63','name' => 'Équateur','iso_code' => 'EC'),
            array('id' => '64','name' => 'El Salvador','iso_code' => 'SV'),
            array('id' => '65','name' => 'Guinée Équatoriale','iso_code' => 'GQ'),
            array('id' => '66','name' => 'Éthiopie','iso_code' => 'ET'),
            array('id' => '67','name' => 'Érythrée','iso_code' => 'ER'),
            array('id' => '68','name' => 'Estonie','iso_code' => 'EE'),
            array('id' => '69','name' => 'Îles Féroé','iso_code' => 'FO'),
            array('id' => '70','name' => 'Îles (malvinas) Falkland','iso_code' => 'FK'),
            array('id' => '71','name' => 'Géorgie du Sud et les Îles Sandwich du Sud','iso_code' => 'GS'),
            array('id' => '72','name' => 'Fidji','iso_code' => 'FJ'),
            array('id' => '73','name' => 'Finlande','iso_code' => 'FI'),
            array('id' => '74','name' => 'Îles Åland','iso_code' => 'AX'),
            array('id' => '75','name' => 'France','iso_code' => 'FR'),
            array('id' => '76','name' => 'Guyane Française','iso_code' => 'GF'),
            array('id' => '77','name' => 'Polynésie Française','iso_code' => 'PF'),
            array('id' => '78','name' => 'Terres Australes Françaises','iso_code' => 'TF'),
            array('id' => '79','name' => 'Djibouti','iso_code' => 'DJ'),
            array('id' => '80','name' => 'Gabon','iso_code' => 'GA'),
            array('id' => '81','name' => 'Géorgie','iso_code' => 'GE'),
            array('id' => '82','name' => 'Gambie','iso_code' => 'GM'),
            array('id' => '83','name' => 'Territoire Palestinien Occupé','iso_code' => 'PS'),
            array('id' => '84','name' => 'Allemagne','iso_code' => 'DE'),
            array('id' => '85','name' => 'Ghana','iso_code' => 'GH'),
            array('id' => '86','name' => 'Gibraltar','iso_code' => 'GI'),
            array('id' => '87','name' => 'Kiribati','iso_code' => 'KI'),
            array('id' => '88','name' => 'Grèce','iso_code' => 'GR'),
            array('id' => '89','name' => 'Groenland','iso_code' => 'GL'),
            array('id' => '90','name' => 'Grenade','iso_code' => 'GD'),
            array('id' => '91','name' => 'Guadeloupe','iso_code' => 'GP'),
            array('id' => '92','name' => 'Guam','iso_code' => 'GU'),
            array('id' => '93','name' => 'Guatemala','iso_code' => 'GT'),
            array('id' => '94','name' => 'Guinée','iso_code' => 'GN'),
            array('id' => '95','name' => 'Guyana','iso_code' => 'GY'),
            array('id' => '96','name' => 'Haïti','iso_code' => 'HT'),
            array('id' => '97','name' => 'Îles Heard et Mcdonald','iso_code' => 'HM'),
            array('id' => '98','name' => 'Saint-Siège (état de la Cité du Vatican)','iso_code' => 'VA'),
            array('id' => '99','name' => 'Honduras','iso_code' => 'HN'),
            array('id' => '100','name' => 'Hong-Kong','iso_code' => 'HK'),
            array('id' => '101','name' => 'Hongrie','iso_code' => 'HU'),
            array('id' => '102','name' => 'Islande','iso_code' => 'IS'),
            array('id' => '103','name' => 'Inde','iso_code' => 'IN'),
            array('id' => '104','name' => 'Indonésie','iso_code' => 'ID'),
            array('id' => '105','name' => 'République Islamique d\'Iran','iso_code' => 'IR'),
            array('id' => '106','name' => 'Iraq','iso_code' => 'IQ'),
            array('id' => '107','name' => 'Irlande','iso_code' => 'IE'),
            array('id' => '108','name' => 'Israël','iso_code' => 'IL'),
            array('id' => '109','name' => 'Italie','iso_code' => 'IT'),
            array('id' => '110','name' => 'Côte d\'Ivoire','iso_code' => 'CI'),
            array('id' => '111','name' => 'Jamaïque','iso_code' => 'JM'),
            array('id' => '112','name' => 'Japon','iso_code' => 'JP'),
            array('id' => '113','name' => 'Kazakhstan','iso_code' => 'KZ'),
            array('id' => '114','name' => 'Jordanie','iso_code' => 'JO'),
            array('id' => '115','name' => 'Kenya','iso_code' => 'KE'),
            array('id' => '116','name' => 'République Populaire Démocratique de Corée','iso_code' => 'KP'),
            array('id' => '117','name' => 'République de Corée','iso_code' => 'KR'),
            array('id' => '118','name' => 'Koweït','iso_code' => 'KW'),
            array('id' => '119','name' => 'Kirghizistan','iso_code' => 'KG'),
            array('id' => '120','name' => 'République Démocratique Populaire Lao','iso_code' => 'LA'),
            array('id' => '121','name' => 'Liban','iso_code' => 'LB'),
            array('id' => '122','name' => 'Lesotho','iso_code' => 'LS'),
            array('id' => '123','name' => 'Lettonie','iso_code' => 'LV'),
            array('id' => '124','name' => 'Libéria','iso_code' => 'LR'),
            array('id' => '125','name' => 'Jamahiriya Arabe Libyenne','iso_code' => 'LY'),
            array('id' => '126','name' => 'Liechtenstein','iso_code' => 'LI'),
            array('id' => '127','name' => 'Lituanie','iso_code' => 'LT'),
            array('id' => '128','name' => 'Luxembourg','iso_code' => 'LU'),
            array('id' => '129','name' => 'Macao','iso_code' => 'MO'),
            array('id' => '130','name' => 'Madagascar','iso_code' => 'MG'),
            array('id' => '131','name' => 'Malawi','iso_code' => 'MW'),
            array('id' => '132','name' => 'Malaisie','iso_code' => 'MY'),
            array('id' => '133','name' => 'Maldives','iso_code' => 'MV'),
            array('id' => '134','name' => 'Mali','iso_code' => 'ML'),
            array('id' => '135','name' => 'Malte','iso_code' => 'MT'),
            array('id' => '136','name' => 'Martinique','iso_code' => 'MQ'),
            array('id' => '137','name' => 'Mauritanie','iso_code' => 'MR'),
            array('id' => '138','name' => 'Maurice','iso_code' => 'MU'),
            array('id' => '139','name' => 'Mexique','iso_code' => 'MX'),
            array('id' => '140','name' => 'Monaco','iso_code' => 'MC'),
            array('id' => '141','name' => 'Mongolie','iso_code' => 'MN'),
            array('id' => '142','name' => 'République de Moldova','iso_code' => 'MD'),
            array('id' => '143','name' => 'Montserrat','iso_code' => 'MS'),
            array('id' => '144','name' => 'Maroc','iso_code' => 'MA'),
            array('id' => '145','name' => 'Mozambique','iso_code' => 'MZ'),
            array('id' => '146','name' => 'Oman','iso_code' => 'OM'),
            array('id' => '147','name' => 'Namibie','iso_code' => 'NA'),
            array('id' => '148','name' => 'Nauru','iso_code' => 'NR'),
            array('id' => '149','name' => 'Népal','iso_code' => 'NP'),
            array('id' => '150','name' => 'Pays-Bas','iso_code' => 'NL'),
            array('id' => '151','name' => 'Antilles Néerlandaises','iso_code' => 'AN'),
            array('id' => '152','name' => 'Aruba','iso_code' => 'AW'),
            array('id' => '153','name' => 'Nouvelle-Calédonie','iso_code' => 'NC'),
            array('id' => '154','name' => 'Vanuatu','iso_code' => 'VU'),
            array('id' => '155','name' => 'Nouvelle-Zélande','iso_code' => 'NZ'),
            array('id' => '156','name' => 'Nicaragua','iso_code' => 'NI'),
            array('id' => '157','name' => 'Niger','iso_code' => 'NE'),
            array('id' => '158','name' => 'Nigéria','iso_code' => 'NG'),
            array('id' => '159','name' => 'Niué','iso_code' => 'NU'),
            array('id' => '160','name' => 'Île Norfolk','iso_code' => 'NF'),
            array('id' => '161','name' => 'Norvège','iso_code' => 'NO'),
            array('id' => '162','name' => 'Îles Mariannes du Nord','iso_code' => 'MP'),
            array('id' => '163','name' => 'Îles Mineures Éloignées des États-Unis','iso_code' => 'UM'),
            array('id' => '164','name' => 'États Fédérés de Micronésie','iso_code' => 'FM'),
            array('id' => '165','name' => 'Îles Marshall','iso_code' => 'MH'),
            array('id' => '166','name' => 'Palaos','iso_code' => 'PW'),
            array('id' => '167','name' => 'Pakistan','iso_code' => 'PK'),
            array('id' => '168','name' => 'Panama','iso_code' => 'PA'),
            array('id' => '169','name' => 'Papouasie-Nouvelle-Guinée','iso_code' => 'PG'),
            array('id' => '170','name' => 'Paraguay','iso_code' => 'PY'),
            array('id' => '171','name' => 'Pérou','iso_code' => 'PE'),
            array('id' => '172','name' => 'Philippines','iso_code' => 'PH'),
            array('id' => '173','name' => 'Pitcairn','iso_code' => 'PN'),
            array('id' => '174','name' => 'Pologne','iso_code' => 'PL'),
            array('id' => '175','name' => 'Portugal','iso_code' => 'PT'),
            array('id' => '176','name' => 'Guinée-Bissau','iso_code' => 'GW'),
            array('id' => '177','name' => 'Timor-Leste','iso_code' => 'TL'),
            array('id' => '178','name' => 'Porto Rico','iso_code' => 'PR'),
            array('id' => '179','name' => 'Qatar','iso_code' => 'QA'),
            array('id' => '180','name' => 'Réunion','iso_code' => 'RE'),
            array('id' => '181','name' => 'Roumanie','iso_code' => 'RO'),
            array('id' => '182','name' => 'Fédération de Russie','iso_code' => 'RU'),
            array('id' => '183','name' => 'Rwanda','iso_code' => 'RW'),
            array('id' => '184','name' => 'Sainte-Hélène','iso_code' => 'SH'),
            array('id' => '185','name' => 'Saint-Kitts-et-Nevis','iso_code' => 'KN'),
            array('id' => '186','name' => 'Anguilla','iso_code' => 'AI'),
            array('id' => '187','name' => 'Sainte-Lucie','iso_code' => 'LC'),
            array('id' => '188','name' => 'Saint-Pierre-et-Miquelon','iso_code' => 'PM'),
            array('id' => '189','name' => 'Saint-Vincent-et-les Grenadines','iso_code' => 'VC'),
            array('id' => '190','name' => 'Saint-Marin','iso_code' => 'SM'),
            array('id' => '191','name' => 'Sao Tomé-et-Principe','iso_code' => 'ST'),
            array('id' => '192','name' => 'Arabie Saoudite','iso_code' => 'SA'),
            array('id' => '193','name' => 'Sénégal','iso_code' => 'SN'),
            array('id' => '194','name' => 'Seychelles','iso_code' => 'SC'),
            array('id' => '195','name' => 'Sierra Leone','iso_code' => 'SL'),
            array('id' => '196','name' => 'Singapour','iso_code' => 'SG'),
            array('id' => '197','name' => 'Slovaquie','iso_code' => 'SK'),
            array('id' => '198','name' => 'Viet Nam','iso_code' => 'VN'),
            array('id' => '199','name' => 'Slovénie','iso_code' => 'SI'),
            array('id' => '200','name' => 'Somalie','iso_code' => 'SO'),
            array('id' => '201','name' => 'Afrique du Sud','iso_code' => 'ZA'),
            array('id' => '202','name' => 'Zimbabwe','iso_code' => 'ZW'),
            array('id' => '203','name' => 'Espagne','iso_code' => 'ES'),
            array('id' => '204','name' => 'Sahara Occidental','iso_code' => 'EH'),
            array('id' => '205','name' => 'Soudan','iso_code' => 'SD'),
            array('id' => '206','name' => 'Suriname','iso_code' => 'SR'),
            array('id' => '207','name' => 'Svalbard etÎle Jan Mayen','iso_code' => 'SJ'),
            array('id' => '208','name' => 'Swaziland','iso_code' => 'SZ'),
            array('id' => '209','name' => 'Suède','iso_code' => 'SE'),
            array('id' => '210','name' => 'Suisse','iso_code' => 'CH'),
            array('id' => '211','name' => 'République Arabe Syrienne','iso_code' => 'SY'),
            array('id' => '212','name' => 'Tadjikistan','iso_code' => 'TJ'),
            array('id' => '213','name' => 'Thaïlande','iso_code' => 'TH'),
            array('id' => '214','name' => 'Togo','iso_code' => 'TG'),
            array('id' => '215','name' => 'Tokelau','iso_code' => 'TK'),
            array('id' => '216','name' => 'Tonga','iso_code' => 'TO'),
            array('id' => '217','name' => 'Trinité-et-Tobago','iso_code' => 'TT'),
            array('id' => '218','name' => 'Émirats Arabes Unis','iso_code' => 'AE'),
            array('id' => '219','name' => 'Tunisie','iso_code' => 'TN'),
            array('id' => '220','name' => 'Turquie','iso_code' => 'TR'),
            array('id' => '221','name' => 'Turkménistan','iso_code' => 'TM'),
            array('id' => '222','name' => 'Îles Turks et Caïques','iso_code' => 'TC'),
            array('id' => '223','name' => 'Tuvalu','iso_code' => 'TV'),
            array('id' => '224','name' => 'Ouganda','iso_code' => 'UG'),
            array('id' => '225','name' => 'Ukraine','iso_code' => 'UA'),
            array('id' => '226','name' => 'L\'ex-République Yougoslave de Macédoine','iso_code' => 'MK'),
            array('id' => '227','name' => 'Égypte','iso_code' => 'EG'),
            array('id' => '228','name' => 'Royaume-Uni','iso_code' => 'GB'),
            array('id' => '229','name' => 'Île de Man','iso_code' => 'IM'),
            array('id' => '230','name' => 'République-Unie de Tanzanie','iso_code' => 'TZ'),
            array('id' => '231','name' => 'États-Unis','iso_code' => 'US'),
            array('id' => '232','name' => 'Îles Vierges des États-Unis','iso_code' => 'VI'),
            array('id' => '233','name' => 'Burkina Faso','iso_code' => 'BF'),
            array('id' => '234','name' => 'Uruguay','iso_code' => 'UY'),
            array('id' => '235','name' => 'Ouzbékistan','iso_code' => 'UZ'),
            array('id' => '236','name' => 'Venezuela','iso_code' => 'VE'),
            array('id' => '237','name' => 'Wallis et Futuna','iso_code' => 'WF'),
            array('id' => '238','name' => 'Samoa','iso_code' => 'WS'),
            array('id' => '239','name' => 'Yémen','iso_code' => 'YE'),
            array('id' => '240','name' => 'Serbie-et-Monténégro','iso_code' => 'CS'),
            array('id' => '241','name' => 'Zambie','iso_code' => 'ZM')
          );
    }


}
