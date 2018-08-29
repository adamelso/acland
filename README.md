Acland Chat Room App
====================

Demonstration app for implementing the Symfony ACL Bundle.

Quick Start
------------

You need at least PHP 7.1.3 and Composer.
If you copy the `.env` as is, you'll need SQLite3 too.

To install, run:


    $ composer install
    $ cp .env.dist .env
    $ bin/console doctrine:database:create
    $ bin/console doctrine:schema:create

then maybe also:

    $ bin/console acl:init

Finally, start the server:

    $ bin/console server:run


Done. You can now open http://127.0.0.1:8000 in your browser.
