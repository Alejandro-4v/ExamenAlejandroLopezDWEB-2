<?php

namespace App\Controller;

use App\DTO\BookingDTO;
use App\DTO\ErrorDTO;
use App\Entity\Booking;
use App\Repository\ActivityRepository;
use App\Repository\ClientRepository;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Client;

final class BookingsController extends AbstractController
{
    #[Route('/bookings', name: 'bookActivityByUser', methods: ['POST'])]
    public function store(
        Request $request,
        ActivityRepository $activityRepository,
        ClientRepository $clientRepository,
        BookingRepository $bookingRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['activity_id']) || !isset($data['client_id'])) {
            return $this->json(new ErrorDTO(Response::HTTP_BAD_REQUEST, 'activity_id and client_id are required'), Response::HTTP_BAD_REQUEST);
        }

        $activity = $activityRepository->find($data['activity_id']);
        $client = $clientRepository->find($data['client_id']);

        if (!$activity || !$client) {
            return $this->json(new ErrorDTO(Response::HTTP_BAD_REQUEST, 'Activity or Client not found'), Response::HTTP_BAD_REQUEST);
        }

        $existingBooking = $bookingRepository->findOneBy([
            'activity' => $activity,
            'client_id' => $client
        ]);
        if ($existingBooking) {
            return $this->json(new ErrorDTO(Response::HTTP_BAD_REQUEST, 'Client already booked this activity'), Response::HTTP_BAD_REQUEST);
        }

        if ($activity->getClientsSigned() >= $activity->getMaxParticipants()) {
            return $this->json(new ErrorDTO(Response::HTTP_BAD_REQUEST, 'Activity is full'), Response::HTTP_BAD_REQUEST);
        }

        if ($client->getType() === Client::TYPE_STANDARD) {
            $weeklyBookings = $bookingRepository->countBookingsInWeek($client);
            if ($weeklyBookings >= Client::WEEKLY_LIMIT_STANDARD) {
                return $this->json(new ErrorDTO(Response::HTTP_BAD_REQUEST, 'Standard users cannot book more than ' . Client::WEEKLY_LIMIT_STANDARD . ' activities per week'), Response::HTTP_BAD_REQUEST);
            }
        }

        $booking = new Booking();
        $booking->setActivity($activity);
        $booking->setClientId($client);

        $entityManager->persist($booking);
        $entityManager->flush();

        return $this->json(BookingDTO::fromEntity($booking));
    }
}
