<?php

namespace Elgentos\IgnoreModuleTemplateFiles\Plugin\Layout\File\Collector;

use Magento\Framework\View\Design\ThemeInterface;
use Magento\Framework\View\File\CollectorInterface;
use Magento\Framework\View\File\FileList\Factory;
use Magento\Framework\View\Layout\File\Collector\Aggregated as MagentoFrameworkAggregated;

class Aggregated
{
    public function __construct(
        private Factory $fileListFactory
    ) {}

    public function aroundGetFiles(MagentoFrameworkAggregated $target, \closure $proceed, ThemeInterface $theme, $filePath): array
    {
        try {
            $property = new \ReflectionProperty(MagentoFrameworkAggregated::class, 'themeFiles');
            $property->setAccessible(true);

            /** @var CollectorInterface $themeFiles */
            $themeFiles = $property->getValue($target);
        } catch (\ReflectionException $e) {
            return $proceed($theme, $filePath);
        }

        $list = $this->fileListFactory->create();
        foreach ($theme->getInheritedThemes() as $currentTheme) {
            $list->add($themeFiles->getFiles($currentTheme, $filePath));
        }

        return $list->getAll();
    }
}
