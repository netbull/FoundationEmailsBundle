<?php

namespace NetBull\FoundationEmailsBundle\EventListener;

use NetBull\FoundationEmailsBundle\Event\SendMailEvent;
use NetBull\FoundationEmailsBundle\Utils\SendMail;

class MailSendListener
{
    /**
     * @var SendMail
     */
    protected SendMail $sendMail;

    /**
     * @param SendMail $sendMail
     */
    public function __construct(SendMail $sendMail)
    {
        $this->sendMail = $sendMail;
    }

    /**
     * @param SendMailEvent $event
     * @return void
     */
	public function __invoke(SendMailEvent $event): void
    {
		if (SendMailEvent::TYPE_SYSTEM === $event->getType()) {
			$message = $this->sendMail->createSystemMessage($event->getTemplate(), $event->getParams());
		} else {
			$message = $this->sendMail->createMessage($event->getTemplate(), $event->getParams())
				->setTo($event->getAddresses());
		}

		foreach ($event->getAttachments() as $attachment) {
			$message->addAttachment($attachment);
		}

		$message->setSubject($event->getSubject())->send();
	}
}
