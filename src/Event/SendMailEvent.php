<?php

namespace NetBull\FoundationEmailsBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class SendMailEvent
 * @package NetBull\FoundationEmailsBundle\Event
 */
class SendMailEvent extends Event
{
	const TYPE_PUBLIC = 'public';
	const TYPE_SYSTEM = 'system';

	/**
	 * @var string
	 */
	private string $type = self::TYPE_PUBLIC;

	/**
	 * @var string
	 */
	private string $template;

	/**
	 * @var array
	 */
	private array $params;

	/**
	 * @var string
	 */
	private string $subject;

	/**
	 * @var array|string
	 */
	private $addresses;

	/**
	 * @var array
	 */
	private array $attachments;

	/**
	 * SendMailEvent constructor.
	 * @param string $template
	 * @param array $params
	 * @param string $subject
	 * @param null $addresses
	 * @param string|null $type
	 * @param array $attachments
	 */
	public function __construct(string $template, array $params, string $subject, $addresses = null, string $type = null, array $attachments = [])
	{
		$this->template = $template;
		$this->params = $params;
		$this->subject = $subject;
		$this->addresses = $addresses;
		if ($type) {
			$this->type = $type;
		}
		$this->attachments = $attachments;
	}

	/**
	 * @return string
	 */
	public function getType(): string
	{
		return $this->type;
	}

	/**
	 * @return string
	 */
	public function getTemplate(): string
	{
		return $this->template;
	}

	/**
	 * @return array
	 */
	public function getParams(): array
	{
		return $this->params;
	}

	/**
	 * @return string
	 */
	public function getSubject(): string
	{
		return $this->subject;
	}

	/**
	 * @return array|string
	 */
	public function getAddresses()
	{
		return $this->addresses;
	}

	/**
	 * @return array
	 */
	public function getAttachments(): array
	{
		return $this->attachments;
	}

	/**
	 * @param array $attachment
	 * @return $this
	 */
	public function addAttachment(array $attachment): SendMailEvent
	{
		$this->attachments[] = $attachment;
		return $this;
	}
}
