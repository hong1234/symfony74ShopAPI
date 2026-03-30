# rest API for Shop using symfony 7.4

## install

git clone https://github.com/hong1234/symfony74ShopAPI.git

cd symfony74ShopAPI

composer install

## database migration

// php bin/console make:entity // make entity

php bin/console make:migration

php bin/console doctrine:migrations:migrate

## run server

// php -S localhost:8000 -t public/

symfony server:start --no-tls

## service endpoints

// get cart of customer id = 2

GET http://localhost:8000/api/cart/2

// add or plus product id=13 to cart of customer id = 2

PUT http://localhost:8000/api/cart/2

{
"operation":"plus",
"productId": 13
}

// remove product Id=13 from cart of customer id = 2

PUT http://localhost:8000/api/cart/2

{
"operation":"minus",
"productId": 13
}

// add a new product to catalog

POST http://localhost:8000/api/products

{
"title": "VinFast Car",
"price": 9999.99,
"category": 1
}

// get product id = 13

GET http://localhost:8000/api/products/13

// get all products

GET http://localhost:8000/api/products

// search products by title "java"

GET http://localhost:8000/api/products/search?searchkey=java
