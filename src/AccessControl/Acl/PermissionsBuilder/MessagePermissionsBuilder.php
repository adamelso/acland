<?php

namespace App\AccessControl\Acl\PermissionsBuilder;

use App\AccessControl\Acl\PermissionsBuilder;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

class MessagePermissionsBuilder implements PermissionsBuilder
{
    public function grantAll(): int
    {
        $builder = new MaskBuilder();

        $builder->add('view');
        $builder->add('edit');
        $builder->add('delete');
        $builder->add('undelete');

        return $builder->get();
    }
}
