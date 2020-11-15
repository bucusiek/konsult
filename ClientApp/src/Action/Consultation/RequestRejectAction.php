<?php

namespace App\Action\Consultation;

use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class RequestRejectAction
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
     * The constructor.
     *
     * @param Responder $responder The responder
     */
    public function __construct(Responder $responder, Session $session)
    {
        $this->session = $session;
        $this->responder = $responder;
    }

    /**
     * Action.
     *
     * @param ServerRequestInterface $request The request
     * @param ResponseInterface $response The response
     *
     * @param array $args
     * @return ResponseInterface The response
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args = []): ResponseInterface
    {
        $requestId = $args['id'];
        $this->rejectRequest($requestId);

        $flash = $this->session->getFlashBag();
        $flash->clear();
        $flash->set('success', __("Odrzucono konsultacje."));

        $response = $response->withStatus(302);
        return $response->withHeader('Location', '../../requestList');
    }


    public function rejectRequest($id){
        $sessionData = $this->session->get('user');
        $token = $sessionData->token;
        $authorization = "Authorization: Bearer " . $token;

        $data = array(
            'Content-Type: application/json',
            $authorization,
        );
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,"http://localhost:1111/api/v1/consultation/" . $id);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $data);

        // execute!
        $response = curl_exec($ch);

        // close the connection, release resources used
        curl_close($ch);
    }

}
