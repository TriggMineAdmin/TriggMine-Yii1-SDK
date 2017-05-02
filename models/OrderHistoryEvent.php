<?php
/**
  * Order history export event class with a set of corresponding attributes.
  *
  * This data should be collected whenever order export is performed.
  */
class OrderHistoryEvent
{
    /**
      * Instances of OrderEvent class to be exported.
      */
    public $orders;
}