<?php

namespace App\Tests\Controller;

use App\Entity\Ouvirer;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class OuvirerControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $ouvirerRepository;
    private string $path = '/ouvirer/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->ouvirerRepository = $this->manager->getRepository(Ouvirer::class);

        foreach ($this->ouvirerRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Ouvirer index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first()->text());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'ouvirer[nom]' => 'Testing',
            'ouvirer[prenom]' => 'Testing',
            'ouvirer[salaire]' => 'Testing',
            'ouvirer[daten]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->ouvirerRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Ouvirer();
        $fixture->setNom('My Title');
        $fixture->setPrenom('My Title');
        $fixture->setSalaire('My Title');
        $fixture->setDaten('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Ouvirer');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Ouvirer();
        $fixture->setNom('Value');
        $fixture->setPrenom('Value');
        $fixture->setSalaire('Value');
        $fixture->setDaten('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'ouvirer[nom]' => 'Something New',
            'ouvirer[prenom]' => 'Something New',
            'ouvirer[salaire]' => 'Something New',
            'ouvirer[daten]' => 'Something New',
        ]);

        self::assertResponseRedirects('/ouvirer/');

        $fixture = $this->ouvirerRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getNom());
        self::assertSame('Something New', $fixture[0]->getPrenom());
        self::assertSame('Something New', $fixture[0]->getSalaire());
        self::assertSame('Something New', $fixture[0]->getDaten());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Ouvirer();
        $fixture->setNom('Value');
        $fixture->setPrenom('Value');
        $fixture->setSalaire('Value');
        $fixture->setDaten('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/ouvirer/');
        self::assertSame(0, $this->ouvirerRepository->count([]));
    }
}
