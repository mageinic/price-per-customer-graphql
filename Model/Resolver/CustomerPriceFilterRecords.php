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

use MageINIC\PricePerCustomer\Api\CustomerPriceRepositoryInterface as CustomerPriceRepository;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\Resolver\Argument\SearchCriteria\Builder as SearchCriteriaBuilder;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * @inheritdoc
 */
class CustomerPriceFilterRecords implements ResolverInterface
{
    /**
     * @var SearchCriteriaBuilder
     */
    protected SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @var CustomerPriceRepository
     */
    protected CustomerPriceRepository $customerPriceRepository;

    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param CustomerPriceRepository $customerPriceRepository
     */
    public function __construct(
        SearchCriteriaBuilder   $searchCriteriaBuilder,
        CustomerPriceRepository $customerPriceRepository
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->customerPriceRepository = $customerPriceRepository;
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        Field       $field,
        $context,
        ResolveInfo $info,
        array       $value = null,
        array       $args = null
    ) {
        try {
            $pageSize = $args['pageSize'];
            $searchCriteria = $this->searchCriteriaBuilder->build('price_per_customer', $args);
            $searchCriteria->setCurrentPage($args['currentPage']);
            $searchCriteria->setPageSize($pageSize);
            $collection = $this->customerPriceRepository->getList($searchCriteria);
            $count = $collection->getTotalCount();
            $total_pages = ceil($count / $pageSize);
            if ($count) {
                $customerPriceCollection = [];
                foreach ($collection->getItems() as $value) {
                    $customerPriceCollection[] = $value;
                }
            } else {
                throw new GraphQlInputException(__('Price Per Customer Not exist.'));
            }
        } catch (LocalizedException $e) {
            throw new GraphQlNoSuchEntityException(__($e->getMessage()), $e);
        }
        return [
            'total_count' => $count,
            'total_pages' => $total_pages,
            'pricePerCustomerList' => $customerPriceCollection
        ];
    }
}
