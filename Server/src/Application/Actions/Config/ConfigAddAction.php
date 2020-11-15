<?php
declare(strict_types=1);

namespace App\Application\Actions\Config;
use App\Application\Models\ConfigModel;
use Psr\Http\Message\ResponseInterface as Response;
use App\Application\Actions\Action;
use Respect\Validation\Validator as v;

class ConfigAddAction extends Action
{
    /**
     * @var ConfigModel
     */
    private $Config;
    protected $headers;
    protected $errorMessages = [];

    protected function action(): Response
    {
        $this->headers =  $this->request->getHeaders();
        $this->Config = new ConfigModel();
        $this->assignValues();

        if($this->validate()){
            $this->post();
            return $this->respondWithData($this->getConfig());
        }

        return $this->respondWithData($this->errorMessages);
    }

    public function assignValues()
    {
        $this->Config->setDayOfWeek(intval($this->headers['day-of-week'][0]));
    }

    /**
     * @return bool
     */
    public function validate(): bool
    {
        $day_of_weekValidation = v::intVal()
            ->between(0, 6)
            ->validate($this->getConfig()->getDayOfWeek());

        if($day_of_weekValidation){
            if(!$this->checkForDuplicate()){
                return true;
            }
            else{
                $this->errorMessages[] .= "This day of week is already taken";
                return false;
            }
        }
        else{
            $this->errorMessages[] .= "Validation error. Some fields are incorrect";
            return false;
        }
    }

    public function checkForDuplicate(){
        if ($this->service->has("config", [
            'day_of_week' => $this->Config->getDayOfWeek()
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
        $this->service->insert("config", [
            'day_of_week' => $this->Config->getDayOfWeek()
        ]);

        $this->Config->setId(intval($this->service->id()));
    }

    /**
     * @return ConfigModel
     */
    public function getConfig(): ConfigModel
    {
        return $this->Config;
    }

    /**
     * @param ConfigModel $Config
     */
    public function setConfig(ConfigModel $Config): void
    {
        $this->Config = $Config;
    }

}