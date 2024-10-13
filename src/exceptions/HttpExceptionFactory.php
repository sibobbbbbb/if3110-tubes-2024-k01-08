<?php

class HttpExceptionFactory
{
    static function create(int $code, string $message, array $fieldErrors = null): BaseHttpException
    {
        switch ($code) {
            case 400:
                return new BadRequestHttpException($message, $fieldErrors);
            case 401:
                return new UnauthorizedHttpException($message);
            case 403:
                return new ForbiddenHttpException($message);
            case 404:
                return new NotFoundHttpException($message);
            case 500:
                return new InternalServerErrorHttpException($message);
            default:
                return new BaseHttpException($code, $message);
        }
    }
}
