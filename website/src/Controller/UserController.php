<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserEditType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user', name: 'user_')]
class UserController extends AbstractController
{
    #[Route('/edit/{user}', name: 'edit')]
    public function edit(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
        User $user
    ): Response {
        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword !== null) {
                $hashedPassword = $passwordHasher->hashPassword(
                    $user,
                    $plainPassword
                );
                $user->setPassword($hashedPassword);
            }
            $em->flush();
            $this->addFlash('success', 'User updated!');
            return $this->redirectToRoute('admin_home');
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }
}
