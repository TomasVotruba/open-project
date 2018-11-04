<?php declare(strict_types=1);

namespace OpenTraining\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\ControllerTrait;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class SecurityController
{
    use ControllerTrait;

    /**
     * @var AuthenticationUtils
     */
    private $authenticationUtils;

    public function __construct(AuthenticationUtils $authenticationUtils)
    {
        $this->authenticationUtils = $authenticationUtils;
    }

    /**
     * @Route("/login/", name="security_login")
     */
    public function loginAction(): Response
    {
        return $this->render('security/login.html.twig', [
            // last username entered by the user
            'last_username' => $this->authenticationUtils->getLastUsername(),
            // last user if there was some
            'error' => $this->authenticationUtils->getLastAuthenticationError(),
        ]);
    }
}
