<?php

namespace Germ\Email;

use Symfony\Component\Translation\TranslatorInterface;

class Mailer
{
    private $twig;
    private $mailer;
    private $translator;
    private $emailFrom;

    public function __construct(
        \Twig_Environment $twig,
        \Swift_Mailer $mailer,
        TranslatorInterface $translator
    ) {
        $this->twig = $twig;
        $this->mailer = $mailer;
        $this->translator = $translator;
    }

    public function setEmailFrom($emailFrom)
    {
        $this->from = $emailFrom;
    }

    public function sendEmailChangePassword($person, $password)
    {
        $message = (new \Swift_Message($this->translator->trans('Your church account')))
            ->setFrom($this->emailFrom)
            ->setTo($person['email'])
            ->setBody(
                $this->twig->render(
                    'Germ:Emails:newPassword.html.twig',
                    [
                        'person' => $person,
                        'password' => $password,
                    ]
                ),
                'text/html'
            );
        $this->mailer->send($message);
    }
}
