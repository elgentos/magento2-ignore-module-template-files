<?php

namespace Elgentos\IgnoreModuleTemplateFiles\Plugin;



use Magento\Framework\View\Design\ThemeInterface;
use Magento\Framework\View\File\CollectorInterface;

class ResolveNoFiles
{
    public function aroundGetFiles(CollectorInterface $subject, callable $target, ThemeInterface $theme, $filePath): array
    {
        return [];
    }
}
