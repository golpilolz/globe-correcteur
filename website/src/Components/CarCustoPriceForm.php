<?php

namespace App\Components;

use App\Entity\Car;
use App\Entity\CarCustoPrice;
use App\Form\CarCustoPriceType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
class CarCustoPriceForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    public int $originalPrice = 50500;

    #[LiveProp]
    public int $price = 0;

    /**
     * The initial data used to create the form.
     */
    #[LiveProp]
    public ?CarCustoPrice $initialFormData = null;

    protected function instantiateForm(): FormInterface
    {
        $this->initialFormData = new CarCustoPrice();
        return $this->createForm(CarCustoPriceType::class, $this->initialFormData);
    }

    #[LiveAction]
    public function save(): void
    {
        // Submit the form! If validation fails, an exception is thrown
        // and the component is automatically re-rendered with the errors
        $this->submitForm();

        /** @var CarCustoPrice $post */
        $post = $this->getForm()->getData();

        $this->price = $this->originalPrice + $this->originalPrice * $post->getEngine();
    }
}