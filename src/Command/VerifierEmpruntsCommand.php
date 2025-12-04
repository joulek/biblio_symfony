<?php

namespace App\Command;

use App\Entity\Emprunt;
use App\Repository\EmpruntRepository;
use App\Service\EmpruntNotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:verifier-emprunts',
    description: 'Vérifie les emprunts bientôt échus et envoie des notifications',
)]
class VerifierEmpruntsCommand extends Command
{
    private $empruntRepository;
    private $notificationService;
    private $entityManager;

    public function __construct(
        EmpruntRepository $empruntRepository,
        EmpruntNotificationService $notificationService,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct();
        $this->empruntRepository = $empruntRepository;
        $this->notificationService = $notificationService;
        $this->entityManager = $entityManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $dateLimite = new \DateTime('+2 days');
        $emprunts = $this->empruntRepository->findEmpruntsARappeler($dateLimite);

        $count = 0;
        foreach ($emprunts as $emprunt) {
            if (!$emprunt->getDateNotificationRappel()) {
                try {
                    $this->notificationService->envoyerRappelRetour($emprunt);
                    $emprunt->setDateNotificationRappel(new \DateTime());
                    $this->entityManager->persist($emprunt);
                    $count++;
                } catch (\Exception $e) {
                    $output->writeln(sprintf('Erreur lors de l\'envoi à %s: %s', 
                        $emprunt->getUser()->getEmail(), 
                        $e->getMessage()
                    ));
                }
            }
        }

        $this->entityManager->flush();
        
        if ($count > 0) {
            $output->writeln(sprintf('%d notifications de rappel envoyées avec succès.', $count));
        } else {
            $output->writeln('Aucune notification à envoyer.');
        }

        return Command::SUCCESS;
    }
}
