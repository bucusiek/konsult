<?php
declare(strict_types=1);

namespace App\Application\Actions\User;
use Psr\Http\Message\ResponseInterface as Response;
use App\Application\Actions\Action;

class UserListAction extends Action
{
    protected function action(): Response
    {
        $data = $this->getService()->select('user', ['id_user', 'firstname', 'surname', 'email']);
        return $this->respondWithData($data);
    }
}
