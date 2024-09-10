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
 * Copyright (c) 2024 (original work) Open Assessment Technologies SA.
 */

declare(strict_types=1);

namespace oat\taoQtiItem\model\Translation\ServiceProvider;

use oat\generis\model\data\Ontology;
use oat\generis\model\DependencyInjection\ContainerServiceProviderInterface;
use oat\tao\model\featureFlag\FeatureFlagChecker;
use oat\taoItems\model\Translation\Form\Modifier\TranslationFormModifierProxy;
use oat\taoQtiItem\model\qti\Service;
use oat\taoQtiItem\model\Translation\Form\Modifier\TranslationFormModifier;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

class TranslationServiceProvider implements ContainerServiceProviderInterface
{
    public function __invoke(ContainerConfigurator $configurator): void
    {
        $services = $configurator->services();

        $services
            ->set(TranslationFormModifier::class, TranslationFormModifier::class)
            ->args([
                service(Ontology::SERVICE_ID),
                service(Service::class),
                service(FeatureFlagChecker::class),
            ]);

        $services = $configurator->services();

        $services
            ->get(TranslationFormModifierProxy::class)
            ->call(
                'addModifier',
                [
                    service(TranslationFormModifier::class),
                ]
            );
    }
}
