<?php

namespace NetBull\FoundationEmailsBundle\EventListener;

use NetBull\FoundationEmailsBundle\Event\SendMailEvent;
use NetBull\FoundationEmailsBundle\Utils\SendMail;

/**
 * Class MailSendListener
 * @package NetBull\FoundationEmailsBundle\EventListener
 */
class MailSendListener
{
    /**
     * @var SendMail
     */
    protected SendMail $sendMail;

    /**
     * CompanyVerifyListener constructor.
     * @param SendMail $sendMail
     */
    public function __construct(SendMail $sendMail)
    {
        $this->sendMail = $sendMail;
    }

	/**
	 * @param SendMailEvent $event
	 */
	public function __invoke(SendMailEvent $event)
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
