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
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\taoQtiItem\model\qti\metadata\ontology;

use core_kernel_classes_Property as Property;
use core_kernel_classes_Resource as Resource;
use core_kernel_classes_Triple as Triple;
use oat\generis\model\OntologyAwareTrait;
use oat\generis\model\OntologyRdf;
use oat\generis\model\OntologyRdfs;
use oat\taoQtiItem\model\qti\metadata\imsManifest\classificationMetadata\ClassificationEntryMetadataValue;
use oat\taoQtiItem\model\qti\metadata\imsManifest\classificationMetadata\ClassificationMetadataValue;
use oat\taoQtiItem\model\qti\metadata\imsManifest\classificationMetadata\ClassificationSourceMetadataValue;
use oat\taoQtiItem\model\qti\metadata\MetadataExtractionException;
use oat\taoQtiItem\model\qti\metadata\MetadataExtractor;
use tao_helpers_Uri;
use taoItems_models_classes_ItemsService;
use taoTests_models_classes_TestsService;

class LabelBasedLomOntologyClassificationExtractor implements MetadataExtractor
{
    use OntologyAwareTrait;

    public static $excludedProperties = [
        OntologyRdf::RDF_TYPE,
        taoItems_models_classes_ItemsService::PROPERTY_ITEM_CONTENT,
        taoItems_models_classes_ItemsService::PROPERTY_ITEM_MODEL,
        taoTests_models_classes_TestsService::PROPERTY_TEST_TESTMODEL,
        taoTests_models_classes_TestsService::PROPERTY_TEST_CONTENT,
    ];

    /**
     * Extract resource metadata and transform it to ClassificationMetadataValue
     *
     * @param Resource $resource
     *
     * @return array
     *
     * @throws MetadataExtractionException
     * @throws \oat\tao\model\metadata\exception\writer\MetadataWriterException
     */
    public function extract($resource)
    {
        if (!$resource instanceof Resource) {
            throw new MetadataExtractionException(
                __('The given target is not an instance of core_kernel_classes_Resource')
            );
        }

        $resourceUri = $resource->getUri();
        $identifier = tao_helpers_Uri::getUniqueId($resourceUri);
        $metadata = [
            $identifier => []
        ];

        /** @var Triple $triple */
        foreach ($resource->getRdfTriples() as $triple) {

            /** @var Property $property */
            $property = $this->getProperty($triple->predicate);
            $value = $this->getResource($triple->object)->getLabel() ?? $triple->object;
            $propertyUri = $property->getUri();

            if (
                trim($value) !== ''
                && $property->isProperty()
                && !in_array($propertyUri, self::$excludedProperties)
            ) {
                $metadata[$identifier][] = new ClassificationMetadataValue(
                    new ClassificationSourceMetadataValue($resourceUri, $propertyUri),
                    [
                        new ClassificationEntryMetadataValue($resourceUri, $value)
                    ]
                );
            }
        }

        if (empty($metadata[$identifier])) {
            return [];
        }

        return $metadata;
    }
}
