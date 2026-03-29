<?php
namespace App\Controller; 

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use Doctrine\ORM\EntityManagerInterface;

// use App\Dao\BookDao;
use App\Entity\Cart;
use App\Entity\Customer;
use App\Entity\Category;
use App\Entity\Product;

use App\Service\CartService; 

#[Route('/api')]
class CartController extends AbstractController {

    private $cartService;
    
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function inputData(Request $request) : array
    {
        $data = json_decode($request->getContent(), true); // array
        if(is_null($data)){
             throw new \Exception('invalid json data in request body');
        }
        return $data;
    }

    #[Route('/carts/{customerId}', name: 'get_cart', methods: ['GET'])]
    public function getCart(int $customerId): JsonResponse
    {
        $rs = [
            'status' => '400',
            'data'   => []
        ];
        
        try {
            $cart = $this->cartService->getCart($customerId);

            $items = [];
            foreach ($cart->getItems() as $item) {
                array_push($items, [
                    'Qty'    => $item->getQty(),
                    'productId' => $item->getProduct()->getId(),
                    'product'   => $item->getProduct()->getTitle()
                ]);
            }

            $rs['status'] = '200';
            $rs['data'] = [
                // 'cartId'     => $cart->getId(),
                'customerId' => $cart->getCustomer()->getId(),
                'items'      => $items
            ];

        } catch (\Exception $e) {
            $rs['data'] = ['errors' => $e->getMessage()];
        }

        return $this->json($rs);
    }

    #[Route('/carts/{customerId}', name: 'update_cart', methods: ['PUT'])]
    public function updateCart(int $customerId, Request $request): JsonResponse 
    {
        $rs = [
            'status' => '200',
            'data' => []
        ];

        try {
            $data = $this->inputData($request);

            if($data['operation'] == 'plus'){
                $this->cartService->plusCart($customerId, $data['productId']);
            } 
            
            if ($data['operation'] == 'minus') {
                $this->cartService->minusCart($customerId, $data['productId']);
            } 
        } catch (\Exception $e) {
            $rs['status'] = '400';
            $rs['data'] = ['errors' => $e->getMessage()];
        }

        return $this->json($rs);
    }

}