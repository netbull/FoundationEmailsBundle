<?php

namespace NetBull\FoundationEmailsBundle\Utils;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

/**
 * Class SendMail
 * @package NetBull\FoundationEmailsBundle\Utils
 */
class SendMail
{
	/**
	 * @var MailerInterface
	 */
	private MailerInterface $mailer;

	/**
	 * @var ParameterBagInterface
	 */
	private ParameterBagInterface $parameterBag;

	/**
	 * @var Email|null
	 */
	private ?Email $message;

	/**
	 * SendMail constructor.
	 * @param MailerInterface $mailer
	 * @param ParameterBagInterface $parameterBag
	 */
	public function __construct(MailerInterface $mailer, ParameterBagInterface $parameterBag)
	{
		$this->mailer = $mailer;
		$this->parameterBag = $parameterBag;
	}

	/**
	 * @param string|null $template
	 * @param array $parameters
	 * @return $this
	 */
	public function createMessage(?string $template = null, array $parameters = []): SendMail
	{
		$this->message = (new TemplatedEmail());

		if ($template) {
			$this->message->htmlTemplate($template);
			if (!empty($parameters)) {
				$this->message->context($parameters);
			}
		}

		return $this;
	}

	/**
	 * @param string|null $template
	 * @param array $parameters
	 * @return $this
	 */
	public function createSystemMessage(?string $template = null, array $parameters = []): SendMail
	{
		$this->createMessage($template, $parameters);

		if ($this->message) {
			$this->message->to(new Address($this->parameterBag->get('system_receiver'), $this->parameterBag->get('project_name')));
		}

		return $this;
	}

	/**
	 * @param string $subject
	 * @return $this
	 */
	public function setSubject(string $subject): SendMail
	{
		if (!$this->message) {
			return $this;
		}

		$this->message->subject(sprintf('=?UTF-8?B?%s?=', base64_encode($subject)));

		return $this;
	}

	/**
	 * @param string[]|string|$addresses
	 * @param string $name
	 * @return $this
	 */
	public function setTo($addresses, string $name = ''): SendMail
	{
		if (!$this->message) {
			return $this;
		}
		if (is_array($addresses)) {
			foreach ($addresses as $address) {
				$this->message->to(new Address($address, $name));
			}
		} else {
			$this->message->to(new Address($addresses, $name));
		}

		return $this;
	}

	/**
	 * @param array $attachment
	 * @return $this
	 */
	public function addAttachment(array $attachment): SendMail
	{
		if (!$this->message || empty($attachment)) {
			return $this;
		}

		list($path, $name, $mime) = $attachment;
		$this->message->attachFromPath($path, $name, $mime);

		return $this;
	}

	/**
	 * @param array $attachments
	 * @return $this
	 */
	public function setAttachments(array $attachments): SendMail
	{
		if (!$this->message || empty($attachments)) {
			return $this;
		}

		foreach ($attachments as $attachment) {
			$this->message->attach($attachment);
		}

		return $this;
	}

	/**
	 * @return int
	 */
	public function send(): int
	{
		if (!$this->message) {
			return 0;
		}

		try {
			$this->mailer->send($this->message);
			return 1;
		} catch (TransportExceptionInterface $e) {
			$this->message = null;
			return 0;
		}
	}
}
