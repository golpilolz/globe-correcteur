<?php

namespace App\Command;

use App\Service\UserManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

// the name of the command is what users type after "php bin/console"
#[AsCommand(
    name: 'app:create-user',
    description: 'Creates a new user.',
    aliases: ['app:add-user'],
    hidden: false,
)]
class CreateUserCommand extends Command
{
    public function __construct(private UserManager $userManager, private bool $requirePassword = false)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln(['User Creator', '============', '',]);
        $output->writeln('EMail: ' . $input->getArgument('email'));
        $password = $this->userManager->create(
            $input->getArgument('email'),
            $input->getArgument('role')
        );
        $output->writeln('Password: ' . $password);

        return Command::SUCCESS;

// or return this if some error happened during the execution
// (it's equivalent to returning int(1))
// return Command::FAILURE;

// or return this to indicate incorrect command usage; e.g. invalid options
// or missing arguments (it's equivalent to returning int(2))
// return Command::INVALID
    }

    protected function configure(): void
    {
        $this
            ->setHelp('This command allows you to create a user...')
            ->addArgument('email', InputArgument::REQUIRED, 'Email :')
            ->addArgument('role', InputArgument::REQUIRED, 'Role :')
            ->addArgument('password', $this->requirePassword ? InputArgument::REQUIRED : InputArgument::OPTIONAL, 'User password');
    }
}