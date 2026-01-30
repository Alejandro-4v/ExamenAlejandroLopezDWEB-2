<?php

namespace App\Controller;

use App\DTO\ActivityDTO;
use App\DTO\ErrorDTO;
use App\Entity\Activity;
use App\Repository\ActivityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

final class ActivitiesController extends AbstractController
{
    #[Route('/activities', name: 'findActivities', methods: ['GET'])]
    public function index(
        ActivityRepository $activityRepository,
        #[MapQueryParameter] ?bool $onlyfree,
        #[MapQueryParameter] ?string $type,
        #[MapQueryParameter] ?int $page,
        #[MapQueryParameter] ?int $page_size,
        #[MapQueryParameter] ?string $sort,
        #[MapQueryParameter] ?string $order
    ): JsonResponse {
        if ($sort === null) {
            return $this->json(new ErrorDTO(Response::HTTP_BAD_REQUEST, 'The sort parameter is mandatory'), Response::HTTP_BAD_REQUEST);
        }

        if (!in_array($sort, Activity::SORT_ENUM)) {
            return $this->json(new ErrorDTO(Response::HTTP_BAD_REQUEST, 'Invalid sort criteria'), Response::HTTP_BAD_REQUEST);
        }

        if ($type !== null && !in_array($type, Activity::TYPE_ENUM)) {
            return $this->json(new ErrorDTO(Response::HTTP_BAD_REQUEST, 'Invalid activity type'), Response::HTTP_BAD_REQUEST);
        }

        $activities = $activityRepository->findByFilters($onlyfree, $type, $page, $page_size, $sort, $order);
        $totalItems = $activityRepository->countByFilters($onlyfree, $type);

        $data = array_map(fn($activity) => ActivityDTO::fromEntity($activity), $activities);

        return $this->json([
            'data' => $data,
            'meta' => [
                'page' => $page,
                'limit' => $page_size,
                'total-items' => $totalItems,
            ],
        ]);
    }
}
