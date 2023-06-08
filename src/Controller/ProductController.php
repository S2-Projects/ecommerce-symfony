<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Category;
use App\Entity\User;
use App\Service\FileUploader;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/api", name="api_")
 */
class ProductController extends AbstractController
{
    private $doctrine;
    private $fileUploader;

    public function __construct(ManagerRegistry $doctrine, FileUploader $fileUploader)
    {
        $this->doctrine = $doctrine;
        $this->fileUploader = $fileUploader;
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
        $entityManager = $this->doctrine->getManager();
        $uploadedFile = $request->files->get('image');

        $product = new Product();

        $category = $entityManager->getRepository(Category::class)->find($request->get('category_id'));
        if (!$category) {
            return $this->json('Category not found', 404);
        }
        $product->setCategory($category);

        $user = $entityManager->getRepository(User::class)->find($request->get('user_id'));
        if (!$user) {
            return $this->json('User not found', 404);
        }
        $product->setUser($user);
        
        
        if (!$uploadedFile) {
            throw new BadRequestException('"file" is required');
        }

        $product->setName($request->get('name'));
        $product->setPrice(doubleval($request->get('price')));
        $product->setDescription($request->get('description'));
        $product->setImage($this->fileUploader->upload($uploadedFile));

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

        $entityManager->flush();

        $updatedData = [
            'id' => $product->getId(),
            'name' => $product->getName(),
            'price' => $product->getPrice(),
            'description' => $product->getDescription(),
            'image' => $product->getImage(),
            'category' => $product->getCategory()->getName(),
            'user' => $product->getUser()->getName()
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


        $data = [
            'id' => $product->getId(),
            'name' => $product->getName(),
            'price' => $product->getPrice(),
            'description' => $product->getDescription(),
            'image' => $product->getImage(),
            'user' => $product->getUser()->getName(),
            'category_id' => $product->getCategory()->getName()
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
