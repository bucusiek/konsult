<?php
declare(strict_types=1);

use App\Auth\JwtAuth;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use App\Application\Actions\User\UserListAction;
use App\Application\Actions\User\UserAction;
use App\Application\Actions\User\LoginAction;
use App\Application\Actions\Consultation\ConsultationListAction;
use App\Application\Actions\Consultation\ConsultationAction;
use App\Application\Actions\Consultation\ConsultationAddAction;
use App\Application\Actions\Consultation\ConsultationEditAction;
use App\Application\Actions\Consultation\ConsultationDeleteAction;
use App\Application\Actions\Type\TypeAction;
use App\Application\Actions\Type\TypeListAction;
use App\Application\Actions\Config\ConfigListAction;
use App\Application\Actions\Config\ConfigAction;
use App\Application\Actions\Config\ConfigAddAction;
use App\Application\Actions\Config\ConfigEditAction;
use App\Application\Actions\Config\ConfigDeleteAction;
use App\Application\Middleware\JwtMiddleware;

return function (App $app) {
    $app->group('/api/v1/users', function (Group $group) {
        $group->get('', function (Request $request, Response $response, $args) {
            $action = new UserListAction();
            return $action($request, $response, $args, $this->get('database'));
        });

        $group->get('/{id}', function ($request, $response, $args) {
            $action = new UserAction();
            return $action($request, $response, $args, $this->get('database'));
        });
    });

    $app->group('/login', function (Group $group) {
        $group->post('', function ($request, $response, $args) {
            $action = new LoginAction(null, $this->get('settings'));
            return $action($request, $response, $args, $this->get('database'));
        });
    });

    $app->group('/api/v1/consultation', function(Group $group){
        $group->get('', function ($request, $response, $args) {
            $action = new ConsultationListAction();
            return $action($request, $response, $args, $this->get('database'));
        });
        $group->get('/{id}', function ($request, $response, $args) {
            $action = new ConsultationAction();
            return $action($request, $response, $args, $this->get('database'));
        });
        $group->post('', function ($request, $response, $args) {
            $action = new ConsultationAddAction();
            return $action($request, $response, $args, $this->get('database'));
        });
        $group->patch('/{id}', function ($request, $response, $args) {
            $action = new ConsultationEditAction();
            return $action($request, $response, $args, $this->get('database'));
        })->add(JwtMiddleware::class);
        $group->delete('/{id}', function ($request, $response, $args) {
            $action = new ConsultationDeleteAction();
            return $action($request, $response, $args, $this->get('database'));
        })->add(JwtMiddleware::class);
    });

    $app->group('/api/v1/type', function(Group $group){
        $group->get('', function ($request, $response, $args) {
            $action = new TypeListAction();
            return $action($request, $response, $args, $this->get('database'));
        });
        $group->get('/{id}', function ($request, $response, $args) {
            $action = new TypeAction();
            return $action($request, $response, $args, $this->get('database'));
        });
    });

    $app->get('/api/v1/config', function ($request, $response, $args) {
        $action = new ConfigListAction();
        return $action($request, $response, $args, $this->get('database'));
    });

    $app->group('/api/v1/config', function(Group $group){
        $group->get('/{id}', function ($request, $response, $args) {
            $action = new ConfigAction();
            return $action($request, $response, $args, $this->get('database'));
        });
        $group->post('', function ($request, $response, $args) {
            $action = new ConfigAddAction();
            return $action($request, $response, $args, $this->get('database'));
        });
        $group->patch('/{id}',function ($request, $response, $args) {
            $action = new ConfigEditAction();
            return $action($request, $response, $args, $this->get('database'));
        });
        $group->delete('/{id}',function ($request, $response, $args) {
            $action = new ConfigDeleteAction();
            return $action($request, $response, $args, $this->get('database'));
        });
    })->add(JwtMiddleware::class);
};
