<?php

namespace App\Command;

use App\Controller\HashController;
use App\Repository\HashRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\RateLimiter\RateLimiterFactory;

#[AsCommand(
    name: 'hash:gen',
    description: 'Command to query hash generation route',
)]
class HashCommand extends Command
{

    private ManagerRegistry $registry;
    private RequestStack $request;
    private RateLimiterFactory $anonymousApiLimiter;
    private HashController $hashController;

    public function __construct(ManagerRegistry $registry, RequestStack $request, RateLimiterFactory $anonymousApiLimiter, string $name = null)
    {
        $this->request = $request;
        $this->anonymousApiLimiter = $anonymousApiLimiter;
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
        $hashRepository = new HashRepository($this->registry);
        $this->hashController = new HashController($this->request, $this->anonymousApiLimiter);

        $string = $input->getArgument('string');
        $requests = $input->getOption('requests');
        $first_hash = $this->requestHash($string);
        $hash_decode = (array)json_decode($first_hash);
        $saveRequest = array(
            'batch' => new \DateTime('now', new \DateTimeZone('America/Sao_Paulo')),
            'block' => 1,
            'string' => $hash_decode['string'],
            'key_string' => $hash_decode['key'],
            'hash' => $hash_decode['hash'],
            'attempts' => $hash_decode['attempts']
        );
        $hashRepository->insertHash($saveRequest);
        $io->note(sprintf('Hash: %s, Key %s, Attempts %s', $hash_decode['hash'], $hash_decode['key'], $hash_decode['attempts']));
        for ($block = 2; $block <= $requests; $block += 1) {
            $second_hash = $this->requestHash($hash_decode['hash']);
            $hash_decode = (array)json_decode($second_hash);
            $saveRequest = array(
                'batch' => new \DateTime('now', new \DateTimeZone('America/Sao_Paulo')),
                'block' => $block,
                'string' => $hash_decode['string'],
                'key_string' => $hash_decode['key'],
                'hash' => $hash_decode['hash'],
                'attempts' => $hash_decode['attempts']
            );
            $hash_decode = (array)json_decode($second_hash);
            $hashRepository->insertHash($saveRequest);
            $io->note(sprintf('Hash: %s, Key %s, Attempts %s', $hash_decode['hash'], $hash_decode['key'], $hash_decode['attempts']));
        }

        return Command::SUCCESS;
    }


    public function requestHash($string): bool|string
    {
        $hash_info = $this->hashController->index($string);
        return $hash_info->getContent();
    }
}
