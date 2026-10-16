<?php

require dirname(__DIR__).'/vendor/autoload.php';

use App\Entity\CustomerRequest;
use App\Entity\Service;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Validation;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

function verifier(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

$twig = new Environment(new FilesystemLoader(dirname(__DIR__).'/templates'));
foreach (['asset', 'path', 'form_start', 'form_end', 'form_errors', 'form_row', 'form_widget', 'form_label'] as $name) {
    $twig->addFunction(new TwigFunction($name, fn ($value) => $value));
}

foreach ([null, (object) ['firstName' => 'Théo']] as $user) {
    $html = $twig->render('base.html.twig', ['app' => (object) ['user' => $user]]);
    verifier(str_contains($html, 'app_supprimer_compte') === ($user !== null), 'Menu du compte incorrect.');
    verifier(str_contains($html, '>Inscription</a>') === ($user === null), 'Inscription visible pour un client connecté.');
    verifier(str_contains($html, 'Connecté : Théo') === ($user !== null), 'Prénom manquant.');
}

$twig->parse($twig->tokenize(new Twig\Source(file_get_contents(dirname(__DIR__).'/templates/compte/supprimer.html.twig'), 'supprimer')));
$validator = Validation::createValidator();
verifier(count($validator->validate(false, new IsTrue())) === 1, 'Confirmation obligatoire.');
verifier(count($validator->validate(true, new IsTrue())) === 0, 'Confirmation refusée.');

$demande = new CustomerRequest();
$service = new Service();
$demande->addService($service);
$demande->removeService($service);
verifier($demande->getServices()->isEmpty(), 'Lien de la demande conservé.');
verifier(!$service->getFkCustomerRequest()->contains($demande), 'Lien du catalogue conservé.');

echo "OK : menu visiteur/client, page de confirmation et retrait des liens des prestations.\n";
