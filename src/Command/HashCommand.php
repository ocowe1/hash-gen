<?php

namespace App\Command;

use App\Controller\HashController;
use App\Entity\Hash;
use App\Repository\HashRepository;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'hash:gen',
    description: 'Command to query hash generation route',
)]
class HashCommand extends Command
{

    private ManagerRegistry $registry;

    public function __construct(ManagerRegistry $registry, string $name = null)
    {
        $this->registry = $registry;
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('string', InputArgument::REQUIRED, 'An initial string that will be sent in the first request.')
            ->addOption('requests', null, InputOption::VALUE_REQUIRED, 'The number of requests that will be made in sequence.')
        ;
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $string = $input->getArgument('string');
        $requests = $input->getOption('requests');

        $this->HashCommandGen($string, $requests, $input, $output);

        return Command::SUCCESS;
    }

    /**
     * @throws Exception
     */
    function HashCommandGen($string, $requests, InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $hashController = new HashController();
        $hashRepository = new HashRepository($this->registry);

        $new_string = $hashController->hashGenerate($string);
        $saveRequest = array(
            'batch' => new \DateTime('now', new \DateTimeZone('America/Sao_Paulo')),
            'block' => 1,
            'string' => $string,
            'key' => $new_string['key'],
            'hash' => $new_string['hash'],
            'attempts' => $new_string['attempts']
        );
        $io->note(sprintf('Hash: %s, Key %s, Attempts %s', $new_string['hash'], $new_string['key'], $new_string['attempts']));
        $hashRepository->insertHash($saveRequest);

        if ($requests >= 2)
        {
            for ($i = 1; $i < $requests; $i++)
            {
                $hash = $hashController->hashGenerate($new_string['hash']);
                $new_string = $hash;
                $saveRequest = array(
                    'batch' => new \DateTime('now', new \DateTimeZone('America/Sao_Paulo')),
                    'block' => $i+=1,
                    'string' => $new_string['hash'],
                    'key' => $hash['key'],
                    'hash' => $hash['hash'],
                    'attempts' => $hash['attempts']
                );
                $hashRepository->insertHash($saveRequest);
                $io->note(sprintf('Hash: %s, Key %s, Attempts %s', $hash['hash'], $hash['key'], $hash['attempts']));
            }
        }
    }
}
