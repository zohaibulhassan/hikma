<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class YourCustomMailClass extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $body;
    /**
     * Create a new message instance.
     *
     * @param string $subject
     * @param string $body
     * @return void
    */

    public function __construct($subject, $body)
    {
        $this->subject = $subject;
        $this->body = $body;
    }
    
    /**
     * Build the message.
     *
     * @return $this
     */
     public function build()
     {
         return $this->from('credentials@hikmahinstitute.org.pk')
             ->subject($this->subject)
             ->view('emails.custom')
             ->with('body', $this->body);
     }
 }