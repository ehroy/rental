<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'domain',
        'name',
        'tag',
        'icon',
        'logo',
        'action_button_text',
        'no_whatsapp',
        'wa_message',
        'meta_author',
        'meta_description',
        'meta_keywords',
        'additional_meta',
    ];
}
