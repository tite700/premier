<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use DateTime;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        for($i=0;$i<3;$i++){
            $category = new Category();
            $category ->setTitle($faker->sentence())
                      ->setDescription($faker->paragraph(3));

            $manager->persist($category);

            for($j=1;$j<=mt_rand(4,6);$j++) {

                $article = new Article();

                $content = '<p>' . join('</p><p>',$faker->paragraphs(1) ) . '</p>'; // voir pq pas 2 paragraphs ?

                $article->setTitle($faker->sentence())
                    ->setContent($content)
                    ->setImage($faker->imageUrl())
                    ->setCreatedAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-6 months')))
                    ->setCategory($category);

                $manager->persist($article);

                for($k=1;$k<=mt_rand(4,10);$k++){

                    $now = new \DateTime();
                    $interval = (DateTime::createFromImmutable($article->getCreatedAt()))->diff($now);
                    $days = $interval->days;
                    $minimum = '-'.$days.' days';

                    $comment = new Comment();
                    $comment->setAuthor($faker->name)
                            ->setText($content)
                            ->setCreatedAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween($minimum)))
                            ->setArticle($article);
                    $manager->persist($comment);

                }


            }

        }
        $manager->flush();
    }
}
