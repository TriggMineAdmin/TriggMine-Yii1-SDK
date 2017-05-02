<?php
/**
  * Customer history export event class with a set of corresponding attributes.
  *
  * This data should be collected whenever customer export is performed.
  */
class CustomerHistoryEvent
{
    /**
      * Instances of ProspectEvent class (customers) to be exported.
      */
    public $prospects;
}