<?php

/**
 * Dto Factory class to create DTOs
 */
class DtoFactory
{
    /**
     * Create a success dto response (no data)
     */
    public static function createSuccessDto(string $message): SuccessDto
    {
        return new SuccessDto($message);
    }

    /**
     * Create a success dto response with data
     */
    public static function createSuccessDtoWithData(string $message, object $data): SuccessDto
    {
        return new SuccessDto($message, $data);
    }

    /**
     * Create a success dto response with paged-based pagination data
     */
    public static function createSuccessDtoWithPagination(string $message, object $data, object $pagination): SuccessPagedPaginationDto
    {
        return new SuccessPagedPaginationDto($message, $data, $pagination);
    }

    /**
     * Create an error dto response
     */
    public static function createErrorDto(string $message, array $errorFields): ErrorDto
    {
        return new ErrorDto($message, $errorFields);
    }

    /**
     * Add more if needed (e.g. more pagination variations)
     */
}
