<?php

namespace App\Controller;

use App\DTO\ClientDTO;
use App\DTO\ErrorDTO;
use App\Repository\ClientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

final class ClientsController extends AbstractController
{
    #[Route('/clients/{id}', name: 'userInformation', methods: ['GET'])]
    public function show(
        int $id,
        ClientRepository $clientRepository,
        #[MapQueryParameter('with_statistics')] ?bool $with_statistics,
        #[MapQueryParameter('with_bookings')] ?bool $with_bookings
    ): JsonResponse {
        $client = $clientRepository->find($id);

        if (!$client) {
            return $this->json(new ErrorDTO(Response::HTTP_BAD_REQUEST, 'Client not found'), Response::HTTP_BAD_REQUEST);
        }

        return $this->json(ClientDTO::fromEntity($client, $with_statistics ?? false, $with_bookings ?? false));
    }
}
