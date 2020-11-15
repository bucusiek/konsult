<?php
declare(strict_types=1);

namespace App\Application\Actions\User;
use Psr\Http\Message\ResponseInterface as Response;
use App\Application\Actions\Action;
use App\Application\Models\UserModel;
use App\Auth\JwtAuth;
use Psr\Log\LoggerInterface;

class LoginAction extends Action
{
    private $email;
    private $pass;
    private $userModel;
    protected $errorMessages = [];
    private $settings;

    public function __construct(LoggerInterface $logger = null, $settings)
    {
        parent::__construct($logger);
        $this->settings = $settings['jwt'];
    }

    protected function action(): Response
    {
        $this->getUserData();
        if($this->validateData())
        {
            $this->generateToken();
            return $this->respondWithData($this->userModel);
        }
        else {
            return $this->respondWithData($this->errorMessages, 401);

        }
    }

    public function getUserData()
    {
        $this->headers =  $this->request->getHeaders();
        $this->email = $this->headers['email'][0] ?? '';
        $passTemp = $this->headers['password'][0] ?? '';
        $this->pass = md5($passTemp);

    }

    public function validateData(): bool
    {
        $user = $this->service->get("user", '*',[
            'email' => $this->email,
            'password' => $this->pass
        ]);

        if($user)
        {
            $this->userModel = new UserModel();
            $this->userModel->setId(intval($user['id_user']));
            $this->userModel->setEmail($user['email']);
            $this->userModel->setName($user['firstname']);
            $this->userModel->setSurname($user['surname']);

            return true;
        }
        else {
            $this->errorMessages[] .= "Validation failed. User or password are incorrect";
            return false;
        }
    }

    public function generateToken()
    {
        $jwtAuth = new JwtAuth($this->settings['issuer'], $this->settings['lifetime'], $this->settings['private_key'], $this->settings['public_key']);
        $token = $jwtAuth->createJwt($this->userModel->getEmail());
        $lifetime = $jwtAuth->getLifetime();
        $result = [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => $lifetime,
        ];
        $this->userModel->setToken($result);
    }

}
