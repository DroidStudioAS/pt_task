<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Import;

class ImportFailedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $import;
    public $errorMessage;

    public function __construct(Import $import, string $errorMessage)
    {
        $this->import = $import;
        $this->errorMessage = $errorMessage;
    }

    public function build()
    {
        return $this->from('aleksandar.smiljanic19@gmail.com')
            ->to('zakopirnicu@gmail.com')
            ->subject('Import Job Failed')
            ->view('emails.import-failed')
            ->with([
                'import' => $this->import,
                'errorMessage' => $this->errorMessage
            ]);
    }
} 