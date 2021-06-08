<?php

namespace App\Security;

use App\Entity\ApiToken;
use App\Repository\ApiTokenRepository;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class ApiTokenAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * @var ApiTokenRepository
     */
    private $apiTokenRepository;
    /**
     * @var LoggerInterface
     */
    private $apiLogger;

    /**
     * ApiTokenAuthenticator constructor.
     *
     * @param  ApiTokenRepository  $apiTokenRepository
     * @param  LoggerInterface     $apiLogger
     */
    public function __construct(
        ApiTokenRepository $apiTokenRepository,
        LoggerInterface $apiLogger
    ) {
        $this->apiTokenRepository = $apiTokenRepository;
        $this->apiLogger = $apiLogger;
    }

    public function supports(Request $request)
    {
        return $request->headers->has('Authorization')
            && 0 === strpos($request->headers->get('Authorization'), 'Bearer ');
    }

    public function getCredentials(Request $request)
    {
        return [
            'token'      => substr($request->headers->get('Authorization'), 7),
            'controller' => $request->attributes->get('_controller'),
            'url'        => $request->server->get('SYMFONY_DEFAULT_ROUTE_URL') . $request->server->get('REQUEST_URI'),
        ];
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = $this->apiTokenRepository->findOneBy(['token' => $credentials['token']]);

        if (!$token) {
            throw new CustomUserMessageAuthenticationException('Invalid token');
        }

        if ($token->isExpired()) {
            throw new CustomUserMessageAuthenticationException('Token expired');
        }

        $this->log($token, $credentials);

        return $token->getUser();
    }

    protected function log(ApiToken $token, array $credentials)
    {
        $this->apiLogger->debug(
            'Success API login',
            [
                'user'       => [
                    'id'        => $token->getUser()->getId(),
                    'email'     => $token->getUser()->getEmail(),
                    'firstName' => $token->getUser()->getFirstName(),
                ],
                'token'      => $token->getToken(),
                'controller' => $credentials['controller'],
                'url'        => $credentials['url'],
            ]
        );
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new JsonResponse(
            [
                'message' => $exception->getMessage(),
            ],
            401
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        throw new Exception('Never called');
    }

    public function supportsRememberMe()
    {
        return false;
    }
}
