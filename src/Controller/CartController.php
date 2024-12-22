<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Product;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/v1", name="api_")
 */
class CartController extends AbstractController
{
    private $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * @Route("/cart", name="add_to_cart", methods={"POST"})
     */
    public function add(Request $request): JsonResponse
    {
        $decoded = json_decode($request->getContent());

        // Get user and product
        $user = $this->getDoctrine()->getRepository(User::class)->find($decoded->user_id);
        $product = $this->getDoctrine()->getRepository(Product::class)->find($decoded->product_id);

        // Add product to cart
        if (!$user || !$product) {
            return $this->json(['error' => 'Invalid user or product ID'], 400);
        }

        $cart = $this->cartService->addProductToCart($user, $product);

        return $this->json([
            'name' => $product->getName(),
            'price' => $product->getPrice(),
            'cart_id' => $cart->getId(),
            'user' => $user->getName(),
        ]);
    }

    /**
     * @Route("/cart/{id}", name="cart_user", methods={"GET"})
     */
    public function index(int $id): JsonResponse
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);

        if (!$user) {
            return $this->json(['error' => 'User not found'], 404);
        }

        $cartItems = $this->cartService->getUserCart($user);
        if (empty($cartItems)) {
            return $this->json(['message' => 'Cart is empty'], 202);
        }

        $data = [];
        foreach ($cartItems as $cart) {
            foreach ($cart->getProducts() as $product) {
                $data[] = [
                    'name' => $product->getName(),
                    'price' => $product->getPrice(),
                ];
            }
        }

        return $this->json($data);
    }

    /**
     * @Route("/cart", name="delete_from_cart", methods={"DELETE"})
     */
    public function delete(Request $request): JsonResponse
    {
        $decoded = json_decode($request->getContent());

        $user = $this->getDoctrine()->getRepository(User::class)->find($decoded->user_id);
        $product = $this->getDoctrine()->getRepository(Product::class)->find($decoded->product_id);

        if (!$user || !$product) {
            return $this->json(['error' => 'Invalid user or product ID'], 400);
        }

        $cart = $this->cartService->deleteProductFromCart($user, $product);

        if ($cart) {
            return $this->json(['message' => 'Product removed from cart']);
        }

        return $this->json(['error' => 'Product not found in cart'], 404);
    }
}
