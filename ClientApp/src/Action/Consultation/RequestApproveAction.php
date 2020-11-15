<?php

namespace App\Action\Consultation;

use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class RequestApproveAction
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

        $requestObject = $this->getRequest($requestId);
        $requestObject = $requestObject[0];
        $requestObject['is_accepted'] = '1';
        $this->approveRequest($requestObject);

        $flash = $this->session->getFlashBag();
        $flash->clear();
        $flash->set('success', __("Zaakceptowano konsultacje. Od teraz pojawi się ona na stronie głównej"));

        $response = $response->withStatus(302);
        return $response->withHeader('Location', '../../requestList');
    }

    public function getRequest($id){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,"http://localhost:1111/api/v1/consultation/" . $id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $jsonResponse = json_decode($response, true);

        return $jsonResponse['data'];
    }

    public function approveRequest($request){
        $sessionData = $this->session->get('user');
        $token = $sessionData->token;
        $authorization = "Authorization: Bearer " . $token;

        $data = array(
            'Content-Type: application/json',
            $authorization,
            'duration: ' . $request['duration'],
            'id_user: ' .  $request['user']['id_user'],
            'id_type: ' .  $request['type']['id_type'],
            'isAccepted: ' . '1',
            'applicant: ' .  $request['applicant'],
            'dateStart: ' .  $request['date_start'],
        );
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,"http://localhost:1111/api/v1/consultation/" . $request['id']);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $data);

        // execute!
        $response = curl_exec($ch);

        // close the connection, release resources used
        curl_close($ch);

        $jsonResponse = json_decode($response, true);
    }

}
