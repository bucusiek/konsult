<?php
declare(strict_types=1);

namespace App\Application\Actions\Consultation;
use Psr\Http\Message\ResponseInterface as Response;
use App\Application\Actions\Action;
use Respect\Validation\Validator as v;
use App\Application\Models\ConsultationModel;

class ConsultationAddAction extends Action
{
    /**
     * @var ConsultationModel
     */
    private $Consultation;
    protected $headers;
    protected $errorMessages = [];

    protected function action(): Response
    {
        $this->headers =  $this->request->getHeaders();
        $this->Consultation = new ConsultationModel();
        $this->assignValues();

        if($this->validate()){
            $this->post();
            return $this->respondWithData($this->getConsultation());
        }

        return $this->respondWithData($this->errorMessages);
    }

    public function assignValues()
    {
        $this->Consultation->setDateStart($this->headers['dateStart'][0]);
        $this->Consultation->setDuration(intval($this->headers['duration'][0]));
        $this->Consultation->setIdUser(intval($this->headers['id-user'][0]));
        $this->Consultation->setIdType(intval($this->headers['id-type'][0]));
        $this->Consultation->setApplicant($this->headers['applicant'][0]);
    }

    /**
     * @return bool
     */
    public function validate(): bool
    {
        $dateStartValidation = v::dateTime()
            ->notEmpty()
            ->validate($this->getConsultation()->getDateStart());
        $durationValidation = v::intVal()
            ->between(10, 360)
            ->notEmpty()
            ->validate($this->getConsultation()->getDuration());
        $idUserValidation = v::intVal()
            ->notEmpty()
            ->validate($this->getConsultation()->getIdUser());
        $idTypeValidation = v::intVal()
            ->notEmpty()
            ->validate($this->getConsultation()->getIdType());
        $applicantValidation = v::alnum()
            ->notEmpty()
            ->validate($this->getConsultation()->getApplicant());

        if($dateStartValidation && $durationValidation && $idUserValidation && $idTypeValidation && $applicantValidation){
            if($this->validateType() && $this->validateUser()) return true;
            else {
                $this->errorMessages[] .= "Validation error. User or Type is invalid.";
                return false;
            }
        }
        else{
            $this->errorMessages[] .= "Validation error. Some fields are incorrect";
            return false;
        }
    }

    /**
     * @return bool
     */
    public function validateType(): bool
    {
        if ($this->service->has("type", [
            'id_type' => $this->Consultation->getIdType()
            ]))
        {
            return true;
        }
        else return false;
    }

    /**
     * @return bool
     */
    public function validateUser(): bool
    {
        if ($this->service->has("user", [
            'id_user' => $this->Consultation->getIdUser()
        ]))
        {
            return true;
        }
        else return false;
    }

    /**
     *
     */
    public function post(): void
    {
        $this->service->insert("consultation", [
            "date_start" => $this->Consultation->getDateStart(),
            "duration" => $this->Consultation->getDuration(),
            "id_user" => $this->Consultation->getIdUser(),
            "id_type" => $this->Consultation->getIdType(),
            "is_accepted" => 0,
            "applicant" => $this->Consultation->getApplicant()
        ]);

        $this->getConsultation()->setId($this->service->id());
    }

    /**
     * @return ConsultationModel
     */
    public function getConsultation(): ConsultationModel
    {
        return $this->Consultation;
    }

    /**
     * @param ConsultationModel $Consultation
     */
    public function setConsultation(ConsultationModel $Consultation): void
    {
        $this->Consultation = $Consultation;
    }

}