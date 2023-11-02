<?php
namespace App\Queues;

use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Transport\SendmailTransport;
use Symfony\Component\Mime\Email;

class SendEmail {

    public static function handle(array $payload) : bool
    {
        $transport = Transport::fromDsn(config('app.mailerDsn')); 
        $mailer = new Mailer($transport); 
        $email = (new Email()) 
                ->from($payload['from'])
                ->to($payload['to'])
                ->cc($payload['cc'])
                ->bcc($payload['bcc'])
                ->priority(Email::PRIORITY_HIGHEST)
                ->subject($payload['subject'])
                ->text($payload['body']);
        try {
            $mailer->send($email);
            return true;
        } catch (TransportException $e) {
            logger('error', $e->getMessage());
            return false;
        }
    }

}