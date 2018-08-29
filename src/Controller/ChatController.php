<?php

namespace App\Controller;

use App\Entity\Message;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Model\MutableAclProviderInterface;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

/**
 * @Route("/", name="chat")
 */
class ChatController extends Controller
{
    public function __invoke(Request $request)
    {
        $em = $this->getDoctrine()
            ->getManagerForClass(Message::class);

        $message = new Message();

        $form = $this->createFormBuilder($message)
            ->add('content', TextareaType::class, [
                'label' => 'Write your message'
            ])
            ->add('submit', SubmitType::class)
            ->getForm();

        if ($request->isMethod('POST')) {
            $message->setAuthor($this->getUser());
            $message->setCreatedAt(new \DateTime());

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->postMessage($message);
            }
        }

        $repository = $em->getRepository(Message::class);

        return $this->render('chat.html.twig', [
            'messages'  => $repository->findAll(),
            'chatbox'   => $form->createView(),
        ]);
    }

    private function postMessage(Message $message)
    {
        $em = $this->getDoctrine()->getManagerForClass(Message::class);

        $em->persist($message);
        $em->flush();

        // creating the ACL

        /** @var MutableAclProviderInterface $aclProvider */
        $aclProvider    = $this->get('security.acl.provider');
        $objectIdentity = ObjectIdentity::fromDomainObject($message);
        $acl            = $aclProvider->createAcl($objectIdentity);

        // retrieving the security identity of the currently logged-in user

        $tokenStorage     = $this->get('security.token_storage');
        $user             = $tokenStorage->getToken()->getUser();
        $securityIdentity = UserSecurityIdentity::fromAccount($user);

        // grant owner access

        $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
        $aclProvider->updateAcl($acl);
    }
}
