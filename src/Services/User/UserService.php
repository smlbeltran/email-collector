<?php

namespace EmailCollector\Services\User;

use EmailCollector\Services\BaseController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UserService extends BaseController
{
    public function create(Request $request, Response $response)
    {
        /** @var \EmailCollector\Helpers\JsonSchemaValidator $validate */
        $validate = $this->container->get('Validator');

        $payload = $validate->validate($request, 'User/create');

        /** @var \EmailCollector\Services\User\UserDatabaseInterface $user */
        $user = $this->container->get('UserDatabaseInterface');

        $user->createUser($payload);

//        /** @var \EmailCollector\Bootstrap\ServiceLogger */
//        $this->container->get("logger")->debug("this is working accordingly");
        return $response->withStatus(201);
    }
}
