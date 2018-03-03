<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Dotpay\Payment\Helper;

use Magento\Framework\Composer\ComposerInformation;
use Magento\Framework\Module\ModuleList;
use Magento\Framework\Module\PackageInfoFactory;

/**
 * Module grid
 */
class Module
{
    /**
     * Const for unknown package name and version
     */
    const UNKNOWN_PACKAGE_NAME = 'unknown';
    const UNKNOWN_VERSION = '—';

    /**
     * @var ComposerInformation
     */
    private $composerInformation;

    /**
     * Module package info
     *
     * @var \Magento\Framework\Module\PackageInfo
     */
    private $packageInfo;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * Full Module info
     *
     * @var \Magento\Framework\Module\FullModuleList
     */
    private $fullModuleList;

    /**
     * Module info
     *
     * @var ModuleList
     */
    private $moduleList;

    /**
     * @param ComposerInformation $composerInformation
     * @param \Magento\Framework\Module\FullModuleList $fullModuleList
     * @param ModuleList $moduleList
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        ComposerInformation $composerInformation,
        \Magento\Framework\Module\FullModuleList $fullModuleList,
        ModuleList $moduleList,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->composerInformation = $composerInformation;
        $this->fullModuleList = $fullModuleList;
        $this->moduleList = $moduleList;
        $this->objectManager = $objectManager;
    }

    /**
     * Get list of installed modules (composer + direct installation)
     *
     * @return array/null
     */
    public function getInfo($moduleName)
    {
        $this->packageInfo = $this->objectManager->create(PackageInfoFactory::class)->create();

        $items = array_replace_recursive(
            $this->getModuleListFromComposer(),
            $this->getFullModuleList()
        );

        $items = $this->addRequiredBy($this->addGeneralInfo($items));

        foreach($items as $item) {
            if($item['moduleName'] === $moduleName) {
                return $item;
            }
        }
        return null;
    }

    /**
     * Get module list from composer
     *
     * @return array
     */
    private function getModuleListFromComposer()
    {
        return array_filter(
            $this->composerInformation->getInstalledMagentoPackages(),
            function ($item) {
                return $item['type'] === ComposerInformation::MODULE_PACKAGE_TYPE;
            }
        );
    }

    /**
     * Get full module list
     *
     * @return array
     */
    private function getFullModuleList()
    {
        return $this->getModulesInfo(
            $this->fullModuleList->getNames()
        );
    }

    /**
     * Add all modules, extensions, metapackages a module required by
     *
     * @param array $items
     * @return array
     */
    private function addRequiredBy(array $items)
    {
        foreach ($items as $key => $item) {
            $items[$key]['requiredBy'] = $item['name'] != self::UNKNOWN_PACKAGE_NAME ?
                $this->addGeneralInfo(
                    $this->getModulesInfo(
                        $this->packageInfo->getRequiredBy($item['name'])
                    )
                ) : [];
        }

        return $items;
    }

    /**
     * Get modules info
     *
     * @param array $moduleList
     * @return array
     */
    private function getModulesInfo(array $moduleList)
    {
        $result = [];
        foreach ($moduleList as $moduleName) {
            $packageName = $this->packageInfo->getPackageName($moduleName);
            $key = $packageName ?: $moduleName;
            $result[$key] = [
                'name' => $packageName ?: self::UNKNOWN_PACKAGE_NAME,
                'moduleName' => $moduleName,
                'type' => ComposerInformation::MODULE_PACKAGE_TYPE,
                'version' => $this->packageInfo->getVersion($moduleName) ?: self::UNKNOWN_VERSION,
            ];
        }

        return $result;
    }

    /**
     * Add general info to result array
     *
     * @param array $items
     * @return array
     */
    private function addGeneralInfo(array $items)
    {
        foreach ($items as &$item) {
            $item['moduleName'] = $item['moduleName'] ?: $this->packageInfo->getModuleName($item['name']);
            $item['enable'] = $this->moduleList->has($item['moduleName']);
            $vendorSource = $item['name'] == self::UNKNOWN_PACKAGE_NAME ? $item['moduleName'] : $item['name'];
            $item['vendor'] = ucfirst(current(preg_split('%[/_]%', $vendorSource)));
        }

        return array_values($items);
    }
}