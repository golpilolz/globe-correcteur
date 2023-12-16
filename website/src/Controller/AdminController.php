<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Employee;
use App\Entity\Order;
use App\Entity\Price;
use App\Entity\Record;
use App\Form\CustomerType;
use App\Form\EmployeeType;
use App\Form\ExportType;
use App\Form\OrderType;
use App\Form\PriceType;
use App\Form\RecordType;
use App\Service\ExportService;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin', name: 'admin_')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(EntityManagerInterface $em): Response
    {
        $customers = $em->getRepository(Customer::class)->count();
        $orders = $em->getRepository(Order::class)->count();
        $prices = $em->getRepository(Price::class)->count();
        $lastRecord = $em->getRepository(Record::class)->findOneBy([], ['id' => 'DESC']);
        return $this->render('admin/index.html.twig', [
            'customers' => $customers,
            'orders' => $orders,
            'prices' => $prices,
            'bank' => $lastRecord ? $lastRecord->getCurrentAmount() : 0
        ]);
    }

    #[Route('/customers', name: 'customers')]
    public function customers(EntityManagerInterface $em)
    {
        $customers = $em->getRepository(Customer::class)->findAll();

        return $this->render('admin/customers.html.twig', [
            'customers' => $customers,
        ]);
    }

    #[Route('/customers/add', name: 'customers_add')]
    public function customerAdd(Request $request, EntityManagerInterface $em): RedirectResponse|Response
    {
        $customer = new Customer();
        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($customer);
            $em->flush();

            return $this->redirectToRoute('admin_customers');
        }

        return $this->render('admin/customers-add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/customers/edit/{id}', name: 'customers_edit')]
    public function customerEdit(Request $request, EntityManagerInterface $em, Customer $customer): RedirectResponse|Response
    {
        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($customer);
            $em->flush();

            return $this->redirectToRoute('admin_customers');
        }

        return $this->render('admin/customers-edit.html.twig', [
            'customer' => $customer,
            'form' => $form->createView()
        ]);
    }

    #[Route('/customers/delete/{id}', name: 'customers_delete')]
    public function customerDelete(EntityManagerInterface $em, Customer $customer): RedirectResponse
    {
        $em->remove($customer);
        $em->flush();

        return $this->redirectToRoute('admin_customers');
    }

    #[Route('/orders', name: 'orders')]
    public function orders(EntityManagerInterface $em): Response
    {
        $orders = $em->getRepository(Order::class)->findAll();

        return $this->render('admin/orders.html.twig', [
            'orders' => $orders,
        ]);
    }

    #[Route('/orders/add', name: 'orders_add')]
    public function orderAdd(Request $request, EntityManagerInterface $em)
    {
        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $order->setWords(str_word_count($order->getText()));
            $em->persist($order);
            $em->flush();

            return $this->redirectToRoute('admin_orders');
        }

        return $this->render('admin/orders-add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/orders/edit/{id}', name: 'orders_edit')]
    public function orderEdit(Request $request, EntityManagerInterface $em, Order $order): Response
    {
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $order->setWords(str_word_count($order->getText()));
            $em->persist($order);
            $em->flush();

            return $this->redirectToRoute('admin_orders');
        }

        return $this->render('admin/orders-edit.html.twig', [
            'order' => $order,
            'form' => $form->createView()
        ]);
    }

    #[Route('/employees', name: 'employees')]
    public function employees(EntityManagerInterface $em): Response
    {
        $employees = $em->getRepository(Employee::class)->findAll();

        return $this->render('admin/employees.html.twig', [
            'employees' => $employees,
        ]);
    }

    #[Route('/employees/add', name: 'employees_add')]
    public function employeeAdd(Request $request, EntityManagerInterface $em): Response
    {
        $employee = new Employee();
        $form = $this->createForm(EmployeeType::class, $employee);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($employee);
            $em->flush();

            return $this->redirectToRoute('admin_employees');
        }

        return $this->render('admin/employees-add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/employees/edit/{employee}', name: 'employees_edit')]
    public function employeeEdit(Request $request, EntityManagerInterface $em, Employee $employee): Response
    {
        $form = $this->createForm(EmployeeType::class, $employee);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($employee);
            $em->flush();

            return $this->redirectToRoute('admin_employees');
        }

        return $this->render('admin/employees-edit.html.twig', [
            'employee' => $employee,
            'form' => $form->createView()
        ]);
    }

    #[Route('/bank', name: 'bank')]
    public function bank(EntityManagerInterface $em): Response
    {
        $records = $em->getRepository(Record::class)->findBy([], ['createdAt' => 'DESC']);
        $form = $this->createForm(ExportType::class, options: [
            'action' => $this->generateUrl('admin_bank_export'),
            'method' => 'GET'
        ]);

        return $this->render('admin/bank.html.twig', [
            'records' => $records,
            'form' => $form->createView()
        ]);
    }

    #[Route('/bank/add-record', name: 'bank_add-record')]
    public function addRecord(Request $request, EntityManagerInterface $em): Response
    {
        $record = new Record();
        $form = $this->createForm(RecordType::class, $record);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $lastRecord = $em->getRepository(Record::class)->findOneBy([], ['id' => 'DESC']);
            if ($lastRecord) {
                if ($record->getOperation() === 'credit') {
                    $record->setCurrentAmount($lastRecord->getCurrentAmount() + $record->getAmount());
                } else {
                    $record->setCurrentAmount($lastRecord->getCurrentAmount() - $record->getAmount());
                }

            } else {
                $record->setCurrentAmount($record->getAmount());
            }
            $record->setUser($this->getUser());
            $em->persist($record);
            $em->flush();

            return $this->redirectToRoute('admin_bank');
        }


        return $this->render('admin/record-add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/bank/export', name: 'bank_export')]
    public function export(Request $request, EntityManagerInterface $em, ExportService $exportService)
    {
        $form = $this->createForm(ExportType::class, options: [
            'action' => $this->generateUrl('admin_bank_export'),
            'method' => 'GET'
        ]);
        $form->handleRequest($request);

        $credits = [];
        $debits = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $month = $form->get('month')->getData();
            $year = $form->get('year')->getData();
            $start = \DateTimeImmutable::createFromFormat('Y-m-d', $year . '-' . $month . '-01')
                ->modify('first day of this month')
                ->setTime(0, 0);
            $end = \DateTimeImmutable::createFromFormat('Y-m-d', $year . '-' . $month . '-01')
                ->modify('last day of this month')
                ->setTime(23, 59, 59);
            $records = $em->getRepository(Record::class)->findByInterval($start, $end);

            foreach ($records as $record) {
                if ($record->getOperation() === Record::DEBIT) {
                    $debits[] = $record;
                } elseif ($record->getOperation() === Record::CREDIT) {
                    $credits[] = $record;
                }
            }
        } else {
            return $this->redirectToRoute('admin_bank');
        }

        $filename = "Globe_Correcteur_{$month}_$year.xlsx";
        $speadsheet = $exportService->generateExcel($credits, $debits, $month, $year);
        $writer = new Xlsx($speadsheet);

        $response = new StreamedResponse();
        $response->headers->set('Content-Type', "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $filename . '"');
        $response->setPrivate();
        $response->headers->addCacheControlDirective('no-cache', true);
        $response->headers->addCacheControlDirective('must-revalidate', true);
        $response->setCallback(function () use ($writer) {
            $writer->save('php://output');
        });

        return $response;
    }

    #[Route('/prices', name: 'prices')]
    public function prices(EntityManagerInterface $em): Response
    {
        $prices = $em->getRepository(Price::class)->findAll();

        return $this->render('admin/prices.html.twig', [
            'prices' => $prices,
        ]);
    }

    #[Route('/prices/add', name: 'prices_add')]
    public function pricesAdd(Request $request, EntityManagerInterface $em, FileUploader $fileUploader): Response
    {
        $price = new Price();
        $form = $this->createForm(PriceType::class, $price);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
          /** @var UploadedFile $file */
          $file = $form->get('image')->getData();
          if ($file) {
            $imageFilename = $fileUploader->upload($file);
            $price->setImage($imageFilename);
          }
            $em->persist($price);
            $em->flush();

            return $this->redirectToRoute('admin_prices');
        }

        return $this->render('admin/prices-add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/prices/edit/{price}', name: 'prices_edit')]
    public function pricesEdit(Request $request, EntityManagerInterface $em, FileUploader $fileUploader, Price $price): Response
    {
        $form = $this->createForm(PriceType::class, $price);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $form->get('image')->getData();
            if ($file) {
                $imageFilename = $fileUploader->upload($file);
                $price->setImage($imageFilename);
            }
            $em->persist($price);
            $em->flush();

            return $this->redirectToRoute('admin_prices');
        }

        return $this->render('admin/prices-edit.html.twig', [
            'price' => $price,
            'form' => $form->createView()
        ]);
    }
}
