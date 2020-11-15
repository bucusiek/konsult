<?php

// Define app routes
use App\Middleware\UserAuthMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {
    //Home
    $app->get('/', \App\Action\Home\ApprovedConsultationsListAction::class)->setName('root');

    //Session management
    $app->get('/login', \App\Action\Login\LoginAction::class)->setName('login');
    $app->post('/login', \App\Action\Login\LoginSubmitAction::class)->setName('login');
    $app->get('/logout', \App\Action\Login\LogoutAction::class)->setName('logout');

    //Consultation management
    $app->get('/request', \App\Action\Consultation\RequestAction::class)->setName('request');
    $app->post('/request', \App\Action\Consultation\RequestSubmitAction::class)->setName('request');


    //Admin management
    $app->get('/requestList', \App\Action\Consultation\RequestListAction::class)
        ->setName('requestList')
        ->add(UserAuthMiddleware::class);
    $app->post('/requestList', \App\Action\Admin\ConfigEditSubmitAction::class)->add(UserAuthMiddleware::class);

    $app->group('/consultation', function (RouteCollectorProxy $group){
        $group->get('/approve/{id}', \App\Action\Consultation\RequestApproveAction::class);
        $group->get('/reject/{id}', \App\Action\Consultation\RequestRejectAction::class);
        $group->get('/edit/{id}', \App\Action\Consultation\RequestEditAction::class);
        $group->post('/edit/{id}', \App\Action\Consultation\RequestEditSubmitAction::class);
    })->add(UserAuthMiddleware::class);
};
