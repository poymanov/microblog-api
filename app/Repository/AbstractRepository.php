<?php

namespace App\Repository;

use App\Exceptions\ValidationException;
use Validator;

/**
 * Class AbstractRepository
 * @package App\Repository
 *
 * Общие методы для работы со всеми репозиториями
 */
class AbstractRepository
{
    /**
     * Валидация данных
     *
     * @param array $data
     * @param array $rules
     * @throws ValidationException
     */
    public function validateData(array $data, array $rules)
    {
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator->errors()->toArray());
        }
    }
}
