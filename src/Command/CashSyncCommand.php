<?php

namespace App\Command;

use App\Service\CashService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CashSyncCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'app:cash-sync';

    /** @var \App\Service\CashService */
    protected $cashService;

    /** @param \App\Service\CashService $cashService */
    public function __construct(CashService $cashService)
    {
        $this->cashService = $cashService;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Background sync cash');
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

        try {
            $msg = $this->cashService->updateCashList() ?
                'Update cash success.' :
                'Nothing update cash.';
            $io->success($msg);
        } catch (\Exception $e) {
            $io->error(
                "Update complete.\n\nNot found users:\n" . $e->getMessage()
            );
        }

        return 0;
    }
}
