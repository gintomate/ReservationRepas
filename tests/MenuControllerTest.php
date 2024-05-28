<?php

namespace App\Tests\Controller\Admin;

use App\Entity\JourReservation;
use App\Entity\Repas;
use App\Entity\SemaineReservation;
use App\Repository\SemaineReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class MenuControllerTest extends WebTestCase
{
    public function testDeleteMenuSuccessfully()
    {
        $client = static::createClient();

        // Step 1: Create mocks for the dependencies
        $semaineRepo = $this->createMock(SemaineReservationRepository::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);

        // Step 2: Create a mock SemaineReservation entity
        $semaine = $this->createMock(SemaineReservation::class);
        $semaine->method('getNumeroSemaine')->willReturn(1);

        // Step 3: Create mock JourReservation and Repas entities
        $repas = $this->createMock(Repas::class);
        $jour = $this->createMock(JourReservation::class);
        $jour->method('getRepas')->willReturn([$repas]);

        // Mocking the jourReservation to return a collection of JourReservation
        $semaine->method('getJourReservation')->willReturn([$jour]);

        // Step 4: Set up the repository mock to return the mock SemaineReservation entity
        $semaineRepo->method('find')->willReturn($semaine);

        // Step 5: Define expectations for the EntityManager mock
        $entityManager->expects($this->exactly(2))
                      ->method('remove')
                      ->withConsecutive([$repas], [$jour]);
        $entityManager->expects($this->once())
                      ->method('flush');

        // Step 6: Replace real services with mocks in the container
        $client->getContainer()->set(SemaineReservationRepository::class, $semaineRepo);
        $client->getContainer()->set(EntityManagerInterface::class, $entityManager);

        // Step 7: Make the DELETE request
        $client->request('DELETE', '/admin/menu/delete/1');

        // Step 8: Assert the response
        $this->assertEquals(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString(
            'Le Menu de la semaine 1 a bien été supprimé.',
            $client->getResponse()->getContent()
        );
    }

    public function testDeleteMenuNotFound()
    {
        $client = static::createClient();

        // Step 1: Create a mock for the SemaineReservationRepository
        $semaineRepo = $this->createMock(SemaineReservationRepository::class);

        // Step 2: Set up the repository mock to return null
        $semaineRepo->method('find')->willReturn(null);

        // Step 3: Replace the real service with the mock in the container
        $client->getContainer()->set(SemaineReservationRepository::class, $semaineRepo);

        // Step 4: Make the DELETE request
        $client->request('DELETE', '/admin/menu/delete/1');

        // Step 5: Assert the response
        $this->assertEquals(Response::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString(
            'Le Menu n\'existe pas.',
            $client->getResponse()->getContent()
        );
    }
}
