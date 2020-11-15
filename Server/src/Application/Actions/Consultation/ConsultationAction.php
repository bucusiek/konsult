<?php
declare(strict_types=1);

namespace App\Application\Actions\Consultation;
use Psr\Http\Message\ResponseInterface as Response;
use App\Application\Actions\Action;

class ConsultationAction extends Action
{
    protected function action(): Response
    {
        $id = $this->headers['id'];
        $data = $this->getService()->select('consultation',
            [
                '[>]type'  => ["id_type" => "id_type"],
                '[>]user'  => ["id_user" => "id_user"],
            ],
            [
                'consultation.id',
                'consultation.date_start',
                'consultation.duration',
                'user' => [
                    'user.id_user',
                    'user.firstname',
                    'user.surname',
                    'user.email'
                ],
                'type' => [
                    'type.id_type',
                    'type.name'
                ],
                'consultation.is_accepted',
                'consultation.applicant'
            ],[
                'consultation.id' => $id
            ]);
        return $this->respondWithData($data);
    }
}