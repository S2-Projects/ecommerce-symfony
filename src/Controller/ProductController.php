<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Category;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/api", name="api_")
 */
class ProductController extends AbstractController
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @Route("/product", name="product_index", methods={"GET"})
     */
    public function index(): JsonResponse
    {
        $products = $this->doctrine->getRepository(Product::class)->findAll();

        $data = [];

        foreach ($products as $product) {
            $data[] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'price' => $product->getPrice(),
                'description' => $product->getDescription(),
                'image' => $product->getImage()
            ];
        }

        return $this->json($data);
    }

    /**
     * @Route("/product", name="product_new", methods={"POST"})
     */
    public function new(Request $request): JsonResponse
    {
        $entityManager = $this->doctrine->getManager();
        $data = json_decode($request->getContent());

        $product = new Product();
        $product->setName($data->name);
        $product->setPrice($data->price);
        $product->setDescription($data->description);
        $product->setImage($data->image);
        $category = $entityManager->getRepository(Category::class)->find($data->category_id);
        if (!$category) {
            return $this->json('Category not found', 404);
        }
        $product->setCategory($category);


        $user = $entityManager->getRepository(User::class)->find($data->user_id);
        if (!$user) {
            return $this->json('User not found', 404);
        }
        $product->setUser($user);



        $entityManager->persist($product);
        $entityManager->flush();

        return $this->json('Created new product successfully with id ' . $product->getId(), 201);
    }

    /**
     * @Route("/product/{id}", name="product_edit", methods={"PUT"})
     */
    public function edit(Request $request, int $id): JsonResponse
    {
        $entityManager = $this->doctrine->getManager();
        $product = $entityManager->getRepository(Product::class)->find($id);

        if (!$product) {
            return $this->json('No product found for id ' . $id, 404);
        }

        $data = json_decode($request->getContent());
        $product->setName($data->name);
        $product->setPrice($data->price);
        $product->setDescription($data->description);
        $product->setImage($data->image);

        $entityManager->flush();

        $updatedData = [
            'id' => $product->getId(),
            'name' => $product->getName(),
            'price' => $product->getPrice(),
            'description' => $product->getDescription(),
            'image' => $product->getImage()
        ];

        return $this->json($updatedData, 201);
    }

    /**
     * @Route("/product/{id}", name="product_delete", methods={"DELETE"})
     */
    public function delete(int $id): JsonResponse
    {
        $entityManager = $this->doctrine->getManager();

        // Find the product by id
        $product = $entityManager->getRepository(Product::class)->find($id);

        // If the product is not found
        if (!$product) {
            return $this->json('No product found for id ' . $id, 404);
        }

        // Remove the product from the entity manager and the database
        $entityManager->remove($product);
        $entityManager->flush();

        return $this->json('Deleted product with id ' . $id, 200);
    }
    /**
     * @Route("/product/{id}", name="product_show", methods={"GET"})
     */
    public function show(int $id): JsonResponse
    {
        $product = $this->doctrine->getRepository(Product::class)->find($id);

        if (!$product) {
            return $this->json('No product found for id ' . $id, 404);
        }
        if ($product->getUser()) {
            $user_id = $product->getUser()->getId();
        }

        if ($product->getCategory()) {
            $category_id = $product->getCategory()->getId();
        }

        $data = [
            'id' => $product->getId(),
            'name' => $product->getName(),
            'price' => $product->getPrice(),
            'description' => $product->getDescription(),
            'image' => $product->getImage(),
            'user_id' => $user_id,
            'category_id' => $category_id
        ];

        return $this->json($data);
    }
}
/* POUR Tester post (new)
{
    "id": 2,
    "name": "TELELE",
    "price": 1237,
    "description": "TELE",
    "image": "https://cdn.pixabay.com/photo/2016/11/04/21/34/beach-1799006__480.jpg",
    "category_id":2,
    "user_id":1
 }
*/
