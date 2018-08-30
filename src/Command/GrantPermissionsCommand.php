<?php

namespace App\Command;

use App\Entity\Message;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Acl\Domain\Acl;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Exception\AclNotFoundException;
use Symfony\Component\Security\Acl\Model\MutableAclProviderInterface;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\Security\Core\User\UserInterface;

class GrantPermissionsCommand extends Command
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
     * @var MutableAclProviderInterface
     */
    private $aclProvider;

    public function __construct(UserManagerInterface $userManager, RegistryInterface $doctrine, MutableAclProviderInterface $aclProvider)
    {
        parent::__construct('app:grant-permissions-on-message');
        $this->userManager = $userManager;
        $this->doctrine = $doctrine;
        $this->aclProvider = $aclProvider;
    }

    protected function configure()
    {
        $this->addArgument('username', InputArgument::REQUIRED);
        $this->addArgument('message-id', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $i, OutputInterface $o)
    {
        $username = $i->getArgument('username');
        $messageId = $i->getArgument('message-id');

        $user = $this->userManager->findUserByUsernameOrEmail($username);

        if (! $user) {
            $o->writeln("<error>Who the hell is {$username}?!</error>");
            return 1;
        }

        $repository = $this->doctrine->getRepository(Message::class);
        $message = $repository->find($messageId);

        if (! $message) {
            $o->writeln("<error>No message with ID {$username}.</error>");
            return 1;
        }

        $this->grantAllPermissionsOnMessageForUser($user, $message);

        $o->writeln("<info>{$username} has now been granted all permissions on message #{$messageId}.</info>");

        return 0;
    }

    private function grantAllPermissionsOnMessageForUser(UserInterface $user, Message $message)
    {
        $bitmask = $this->buildBossPermissionsMask();

        $objectIdentity       = ObjectIdentity::fromDomainObject($message);
        $userSecurityIdentity = UserSecurityIdentity::fromAccount($user);

        try {
            /** @var Acl $acl */
            $acl = $this->aclProvider->findAcl($objectIdentity);

        } catch (AclNotFoundException $e) {
            $acl = $this->aclProvider->createAcl($objectIdentity);
        }

        $acl->insertObjectAce($userSecurityIdentity, $bitmask);

        $this->aclProvider->updateAcl($acl);
    }

    private function buildBossPermissionsMask(): int
    {
        $builder = new MaskBuilder();

        $builder->add('view');
        $builder->add('edit');
        $builder->add('delete');
        $builder->add('undelete');

        return $builder->get();
    }
}
