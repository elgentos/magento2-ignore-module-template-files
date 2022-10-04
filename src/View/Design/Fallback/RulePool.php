<?php

namespace Elgentos\IgnoreModuleTemplateFiles\View\Design\Fallback;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\View\Design\Fallback\Rule;
use Magento\Framework\View\Design\Fallback\Rule\Composite;
use Magento\Framework\View\Design\Fallback\Rule\RuleInterface;
use Magento\Framework\View\Design\Fallback\RulePool as MagentoRulePool;

class RulePool extends MagentoRulePool
{

    private Rule\ModularSwitchFactory $modularSwitchFactory;
    private Rule\ThemeFactory $themeFactory;
    private Rule\SimpleFactory $simpleFactory;

    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        Rule\SimpleFactory $simpleFactory,
        Rule\ThemeFactory $themeFactory,
        Rule\ModuleFactory $moduleFactory,
        Rule\ModularSwitchFactory $modularSwitchFactory
    ) {
        parent::__construct($filesystem, $simpleFactory, $themeFactory, $moduleFactory, $modularSwitchFactory);
        $this->modularSwitchFactory = $modularSwitchFactory;
        $this->themeFactory = $themeFactory;
        $this->simpleFactory = $simpleFactory;
    }

    /**
     * Retrieve newly created fallback rule for template files
     *
     * @return RuleInterface
     */
    protected function createTemplateFileRule()
    {
        return $this->modularSwitchFactory->create(
            [
                'ruleNonModular' =>
                    $this->themeFactory->create(
                        ['rule' => $this->simpleFactory->create(['pattern' => "<theme_dir>/templates"])]
                    ),
                'ruleModular' => new Composite(
                    [
                        $this->themeFactory->create(
                            ['rule' => $this->simpleFactory->create(['pattern' => "<theme_dir>/<module_name>/templates"])]
                        ),
//                    $this->moduleFactory->create(
//                        ['rule' => $this->simpleFactory->create(['pattern' => "<module_dir>/view/<area>/templates"])]
//                    ),
//                    $this->moduleFactory->create(
//                        ['rule' => $this->simpleFactory->create(['pattern' => "<module_dir>/view/base/templates"])]
//                    ),
                    ]
                )
            ]
        );
    }

    /**
     * Retrieve newly created fallback rule for dynamic view files
     *
     * @return RuleInterface
     */
    protected function createFileRule()
    {
        return $this->modularSwitchFactory->create(
            [
                'ruleNonModular' => $this->themeFactory->create(
                    ['rule' => $this->simpleFactory->create(['pattern' => "<theme_dir>"])]
                ),
                'ruleModular' => new Composite(
                    [
                        $this->themeFactory->create(
                            ['rule' => $this->simpleFactory->create(['pattern' => "<theme_dir>/<module_name>"])]
                        ),
//                    $this->moduleFactory->create(
//                        ['rule' => $this->simpleFactory->create(['pattern' => "<module_dir>/view/<area>"])]
//                    ),
//                    $this->moduleFactory->create(
//                        ['rule' => $this->simpleFactory->create(['pattern' => "<module_dir>/view/base"])]
//                    ),
                    ]
                )
            ]
        );
    }

    /**
     * Retrieve newly created fallback rule for static view files, such as CSS, JavaScript, images, etc.
     *
     * @return RuleInterface
     */
    protected function createViewFileRule()
    {
        $libDir = rtrim($this->filesystem->getDirectoryRead(DirectoryList::LIB_WEB)->getAbsolutePath(), '/');
        return $this->modularSwitchFactory->create(
            [
                'ruleNonModular' => new Composite(
                    [
                        $this->themeFactory->create(
                            [
                                'rule' =>
                                    new Composite(
                                        [
                                            $this->simpleFactory
                                                ->create([
                                                    'pattern' => "<theme_dir>/web/i18n/<locale>",
                                                    'optionalParams' => ['locale']
                                                ]),
                                            $this->simpleFactory
                                                ->create(['pattern' => "<theme_dir>/web"]),
                                            $this->simpleFactory
                                                ->create([
                                                    'pattern' => "<theme_pubstatic_dir>",
                                                    'optionalParams' => ['theme_pubstatic_dir']
                                                ]),
                                        ]
                                    )
                            ]
                        ),
                        $this->simpleFactory->create(['pattern' => $libDir]),
                    ]
                ),
                'ruleModular' => new Composite(
                    [
                        $this->themeFactory->create(
                            [
                                'rule' =>
                                    new Composite(
                                        [
                                            $this->simpleFactory->create(
                                                [
                                                    'pattern' => "<theme_dir>/<module_name>/web/i18n/<locale>",
                                                    'optionalParams' => ['locale'],
                                                ]
                                            ),
                                            $this->simpleFactory->create(
                                                ['pattern' => "<theme_dir>/<module_name>/web"]
                                            ),
                                        ]
                                    )
                            ]
                        ),
//                    $this->moduleFactory->create(
//                        ['rule' => $this->simpleFactory->create(
//                            [
//                                'pattern' => "<module_dir>/view/<area>/web/i18n/<locale>",
//                                'optionalParams' => ['locale']
//                            ]
//                        )]
//                    ),
//                    $this->moduleFactory->create(
//                        ['rule' => $this->simpleFactory->create(
//                            [
//                                'pattern' => "<module_dir>/view/base/web/i18n/<locale>",
//                                'optionalParams' => ['locale']
//                            ]
//                        )]
//                    ),
//                    $this->moduleFactory->create(
//                        ['rule' => $this->simpleFactory->create(['pattern' => "<module_dir>/view/<area>/web"])]
//                    ),
//                    $this->moduleFactory->create(
//                        ['rule' => $this->simpleFactory->create(['pattern' => "<module_dir>/view/base/web"])]
//                    ),
                    ]
                )
            ]
        );
    }

    /**
     * Retrieve newly created fallback rule for email templates.
     *
     * Emails are only loaded in a modular context, so a non-modular rule is not specified.
     *
     * @return RuleInterface
     */
    protected function createEmailTemplateFileRule()
    {
        return new Composite(
            [
                $this->themeFactory->create(
                    [
                        'rule' =>
                            $this->simpleFactory->create(
                                ['pattern' => "<theme_dir>/<module_name>/email"]
                            )
                    ]
                ),
//                $this->moduleFactory->create(
//                    ['rule' => $this->simpleFactory->create(['pattern' => "<module_dir>/view/<area>/email"])]
//                ),
            ]
        );
    }

}
