<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2013-2021 (original work) Open Assessment Technologies SA;
 */

namespace oat\taoQtiItem\helpers;

use core_kernel_classes_Resource as RdfResource;

class CssHelper
{

    private static function _buildWarning(): string
    {
        return " /* Do not edit */";
    }

    /**
     * Stores an css array in the file
     */
    public static function saveCssFile(RdfResource $item, string $lang, string $styleSheetPath, array $cssArr): bool
    {
        $directory = \taoItems_models_classes_ItemsService::singleton()->getItemDirectory($item, $lang);

        $file = $directory->getFile($styleSheetPath);

        if (empty($cssArr) && $file->exists()) {
            return $file->delete();
        }

        $css = self::_buildWarning() . self::arrayToCss($cssArr);
        return $file->put($css);
    }

    /**
     * Download existing CSS file
     */
    public static function downloadCssFile(RdfResource $item, string $lang, string $styleSheetPath): ?string
    {
        $directory = \taoItems_models_classes_ItemsService::singleton()->getItemDirectory($item, $lang);
        $file = $directory->getFile($styleSheetPath);
        if ($file->exists()) {
            return $file->read();
        }
        return null;
    }

    /**
     * Convert incoming CSS to CSS array
     * This CSS must have the format generated by the online editor
     */
    public static function cssToArray(string $css): array
    {
        if (!$css) {
            return [];
        }
        $css = str_replace(self::_buildWarning(), '', $css);
        $oldCssArr = explode("\n", $css);
        $newCssArr = [];
        foreach ($oldCssArr as $line) {
            if (false === strpos($line, '{')) {
                continue;
            }

            preg_match('~(?P<selector>[^{]+)(\{)(?P<rules>[^}]+)\}~', $line, $matches);

            foreach ($matches as $key => &$match) {
                if (is_numeric($key)) {
                    continue;
                }
                $match = trim($match);
                if ($key === 'rules') {
                    $ruleSet = array_filter(array_map('trim', explode(';', $match)));
                    $match = [];
                    foreach ($ruleSet as $rule) {
                        $rule = array_map('trim', explode(':', $rule));
                        $match[$rule[0]] = $rule[1];
                    }
                }
            }

            $newCssArr[$matches['selector']] = $matches['rules'];
        }
        return $newCssArr;
    }

    /**
     * Convert incoming CSS array to proper CSS
     */
    public static function arrayToCss(array $array): string
    {
        $css = '';

        // rebuild CSS
        foreach ($array as $key1 => $value1) {
            $css .= $key1 . '{';

            foreach ($value1 as $key2 => $value2) {
                // in the case that the code is embedded in a media query
                if (is_array($value2)) {
                    foreach ($value2 as $value3) {
                        $css .= $key2 . '{';
                        foreach ($value3 as $mProp) {
                            $css .= $mProp . ':' . $value3 . ';';
                        }
                        $css .= '}';
                    }
                } // regular selectors
                else {
                    $css .= $key2 . ':' . $value2 . ';';
                }
            }
            $css .= "}\n";
        }
        return $css;
    }

    /**
     * Loads the content of a css file into a css array
     * Returns an empty stylesheet if it does not yet exist
     */
    public static function loadCssFile(RdfResource $item, string $lang, string $styleSheet): array
    {
        $directory = \taoItems_models_classes_ItemsService::singleton()->getItemDirectory($item, $lang);

        // no user style sheet has been created yet
        $file = $directory->getFile($styleSheet);
        if (!$file->exists()) {
            \common_Logger::d('Stylesheet ' . $styleSheet . ' does not exist yet, returning empty array');
            return [];
        }

        return self::cssToArray($file->read());
    }
}
