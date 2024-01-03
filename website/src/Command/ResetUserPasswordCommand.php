<?php

namespace App\Command;

use App\Entity\User;
use App\Service\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

// the name of the command is what users type after "php bin/console"
#[AsCommand(
    name: 'app:update-user-password',
    description: 'Update user password',
    aliases: ['app:uup'],
    hidden: false,
)]
class ResetUserPasswordCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserManager $userManager)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln(['User Creator', '============', '',]);
        $output->writeln('ID: ' . $input->getArgument('id'));

        $user = $this->em->getRepository(User::class)->findOneById($input->getArgument('id'));

        if (!$user) {
            $output->writeln('User not found');
            return Command::FAILURE;
        }

        $password = $this->userManager->updatePassword($user);

        $output->writeln('Password: ' . $password);

        return Command::SUCCESS;
    }

    protected function configure(): void
    {
        $this
            ->setHelp('This command allows you to create a user...')
            ->addArgument('id', InputArgument::REQUIRED, 'User ID :');
    }
}