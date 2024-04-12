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
 * Copyright (c) 2022 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\taoQtiItem\model\qti\ServiceProvider;

use oat\generis\model\DependencyInjection\ContainerServiceProviderInterface;
use oat\taoQtiItem\model\presentation\web\UpdateMetadataRequestHandler;
use oat\taoQtiItem\model\qti\metadata\imsManifest\MetaMetadataExtractor;
use oat\taoQtiItem\model\qti\metadata\imsManifest\MetaMetadataValidator;
use oat\taoQtiTest\models\classes\metadata\ChecksumGenerator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

class MetadataServiceProvider implements ContainerServiceProviderInterface
{
    public function __invoke(ContainerConfigurator $configurator): void
    {
        $services = $configurator->services();
        $services->set(UpdateMetadataRequestHandler::class, UpdateMetadataRequestHandler::class)
            ->public();

        $services->set(MetaMetadataExtractor::class, MetaMetadataExtractor::class)
            ->public();

        $services->set(MetaMetadataValidator::class, MetaMetadataValidator::class)
            ->args([service(ChecksumGenerator::class)])
            ->public();
    }
}
