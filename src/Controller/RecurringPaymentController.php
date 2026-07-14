<?php

namespace App\Controller;

use App\Entity\ExpenseTemplate;
use App\Form\RecurringPaymentType;
use App\Repository\ExpenseTemplateRepository;
use App\Service\RecurringPaymentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RecurringPaymentController extends AbstractController
{
    #[Route('/recurring-payments', name: 'app_recurring_payments', methods: ['GET'])]
    public function index(ExpenseTemplateRepository $templateRepository): Response
    {
        $templates = $templateRepository->findAllActive();
        $createForm = $this->createForm(RecurringPaymentType::class, new ExpenseTemplate(), [
            'action' => $this->generateUrl('app_recurring_payments_create'),
            'method' => 'POST',
        ]);

        $editForms = [];
        foreach ($templates as $template) {
            $editForms[$template->getId()] = $this->createForm(RecurringPaymentType::class, $template, [
                'action' => $this->generateUrl('app_recurring_payments_edit', ['id' => $template->getId()]),
                'method' => 'POST',
            ])->createView();
        }

        return $this->render('recurring_payment/index.html.twig', [
            'templates' => $templates,
            'createForm' => $createForm->createView(),
            'editForms' => $editForms,
        ]);
    }

    #[Route('/recurring-payments/new', name: 'app_recurring_payments_create', methods: ['POST'])]
    public function create(Request $request, ExpenseTemplateRepository $templateRepository, RecurringPaymentService $service): Response
    {
        $template = new ExpenseTemplate();
        $form = $this->createForm(RecurringPaymentType::class, $template);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $service->create($template);
            $this->addFlash('success', 'Recurring payment created successfully.');

            return $this->redirectToRoute('app_recurring_payments');
        }

        $templates = $templateRepository->findAllActive();
        $editForms = [];
        foreach ($templates as $t) {
            $editForms[$t->getId()] = $this->createForm(RecurringPaymentType::class, $t, [
                'action' => $this->generateUrl('app_recurring_payments_edit', ['id' => $t->getId()]),
                'method' => 'POST',
            ])->createView();
        }

        return $this->render('recurring_payment/index.html.twig', [
            'templates' => $templates,
            'createForm' => $form->createView(),
            'editForms' => $editForms,
        ]);
    }

    #[Route('/recurring-payments/{id}/edit', name: 'app_recurring_payments_edit', methods: ['POST'])]
    public function edit(int $id, Request $request, ExpenseTemplateRepository $templateRepository, RecurringPaymentService $service): Response
    {
        $template = $templateRepository->find($id);
        if (!$template) {
            throw $this->createNotFoundException('Recurring payment not found.');
        }

        $form = $this->createForm(RecurringPaymentType::class, $template);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $service->update($template);
            $this->addFlash('success', 'Recurring payment updated successfully.');

            return $this->redirectToRoute('app_recurring_payments');
        }

        $allTemplates = $templateRepository->findAllActive();
        $editForms = [];
        foreach ($allTemplates as $t) {
            $editForms[$t->getId()] = $this->createForm(RecurringPaymentType::class, $t, [
                'action' => $this->generateUrl('app_recurring_payments_edit', ['id' => $t->getId()]),
                'method' => 'POST',
            ])->createView();
        }
        // Override with the form that has errors
        $editForms[$template->getId()] = $form->createView();

        return $this->render('recurring_payment/index.html.twig', [
            'templates' => $allTemplates,
            'createForm' => $this->createForm(RecurringPaymentType::class, new ExpenseTemplate(), [
                'action' => $this->generateUrl('app_recurring_payments_create'),
                'method' => 'POST',
            ])->createView(),
            'editForms' => $editForms,
            'activeEditId' => $template->getId(),
        ]);
    }

    #[Route('/recurring-payments/{id}/delete', name: 'app_recurring_payments_delete', methods: ['POST'])]
    public function delete(int $id, ExpenseTemplateRepository $templateRepository, RecurringPaymentService $service): Response
    {
        $template = $templateRepository->find($id);
        if (!$template) {
            throw $this->createNotFoundException('Recurring payment not found.');
        }

        $service->softDelete($template);
        $this->addFlash('success', 'Recurring payment deleted successfully.');

        return $this->redirectToRoute('app_recurring_payments');
    }
}
