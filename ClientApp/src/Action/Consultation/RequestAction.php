<?php

namespace App\Action\Consultation;

use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class RequestAction
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
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $availableOwners = $this->getOwners();
        $availableTypes = $this->getTypes();

        $config = $this->getConfig();
        $configArray = array();
        foreach($config as $day){
            array_push( $configArray, $day['day_of_week'] );
        }

        $viewData = [
            'owners' => $availableOwners,
            'types' => $availableTypes,
            'config' => $configArray,
        ];

        return $this->responder->render($response, 'consultation/request.twig', $viewData);
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

    public function getConfig(){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' ));
        curl_setopt($ch, CURLOPT_URL,"http://localhost:1111/api/v1/config");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $jsonResponse = json_decode($response, true);

        return $jsonResponse['data'];
    }
}
