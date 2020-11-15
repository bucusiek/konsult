<?php
declare(strict_types=1);

namespace App\Application\Actions\Config;
use App\Application\Models\ConfigModel;
use Psr\Http\Message\ResponseInterface as Response;
use App\Application\Actions\Action;
use Respect\Validation\Validator as v;

class ConfigDeleteAction extends Action
{
    /**
     * @var ConfigModel
     */
    private $Config;
    protected $headers;
    protected $errorMessages = [];
    protected $id;

    protected function action(): Response
    {
        $this->id = $this->headers['id'];
        $this->headers =  $this->request->getHeaders();
        $this->Config = new ConfigModel();
        $this->delete();

        return $this->respondWithData('OK');
    }

    /**
     *
     */
    public function delete(): void
    {
        $this->service->delete("config", [
            "id" => $this->id,
        ]);
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