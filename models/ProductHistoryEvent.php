<?php
/**
  * Product history export event class with a set of corresponding attributes.
  *
  * This data should be collected whenever product export is performed.
  */
class ProductHistoryEvent
{
    /**
      * Instances of ProductExportEvent class to be exported.
      */
    public $products;
}