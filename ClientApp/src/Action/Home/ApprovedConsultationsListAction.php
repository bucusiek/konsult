<?php

namespace App\Action\Home;

use App\Responder\Responder;
use DateTime;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class ApprovedConsultationsListAction
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
        $allConsultations = $this->getConsultations();
        $consultationList = $this->getAcceptedConsultations($allConsultations);
        $consultationList = $this->getThisWeekConsultations($consultationList);

        $viewData = [
            'consultation' => $consultationList,
        ];

        return $this->responder->render($response, 'home/home.twig', $viewData);
    }

    public function getAcceptedConsultations($consultationList){
        $result = array();
        foreach ($consultationList as $consultation) {
            if($consultation['is_accepted'] === "1"){
                array_push($result, $consultation);
            }
        }

        return $result;
    }

    public function getThisWeekConsultations($consultationList){
        $result = array();
        foreach ($consultationList as $consultation) {
            $consultationMonth = date('n', strtotime($consultation['date_start']));
            $nowMonth = date('n', strtotime("now"));

            if($consultationMonth === $nowMonth){
                array_push($result, $consultation);
            }
        }
        return $result;
    }

    public function getConsultations(){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,"http://localhost:1111/api/v1/consultation");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $jsonResponse = json_decode($response, true);

        return $jsonResponse['data'];
    }
}
