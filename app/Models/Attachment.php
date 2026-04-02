<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Attachment extends Model
{
    protected $fillable = [
        'module',
        'module_id',
        'filename',
        'path',
        'ext',
        'type',
        'size'
    ];

    public function getFileUrl(): string
    {
        return Storage::disk('public')->url($this->path . '/' .rawurlencode($this->filename));
    }
}
