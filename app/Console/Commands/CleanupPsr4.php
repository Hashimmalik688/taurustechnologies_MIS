<?php

namespace App\Console\Commands;

use App\Support\Cleanup\NamespaceFixer;
use Illuminate\Console\Command;

class CleanupPsr4 extends Command
{
    protected $signature = 'cleanup:psr4 {--dry-run} {--rename-files : Also rename files to match class names}';

    protected $description = 'Fix PSR-4 namespaces under app/ based on composer.json autoload.';

    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');
        $rename = (bool) $this->option('rename-files');

        $fixer = new NamespaceFixer(base_path('composer.json'));
        $result = $fixer->fixApp($dry, $rename);

        $this->info('📦 PSR-4 Namespace Fix');
        $this->line(' - Files scanned: '.$result['scanned']);
        $this->line(' - Namespaces updated: '.count($result['namespaces_fixed']));
        foreach ($result['namespaces_fixed'] as $f => $fromTo) {
            $this->line("   • $f : {$fromTo['from']}  →  {$fromTo['to']}");
        }
        if (! empty($result['renamed_files'])) {
            $this->line(' - Files renamed: '.count($result['renamed_files']));
            foreach ($result['renamed_files'] as $r) {
                $this->line("   • {$r['from']} → {$r['to']}");
            }
        }
        $this->line(' - Errors: '.count($result['errors']));
        foreach ($result['errors'] as $e) {
            $this->warn('   • '.$e);
        }

        $this->info('✅ PSR-4 check finished.');

        return self::SUCCESS;
    }
}
