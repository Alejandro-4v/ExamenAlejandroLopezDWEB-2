<?php

namespace App\DTO;

class ClientDTO implements \JsonSerializable
{
    public function __construct(
        public int $id,
        public string $type,
        public string $name,
        public string $email,
        public array $activities_booked = [],
        public array $activity_statistics = [],
    ) {
    }

    public static function fromEntity(\App\Entity\Client $client, bool $withStatistics = false, bool $withBookings = false): self
    {
        $activitiesBooked = [];
        $activityStatistics = [];

        if ($withBookings || $withStatistics) {
            $statsMap = [];
            foreach ($client->getBookings() as $booking) {
                $activity = $booking->getActivity();
                if ($withBookings) {
                    $activitiesBooked[] = ActivityDTO::fromEntity($activity);
                }
                if ($withStatistics) {
                    $type = $activity->getType();
                    $statsMap[$type] = ($statsMap[$type] ?? 0) + 1;
                }
            }

            if ($withStatistics) {
                foreach ($statsMap as $type => $count) {
                    $activityStatistics[] = [
                        'type' => $type,
                        'count' => $count,
                    ];
                }
            }
        }

        return new self(
            $client->getId(),
            $client->getType(),
            $client->getName(),
            $client->getEmail(),
            $activitiesBooked,
            $activityStatistics
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'name' => $this->name,
            'email' => $this->email,
            'activities_booked' => $this->activities_booked,
            'activity_statistics' => $this->activity_statistics,
        ];
    }
}
