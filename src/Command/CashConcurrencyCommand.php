<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class CashConcurrencyCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'app:cash-concurrency';

    /** @var \Doctrine\ORM\EntityManagerInterface */
    protected $entityManager;

    /** @param \Doctrine\ORM\EntityManagerInterface $entityManager */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Test concurrency change user cash.')
            ->addArgument('action', InputArgument::REQUIRED, 'Choose a action (e.g. add/sub)')
            ->addArgument('account', InputArgument::REQUIRED, 'User account')
            ->addOption('concurrency', 'C', InputOption::VALUE_REQUIRED, 'Number of multiple requests to make at a time')
            ->addOption('requests', 'N', InputOption::VALUE_REQUIRED, 'Number of requests to perform');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $action = $input->getArgument('action');
        $io->note(sprintf('Cash action type: %s', $action));

        $account = $input->getArgument('account');

        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['account' => $account]);

        if (!$user) {
            $io->error(sprintf('Not found user: %s', $account));
            return 0;
        }

        $command = ['ab', '-T', 'application/json', '-u', 'data.txt'];

        if ($concurrency = $input->getOption('concurrency')) {
            array_push($command, "-c", $concurrency);
        }

        if ($requests = $input->getOption('requests')) {
            array_push($command, "-n", $requests);
        }

        $command[] = "nginx/api/cash/{$user->getId()}/{$action}";
        $process = new Process($command);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $io->text($process->getOutput());
        $io->success('Cash concurrency test complete.');

        return 0;
    }
}
