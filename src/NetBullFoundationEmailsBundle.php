<?php

namespace NetBull\FoundationEmailsBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use NetBull\FoundationEmailsBundle\DependencyInjection\NetBullFoundationEmailsExtension;

class NetBullFoundationEmailsBundle extends Bundle
{
    /**
     * @return NetBullFoundationEmailsExtension
     */
    public function getContainerExtension(): NetBullFoundationEmailsExtension
    {
        return new NetBullFoundationEmailsExtension();
    }
}
