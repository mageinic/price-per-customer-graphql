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

namespace MageINIC\PricePerCustomerGraphQl\Model\Resolver;

use MageINIC\PricePerCustomer\Helper\Data;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\QuoteGraphQl\Model\Resolver\AddProductsToCart;
use Magento\Framework\App\Request\Http;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\GraphQl\Model\Query\ContextInterface;
use Magento\Quote\Api\CartItemRepositoryInterface;

/**
 * Resolver Of Graphql Custom Price
 */
class GraphQlCustomPrice
{
    /**
     * @var Http
     */
    protected Http $request;

    /**
     * @var Data
     */
    private Data $helperData;

    /**
     * @var CartItemRepositoryInterface
     */
    private CartItemRepositoryInterface $cartItemRepository;

    /**
     * @param Data $helperData
     * @param Http $request
     * @param CartItemRepositoryInterface $cartItemRepository
     */
    public function __construct(
        Data                        $helperData,
        Http                        $request,
        CartItemRepositoryInterface $cartItemRepository
    ) {
        $this->request = $request;
        $this->helperData = $helperData;
        $this->cartItemRepository = $cartItemRepository;
    }

    /**
     * After Resolver
     *
     * @param AddProductsToCart $subject
     * @param AddProductsToCart $result
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return mixed
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function afterResolve(
        AddProductsToCart $subject,
        $result,
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        if ($this->helperData->isEnable()) {
            if ($context->getExtensionAttributes()->getIsCustomer()) {
                $customerId = $context->getUserId();
                foreach ($result['cart']['model']['items'] as $quote_item) {
                    if ($customerId) {
                        $qty = $this->helperData->setCustomQty($quote_item['product'], $customerId);
                        if (!empty($qty) && $quote_item->getQty() > $qty) {
                            $this->cartItemRepository->deleteById($quote_item['quote_id'], $quote_item['item_id']);
                            throw new GraphQlNoSuchEntityException(
                                __("This quantity is not allowed to make purchase from  product")
                            );
                        }
                    }
                    $price = $this->helperData->setCustomPrice($quote_item['product'], $customerId);
                    if (!empty($price)) {
                        $quote_item->setCustomPrice($price);
                        $quote_item->setOriginalCustomPrice($price);
                        $quote_item->getProduct()->setIsSuperMode(true);
                        $quote_item->save();
                    }
                }
            }
        }
        return $result;
    }
}
