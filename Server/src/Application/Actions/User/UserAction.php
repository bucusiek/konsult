<?php
declare(strict_types=1);

namespace App\Application\Actions\User;
use Psr\Http\Message\ResponseInterface as Response;
use App\Application\Actions\Action;

class UserAction extends Action
{
    protected function action(): Response
    {
        $id = $this->headers['id'];
        $data = $this->service->select('user', ['id_user', 'firstname', 'surname', 'email'], ['id_user' => $id]);
        return $this->respondWithData($data);
    }
}
