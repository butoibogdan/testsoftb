<?php

namespace AppBundle\Security;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;


class ApiAuthentification extends AbstractGuardAuthenticator
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @param EntityManager $em
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(EntityManager $em,
                                UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function getCredentials(Request $request):array
    {
        return [
            'email' => $request->request->get('email'),
            'password' => $request->request->get('password'),
            'token' => $request->headers->get('X-AUTH-TOKEN'),
        ];
    }

    /**
     * @param mixed $credentials
     * @param UserProviderInterface $userProvider
     * @return User|null
     */
    public function getUser($credentials, UserProviderInterface $userProvider):?User
    {
        $array = [];

        if ($credentials['token'] !== null) {
            $array['apiToken'] = $credentials['token'];
        }

        if ($credentials['email'] !== null && $credentials['password'] !== null) {
            $array['email'] = $credentials['email'];
        }

        return $this->em->getRepository(User::class)
            ->findOneBy($array);
    }

    /**
     * @param mixed $credentials
     * @param UserInterface $user
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user):bool
    {
        if ($credentials['token'] !== null) {
            return true;
        }

        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        if (\count(array_intersect(['email', 'password'], array_keys($request->request->all()))) > 0) {
            $user = $token->getUser()->getApiToken();
            return new JsonResponse($user, Response::HTTP_OK);
        }

        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = [
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_FORBIDDEN);
    }

    /**
     * @param Request $request
     * @param AuthenticationException|null $authException
     * @return JsonResponse
     */
    public function start(Request $request, AuthenticationException $authException = null):JsonResponse
    {
        $data = [
            'message' => 'Authentication Required'
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @return bool
     */
    public function supportsRememberMe():bool
    {
        return false;
    }
}