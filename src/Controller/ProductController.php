<?php

namespace App\Controller;

use App\Service\ProductService;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

/**
 * @Route("/api/v1", name="api_")
 */
class ProductController extends AbstractController
{
    private $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * @Route("/product", name="product_index", methods={"GET"})
     */
    public function index(): JsonResponse
    {
        $products = $this->productService->getAllProducts();

        $data = [];
        foreach ($products as $product) {
            $data[] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'price' => $product->getPrice(),
                'description' => $product->getDescription(),
                'image' => $product->getImage(),
                'category' => $product->getCategory()->getName(),
                'user' => $product->getUser()->getName()
            ];
        }

        return $this->json($data);
    }

    /**
     * @Route("/product", name="product_new", methods={"POST"})
     */
    public function new(Request $request): JsonResponse
    {
        $uploadedFile = $request->files->get('image');
        if (!$uploadedFile) {
            throw new BadRequestException('"file" is required');
        }

        $categoryId = $request->get('category_id');
        $userId = $request->get('user_id');
        $name = $request->get('name');
        $price = $request->get('price');
        $description = $request->get('description');

        try {
            $product = $this->productService->createProduct($name, $price, $description, $categoryId, $userId, $uploadedFile);
            return $this->json('Created new product successfully with id ' . $product->getId(), 201);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * @Route("/product/{id}", name="product_edit", methods={"PUT"})
     */
    public function edit(Request $request, int $id): JsonResponse
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);

        if (!$product) {
            return $this->json('No product found for id ' . $id, 404);
        }

        $data = json_decode($request->getContent(), true);

        $this->productService->updateProduct($product, $data['name'], $data['price'], $data['description']);

        return $this->json([
            'id' => $product->getId(),
            'name' => $product->getName(),
            'price' => $product->getPrice(),
            'description' => $product->getDescription(),
            'image' => $product->getImage(),
            'category' => $product->getCategory()->getName(),
            'user' => $product->getUser()->getName()
        ], 201);
    }

    /**
     * @Route("/product/{id}", name="product_delete", methods={"DELETE"})
     */
    public function delete(int $id): JsonResponse
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);

        if (!$product) {
            return $this->json('No product found for id ' . $id, 404);
        }

        $this->productService->deleteProduct($product);

        return $this->json('Deleted product with id ' . $id, 200);
    }

    /**
     * @Route("/product/{id}", name="product_show", methods={"GET"})
     */
    public function show(int $id): JsonResponse
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);

        if (!$product) {
            return $this->json('No product found for id ' . $id, 404);
        }

        $data = [
            'id' => $product->getId(),
            'name' => $product->getName(),
            'price' => $product->getPrice(),
            'description' => $product->getDescription(),
            'image' => $product->getImage(),
            'category' => $product->getCategory()->getName(),
            'user' => $product->getUser()->getName()
        ];

        return $this->json($data);
    }
}
