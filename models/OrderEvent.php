<?php
/**
  * Order event class with a set of corresponding attributes.
  *
  * This data should be collected whenever an order is successfully placed.
  */
class OrderEvent
{
    /**
      * Object of ProspectEvent class.
      */
    public $customer;
    
    /**
      * Unique ID of the order.
      */
    public $order_id;
    
    /**
      * Date when the order has been placed.
      */
    public $date_created;
    
    /**
      * Order status - pending, paid, closed etc.
      */
    public $status;

    /**
      * Total quantity of all products in the order.
      */
    public $qty_total;

    /**
      * Total price for the order.
      */
    public $price_total;

    /**
      * Array of objects of ProductEvent class.
      */
    public $products;
}