<?php
namespace Paranerd\Referrer\Model\Resource\PendingBonus;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define model & resource model
     */
    protected function _construct()
    {
    $this->_init(
        'Paranerd\Referrer\Model\PendingBonus',
        'Paranerd\Referrer\Model\Resource\PendingBonus'
    );

    }
}