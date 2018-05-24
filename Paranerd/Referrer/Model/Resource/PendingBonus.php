<?php
namespace Paranerd\Referrer\Model\Resource;

class PendingBonus extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	/**
	* Define main table
	*/
	 protected function _construct()
	{
		$this->_init('pending_bonus', 'id');   //here id is the primary key of custom table
	}
}