<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\User;
use App\Entity\Product;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api", name="api_")
 */

class CartController extends AbstractController
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine->getManager();
    }


    // Add item to cart
    /**
     * @Route("/cart", name="add_to_cart", methods={"POST"})
     */
    public function add(Request $request): JsonResponse
    {
        $decoded = json_decode($request->getContent());

        // Get user
        $user = $this->doctrine
            ->getRepository(User::class)
            ->find($decoded->user_id);

        // Create new cart object
        $cart = new Cart();

        // Set user to this cart item
        $cart->setUser($user);

        // Find product
        $product = $this->doctrine
            ->getRepository(Product::class)
            ->find($decoded->product_id);


        // Associate this product to this cart
        $cart->addProduct($product);

        // Persist and flush changes to the database
        $this->doctrine->persist($cart);
        $this->doctrine->flush();

        $data = [
            'name' => $product->getName(),
            'price' => $product->getPrice(),
            'cart_id' => $cart->getId(),
            'user' => $user->getName()
        ];

        // Return this data as JSON
        return $this->json($data);
    }

    // Get user cart
    /**
     * @Route("/cart/{id}", name="cart_user", methods={"GET"})
     */
    public function index(int $id): JsonResponse
    {
        // Get user
        $user = $this->doctrine
            ->getRepository(User::class)
            ->find($id);

        // Array to store data
        $data = [];

        // If user does not exist
        if (!$user) {
            return $this->json('No user found for id ' . $id, 404);
        }

        // If cart is empty
        if ($user->getCarts()->count() === 0) {
            return $this->json('Cart is empty for user with id = ' . $id, 202);
        }

        // Get all products
        $products = $this->doctrine
            ->getRepository(Product::class)
            ->findAll();

        // Add products to data if they have the same cart associated with the user
        foreach ($products as $product) {
            foreach ($product->getCarts() as $cart) {
                if ($cart->getUser() === $user) {
                    $data[] = [
                        "name" => $product->getName(),
                        "price" => $product->getPrice(),
                    ];
                }
            }
        }

        // Return this data as JSON
        return $this->json($data);
    }


     // Delete item from cart
    /**
     * @Route("/cart", name="delete_from_cart", methods={"DELETE"})
     */
    public function delete(Request $request): JsonResponse
    {
        $decoded = json_decode($request->getContent());

        // Find product by id
        $product = $this->doctrine->getRepository(Product::class)->find($decoded->product_id);

        // If product with id is not found
        if (!$product) {
            return $this->json('No Product found for id ' . $decoded->product_id, 404);
        }

        // If product is not associated with any cart
        if ($product->getCarts()->count() === 0) {
            return $this->json('This Product is not associated with any cart ' . $decoded->product_id, 405);
        }

        // If product is associated with another user's cart
        if ($product->getCarts()->first()->getUser()->getId() !== $decoded->user_id) {
            return $this->json('This Product was not found in the cart of user id ' . $decoded->user_id, 406);
        }

        // Get the cart associated with the product
        $cart = $product->getCarts()->first();

        // Remove the product from the cart
        $cart->removeProduct($product);

        // Delete the cart if it becomes empty
        if ($cart->getProducts()->count() === 0) {
            $this->doctrine->remove($cart);
        }

        // Flush changes to the database
        $this->doctrine->flush();

        return $this->json('Deleted a cart successfully with id ' . $cart->getId(), 200);
    }
}
