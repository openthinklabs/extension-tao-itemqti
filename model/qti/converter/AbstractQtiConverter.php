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
 * Copyright (c) 2024 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\taoQtiItem\model\qti\converter;

use DOMDocument;
use DOMElement;
use DOMText;

abstract class AbstractQtiConverter
{
    private const QTI_22_NS = 'http://www.imsglobal.org/xsd/imsqti_v2p2';
    private const XSI_NAMESPACE = 'http://www.w3.org/2001/XMLSchema-instance';
    private const XSI_SCHEMA_LOCATION = 'xsi:schemaLocation';
    private const SCHEMA_LOCATION = 'http://www.imsglobal.org/xsd/imsqti_v2p2 ' .
    'http://www.imsglobal.org/xsd/qti/qtiv2p2/imsqti_v2p2p1.xsd';
    private const XML_NS_NAMESPACE = 'http://www.w3.org/2000/xmlns/';
    private const QUALIFIED_NAME_NS = 'xmlns';
    private const QUALIFIED_NAME_XSI = self::QUALIFIED_NAME_NS . ':xsi';

    private CaseConversionService $caseConversionService;

    public function __construct(CaseConversionService $caseConversionService)
    {
        $this->caseConversionService = $caseConversionService;
    }

    public function convert(string $filename): void
    {
        // Load the QTI XML document
        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->load($filename);
        $rootElement = $dom->firstChild;
        if ($rootElement instanceof DOMElement) {
            $this->convertRootElementsRecursively(iterator_to_array($rootElement->childNodes));
            $this->convertRootElement($rootElement);
            $dom->save($filename);
        }
    }

    private function convertRootElementsRecursively(array $children): void
    {
        foreach ($children as $child) {
            if ($child instanceof DOMText) {
                continue;
            }

            if ($child instanceof DOMElement) {
                $childNodes = null;
                if ($child->hasChildNodes()) {
                    $this->convertRootElementsRecursively(iterator_to_array($child->childNodes));
                    $childNodes = $child->childNodes;
                }
            }

            $tagName = $child->tagName;
            // When elements has child we do not want to create literal value
            $nodeValue = $child->childElementCount === 0 ? $child->nodeValue : '';

            $newElement = $child->ownerDocument->createElement(
                $this->caseConversionService->kebabToCamelCase($tagName),
                $nodeValue
            );

            if ($childNodes) {
                foreach (iterator_to_array($childNodes) as $childNode) {
                    if ($childNode instanceof DOMElement) {
                        $newElement->appendChild($childNode);
                    }
                }
            }

            $this->adjustAttributes($newElement, $child);
            $child->parentNode->replaceChild($newElement, $child);
        }
    }

    private function adjustAttributes(DOMElement $newElement, DOMElement $childNode)
    {
        foreach (iterator_to_array($childNode->attributes) as $attribute) {
            //If attribute name is not equal to nodeName it's most probably namespace attribute
            //This will be hardcoded below therefore we wish to ignore it.
            if ($attribute->name !== $attribute->nodeName) {
                continue;
            }

            // Only replace attribute names from map
            if ($attrReplacement = $this->caseConversionService->kebabToCamelCase($attribute->name)) {
                $newElement->setAttribute($attrReplacement, $attribute->value);
                continue;
            }

            $newElement->setAttribute($attribute->name, $attribute->value);
        }
    }

    private function convertRootElement(DOMElement $rootElement): void
    {
        $newElement = $rootElement->ownerDocument->createElementNS(
            self::QTI_22_NS,
            $this->caseConversionService->kebabToCamelCase($this->getRootElement())
        );

        $newElement->setAttributeNS(
            self::XSI_NAMESPACE,
            self::XSI_SCHEMA_LOCATION,
            self::XSI_NAMESPACE
        );

        $newElement->setAttributeNS(
            self::XSI_NAMESPACE,
            self::XSI_SCHEMA_LOCATION,
            self::SCHEMA_LOCATION
        );

        $newElement->setAttributeNS(
            self::XSI_NAMESPACE,
            self::XSI_SCHEMA_LOCATION,
            self::SCHEMA_LOCATION
        );

        $newElement->setAttributeNS(
            self::XML_NS_NAMESPACE,
            self::QUALIFIED_NAME_XSI,
            self::XSI_NAMESPACE
        );

        foreach (iterator_to_array($rootElement->childNodes) as $childNode) {
            $newElement->appendChild($childNode);
        }

        $this->adjustAttributes($newElement, $rootElement);
        $rootElement->parentNode->replaceChild($newElement, $rootElement);
    }

    abstract protected function getRootElement(): string;
}
