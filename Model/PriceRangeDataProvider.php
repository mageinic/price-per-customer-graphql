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

namespace MageINIC\PricePerCustomerGraphQl\Model;

use MageINIC\PricePerCustomer\Helper\Data;
use Magento\Catalog\Model\Product;
use Magento\CatalogGraphQl\Model\PriceRangeDataProvider as PriceRangeDataProviderPlugin;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\GraphQl\Model\Query\ContextInterface;

class PriceRangeDataProvider
{
    /**
     * @var Data
     */
    protected Data $helperData;

    /**
     * Price Range Data Constructor
     *
     * @param Data $helperData
     */
    public function __construct(
        Data $helperData
    ) {
        $this->helperData = $helperData;
    }

    /**
     * Price Range Data Plugin
     *
     * @param PriceRangeDataProviderPlugin $subject
     * @param array $result
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array $value
     * @return array
     * @throws GraphQlAuthorizationException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function afterPrepare(
        PriceRangeDataProviderPlugin $subject,
        array                        $result,
        ContextInterface             $context,
        ResolveInfo                  $info,
        array                        $value
    ): array {
        if (false === $context->getExtensionAttributes()->getIsCustomer()) {
            throw new GraphQlAuthorizationException(
                __('The current customer isn\'t authorized.')
            );
        }
        /** @var Product $product */
        $product = $value['model'];
        if ($this->helperData->isEnable()) {
            $customerId = $context->getUserId();
            if ($customerId) {
                $price = $this->helperData->setCustomPrice($product, $customerId);
                $result['minimum_price']['final_price']['value'] = $price;
                $result['maximum_price']['final_price']['value'] = $price;
            }
        }
        return $result;
    }
}
