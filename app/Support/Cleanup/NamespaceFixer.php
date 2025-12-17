<?php

namespace App\Support\Cleanup;

use Illuminate\Support\Facades\File;

class NamespaceFixer
{
    protected array $mapping = []; // psr-4 map, e.g. ["App\\" => "app/"]

    public function __construct(string $composerJson)
    {
        if (! file_exists($composerJson)) {
            throw new \RuntimeException("composer.json not found at $composerJson");
        }
        $data = json_decode(file_get_contents($composerJson), true);
        $this->mapping = $data['autoload']['psr-4'] ?? ['App\\' => 'app/'];
    }

    /**
     * Fix namespaces for files under app/
     *
     * @return array{scanned:int,namespaces_fixed:array,renamed_files:array,errors:array}
     */
    public function fixApp(bool $dryRun = false, bool $renameFiles = false): array
    {
        $appDir = base_path('app');
        $result = [
            'scanned' => 0,
            'namespaces_fixed' => [],
            'renamed_files' => [],
            'errors' => [],
        ];

        if (! is_dir($appDir)) {
            $result['errors'][] = "Directory not found: $appDir";

            return $result;
        }

        foreach (\Illuminate\Support\Facades\File::allFiles($appDir) as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }
            $result['scanned']++;

            $path = $file->getRealPath();
            $content = file_get_contents($path);
            if ($content === false) {
                $result['errors'][] = "Cannot read file: $path";

                continue;
            }

            $relative = str_replace(base_path().DIRECTORY_SEPARATOR, '', $path);
            $expected = $this->expectedNamespaceFor($relative);
            if (! $expected) {
                continue;
            }

            // Parse current namespace + class name with simple regex (best-effort)
            $currentNs = null;
            if (preg_match('/^namespace\s+([^;]+);/m', $content, $m)) {
                $currentNs = trim($m[1]);
            }
            $className = null;
            if (preg_match('/\b(class|interface|trait)\s+([A-Za-z0-9_]+)/', $content, $m)) {
                $className = $m[2];
            }

            // Fix namespace if differs
            if ($currentNs !== $expected) {
                $new = preg_replace('/^namespace\s+[^;]+;/m', 'namespace '.$expected.';', $content, 1);
                if ($new && ! $dryRun) {
                    file_put_contents($path, $new);
                }
                $result['namespaces_fixed'][$relative] = ['from' => $currentNs ?? '(none)', 'to' => $expected];
                $content = $new ?? $content;
            }

            // Optionally rename file to match class
            if ($className && $renameFiles) {
                $fileBase = basename($path, '.php');
                if ($fileBase !== $className) {
                    $newPath = dirname($path).DIRECTORY_SEPARATOR.$className.'.php';
                    if (! $dryRun) {
                        @rename($path, $newPath);
                    }
                    $result['renamed_files'][] = [
                        'from' => $relative,
                        'to' => str_replace(base_path().DIRECTORY_SEPARATOR, '', $newPath),
                    ];
                }
            }
        }

        return $result;
    }

    /**
     * Infer the expected namespace from PSR-4 mapping and file path.
     */
    protected function expectedNamespaceFor(string $relativePath): ?string
    {
        $relativePath = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $relativePath);
        foreach ($this->mapping as $ns => $dir) {
            $dir = rtrim(str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $dir), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
            if (str_starts_with($relativePath, $dir)) {
                $sub = substr($relativePath, strlen($dir));
                $subDir = dirname($sub);
                if ($subDir === '.' || $subDir === DIRECTORY_SEPARATOR) {
                    return rtrim($ns, '\\');
                }
                $parts = array_filter(explode(DIRECTORY_SEPARATOR, $subDir));
                $nsFull = rtrim($ns, '\\').'\\'.implode('\\', $parts);

                return $nsFull;
            }
        }

        return null;
    }
}
