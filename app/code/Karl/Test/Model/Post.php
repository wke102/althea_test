<?php
namespace Karl\Test\Model;
class Post extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
	const CACHE_TAG = 'catalog_product_entity';

	protected $_cacheTag = 'catalog_product_entity';

	protected $_eventPrefix = 'catalog_product_entity';

	protected function _construct()
	{
		$this->_init('Karl\Test\Model\ResourceModel\Post');
	}

	public function getIdentities()
	{
		return [self::CACHE_TAG . '_' . $this->getId()];
	}

	public function getDefaultValues()
	{
		$values = [];

		return $values;
	}
}