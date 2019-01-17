<?php

namespace App\Services;

use Validator;
use Illuminate\Http\Request;

class BaseService
{
    const VALIDATION_RULES = [];

    /**
     * @param Request $request
     * @return array
     */
    public function validateJsonRequest(Request $request)
    {
        $validationData = $request->all();
        $validationData['user_id'] = request()->user()->id;

        $validator = Validator::make($validationData, static::VALIDATION_RULES);

        if ($validator->fails()) {
            $message = 'Validation failed';
            $errorData = $this->getFailedValidationData($message, $validator->errors());

            return [false, $errorData];
        }

        return [true, $validator->getData()];
    }

    /**
     * @return array
     */
    public function deletedResponseData()
    {
        $message = 'Successfully deleted';

        return $this->createJsonResponseData($message);
    }

    /**
     * @param $instance
     * @param $routeName
     * @return array
     */
    public function createdResponseDataBase()
    {
        $message = 'Successfully created';

        $data = $this->createJsonResponseData($message);

        return $data;
    }

    /**
     * @param $errors
     * @return array
     */
    protected function getFailedValidationData($message, $errors)
    {
        $data = $this->createJsonResponseData($message);
        $data['data']['errors'] = $errors;

        return $data;
    }

    /**
     * @param $message
     * @return array
     */
    protected function createJsonResponseData($message)
    {
        return [
            'data' => [
                'message' => $message,
            ]
        ];
    }

    public function getAccessDeniedResponseData()
    {
        $authService = new AuthService();
        return $authService->getAccessDeniedResponseData();
    }
}
