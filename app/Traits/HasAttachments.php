<?php

namespace App\Traits;

use App\Models\Attachment;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait HasAttachments
{
    public function attachment(): HasOne
    {
        return $this->hasOne(Attachment::class, 'module_id', 'id')->where('module', self::MODULE_NAME);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class, 'module_id', 'id')->where('module', self::MODULE_NAME);
    }

    public function getAttachmentsShortInfo(): bool|string
    {
        if ($this->attachments->count() == 0)
            return '[]';

        $result = array();
        foreach ($this->attachments as $attachment) {
            $result[] = $attachment->getShortInfo();
        }

        return json_encode($result);
    }
}
