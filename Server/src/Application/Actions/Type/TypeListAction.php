<?php
declare(strict_types=1);

namespace App\Application\Actions\Type;
use Psr\Http\Message\ResponseInterface as Response;
use App\Application\Actions\Action;

class TypeListAction extends Action
{
    protected function action(): Response
    {
        $data = $this->getService()->select('type', ['id_type', 'name']);
        return $this->respondWithData($data);
    }
}
