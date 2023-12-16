<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Random\Randomizer;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserManager
{
    const string PWD_STRING = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!?_-';
    const int PWD_LENGTH = 16;
    private Randomizer $randomizer;

    public function __construct(
        public UserPasswordHasherInterface $passwordHasher,
        public EntityManagerInterface $em
    )
    {
        $this->randomizer = new Randomizer();
    }

    public function create(string $email, string $role, ?string $plaintextPassword = null): string
    {
        if ($plaintextPassword === null) {
            $plaintextPassword = $this->randomizer->getBytesFromString(self::PWD_STRING, self::PWD_LENGTH);
        }

        $user = new User();
        $user->setEmail($email);

        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );
        $user->setPassword($hashedPassword);
        $user->setRoles([$role]);

        $this->em->persist($user);
        $this->em->flush();

        return $plaintextPassword;
    }
}