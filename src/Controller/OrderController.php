<?php
namespace App\Controller; 

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Order;

use App\Service\OrderService;

#[Route('/api')]
class OrderController extends AbstractController {
    
    private $orderService;
    
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    #[Route('/orders/{orderId}', name: 'get_order', methods: ['GET'])]
    public function getOrder(int $orderId): JsonResponse
    {
        $rs = [
            'status' => '400',
            'data'   => []
        ];

        try {
            $order = $this->orderService->getOrder($orderId);

            $rs['status'] = '200';
            $rs['data'] = [
                'orderId'   => $order->getId(),
                'customer'  => $order->getCustomer()->getUsername(),
                'Total-Sum' => $order->getTotalPrice(),
            ];
            
        } catch (\Exception $e) {
            $rs['data'] = ['errors' => $e->getMessage()];
        }

        return $this->json($rs);
    }

}