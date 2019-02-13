<?php
namespace Lucid\Booking\Block\Html;

use Lucid\Booking\Model\CalendarRepository;
use Magento\Framework\View\Element\Template;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;

class LucidProduct extends \Magento\Catalog\Block\Product\View
{

	protected $_helper;

	protected $_objectManager;

	protected $calendarRepository;

	public function __construct(
		\Magento\Catalog\Block\Product\Context $context,
		\Magento\Framework\Url\EncoderInterface $urlEncoder,
		\Magento\Framework\Json\EncoderInterface $jsonEncoder,
		\Magento\Framework\Stdlib\StringUtils $string,
		\Magento\Catalog\Helper\Product $productHelper,
		\Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig,
		\Magento\Framework\Locale\FormatInterface $localeFormat,
		\Magento\Customer\Model\Session $customerSession,
		ProductRepositoryInterface $productRepository,
		\Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
		CalendarRepository $calendarRepository,
		array $data = []
	)
	{

		parent::__construct(
			$context,
			$urlEncoder,
			$jsonEncoder,
			$string,
			$productHelper,
			$productTypeConfig,
			$localeFormat,
			$customerSession,
			$productRepository,
			$priceCurrency,
			$data
		);
		$this->calendarRepository = $calendarRepository;
	}

	protected function _toHtml()
	{
		$this->setModuleName($this->extractModuleName('Magento\Catalog\Block\Product\View'));
		return parent::_toHtml();
	}

	public function getCalendar(){
		return   $this->calendarRepository->getById(1);
	}

	/**
	 * @param string $relativeMediaPath
	 * @return string
	 */
	public function getAbsoluteMediaPath($relativeMediaPath) {
		/** @var \Magento\Framework\App\ObjectManager $om */
		$om = \Magento\Framework\App\ObjectManager::getInstance();
		/** @var \Magento\Framework\Filesystem $filesystem */
		$filesystem = $om->get('Magento\Framework\Filesystem');
		/** @var \Magento\Framework\Filesystem\Directory\ReadInterface|\Magento\Framework\Filesystem\Directory\Read $reader */
		$reader = $filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
		return $reader->getAbsolutePath($relativeMediaPath);
	}
}