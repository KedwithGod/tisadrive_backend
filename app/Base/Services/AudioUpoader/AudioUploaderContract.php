<?php

namespace App\Base\Services\AudioUploader;

use Illuminate\Http\UploadedFile;

interface AudioUploaderContract{

    /**
     * Save the audio file for chat message.
     *
     * @param int|string $conversationId
     * @return string filename
     */
    public function saveChatAudio($conversationId);
}