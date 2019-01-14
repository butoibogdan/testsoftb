<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends Controller
{
    /**
     * @Route("/login", name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     * @throws \LogicException
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        if ($this->getUser() !== null) {
            return new Response('User login successfully');
        }

        return $this->render('@App/login/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/createuser", name="user_create")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \LogicException
     * @throws \Exception
     */
    public function userCreate(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        if ($request->isMethod('POST')) {
            $user = new User();
            $password = $passwordEncoder->encodePassword($user, $request->request->get('password'));
            $user->setApiToken(rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '='))
                ->setPassword($password)
                ->setName($request->request->get('name'))
                ->setCnp($request->request->get('cnp'))
                ->setEmail($request->request->get('email'));

            $entityManager = $this->getDoctrine()->getManager();

            try {
                $entityManager->persist($user);
                $entityManager->flush();
                $output = 'User create successfully';
            } catch (\Exception $exception) {
                $output = 'User not created: '. $exception->getMessage();
            }
            if ($request->isXmlHttpRequest()) {
                return $this->json($output);
            }
            return $this->redirectToRoute('app_login');
        }

        return $this->render(
            '@App/register/register.html.twig'
        );
    }
}
