<?php
namespace App\Controller; 

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

// use App\Dao\BookDao;
use App\Entity\Category;
use App\Entity\Product;
use App\Service\ProductService;
use App\Service\DaoService;

#[Route('/api')]
class ProductController extends AbstractController {
    private $daoService;
    private $productService;
    
    public function __construct(DaoService $daoService, ProductService $productService)
    {
        $this->daoService = $daoService;
        $this->productService = $productService;
    }

    public function inputData(Request $request) : array
    {
        $data = json_decode($request->getContent(), true); // array
        if(is_null($data)){
            throw new \Exception('invalid json data in request body');
        }
        return $data;
    }

    #[Route('/products/search', name: 'search_product', methods: ['GET'])]
    public function searchProducts(Request $request) : JsonResponse 
    { 
        $key = $request->get('searchkey');
        if(is_null($key)){
            $rs = [
                'code' => '200',
                'data' => ['errors' => 'query string must be: ?searchkey=']
            ];
            return $this->json($rs);
        }

        $result_array = $this->daoService->searchProducts($key);
        $rs = [
            'code' => '200',
            'data' => $result_array
        ];
        return $this->json($rs);
    }

    #[Route('/products', name: 'all_product', methods: ['GET'])]
    public function allProducts() : JsonResponse 
    { 
        $result_array = $this->daoService->allProducts();
        $rs = [
            'code' => '200',
            'data' => $result_array
        ];
        return $this->json($rs);
    }

    #[Route('/products', name: 'add_product', methods: ['POST'])]
    public function addProduct(Request $request): JsonResponse
    {
        $rs = [
            'status' => '400',
            'data'   => []
        ];

        try {
            $data = $this->inputData($request);
            $product = $this->productService->newProduct($data);

            $rs['status'] = '200';
            $rs['data'] = [
                'id'    => $product->getId(),
                'title' => $product->getTitle()
            ];

        } catch (\Exception $e) {
            $rs['data'] = ['errors' => $e->getMessage()];
        }

        return $this->json($rs);
        // return new Response((string) $errors, 400);
    }

    #[Route('/products/{id}', name: 'get_product', methods: ['GET'])]
    public function productDetail(int $id): JsonResponse 
    {
        $rs = [
            'status' => '400',
            'data'   => []
        ];

        try {
            $product =  $this->productService->getProduct($id);
            $rs['status'] = '200';
            $rs['data'] = [
                'id'       => $product->getId(),
                'title'    => $product->getTitle(),
                'category' => $product->getCategory()->getName(),
            ];
            
        } catch (\Exception $e) {
            $rs['data'] = ['errors' => $e->getMessage()];
        }

        return $this->json($rs);
    }

}