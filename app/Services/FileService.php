<?php

namespace App\Services;

use App\Models\AssignmentFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileService
{
    /**
     * Загружает файл и создает запись в базе данных
     *
     * @param UploadedFile $file
     * @param int $assignmentId
     * @return AssignmentFile
     */
    public function uploadFile(UploadedFile $file, int $assignmentId): AssignmentFile
    {
        $path = $file->store('assignments/' . $assignmentId);

        return AssignmentFile::create([
            'assignment_id' => $assignmentId,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize()
        ]);
    }

    /**
     * Удаляет файл и его запись из базы данных
     *
     * @param AssignmentFile $file
     * @return bool
     */
    public function deleteFile(AssignmentFile $file): bool
    {
        if (Storage::exists($file->file_path)) {
            Storage::delete($file->file_path);
        }

        return $file->delete();
    }

    /**
     * Получает URL для скачивания файла
     *
     * @param AssignmentFile $file
     * @return string
     */
    public function getDownloadUrl(AssignmentFile $file): string
    {
        return Storage::url($file->file_path);
    }

    /**
     * Получает все файлы для задания
     *
     * @param int $assignmentId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAssignmentFiles(int $assignmentId)
    {
        return AssignmentFile::where('assignment_id', $assignmentId)->get();
    }

    /**
     * Проверяет, является ли файл изображением
     *
     * @param string $mimeType
     * @return bool
     */
    public function isImage(string $mimeType): bool
    {
        return Str::startsWith($mimeType, 'image/');
    }

    /**
     * Проверяет, является ли файл PDF
     *
     * @param string $mimeType
     * @return bool
     */
    public function isPdf(string $mimeType): bool
    {
        return $mimeType === 'application/pdf';
    }

    /**
     * Проверяет, является ли файл презентацией
     *
     * @param string $mimeType
     * @return bool
     */
    public function isPresentation(string $mimeType): bool
    {
        return in_array($mimeType, [
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation'
        ]);
    }
} 