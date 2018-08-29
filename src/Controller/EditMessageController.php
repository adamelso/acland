<?php

namespace App\Controller;

use App\Entity\Message;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @Route("/message/{id}", name="edit")
 * @ParamConverter("message", class="App\Entity\Message")
 */
class EditMessageController extends Controller
{
    public function __invoke(Message $message)
    {
        /** @var AuthorizationCheckerInterface $authorizationChecker */
        $authorizationChecker = $this->get('security.authorization_checker');

        // check for edit access
        if (false === $authorizationChecker->isGranted('EDIT', $message)) {
            throw new AccessDeniedException();
        }

        return new Response('ok');
    }
}
