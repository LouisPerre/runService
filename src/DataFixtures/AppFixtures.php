<?php

namespace App\DataFixtures;

use App\Entity\Projects;
use App\Entity\Tasks;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $creator = new User();
        $creator
            ->setEmail('test@test.com')
            ->setRoles(['ROLE_ADMIN']);
        $password = $this->hasher->hashPassword($creator, 'test');
        $creator->setPassword($password);
        $manager->persist($creator);
        $faker = Faker\Factory::create('fr_FR');
        $allUser = [];
        for ($i = 0; $i < 25; $i++) {
            $user = new User();
            $user->setEmail($faker->email);
            $user->setRoles([$faker->randomElement(['ROLE_ADMIN', 'ROLE_MODERATOR'])]);
            $password = $this->hasher->hashPassword($user, $faker->name);
            $user->setPassword($password);
            $allUser[] = $user;
            $manager->persist($user);
        }

        $allProject = [];
        for ($k = 0; $k < 200; $k++) {
            $project = new Projects();
            $project
                ->setTitle($faker->title)
                ->setDescription($faker->paragraph)
                ->setCreatedAt($faker->dateTime)
                ->setEndAt($faker->dateTime)
                ->setCreator($creator)
                ->addCollaborator($faker->randomElement($allUser));
            $allProject[] = $project;
            $manager->persist($project);
        }

        for ($j = 0; $j < 500; $j++) {
            $task = new Tasks();
            $task
                ->setTitle($faker->title)
                ->setDescription($faker->paragraph)
                ->setPriority($faker->randomElement(['HIGH', 'NORMAL', 'LOW']))
                ->setStatus($faker->randomElement(['OPEN', 'IN PROGRESS', 'FINISH']))
                ->setEndAt($faker->dateTime)
                ->setProjects($faker->randomElement($allProject));
            $manager->persist($task);
        }

        $manager->flush();
    }
}
