<?php

namespace App\DTO;

class BookingDTO implements \JsonSerializable
{
    public function __construct(
        public int $id,
        public ActivityDTO $activity,
        public int $client_id,
    ) {
    }

    public static function fromEntity(\App\Entity\Booking $booking): self
    {
        return new self(
            $booking->getId(),
            ActivityDTO::fromEntity($booking->getActivity()),
            $booking->getClientId()->getId()
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'activity' => $this->activity,
            'client_id' => $this->client_id,
        ];
    }
}
