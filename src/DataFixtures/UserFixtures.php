<?php

namespace App\DataFixtures;

use App\Entity\ApiToken;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends BaseFixtures
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function loadData(ObjectManager $manager)
    {
        $this->createAdminUser('admin@symfony.skillbox', '123456');
        $this->createApiUser('api@symfony.skillbox', '123456');

        $this->createMany(User::class, 10, function (User $user) {
            $user->setFirstName($this->faker->firstName)
                ->setIsActive($this->faker->boolean(30))
                ->setEmail($this->faker->email)
                ->setPassword($this->passwordEncoder->encodePassword($user, '123456'));

            $this->manager->persist(new ApiToken($user));
        });
    }

    protected function createAdminUser(string $email, string $password, string $name = 'Администратор')
    {
        $this->create(User::class, function (User $user) use ($email, $password, $name) {
            $user
                ->setEmail($email)
                ->setFirstName($name)
                ->setPassword($this->passwordEncoder->encodePassword($user, $password))
                ->setRoles(['ROLE_ADMIN']);

            $this->manager->persist(new ApiToken($user));
        });
    }

    protected function createApiUser(string $email, string $password, string $name = 'API пользователь')
    {
        $this->create(User::class, function (User $user) use ($email, $password, $name) {
            $user
                ->setEmail($email)
                ->setFirstName($name)
                ->setPassword($this->passwordEncoder->encodePassword($user, $password))
                ->setRoles(['ROLE_API']);

            $this->manager->persist(new ApiToken($user));
            $this->manager->persist(new ApiToken($user));
            $this->manager->persist(new ApiToken($user));
        });
    }
}
