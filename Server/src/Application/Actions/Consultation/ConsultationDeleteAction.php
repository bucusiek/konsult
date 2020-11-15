<?php
declare(strict_types=1);

namespace App\Application\Actions\Consultation;
use Psr\Http\Message\ResponseInterface as Response;
use App\Application\Actions\Action;
use Respect\Validation\Validator as v;
use App\Application\Models\ConsultationModel;

class ConsultationDeleteAction extends Action
{
    /**
     * @var ConsultationModel
     */
    private $Consultation;
    protected $headers;
    protected $id;
    protected $errorMessages = [];

    protected function action(): Response
    {
        $this->id = $this->headers['id'];
        $this->headers =  $this->request->getHeaders();
        $this->Consultation = new ConsultationModel();
        //Check if consultation with that ID already exists, if not - throw error
        $this->delete();

        return $this->respondWithData('OK');
    }

    /**
     *
     */
    public function delete(): void
    {
        $this->service->delete("consultation", [
            'id' => $this->id
        ]);
    }
}