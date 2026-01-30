<?php

namespace App\DataFixtures;

use App\Entity\Activity;
use App\Entity\Client;
use App\Entity\Playlist;
use App\Entity\Song;
use App\Entity\Booking;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $artistData = [
            'Camilo Sesto' => ['Vivir así es morir de amor', 'Algo de mí', 'Perdóname'],
            'Nino Bravo' => ['Libre', 'Un beso y una flor', 'Noelia'],
            'Jose Luis Perales' => ['¿Y cómo es él?', 'Que canten los niños', 'Un velero llamado Libertad'],
            'Calvin Harris' => ['Summer', 'Feel So Close', 'One Kiss'],
            'Oliver Heldens' => ['Gecko (Overdrive)', 'Koala', 'Turn Me On'],
            'Robin Schulz' => ['Sugar', 'Waves', 'Prayer in C'],
        ];

        $clients = [];
        $i = 0;
        foreach ($artistData as $name => $songs) {
            $client = new Client();
            $client->setName($name);
            $client->setEmail(strtolower(str_replace(' ', '_', $name)) . '@cuatrovientos.org');
            $client->setType($i < 3 ? 'premium' : 'standard');
            $manager->persist($client);
            $clients[] = $client;
            $i++;
        }

        $activities = [];
        $activityTypes = Activity::TYPE_ENUM;
        $artistNames = array_keys($artistData);

        for ($i = 0; $i < 30; $i++) {
            $artistIndex = $i % count($artistNames);

            if ($i >= 18) {
                $artistIndex = ($i % 2 == 0) ? 1 : 5;
            }

            $artistName = $artistNames[$artistIndex];
            $artistSongs = $artistData[$artistName];

            $playlist = new Playlist();
            $playlist->setName("Session $i - $artistName");
            $manager->persist($playlist);

            foreach ($artistSongs as $songName) {
                $song = new Song();
                $song->setName($songName);
                $song->setDurationSeconds(rand(180, 300));
                $song->setPlaylist($playlist);
                $manager->persist($song);
            }

            $activity = new Activity();
            $activity->setType($activityTypes[array_rand($activityTypes)]);
            $activity->setMaxParticipants(rand(15, 40));

            $daysOffset = rand(0, 7);
            $hoursOffset = rand(8, 20);
            $startDate = (new \DateTime())->modify("+$daysOffset days")->setTime($hoursOffset, 0);
            $endDate = (clone $startDate)->modify("+1 hour");

            $activity->setDateStart($startDate);
            $activity->setDateEnd($endDate);
            $activity->setPlayList($playlist);
            $manager->persist($activity);
            $activities[] = $activity;
        }

        foreach ($clients as $client) {
            $numBookings = rand(5, 10);
            $chosenActivities = (array) array_rand($activities, $numBookings);

            foreach ($chosenActivities as $activityIndex) {
                $activity = $activities[$activityIndex];

                if ($activity->getClientsSigned() < $activity->getMaxParticipants()) {
                    $booking = new Booking();
                    $booking->setActivity($activity);
                    $booking->setClientId($client);
                    $manager->persist($booking);
                }
            }
        }

        $manager->flush();
    }
}
