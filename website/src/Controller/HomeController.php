<?php

namespace App\Controller;

use App\Entity\CarCustoPrice;
use App\Form\CarCustoPriceType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/home', name: 'home_')]
class HomeController extends AbstractController
{
    #[Route('/car', name: 'car')]
    public function car(): Response
    {
        $carCustoPrice = new CarCustoPrice();
        $form = $this->createForm(CarCustoPriceType::class, $carCustoPrice);
        return $this->render('home/car.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}