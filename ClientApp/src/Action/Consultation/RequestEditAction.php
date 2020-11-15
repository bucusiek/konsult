<?php

namespace App\Action\Consultation;

use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class RequestEditAction
{
    /**
     * @var Responder
     */
    private $responder;

    /**
     * The constructor.
     *
     * @param Responder $responder The responder
     */
    public function __construct(Responder $responder)
    {
        $this->responder = $responder;
    }

    /**
     * Action.
     *
     * @param ServerRequestInterface $request The request
     * @param ResponseInterface $response The response
     *
     * @return ResponseInterface The response
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args = []): ResponseInterface
    {
        $availableOwners = $this->getOwners();
        $availableTypes = $this->getTypes();
        $requestData = $this->getDataRequest($args['id']);
        $requestData['date'] = substr($requestData['date_start'], 0, 10);
        $requestData['time'] = substr($requestData['date_start'], 11, 5);

        $viewData = [
            'requestData' => $requestData,
            'owners' => $availableOwners,
            'types' => $availableTypes,
        ];

        return $this->responder->render($response, 'consultation/edit/request.twig', $viewData);
    }

    public function getTypes(){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,"http://localhost:1111/api/v1/type");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $jsonResponse = json_decode($response, true);

        return $jsonResponse['data'];
    }

    public function getOwners(){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,"http://localhost:1111/api/v1/users");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $jsonResponse = json_decode($response, true);

        return $jsonResponse['data'];
    }

    public function getDataRequest($id){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,"http://localhost:1111/api/v1/consultation/" . $id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $jsonResponse = json_decode($response, true);

        return $jsonResponse['data'][0];
    }
}
