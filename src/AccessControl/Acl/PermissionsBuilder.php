<?php

namespace App\AccessControl\Acl;

interface PermissionsBuilder
{
    public function grantAll(): int;
}
