<?php
declare(strict_types=1);

namespace App\Application\Actions\Config;
use Psr\Http\Message\ResponseInterface as Response;
use App\Application\Actions\Action;

class ConfigListAction extends Action
{
    protected function action(): Response
    {
        $data = $this->getService()->select('config', ['id', 'day_of_week']);
        return $this->respondWithData($data);
    }
}
