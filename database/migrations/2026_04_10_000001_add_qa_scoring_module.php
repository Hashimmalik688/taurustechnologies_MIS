<?php

use App\Models\Module;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Module::updateOrCreate(
            ['slug' => 'qa-scoring'],
            [
                'name'        => 'QA Scoring Page',
                'slug'        => 'qa-scoring',
                'description' => 'AI-powered call quality assurance scoring dashboard',
                'category'    => 'Sales Operations',
                'sort_order'  => 31,
                'is_active'   => true,
            ]
        );
    }

    public function down(): void
    {
        Module::where('slug', 'qa-scoring')->delete();
    }
};
