<?php

namespace App\AccessControl\Acl;

use App\Entity\Message;
use Symfony\Component\Security\Acl\Domain\Acl;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Exception\AclNotFoundException;
use Symfony\Component\Security\Acl\Model\MutableAclProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class PermissionsAdministrationPoint
{
    /**
     * @var PermissionsBuilder
     */
    private $permissionsBuilder;

    /**
     * @var MutableAclProviderInterface
     */
    private $aclProvider;

    public function __construct(PermissionsBuilder $permissionsBuilder, MutableAclProviderInterface $aclProvider)
    {
        $this->permissionsBuilder = $permissionsBuilder;
        $this->aclProvider = $aclProvider;
    }

    public function grantAllOnEveryMessageTo(UserInterface $user)
    {
        $bitmask = $this->permissionsBuilder->grantAll();

        // Notice we use 'class', and not an entity ID.
        $objectIdentity       = new ObjectIdentity('class', Message::class);
        $userSecurityIdentity = UserSecurityIdentity::fromAccount($user);

        try {
            /** @var Acl $acl */
            $acl = $this->aclProvider->findAcl($objectIdentity);

        } catch (AclNotFoundException $e) {
            $acl = $this->aclProvider->createAcl($objectIdentity);
        }

        // Notice we use 'insertClassAce', instead of 'insertObjectAce'.
        $acl->insertClassAce($userSecurityIdentity, $bitmask);

        $this->aclProvider->updateAcl($acl);
    }
}
