<?php
declare(strict_types=1);

namespace App\Application\Actions\Config;
use Psr\Http\Message\ResponseInterface as Response;
use App\Application\Actions\Action;

class ConfigAction extends Action
{
    protected function action(): Response
    {
        $id = $this->headers['id'];
        $data = $this->service->select('config', ['id', 'day_of_week'], ['id' => $id]);
        return $this->respondWithData($data);
    }
}
