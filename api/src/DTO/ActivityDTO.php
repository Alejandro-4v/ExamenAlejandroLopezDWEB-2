<?php

namespace App\DTO;

class ActivityDTO implements \JsonSerializable
{
    public function __construct(
        public int $id,
        public int $max_participants,
        public int $clients_signed,
        public string $type,
        public array $play_list,
        public string $date_start,
        public string $date_end,
    ) {
    }

    public static function fromEntity(\App\Entity\Activity $activity): self
    {
        $songs = [];
        if ($activity->getPlayList()) {
            foreach ($activity->getPlayList()->getSongs() as $song) {
                $songs[] = [
                    'id' => $song->getId(),
                    'name' => $song->getName(),
                    'duration_seconds' => $song->getDurationSeconds(),
                ];
            }
        }

        return new self(
            $activity->getId(),
            $activity->getMaxParticipants(),
            $activity->getClientsSigned(),
            $activity->getType(),
            $songs,
            $activity->getDateStart()->format(\DateTimeInterface::RFC3339),
            $activity->getDateEnd()->format(\DateTimeInterface::RFC3339),
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'max_participants' => $this->max_participants,
            'clients_signed' => $this->clients_signed,
            'type' => $this->type,
            'play_list' => $this->play_list,
            'date_start' => $this->date_start,
            'date_end' => $this->date_end,
        ];
    }
}
