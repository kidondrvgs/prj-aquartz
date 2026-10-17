<?php

namespace App\Tests;

use App\Entity\CustomerRequest;
use App\Entity\Service;
use PHPUnit\Framework\TestCase;

class PrixTest extends TestCase
{
    public function testTotalEtConservationDuMontant(): void
    {
        $premiere = (new Service())->setPrix(10050);
        $seconde = (new Service())->setPrix(25000);
        $demande = new CustomerRequest();
        self::assertNull($demande->calculerTotal());
        $demande->addService($premiere)->addService($seconde);
        self::assertSame(35050, $demande->calculerTotal());
        $demande->setMontantEstime($demande->calculerTotal());
        $premiere->setPrix(20000);
        self::assertSame(35050, $demande->getMontantEstime());
        $seconde->setPrix(null);
        self::assertNull($demande->calculerTotal());
        $seconde->setPrix(0);
        self::assertSame(20000, $demande->calculerTotal());
    }
}
