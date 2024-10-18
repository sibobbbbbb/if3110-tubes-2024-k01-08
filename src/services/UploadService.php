<?php

namespace src\services;

use src\utils\PathResolver;

/**
 * Service class for uploading files
 */
class UploadService
{
    /**
     * Uploads a file to a certain target directory
     * @param file: file array 
     * @param targetDirectory: target directory to upload the file
     * I.S. file is already validated at the start of controller
     * F.S. file is uploaded to the target directory
     */
    public function uploadOneFile(array $normalizedFile, string $directoryFromPublic): string
    {
        $originalFileName = $normalizedFile['name'];
        // Remove illegal characters from the
        // change non-alphanumeric characters (except . - _ ) to underscore 
        $validatedOriginalFileName = preg_replace('/[^a-zA-Z0-9.-_]/', '_', $originalFileName);
        $finalFilename = uniqid() . '_' . $validatedOriginalFileName;

        $folderDirectoryToMove = PathResolver::resolve(__DIR__ . '/../../public/' . $directoryFromPublic);
        $fileDirectoryToMove = PathResolver::resolve($folderDirectoryToMove . '/' . $finalFilename);
        $fileDirectoryFromPublic = PathResolver::resolve($directoryFromPublic . '/' . $finalFilename);

        // Create the target directory if it doesn't exist
        if (!file_exists($folderDirectoryToMove)) {
            if (!mkdir($folderDirectoryToMove, 0755, true)) {
                throw new \Exception('Failed to create directory: ' . $folderDirectoryToMove);
            }
        }

        // Move the file to the target directory
        if (!move_uploaded_file($normalizedFile['tmp_name'], $fileDirectoryToMove)) {
            throw new \Exception('Failed to upload file');
        }

        return $fileDirectoryFromPublic;
    }

    public function uploadMultipleFiles(array $rawFiles, string $directoryFromPublic): array
    {
        $uploadedFiles = [];

        for ($i = 0; $i < count($rawFiles['name']); $i++) {
            $normalizedFile = [
                'name' => $rawFiles['name'][$i],
                'type' => $rawFiles['type'][$i],
                'tmp_name' => $rawFiles['tmp_name'][$i],
                'error' => $rawFiles['error'][$i],
                'size' => $rawFiles['size'][$i],
            ];

            $uploadedFiles[] = self::uploadOneFile($normalizedFile, $directoryFromPublic);
        }

        return $uploadedFiles;
    }
}
