<?php

namespace App\Tests\Controller;

use App\Entity\Livre;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class LivreControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $livreRepository;
    private string $path = '/livre/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->livreRepository = $this->manager->getRepository(Livre::class);

        foreach ($this->livreRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Livre index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first()->text());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'livre[titre]' => 'Testing',
            'livre[qte]' => 'Testing',
            'livre[prixunitaire]' => 'Testing',
            'livre[datepub]' => 'Testing',
            'livre[isbn]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->livreRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Livre();
        $fixture->setTitre('My Title');
        $fixture->setQte('My Title');
        $fixture->setPrixunitaire('My Title');
        $fixture->setDatepub('My Title');
        $fixture->setIsbn('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Livre');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Livre();
        $fixture->setTitre('Value');
        $fixture->setQte('Value');
        $fixture->setPrixunitaire('Value');
        $fixture->setDatepub('Value');
        $fixture->setIsbn('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'livre[titre]' => 'Something New',
            'livre[qte]' => 'Something New',
            'livre[prixunitaire]' => 'Something New',
            'livre[datepub]' => 'Something New',
            'livre[isbn]' => 'Something New',
        ]);

        self::assertResponseRedirects('/livre/');

        $fixture = $this->livreRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getTitre());
        self::assertSame('Something New', $fixture[0]->getQte());
        self::assertSame('Something New', $fixture[0]->getPrixunitaire());
        self::assertSame('Something New', $fixture[0]->getDatepub());
        self::assertSame('Something New', $fixture[0]->getIsbn());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Livre();
        $fixture->setTitre('Value');
        $fixture->setQte('Value');
        $fixture->setPrixunitaire('Value');
        $fixture->setDatepub('Value');
        $fixture->setIsbn('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/livre/');
        self::assertSame(0, $this->livreRepository->count([]));
    }
}
