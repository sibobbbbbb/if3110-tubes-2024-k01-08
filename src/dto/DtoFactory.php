<?php

namespace src\dto;

/**
 * Dto Factory class to create DTOs
 */
class DtoFactory
{
    /**
     * Create a success dto response (no data)
     */
    public static function createSuccessDto(string $message): array
    {
        $dto = new SuccessDto($message);
        return $dto->toArray();
    }

    /**
     * Create a success dto response with data
     */
    public static function createSuccessDtoWithData(string $message, object $data): array
    {
        $dto = new SuccessDto($message, $data);
        return $dto->toArray();
    }

    /**
     * Create a success dto response with paged-based pagination data
     */
    public static function createSuccessDtoWithPagination(string $message, object $data, object $pagination): array
    {
        $dto = new SuccessPagedPaginationDto($message, $data, $pagination);
        return $dto->toArray();
    }

    /**
     * Create an error dto response
     */
    public static function createErrorDto(string $message, array $errorFields = []): array
    {
        $dto = new ErrorDto($message, $errorFields);
        return $dto->toArray();
    }

    /**
     * Add more if needed (e.g. more pagination variations)
     */
}
