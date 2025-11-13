<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FormMetaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('form_meta')->truncate();

        DB::table('form_meta')->insert([
            [
                'model' => 'posts',
                'category' => 'default',
                'field_name' => 'title',
                'label' => 'Judul',
                'type' => 'text',
                'rules' => 'required|string|max:255',
                'options' => null,
                'visible_roles' => json_encode(['admin', 'editor']),
                'editable_roles' => json_encode(['admin', 'editor']),
                'order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'model' => 'posts',
                'category' => 'default',
                'field_name' => 'content',
                'label' => 'Konten',
                'type' => 'textarea',
                'rules' => 'required|string',
                'options' => null,
                'visible_roles' => json_encode(['admin', 'editor']),
                'editable_roles' => json_encode(['admin', 'editor']),
                'order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'model' => 'posts',
                'category' => 'default',
                'field_name' => 'status',
                'label' => 'Status',
                'type' => 'select',
                'rules' => 'required|string',
                'options' => json_encode([
                    ['value' => 'draft', 'label' => 'Draft'],
                    ['value' => 'published', 'label' => 'Published'],
                ]),
                'visible_roles' => json_encode(['admin']),
                'editable_roles' => json_encode(['admin']),
                'order' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
