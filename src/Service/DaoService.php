<?php
namespace App\Service;

use App\Dao\ProductDao;
use App\Dao\CustomerDao;

class DaoService {

    private $prodDao;
    private $customerDao;
    
    function __construct(ProductDao $prodDao, CustomerDao $customerDao) {
        $this->prodDao = $prodDao;
        $this->customerDao = $customerDao;
    }

    public function searchProducts(string $key) : array
    {
        $result_array = $this->prodDao->searchProducts($key);
        return $result_array;    
    }

    public function allProducts() : array
    {
        $result_array = $this->prodDao->allProducts();
        return $result_array;    
    }

    public function allCustomers() : array
    {
        $result_array = $this->customerDao->allCustomers();
        return $result_array;    
    }
}