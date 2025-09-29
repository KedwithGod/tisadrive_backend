namespace App\Services\Uploaders;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AudioUploader
{
    protected UploadedFile $file;
    protected object $config;
    protected $hashGenerator;

    public function __construct(UploadedFile $file, $hashGenerator = null)
    {
        $this->file = $file;
        $this->hashGenerator = $hashGenerator ?? new \Illuminate\Support\Str;
    }

    public function validateAudio(): void
    {
        if (!$this->file->isValid()) {
            throw ValidationException::withMessages(['audio' => 'Invalid audio upload.']);
        }

        $mime = $this->file->getMimeType();
        if (!in_array($mime, ['audio/webm', 'audio/mpeg', 'audio/ogg', 'audio/wav'])) {
            throw ValidationException::withMessages(['audio' => 'Unsupported audio type.']);
        }
    }

    /**
     * Save the audio file for chat message.
     *
     * @param int|string $conversationId
     * @return string filename
     */
    public function saveChatAudio($conversationId): string
    {
        $this->validateAudio();

        $path = config('chat.upload.audio_path', 'chat_audios');
        $filePath = file_path($path, $conversationId);

        $extension = $this->file->getClientOriginalExtension();
        $filename = ($this->hashGenerator)::random(40) . '.' . $extension;

        Storage::makeDirectory($filePath);

        Storage::putFileAs($filePath, $this->file, $filename);

        return $filePath . '/' . $filename;
    }
}
