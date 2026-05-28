<?php

namespace App\Services;

use ZipArchive;
use DOMDocument;
use App\Models\Course;
use App\Models\Category;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Aukstructure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ScormParserService
{
    public function parseScorm($filePath, $courseId)
    {
        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            throw new \Exception('Не удалось открыть SCORM пакет');
        }

        $manifest = $zip->getFromName('imsmanifest.xml');
        if (!$manifest) {
            throw new \Exception('Файл imsmanifest.xml не найден');
        }

        $xml = new DOMDocument();
        $xml->loadXML($manifest);

        $organizations = $xml->getElementsByTagName('organization');
        $course = Course::findOrFail($courseId);

        DB::transaction(function () use ($zip, $organizations, $course, $filePath) {
            $rootPath = dirname($filePath) . '/scorm_extract_' . uniqid();
            
            // Распаковка для доступа к файлам ресурсов
            $zip->extractTo($rootPath);
            
            foreach ($organizations as $org) {
                $items = $org->getElementsByTagName('item');
                foreach ($items as $item) {
                    $title = $item->getAttribute('title');
                    $identifier = $item->getAttribute('identifierref');
                    
                    // Создаем категорию/модуль
                    $category = Category::firstOrCreate(
                        ['name' => $title],
                        ['course_id' => $course->id]
                    );

                    // Поиск ресурса
                    $resources = $item->ownerDocument->getElementsByTagName('resource');
                    foreach ($resources as $res) {
                        if ($res->getAttribute('identifier') === $identifier) {
                            $href = $res->getAttribute('href');
                            $fullPath = $rootPath . '/' . dirname($href) . '/';
                            
                            // Сохраняем путь к контенту в базу или копируем в storage
                            // Здесь упрощенная логика: сохраняем структуру
                            $aukstructure = Aukstructure::create([
                                'category_id' => $category->id,
                                'title' => $title,
                                'path' => 'scorm_content/' . $course->id . '/' . $identifier,
                                'type' => 'scorm'
                            ]);
                            
                            // Копирование файлов в постоянное хранилище
                            Storage::disk('public')->makeDirectory($aukstructure->path);
                            $this->copyDirectory($fullPath, storage_path('app/public/' . $aukstructure->path));
                        }
                    }
                }
            }
            
            // Очистка временных файлов
            $this->deleteDirectory($rootPath);
        });

        $zip->close();
        return true;
    }

    private function copyDirectory($src, $dst)
    {
        $dir = opendir($src);
        @mkdir($dst);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->copyDirectory($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    private function deleteDirectory($dir)
    {
        if (!file_exists($dir)) return;
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->deleteDirectory("$dir/$file") : unlink("$dir/$file");
        }
        rmdir($dir);
    }
}
