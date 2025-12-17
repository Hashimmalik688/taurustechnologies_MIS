<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\ChatAttachment;
use App\Models\ChatParticipant;

class ChatBackup extends Command
{
    protected $signature = 'chat:backup {--copy-attachments : Copy attachment files into the backup folder}';
    protected $description = 'Export chat data (conversations, messages, participants, attachments) to storage/app/chat-backups/<timestamp>'; 

    public function handle(): int
    {
        $timestamp = now()->format('Ymd_His');
        $basePath = "chat-backups/{$timestamp}";

        Storage::makeDirectory($basePath);

        $this->info("Backing up chat data to storage/app/{$basePath}");

        $data = [
            'conversations' => ChatConversation::with(['users:id,name,email'])->get(),
            'participants' => ChatParticipant::get(),
            'messages' => ChatMessage::with(['user:id,name,email'])->get(),
            'attachments' => ChatAttachment::get(),
            'meta' => [
                'exported_at' => now()->toISOString(),
                'app_url' => config('app.url'),
                'app_env' => config('app.env'),
            ],
        ];

        foreach ($data as $key => $collection) {
            Storage::put("{$basePath}/{$key}.json", json_encode($collection, JSON_PRETTY_PRINT));
        }

        if ($this->option('copy-attachments')) {
            $this->info('Copying attachments...');
            $attachmentsDir = "{$basePath}/attachments";
            Storage::makeDirectory($attachmentsDir);

            /** @var \Illuminate\Support\Collection<int, ChatAttachment> $atts */
            $atts = $data['attachments'];
            foreach ($atts as $att) {
                $src = Storage::disk('public')->path($att->file_path);
                $dst = storage_path("app/{$attachmentsDir}/" . basename($att->file_path));
                if (is_file($src)) {
                    @copy($src, $dst);
                }
            }
        }

        $this->info('Chat backup complete.');
        return self::SUCCESS;
    }
}
