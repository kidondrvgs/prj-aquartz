<?php

namespace App\Tests;

use App\Entity\CustomerRequest;
use App\Entity\Service;
use App\Entity\User;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminTest extends WebTestCase
{
    private $client;
    private int $clientId;
    private int $adminId;
    private int $demandeId;
    private int $serviceId;
    private int $secondId;

    protected function setUp(): void
    {
        $this->client = self::createClient(['debug' => false]);
        $em = self::getContainer()->get('doctrine')->getManager();
        self::assertInstanceOf(\Doctrine\DBAL\Platforms\SQLitePlatform::class, $em->getConnection()->getDatabasePlatform());
        $schema = new SchemaTool($em);
        $metadata = $em->getMetadataFactory()->getAllMetadata();
        $schema->dropSchema($metadata);
        $schema->createSchema($metadata);

        $client = (new User())
            ->setFirstName('Théo')
            ->setLastName('Client')
            ->setEmail('client@test.fr')
            ->setRoles(['ROLE_USER'])
            ->setPassword('test');
        $admin = (new User())
            ->setFirstName('Théo')
            ->setLastName('Admin')
            ->setEmail('admin@test.fr')
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword('test');
        $hasher = self::getContainer()->get('security.user_password_hasher');
        $client->setPassword($hasher->hashPassword($client, 'Client123!'));
        $admin->setPassword($hasher->hashPassword($admin, 'Admin123!'));
        $service = (new Service())
            ->setTitle('Terrassement')
            ->setSlug('terrassement')
            ->setDescription('Préparation du terrain')
            ->setPrix(10000);
        $second = (new Service())
            ->setTitle('Graviers')
            ->setSlug('apport-graviers')
            ->setDescription('Apport de graviers')
            ->setPrix(25000);
        $demande = (new CustomerRequest())
            ->setUser($client)
            ->setFirstName('Théo')
            ->setLastName('Client')
            ->setEmail('client@test.fr')
            ->setLocation('12 rue des Lilas, 76000 Rouen')
            ->setStatus('Nouvelle')
            ->setSurface(20)
            ->setDescription('Chantier test')
            ->setDateRequest(new \DateTime('+2 days'));
        $demande->addService($service);
        foreach ([$client, $admin, $service, $second, $demande] as $objet) {
            $em->persist($objet);
        }
        $em->flush();
        $this->clientId = $client->getId();
        $this->adminId = $admin->getId();
        $this->demandeId = $demande->getId();
        $this->serviceId = $service->getId();
        $this->secondId = $second->getId();
    }

    private function connecter(int $id): void
    {
        $user = self::getContainer()->get('doctrine')->getRepository(User::class)->find($id);
        $this->client->loginUser($user);
    }

    public function testAccesEtNavigation(): void
    {
        $this->client->request('GET', '/admin');
        self::assertResponseRedirects('/login');
        $this->connecter($this->clientId);
        foreach (['/admin', '/admin/prestations', '/admin/demandes'] as $url) {
            $this->client->request('GET', $url);
            self::assertResponseStatusCodeSame(403);
        }
        $this->connecter($this->adminId);
        $this->client->request('GET', '/admin');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Tableau de bord');
        self::assertSelectorNotExists('a[href="/nouvelle-demande"]');
        self::assertSelectorNotExists('a[href="/mes-demandes"]');
        $this->client->request('GET', '/nouvelle-demande');
        self::assertResponseRedirects('/admin/demandes');
    }

    public function testTraitementEtLectureParLeClient(): void
    {
        $this->connecter($this->adminId);
        $this->client->request('GET', '/admin/demandes/'.$this->demandeId);
        self::assertResponseIsSuccessful();
        $this->client->submitForm('Enregistrer la réponse et le statut', [
            'traitement_demande[status]' => 'Acceptée',
            'traitement_demande[reponse]' => 'Nous sommes disponibles lundi.',
        ]);
        self::assertResponseRedirects();
        $this->client->request('GET', '/admin/demandes?statut=Terminée');
        self::assertSelectorTextContains('.admin-liste', 'Aucune demande');
        $this->connecter($this->clientId);
        $this->client->request('GET', '/mes-demandes/'.$this->demandeId);
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('body', 'Nous sommes disponibles lundi.');
        self::assertSelectorTextContains('.badge', 'Acceptée');
    }

    public function testPrestationsEtSuppressionProtegee(): void
    {
        $this->connecter($this->adminId);
        $this->client->request('GET', '/admin/prestations');
        self::assertResponseIsSuccessful();
        $this->client->submitForm('Enregistrer', [
            'prestation[title]' => 'Nouveau chantier',
            'prestation[description]' => 'Une prestation de test',
            'prestation[prix]' => '120,50',
        ]);
        self::assertResponseRedirects('/admin/prestations');
        $repository = self::getContainer()->get('doctrine')->getRepository(Service::class);
        $nouvelle = $repository->findOneBy(['title' => 'Nouveau chantier']);
        self::assertSame(12050, $nouvelle->getPrix());
        $id = $nouvelle->getId();
        $this->client->request('POST', '/admin/prestations/'.$id.'/supprimer', ['_token' => 'invalide']);
        self::assertResponseStatusCodeSame(403);
        $crawler = $this->client->request('GET', '/admin/prestations');
        $token = $crawler->filter('form[action="/admin/prestations/'.$this->serviceId.'/supprimer"] input')->attr('value');
        $this->client->request('POST', '/admin/prestations/'.$this->serviceId.'/supprimer', ['_token' => $token]);
        self::assertResponseRedirects();
        $this->client->followRedirect();
        self::assertSelectorTextContains('.form-error', 'utilisée');
        $crawler = $this->client->getCrawler();
        $token = $crawler->filter('form[action="/admin/prestations/'.$id.'/supprimer"] input')->attr('value');
        $this->client->request('POST', '/admin/prestations/'.$id.'/supprimer', ['_token' => $token]);
        self::assertResponseRedirects();
        self::assertNull(self::getContainer()->get('doctrine')->getRepository(Service::class)->find($id));
    }

    public function testTotalDePlusieursPrestations(): void
    {
        $this->connecter($this->clientId);
        $this->client->request('GET', '/nouvelle-demande');
        self::assertResponseIsSuccessful();
        $this->client->submitForm('Envoyer ma demande', [
            'customer_request[prestation]' => [(string) $this->serviceId, (string) $this->secondId],
            'customer_request[description]' => 'Deux prestations pour mon chantier',
            'customer_request[dateRequest]' => (new \DateTime('+3 days'))->format('Y-m-d'),
            'customer_request[surface]' => 30,
            'customer_request[location]' => '12 rue des Lilas, 76000 Rouen',
        ]);
        self::assertResponseRedirects();
        $demande = self::getContainer()->get('doctrine')->getRepository(CustomerRequest::class)->findOneBy(['description' => 'Deux prestations pour mon chantier']);
        self::assertSame(35000, $demande->getMontantEstime());
        self::assertCount(2, $demande->getServices());
    }

    public function testFormulairesInvalidesEtProtectionDesDemandes(): void
    {
        $this->connecter($this->adminId);
        $this->client->request('POST', '/admin/demandes/'.$this->demandeId, [
            'traitement_demande' => ['status' => 'Terminée', 'reponse' => 'Réponse sans jeton'],
        ]);
        self::assertResponseIsSuccessful();
        $em = self::getContainer()->get('doctrine')->getManager();
        $em->clear();
        self::assertSame('Nouvelle', $em->find(CustomerRequest::class, $this->demandeId)->getStatus());
        $this->client->request('GET', '/admin/prestations');
        $this->client->submitForm('Enregistrer', [
            'prestation[title]' => 'Prix invalide',
            'prestation[description]' => 'Test de validation',
            'prestation[prix]' => '-1',
        ]);
        self::assertResponseStatusCodeSame(422);
        self::assertNull(self::getContainer()->get('doctrine')->getRepository(Service::class)->findOneBy(['title' => 'Prix invalide']));
        $this->client->request('GET', '/admin/demandes/999999');
        self::assertResponseStatusCodeSame(404);
        $this->client->request('GET', '/admin/prestations/999999/modifier');
        self::assertResponseStatusCodeSame(404);
        $this->client->request('GET', '/admin/demandes?statut=inconnu');
        self::assertResponseStatusCodeSame(404);
    }

    public function testConnexionEtRedirectionSelonLeRole(): void
    {
        $this->client->request('GET', '/login');
        $this->client->submitForm('Se connecter', ['_username' => 'admin@test.fr', '_password' => 'Admin123!']);
        self::assertResponseRedirects('http://localhost/mon-espace');
        $this->client->followRedirect();
        self::assertResponseRedirects('/admin');
    }

    public function testSuppressionDuCompteEnBaseDeTest(): void
    {
        $this->connecter($this->clientId);
        $this->client->request('GET', '/supprimer-mon-compte');
        $this->client->submitForm('Supprimer définitivement', [
            'form[motDePasse]' => 'incorrect',
            'form[confirmation]' => true,
        ]);
        self::assertResponseStatusCodeSame(422);
        self::assertNotNull(self::getContainer()->get('doctrine')->getRepository(User::class)->find($this->clientId));
        $this->client->request('GET', '/supprimer-mon-compte');
        $this->client->submitForm('Supprimer définitivement', [
            'form[motDePasse]' => 'Client123!',
            'form[confirmation]' => true,
        ]);
        self::assertResponseRedirects();
        $em = self::getContainer()->get('doctrine')->getManager();
        self::assertNull($em->find(User::class, $this->clientId));
        self::assertNull($em->find(CustomerRequest::class, $this->demandeId));
        self::assertNotNull($em->find(Service::class, $this->serviceId));
    }

    public function testUnClientNeVoitPasLaDemandeDunAutre(): void
    {
        $entityManager = self::getContainer()->get('doctrine')->getManager();
        $autreClient = (new User())
            ->setFirstName('Autre')
            ->setLastName('Client')
            ->setEmail('autre@test.fr')
            ->setPassword('test');
        $entityManager->persist($autreClient);
        $entityManager->flush();
        $this->connecter($autreClient->getId());
        $this->client->request('GET', '/mes-demandes/'.$this->demandeId);
        self::assertResponseStatusCodeSame(403);
        self::assertStringNotContainsString('Chantier test', $this->client->getResponse()->getContent());
    }

    public function testImageNonProposeeRefusee(): void
    {
        $this->connecter($this->adminId);
        $page = $this->client->request('GET', '/admin/prestations');
        $form = $page->selectButton('Enregistrer')->form();
        $valeurs = $form->getPhpValues();
        $valeurs['prestation']['title'] = 'Image invalide';
        $valeurs['prestation']['description'] = 'Test de validation';
        $valeurs['prestation']['image'] = '../../.env';
        $this->client->request('POST', $form->getUri(), $valeurs);
        self::assertResponseStatusCodeSame(422);
        self::assertNull(self::getContainer()->get('doctrine')->getRepository(Service::class)->findOneBy(['title' => 'Image invalide']));
    }
}
