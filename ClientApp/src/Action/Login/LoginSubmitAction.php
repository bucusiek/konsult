<?php

namespace App\Action\Login;

use App\Domain\User\Data\UserSessionData;
use App\Domain\User\Service\UserAuth;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class LoginSubmitAction
{
    /**
     * @var Responder
     */
    private $responder;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var UserAuth
     */
    private $auth;

    /**
     * The constructor.
     *
     * @param Responder $responder The responder
     * @param Session $session The session handler
     * @param UserAuth $auth The user auth
     */
    public function __construct(Responder $responder, Session $session, UserAuth $auth)
    {
        $this->responder = $responder;
        $this->session = $session;
        $this->auth = $auth;
    }

    /**
     * Action.
     *
     * @param ServerRequestInterface $request The request
     * @param ResponseInterface $response The response
     *
     * @return ResponseInterface The response
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();
        $email = (string)($data['email'] ?? '');
        $password = (string)($data['password'] ?? '');

        $user = $this->authenticate($email, $password);

        $flash = $this->session->getFlashBag();
        $flash->clear();

        if ($user) {
            $this->startUserSession($user);
            $flash->set('success', __('Zalogowano pomyślnie, witaj ' . $user->name));
            $url = 'requestList';
        } else {
            $flash->set('error', __('Nieprawidłowy login lub hasło'));
            $url = 'login';
        }

        return $this->responder->redirect($response, $url);
    }

    public function authenticate($email, $password){
        $data =  array(
            'email: ' . $email,
            'password: ' . $password
        );

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,"http://localhost:1111/login");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $data);

        // execute!
        $response = curl_exec($ch);

        // close the connection, release resources used
        curl_close($ch);

        $jsonResponse = json_decode($response, false);

        $user = null;

        if($jsonResponse->statusCode === 200){
            $user = new UserSessionData();
            $user->id = $jsonResponse->data->id;
            $user->name = $jsonResponse->data->name;
            $user->surname = $jsonResponse->data->surname;
            $user->email = $jsonResponse->data->email;
            $user->token = $jsonResponse->data->token->access_token;
            $user->validateTo = microtime() + $jsonResponse->data->token->expires_in;
        }

        return $user;
    }

    /**
     * Init user session.
     *
     * @param UserSessionData $user The user
     *
     * @return void
     */
    private function startUserSession(UserSessionData $user): void
    {
        // Clears all session data and regenerates session ID
        $this->session->invalidate();
        $this->session->start();

        $this->session->set('user', $user);

        // Store user settings in session
        $this->auth->setUser($user);
    }
}
