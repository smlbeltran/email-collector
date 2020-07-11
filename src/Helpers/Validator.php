<?php
declare(strict_types=1);

namespace EmailCollector\Helpers;

use JsonSchema\Constraints\Constraint;
use JsonSchema\Validator;

class JsonSchemaValidator
{
    public function validate($data, string $schema)
    {
        $validator = new Validator();

        if (!empty($data->getQueryParams())) {
            //get
            $data = (object)$data->getQueryParams();
        } else {
            //post
            $data = json_decode($data->getBody()->getContents());
        }
//        die(var_dump(APP_PATH/..//));
        $validator->validate(
            $data,
            (object)['$ref' => 'file://' . APP_PATH . '/../src/JsonSchemas/' . $schema . '.json'],
            Constraint::CHECK_MODE_APPLY_DEFAULTS
        );

        if (!$validator->isValid()) {
            foreach ($validator->getErrors() as $error) {
                echo sprintf("[%s] %s\n", $error['property'], $error['message']);
            }
        }

        return $data;
    }
}
