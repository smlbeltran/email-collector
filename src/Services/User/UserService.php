<?php

namespace App\Services\User;

use App\Services\BaseController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UserService extends BaseController
{
    public function create(Request $request, Response $response)
    {
        /** @var \App\Helpers\JsonSchemaValidator $validate */
        $validate = $this->container->get('Validator');

        $payload = $validate->validate($request, 'User/create');

        /** @var \App\Services\User\UserDatabaseInterface $user */
        $user = $this->container->get('UserDatabaseInterface');

        $user->createUser($payload);

//        /** @var \App\Bootstrap\ServiceLogger */
//        $this->container->get("logger")->debug("this is working accordingly");
        return $response->withStatus(201);
    }
}
