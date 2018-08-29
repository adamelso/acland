<?php

namespace App\Command;

use App\AccessControl\Acl\PermissionsAdministrationPoint;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GrantPermissionsOnAllMessagesCommand extends Command
{
    /**
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * @var RegistryInterface
     */
    private $doctrine;

    /**
     * @var PermissionsAdministrationPoint
     */
    private $permissionsAdministrationPoint;

    public function __construct(UserManagerInterface $userManager, RegistryInterface $doctrine, PermissionsAdministrationPoint $permissionsAdministration)
    {
        parent::__construct('app:grant-permissions-on-everything');
        $this->userManager = $userManager;
        $this->doctrine = $doctrine;
        $this->permissionsAdministrationPoint = $permissionsAdministration;
    }

    protected function configure()
    {
        $this->addArgument('username', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $i, OutputInterface $o)
    {
        $username = $i->getArgument('username');

        $user = $this->userManager->findUserByUsernameOrEmail($username);

        if (! $user) {
            $o->writeln("<error>Who the hell is {$username}?!</error>");
            return 1;
        }

        $this->permissionsAdministrationPoint->grantAllOnEveryMessageTo($user);

        $o->writeln("<info>{$username} has now been granted all permissions to every message.</info>");

        return 0;
    }
}
