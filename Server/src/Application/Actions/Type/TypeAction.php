<?php
declare(strict_types=1);

namespace App\Application\Actions\Type;
use Psr\Http\Message\ResponseInterface as Response;
use App\Application\Actions\Action;

class TypeAction extends Action
{
    protected function action(): Response
    {
        $id = $this->headers['id'];
        $data = $this->service->select('type', ['id_type', 'name'], ['id_type' => $id]);
        return $this->respondWithData($data);
    }
}
