<?php
/**
  * Cart event class with a set of corresponding attributes.
  * 
  * This data should be collected whenever a cart is created or updated.
  */
class CartEvent
{
    /**
      * Object of ProspectEvent class.
      */
    public $customer;
    
    /**
      * Unique ID of the order (null for Cart).
      */
    public $order_id;
    
    /**
      * Total quantity of all products in the cart.
      */
    public $qty_total;
    
    /**
      * Total price of all products in the cart.
      */
    public $price_total;
    
    /**
      * Array of objects of ProductEvent class.
      */
    public $products;
}