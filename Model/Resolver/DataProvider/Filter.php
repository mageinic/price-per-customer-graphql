<?php
/**
 * MageINIC
 * Copyright (C) 2023 MageINIC <support@mageinic.com>
 *
 * NOTICE OF LICENSE
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see https://opensource.org/licenses/gpl-3.0.html.
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category MageINIC
 * @package MageINIC_PricePerCustomerGraphQl
 * @copyright Copyright (c) 2023 MageINIC (https://www.mageinic.com/)
 * @license https://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author MageINIC <support@mageinic.com>
 */

namespace MageINIC\PricePerCustomerGraphQl\Model\Resolver\DataProvider;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\ConfigInterface;
use Magento\Framework\GraphQl\Query\Resolver\Argument\FieldEntityAttributesInterface;

/**
 * Filter Resolver Model
 */
class Filter implements FieldEntityAttributesInterface
{
    /**
     * define filter input name
     */
    public const CUSTOMER_PRICE_FILTER_INPUT = "PricePerCustomerFilterInput";

    /**
     * @var ConfigInterface
     */
    private ConfigInterface $config;

    /**
     * CategoryEntityAttributes Constructor.
     *
     * @param ConfigInterface $config
     */
    public function __construct(
        ConfigInterface $config
    ) {
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function getEntityAttributes(): array
    {
        $filterFields = [];
        $categoryAttributeFilterSchema = $this->config->getConfigElement(
            self::CUSTOMER_PRICE_FILTER_INPUT
        );
        $categoryAttributeFilterFields = $categoryAttributeFilterSchema->getFields();
        /** @var Field $filterField */
        foreach ($categoryAttributeFilterFields as $filterField) {
            $filterFields[$filterField->getName()] = [
                'type' => 'String',
                'fieldName' => $filterField->getName(),
            ];
        }
        return $filterFields;
    }
}
