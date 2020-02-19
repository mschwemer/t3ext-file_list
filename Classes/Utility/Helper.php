<?php
/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace Causal\FileList\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Helper class for the 'file_list' extension.
 *
 * @category    Utility
 * @package     TYPO3
 * @subpackage  tx_filelist
 * @author      Xavier Perseguers <xavier@causal.ch>
 * @license     http://www.gnu.org/copyleft/gpl.html
 */
class Helper
{

    /**
     * Casts an object.
     *
     * @param object $object
     * @param string $toClass
     * @return bool|object
     */
    static public function cast($object, $toClass)
    {
        if (class_exists($toClass)) {
            $objIn = serialize($object);
            $classIn = get_class($object);
            $prefixChars = strlen((string)strlen($classIn)) + 6 + strlen($classIn);
            $objOut = 'O:' . strlen($toClass) . ':"' . $toClass . '":' . substr($objIn, $prefixChars);
            return unserialize($objOut);
        } else {
            return false;
        }
    }

    /**
     * Filters out inaccessible files for the current Frontend user.
     *
     * @param \TYPO3\CMS\Core\Resource\File[] $files
     * @return \TYPO3\CMS\Core\Resource\File[]
     */
    static public function filterInaccessibleFiles(array $files): array
    {
        if (TYPO3_MODE !== 'FE') {
            return $files;
        }

        $filteredFiles = [];

        $userGroups = GeneralUtility::intExplode(',', $GLOBALS['TSFE']->gr_list, true);

        foreach ($files as $file) {
            $isVisible = (bool)$file->getProperty('visible');
            if (!$isVisible) continue;

            $accessGroups = $file->getProperty('fe_groups');
            if (!empty($accessGroups)) {
                $accessGroups = GeneralUtility::intExplode(',', $accessGroups, true);
                if (empty(array_intersect($accessGroups, $userGroups))) {
                    continue;
                }
            }

            $filteredFiles[] = $file;
        }

        return $filteredFiles;
    }

}
