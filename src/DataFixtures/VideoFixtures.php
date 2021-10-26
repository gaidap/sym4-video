<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Video;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;
use RuntimeException;

class VideoFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        try {
            foreach ($this->videoData() as [$title, $videoId, $categoryId]) {
                $duration = random_int(10, 300);
                $category = $manager->getRepository(Category::class)->find($categoryId);
                $video = new Video();
                $video
                    ->setTitle($title)
                    ->setCategory($category)
                    ->setDuration($duration)
                    ->setPath("https://player.vimeo.com/video/${videoId}")
                ;
                $manager->persist($video);
            }
            $manager->flush();
        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @throws Exception
     */
    private function videoData(): array
    {
        return [
            ['Movie 1', 289729765, random_int(1, 4)],
            ['Movie 2', 238902809, random_int(1, 4)],
            ['Movie 3', 150870038, random_int(1, 4)],
            ['Movie 4', 219727723, random_int(1, 4)],
            ['Movie 5', 289879647, random_int(1, 4)],
            ['Movie 6', 261379936, random_int(1, 4)],
            ['Movie 7', 289029793, random_int(1, 4)],
        ];
    }
}
