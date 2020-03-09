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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\taoQtiItem\model;

use common_exception_Error;
use common_report_Report;
use core_kernel_classes_Resource;
use Exception;
use oat\taoQtiItem\model\pack\QtiItemPacker;
use oat\taoQtiItem\model\qti\exception\XIncludeException;
use oat\taoQtiItem\model\qti\Service;
use oat\taoQtiItem\model\qti\Element;
use tao_helpers_Xml;
use tao_models_classes_service_StorageDirectory;
use oat\taoQtiItem\model\portableElement\PortableElementService;
use taoItems_models_classes_CompilationFailedException;

/**
 * The QTI Json Item Compiler
 *
 * @access public
 * @author Antoine Robin
 * @package taoItems
 */
class QtiJsonItemCompiler extends QtiItemCompiler
{

    const ITEM_FILE_NAME = 'item.json';
    const VAR_ELT_FILE_NAME = 'variableElements.json';
    const METADATA_FILE_NAME = 'metadataElements.json';
    const PORTABLE_ELEMENT_FILE_NAME = 'portableElements.json';

    /**
     * @var string json from the item packed
     */
    private $itemJson;

    /**
     * Generate JSON version of item
     * @return common_report_Report
     * @throws taoItems_models_classes_CompilationFailedException
     */
    public function compileJson()
    {
        $report = $this->internalCompile();
        if ($report->getType() == common_report_Report::TYPE_SUCCESS) {
            // replace instances with strign identifiers
            list($item, $publicDirectory, $privateDirectory) = $report->getData();
            $report->setData([$item->getUri(), $publicDirectory->getId(), $privateDirectory->getId()]);
        }
        return $report;
    }

    /**
     * Deploy all the required files into the provided directories
     *
     * @param core_kernel_classes_Resource $item
     * @param string $language
     * @param tao_models_classes_service_StorageDirectory $publicDirectory
     * @param tao_models_classes_service_StorageDirectory $privateDirectory
     * @return common_report_Report
     */
    protected function deployQtiItem(
        core_kernel_classes_Resource $item,
        $language,
        tao_models_classes_service_StorageDirectory $publicDirectory,
        tao_models_classes_service_StorageDirectory $privateDirectory
    ) {
        $qtiService = Service::singleton();


        // retrieve the media assets
        try {
            $qtiItem = $this->retrieveAssets($item, $language, $publicDirectory);

            // @todo find a better solution to eliminate empty items.
            /*if (count($qtiItem->getBody()->getElements()) === 0) {
                return new common_report_Report(common_report_Report::TYPE_ERROR, 'The item has an empty body.');
            }*/

            $this->compileItemIndex($item->getUri(), $qtiItem, $language);

            //store variable qti elements data into the private directory
            $variableElements = $qtiService->getVariableElements($qtiItem);
            $privateDirectory->write($language . DIRECTORY_SEPARATOR . self::VAR_ELT_FILE_NAME, json_encode($variableElements));

            //create the item.json file in private directory
            $itemPacker = new QtiItemPacker();
            $itemPacker->setReplaceXinclude(false);
            $itemPack = $itemPacker->packQtiItem($item, $language, $qtiItem, $publicDirectory);
            $this->itemJson = $itemPack->JsonSerialize();
            //get the filtered data to avoid cheat
            $data = $qtiItem->getDataForDelivery();
            $data = $this->convertXmlAttributes($data);
            $this->itemJson['data'] = $data['core'];
            $metadata = $this->getMetadataProperties();

            $privateDirectory->write($language . DIRECTORY_SEPARATOR . self::ITEM_FILE_NAME, json_encode($this->itemJson));
            $privateDirectory->write($language . DIRECTORY_SEPARATOR . self::METADATA_FILE_NAME, json_encode($metadata));
            $privateDirectory->write($language . DIRECTORY_SEPARATOR . self::PORTABLE_ELEMENT_FILE_NAME, json_encode($this->getItemPortableElements($qtiItem)));

            return new common_report_Report(
                common_report_Report::TYPE_SUCCESS,
                __('Successfully compiled "%s"', $language)
            );
        } catch (\tao_models_classes_FileNotFoundException $e) {
            return new common_report_Report(
                common_report_Report::TYPE_ERROR,
                __('Unable to retrieve asset "%s"', $e->getFilePath())
            );
        } catch (XIncludeException $e) {
            return new common_report_Report(
                common_report_Report::TYPE_ERROR,
                $e->getUserMessage()
            );
        } catch (Exception $e) {
            return new common_report_Report(
                common_report_Report::TYPE_ERROR,
                $e->getMessage()
            );
        }
    }

    /**
     * Convert internal parameters to json if needed
     * @param $data
     * @return mixed
     * @throws common_exception_Error
     */
    protected function convertXmlAttributes($data)
    {

        if (
            is_array($data)
            && array_key_exists('core', $data)
            && is_array($data['core'])
            && array_key_exists('apipAccessibility', $data['core'])
            && $data['core']['apipAccessibility']
        ) {
            $data['core']['apipAccessibility'] = tao_helpers_Xml::to_array($data['core']['apipAccessibility']);
        }

        return $data;
    }

    /**
     * Get the portable elements data in use in the item
     * @param Element $qtiItem
     * @return array
     */
    private function getItemPortableElements(Element $qtiItem)
    {
        $portableElementService = new PortableElementService();
        $portableElementService->setServiceLocator($this->getServiceLocator());
        return [
            'pci' => $portableElementService->getPortableElementByClass(PortableElementService::PORTABLE_CLASS_INTERACTION, $qtiItem, true),
            'pic' => $portableElementService->getPortableElementByClass(PortableElementService::PORTABLE_CLASS_INFOCONTROL, $qtiItem, true)
        ];
    }

    /**
     * Get the item properties as compiled metadata
     * @return array
     */
    private function getMetadataProperties()
    {
        $triples = $this->getResource()->getRdfTriples();
        $properties = [];
        foreach ($triples as $triple) {
            $properties[$triple->predicate] = $triple->object;
        }
        //we also include a shortcut to the item URI
        $properties['@uri'] = $this->getResource()->getUri();
        return $properties;
    }
}
