<?php

namespace App\Services;

class PostsService extends BaseService
{
    const VALIDATION_RULES = [
        'text' => 'required|max:300',
        'user_id' => 'required|exists:users,id',
    ];

    /**
     * @return array
     */
    public function createdResponseData()
    {
        return $this->createdResponseDataBase();
    }
}
