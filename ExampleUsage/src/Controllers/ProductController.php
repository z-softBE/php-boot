<?php

namespace App\Controllers;

use App\Model\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManager;
use PhpBoot\Http\Common\HttpStatusCode;
use PhpBoot\Http\Routing\Attributes\Controller;
use PhpBoot\Http\Routing\Attributes\Path\GetPath;
use PhpBoot\Http\Routing\Attributes\Path\PathVariable;
use PhpBoot\Http\Routing\Attributes\Path\PostPath;
use PhpBoot\Http\Routing\Attributes\Response\ResponseBody;
use PhpBoot\Http\Routing\Attributes\Response\ResponseBodyType;
use PhpBoot\Http\Routing\Attributes\Response\ResponseStatus;

#[Controller]
class ProductController
{

    public function __construct(
        private EntityManager $entityManager,
        private ProductRepository $productRepository
    )
    {

    }

    #[PostPath(path: '/products/{name}')]
    #[ResponseBody(type: ResponseBodyType::JSON)]
    #[ResponseStatus(statusCode: HttpStatusCode::HTTP_CREATED)]
    public function createProduct(#[PathVariable] string $name): Product
    {
        $product = new Product();
        $product->setName($name);

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return $product;
    }

    #[GetPath(path: '/products/{name}')]
    #[ResponseBody(type: ResponseBodyType::JSON)]
    #[ResponseStatus(statusCode: HttpStatusCode::HTTP_OK)]
    public function getProduct(#[PathVariable] string $name): Product
    {
        return $this->productRepository->getProductByName($name);
    }
}