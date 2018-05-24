<?php
namespace Paranerd\Referrer\Model;
use Magento\Framework\Model\AbstractModel;

class PendingBonus extends AbstractModel
{
	/**
	 * Define resource model
	 */
	 protected function _construct() {
		$this->_init('Paranerd\Referrer\Model\Resource\PendingBonus');
	}
}