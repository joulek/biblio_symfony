<?php

namespace App\Tests\Controller;

use App\Entity\Editeur;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class EditeurControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $editeurRepository;
    private string $path = '/editeur/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->editeurRepository = $this->manager->getRepository(Editeur::class);

        foreach ($this->editeurRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Editeur index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first()->text());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'editeur[nom]' => 'Testing',
            'editeur[pays]' => 'Testing',
            'editeur[adresse]' => 'Testing',
            'editeur[telephone]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->editeurRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Editeur();
        $fixture->setNom('My Title');
        $fixture->setPays('My Title');
        $fixture->setAdresse('My Title');
        $fixture->setTelephone('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Editeur');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Editeur();
        $fixture->setNom('Value');
        $fixture->setPays('Value');
        $fixture->setAdresse('Value');
        $fixture->setTelephone('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'editeur[nom]' => 'Something New',
            'editeur[pays]' => 'Something New',
            'editeur[adresse]' => 'Something New',
            'editeur[telephone]' => 'Something New',
        ]);

        self::assertResponseRedirects('/editeur/');

        $fixture = $this->editeurRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getNom());
        self::assertSame('Something New', $fixture[0]->getPays());
        self::assertSame('Something New', $fixture[0]->getAdresse());
        self::assertSame('Something New', $fixture[0]->getTelephone());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Editeur();
        $fixture->setNom('Value');
        $fixture->setPays('Value');
        $fixture->setAdresse('Value');
        $fixture->setTelephone('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/editeur/');
        self::assertSame(0, $this->editeurRepository->count([]));
    }
}
