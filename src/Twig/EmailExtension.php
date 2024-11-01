<?php

namespace NetBull\FoundationEmailsBundle\Twig;

use DOMDocument;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Finder\Finder;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Pinky;
use XSLTProcessor;

class EmailExtension extends AbstractExtension
{
    /**
     * @var string|null
     */
	private ?string $customInkyPath;

    /**
     * @param ParameterBagInterface $parameterBag
     */
	public function __construct(ParameterBagInterface $parameterBag)
	{
		$this->customInkyPath = $parameterBag->get('netbull_foundation_emails.custom_inky_path');
	}

	/**
	 * @return TwigFunction[]
	 */
	public function getFunctions(): array
	{
		return [
			new TwigFunction('css_source', [$this, 'getCssSource']),
		];
	}

	/**
	 * @return TwigFilter[]
	 */
	public function getFilters(): array
	{
		return [
			new TwigFilter('custom_inky_to_html', [$this, 'parseInky'], ['is_safe' => ['html']]),
		];
	}

    /**
     * @param string|null $file
     * @return string
     */
	public function getCssSource(string $file = null): string
	{
		$source = '';
		if (is_null($file)) {
		    $file = __DIR__.'/../Resources/build/email.css';
        }
        if (file_exists($file)) {
            $source .= file_get_contents($file, false, null);
        }

		return $source;
	}

    /**
     * @return array
     */
	private function getProcessors(): array
    {
        $processors = Pinky\createInkyProcessor();

        if (is_null($this->customInkyPath)) {
            return $processors;
        }

        $finder = new Finder();
        $finder->in($this->customInkyPath)
            ->name('*.xsl')
            ->files();

        $security = XSL_SECPREF_READ_FILE | XSL_SECPREF_READ_NETWORK | XSL_SECPREF_DEFAULT;
        foreach ($finder as $file) {
            $custom = new DOMDocument();
            $custom->load($file->getPathname());

            $customProcessor = new XSLTProcessor();
            $customProcessor->setSecurityPrefs($security);
            $customProcessor->importStylesheet($custom);

            $processors[] = $customProcessor;
        }

        return $processors;
    }

	public function parseInky(string $body): string
	{
		return false === ($html = Pinky\transformWithProcessor($this->getProcessors(), Pinky\loadTemplateString($body))->saveHTML()) ? '' : $html;
	}
}
