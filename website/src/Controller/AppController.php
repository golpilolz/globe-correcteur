<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Entity\Price;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $em): Response
    {
        $prices = $em->getRepository(Price::class)->findAll();
        $employees = $em->getRepository(Employee::class)->findAll();

        return $this->render('app/index.html.twig', [
            'prices' => $prices,
            'employees' => $employees,
        ]);
    }
}
