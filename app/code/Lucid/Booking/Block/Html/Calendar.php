<?php
namespace Lucid\Booking\Block\Html;

use Lucid\Booking\Model\CalendarRepository;
use Magento\Framework\View\Element\Template;

class Calendar extends Template
{
	protected $calendarRepository;

	public function __construct(
		Template\Context $context,
		array $data = [],
		CalendarRepository $calendarRepository
	)
	{
		$this->calendarRepository = $calendarRepository;
		parent::__construct($context, $data);
	}

	public function getCalendar(){

		return   $this->calendarRepository->getById(1);

	}

    public function getDynamicWebsites(){

	    $calendar = $this->calendarRepository->getById(1);
        return   json_decode($calendar->getWebsitesJson());

    }
}
