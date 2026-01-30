<?php

namespace App\Entity;

use App\Repository\ActivityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ActivityRepository::class)]
class Activity
{
    public const TYPE_BODYPUMP = 'BodyPump';
    public const TYPE_SPINNING = 'Spinning';
    public const TYPE_CORE = 'Core';

    public const TYPE_ENUM = [self::TYPE_BODYPUMP, self::TYPE_SPINNING, self::TYPE_CORE];
    public const SORT_ENUM = ['date'];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $max_participants = null;

    #[ORM\OneToMany(targetEntity: Booking::class, mappedBy: 'activity', cascade: ['persist', 'remove'])]
    private Collection $bookings;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Playlist $play_list = null;

    #[ORM\Column]
    private ?\DateTime $date_start = null;

    #[ORM\Column]
    private ?\DateTime $date_end = null;

    public function __construct()
    {
        $this->bookings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMaxParticipants(): ?int
    {
        return $this->max_participants;
    }

    public function setMaxParticipants(int $max_participants): static
    {
        $this->max_participants = $max_participants;

        return $this;
    }

    public function getClientsSigned(): int
    {
        return $this->bookings->count();
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getPlayList(): ?Playlist
    {
        return $this->play_list;
    }

    public function setPlayList(?Playlist $play_list): static
    {
        $this->play_list = $play_list;

        return $this;
    }

    public function getDateStart(): ?\DateTime
    {
        return $this->date_start;
    }

    public function setDateStart(\DateTime $date_start): static
    {
        $this->date_start = $date_start;

        return $this;
    }

    public function getDateEnd(): ?\DateTime
    {
        return $this->date_end;
    }

    public function setDateEnd(\DateTime $date_end): static
    {
        $this->date_end = $date_end;

        return $this;
    }
}
