<?php

namespace App\Action\Admin;

use App\Domain\User\Service\UserAuth;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use function Respect\Stringifier\stringify;

/**
 * Action.
 */
final class ConfigEditSubmitAction
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
     * @var []
     */
    private $data;

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
        $currentDays = $this->getDays();
        $this->removeAllDays($currentDays);

        $this->data = (array)$request->getParsedBody();

        $this->postNewDays();

        $flash = $this->session->getFlashBag();
        $flash->clear();
        $flash->set('success', __('Pomyśnie zmieniono dni tygodnia'));

        $response = $response->withStatus(302);
        return $response->withHeader('Location', '../../requestList');
    }

    public function getDays(){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_URL,"http://localhost:1111/api/v1/config");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $jsonResponse = json_decode($response, true);

        return $jsonResponse['data'];
    }

    public function removeAllDays($currentDays){
        $sessionData = $this->session->get('user');
        $token = $sessionData->token;
        $authorization = "Authorization: Bearer " . $token;

        if(!empty($currentDays)){
            $responses = [];
            foreach ($currentDays as $day){
                $id = $day['id'];
                $node = "http://localhost:1111/api/v1/config/" . $id;
                $data = array(
                    'Content-Type: application/json',
                    $authorization,
                );

                $ch_delete = curl_init();
                curl_setopt($ch_delete, CURLOPT_URL, $node);
                curl_setopt($ch_delete, CURLOPT_HTTPHEADER, $data);
                curl_setopt($ch_delete, CURLOPT_CUSTOMREQUEST, 'DELETE');
                curl_setopt($ch_delete, CURLOPT_RETURNTRANSFER, true);
                // execute!
                $responses[] = curl_exec($ch_delete);
                // close the connection, release resources used
                curl_close($ch_delete);
            }
        }

    }

    public function postNewDays(){
        if(!empty($this->data)){
            $sessionData = $this->session->get('user');
            $token = $sessionData->token;
            $authorization = "Authorization: Bearer " . $token;

            foreach($this->data as $newDay){
                $data = array(
                    'Content-Type: application/json',
                    $authorization,
                    'day_of_week: ' . strval($newDay),
                );

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL,"http://localhost:1111/api/v1/config");
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $data);

                // execute!
                $response = curl_exec($ch);

                // close the connection, release resources used
                curl_close($ch);

            }
        }
    }
}
