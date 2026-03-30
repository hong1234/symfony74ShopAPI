<?php
namespace App\Controller; 

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

// use App\Service\ProductService;
use App\Service\DaoService;

#[Route('/api')]
class CustomerController extends AbstractController {
    
    private $daoService;
    
    public function __construct(DaoService $daoService)
    {
        $this->daoService = $daoService;
    }

    #[Route('/customers', name: 'all_customer', methods: ['GET'])]
    public function allCustomers() : JsonResponse 
    { 
        $result_array = $this->daoService->allCustomers();
        $rs = [
            'code' => '200',
            'data' => $result_array
        ];
        return $this->json($rs);
    }
}