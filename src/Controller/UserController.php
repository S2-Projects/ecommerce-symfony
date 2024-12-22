<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\UserService;

/**
 * @Route("/api/v1", name="api_")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/register", name="register", methods={"POST"})
     */
    public function register(Request $request, UserService $userService): JsonResponse
    {
        $decoded = json_decode($request->getContent(), true);

        if (!isset($decoded['name'], $decoded['email'], $decoded['password'])) {
            return $this->json(['error' => 'Invalid data'], JsonResponse::HTTP_BAD_REQUEST);
        }

        try {
            $user = $userService->createUser($decoded['name'], $decoded['email'], $decoded['password']);
            return $this->json(['message' => 'Registered Successfully', 'user' => $user->getId()]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}