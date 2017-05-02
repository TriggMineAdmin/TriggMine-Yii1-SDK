<?php
/**
  * Product event class with a set of corresponding attributes.
  *
  * This data should be collected and used to represent a product in any event which requires it.
  */
class ProductEvent
{
    /**
      * ID of a product in your DB.
      */
    public $product_id;

    /**
      * Product name (in case of variable product you can append variable attributes here).
      */
    public $product_name;

    /**
      * Product description.
      */
    public $product_desc;

    /**
      * Product SKU.
      */
    public $product_sku;

    /**
      * Link to the main product image.
      */
    public $product_image;

    /**
      * Link to the single product page in your store.
      */
    public $product_url;

    /**
      * Product quantity (when using in Navigation event - set this to 1).
      */
    public $product_qty;
    
    /**
      * Product price.
      */
    public $product_price;
    
    /**
      * Total price for all items of the product.
      */
    public $product_total_val;
    
    /**
      * List of categories, which the product belongs to.
      */
    public $product_categories;
}