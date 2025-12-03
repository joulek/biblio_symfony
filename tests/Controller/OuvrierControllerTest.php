<?php

namespace App\Tests\Controller;

use App\Entity\Ouvrier;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class OuvrierControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $ouvrierRepository;
    private string $path = '/ouvrier/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->ouvrierRepository = $this->manager->getRepository(Ouvrier::class);

        foreach ($this->ouvrierRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Ouvrier index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first()->text());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'ouvrier[nom]' => 'Testing',
            'ouvrier[prenom]' => 'Testing',
            'ouvrier[salaire]' => 'Testing',
            'ouvrier[daten]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->ouvrierRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Ouvrier();
        $fixture->setNom('My Title');
        $fixture->setPrenom('My Title');
        $fixture->setSalaire('My Title');
        $fixture->setDaten('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Ouvrier');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Ouvrier();
        $fixture->setNom('Value');
        $fixture->setPrenom('Value');
        $fixture->setSalaire('Value');
        $fixture->setDaten('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'ouvrier[nom]' => 'Something New',
            'ouvrier[prenom]' => 'Something New',
            'ouvrier[salaire]' => 'Something New',
            'ouvrier[daten]' => 'Something New',
        ]);

        self::assertResponseRedirects('/ouvrier/');

        $fixture = $this->ouvrierRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getNom());
        self::assertSame('Something New', $fixture[0]->getPrenom());
        self::assertSame('Something New', $fixture[0]->getSalaire());
        self::assertSame('Something New', $fixture[0]->getDaten());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Ouvrier();
        $fixture->setNom('Value');
        $fixture->setPrenom('Value');
        $fixture->setSalaire('Value');
        $fixture->setDaten('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/ouvrier/');
        self::assertSame(0, $this->ouvrierRepository->count([]));
    }
}
