<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadService
{
    /**
     * Upload configuration
     */
    private const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB
    private const ALLOWED_AVATAR_TYPES = ['jpg', 'jpeg', 'png', 'gif'];
    private const ALLOWED_DOCUMENT_TYPES = ['pdf', 'doc', 'docx', 'xlsx', 'xls', 'csv', 'txt'];
    private const ALLOWED_ALL_TYPES = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xlsx', 'xls', 'csv', 'txt'];

    private string $disk = 'local';
    private string $uploadPath = 'uploads';

    /**
     * Upload an avatar file
     */
    public function uploadAvatar(UploadedFile $file, string $userId): ?string
    {
        return $this->validateAndStore($file, 'avatars', self::ALLOWED_AVATAR_TYPES, $userId);
    }

    /**
     * Upload a document file
     */
    public function uploadDocument(UploadedFile $file, string $userId): ?string
    {
        return $this->validateAndStore($file, 'documents', self::ALLOWED_DOCUMENT_TYPES, $userId);
    }

    /**
     * Upload any allowed file
     */
    public function uploadFile(UploadedFile $file, string $userId, string $directory = 'files'): ?string
    {
        return $this->validateAndStore($file, $directory, self::ALLOWED_ALL_TYPES, $userId);
    }

    /**
     * Validate and store the file
     */
    private function validateAndStore(
        UploadedFile $file,
        string $directory,
        array $allowedTypes,
        string $userId
    ): ?string {
        try {
            // Validate file size
            if ($file->getSize() > self::MAX_FILE_SIZE) {
                throw new \Exception('File size exceeds maximum limit of 5MB');
            }

            // Validate file extension
            $extension = strtolower($file->getClientOriginalExtension());
            if (!in_array($extension, $allowedTypes)) {
                throw new \Exception('File type not allowed. Allowed types: ' . implode(', ', $allowedTypes));
            }

            // Validate MIME type
            $this->validateMimeType($file, $allowedTypes);

            // Generate unique filename
            $filename = $this->generateUniqueFilename($file, $userId);

            // Store file
            $path = $this->uploadPath . '/' . $directory;
            $storagePath = Storage::disk($this->disk)->putFileAs(
                $path,
                $file,
                $filename
            );

            // Return the storage path
            return $storagePath ? $storagePath : null;

        } catch (\Exception $e) {
            \Log::error('File upload failed: ' . $e->getMessage(), [
                'user_id' => $userId,
                'file' => $file->getClientOriginalName(),
                'directory' => $directory,
            ]);

            return null;
        }
    }

    /**
     * Validate MIME type
     */
    private function validateMimeType(UploadedFile $file, array $allowedTypes): void
    {
        $mimeType = $file->getMimeType();
        $allowedMimes = $this->getAllowedMimes($allowedTypes);

        if (!in_array($mimeType, $allowedMimes)) {
            throw new \Exception('Invalid file MIME type');
        }
    }

    /**
     * Get allowed MIME types
     */
    private function getAllowedMimes(array $extensions): array
    {
        $mimeMap = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'xls' => 'application/vnd.ms-excel',
            'csv' => 'text/csv',
            'txt' => 'text/plain',
        ];

        return array_map(fn($ext) => $mimeMap[strtolower($ext)] ?? '', $extensions);
    }

    /**
     * Generate unique filename
     */
    private function generateUniqueFilename(UploadedFile $file, string $userId): string
    {
        $extension = $file->getClientOriginalExtension();
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        // Create slug-friendly name
        $name = Str::slug($originalName);

        // Add timestamp and random string to ensure uniqueness
        return "{$userId}_{$name}_" . time() . '_' . Str::random(8) . '.' . $extension;
    }

    /**
     * Delete a file
     */
    public function deleteFile(string $filePath): bool
    {
        try {
            return Storage::disk($this->disk)->delete($filePath);
        } catch (\Exception $e) {
            \Log::error('File deletion failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get file URL
     */
    public function getFileUrl(string $filePath): string
    {
        return Storage::disk($this->disk)->url($filePath);
    }

    /**
     * Check if file exists
     */
    public function fileExists(string $filePath): bool
    {
        return Storage::disk($this->disk)->exists($filePath);
    }

    /**
     * Get file size in MB
     */
    public function getFileSizeMB(string $filePath): float
    {
        $sizeInBytes = Storage::disk($this->disk)->size($filePath);
        return round($sizeInBytes / (1024 * 1024), 2);
    }
}
