<?php

namespace App\Controller;

use App\Form\CsvFileType;
use App\Service\EmployeeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'main')]
    public function indexCsv(Request $request, EmployeeService $employeeService): Response
    {
        $form = $this->createForm(CsvFileType::class);
        $form->handleRequest($request);
        $employeePairs = [];
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->getData()['csvFile'];
            if ($file) $employeePairs = $employeeService->findEmployeePairs($file);
        }

        return $this->render('main/index.html.twig', [
            'form' => $form->createView(),
            'employeePairs' => $employeePairs,
        ]);
    }


}