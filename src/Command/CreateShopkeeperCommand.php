<?php
namespace App\Command;

use App\Entity\Genre;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name: 'app:create-shopkeeper')]
class CreateShopkeeperCommand extends Command
{
    private $em;
    private $passwordHasher;

    public function __construct(EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();
        $this->em = $em;
        $this->passwordHasher = $passwordHasher;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => 'testshopkeeper@g.c']);
        $msg = new SymfonyStyle($input, $output);

        if (!$user) {
            $user = new User();
            $user->setEmail("testshopkeeper@g.c");
            $user->setPassword($this->passwordHasher->hashPassword($user, 'testshopkeeper'));
            $user->setFirstname("shopkeeper");
            $user->setLastname("shopkeeper");
            $user->setRoles(["ROLE_SHOPKEEPER"]);
            $user->setCellphone(1234567890);
            $user->setAddress('xyz');
            $this->em->persist($user);

            $genre=new Genre();
            $genre->setName('Default');
            $this->em->persist($genre);
            $this->em->flush();
            $msg->success("Shopkeeper created successfully, now login using email:testshopkeeper@g.c and password:testshopkeeper then create some books for your shop");
        } else {
            $msg->success("Shopkeeper created successfully, now login using email:testshopkeeper@g.c and password:testshopkeeper then create some books for your shop");
        }

        return Command::SUCCESS;
    }
}
