<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        foreach ($this->getUserData() as [$firstname, $lastname, $username, $password, $email, $gender, $roles]) {
            $user = new User();
            $user->setFirstname($firstname);
            $user->setLastname($lastname);
            $user->setUsername($username);
            $user->setPassword($this->passwordEncoder->encodePassword($user, $password));
            $user->setEmail($email);
            $user->setGender($gender);
            $user->setRoles($roles);

            $manager->persist($user);
            $this->addReference($username, $user);
        }

        $manager->flush();
    }

    private function getUserData(): array
    {
        return [
            // $userData = [$firstname, $lastname, $username, $password, $email, $gender, $roles];
            ['Admin', 'Admin', 'admin', 'admin', 'admin@gmail.com', 1, ['ROLE_ADMIN']],
            ['Blogger', 'Blogger', 'blogger', 'blogger', 'blogger@gmail.com', 1, ['ROLE_BLOGGER']],
            ['User', 'User', 'user', 'user', 'user@gmail.com', 1, ['ROLE_USER']],
        ];
    }


}