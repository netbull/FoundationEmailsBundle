<?php

namespace NetBull\FoundationEmailsBundle\Twig;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Symfony\WebpackEncoreBundle\Asset\EntrypointLookupInterface;
use Twig\TwigFunction;

/**
 * Class EmailWebpackEncoreExtension
 * @package NetBull\FoundationEmailsBundle\Twig
 */
class EmailWebpackEncoreExtension extends EmailExtension implements ServiceSubscriberInterface
{
    /**
     * @var EntrypointLookupInterface
     */
    private EntrypointLookupInterface $entrypointLookup;

    /**
     * @var string
     */
    private string $publicDir;

    /**
     * EmailExtensionWebpackEncore constructor.
     * @param EntrypointLookupInterface $entrypointLookup
     * @param ParameterBagInterface $parameterBag
     */
    public function __construct(ParameterBagInterface $parameterBag, EntrypointLookupInterface $entrypointLookup)
    {
        parent::__construct($parameterBag);

        $this->entrypointLookup = $entrypointLookup;
        $this->publicDir = $parameterBag->get('kernel.project_dir').'/public';
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
     * @param string|null $file Entry name
     * @return string
     */
    public function getCssSource(string $file = null): string
    {
        $context = null;

        $source = parent::getCssSource($file);
        if ($file) {
            $files = $this->entrypointLookup->getCssFiles($file);
            foreach ($files as $f) {
                if (0 === strpos($f, '/')) {
                    $f = $this->publicDir.$f;
                }

                if (false === strpos($f, 'http') && !file_exists($f)) {
                    continue;
                }

                $source .= file_get_contents($f, false, $context);
            }

            $this->entrypointLookup->reset();
        }
        return $source;
    }

    /**
     * @return string[]
     */
    public static function getSubscribedServices(): array
    {
        return [
            EntrypointLookupInterface::class,
        ];
    }
}
