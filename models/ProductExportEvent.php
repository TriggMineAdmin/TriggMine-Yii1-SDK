<?php
/**
  * Product export event class with a set of attributes needed in Product Export.
  */
class ProductExportEvent
{
    /**
      * ID of a product in your DB.
      */
    public $product_id;
    
    /**
      * ID of a product parent in your DB.
      */
    public $parent_id;

    /**
      * Product name (in case of variable product you can append variable attributes here).
      */
    public $product_name;

    /**
      * Product description.
      */
    public $product_desc;
    
    /**
      * Product create date.
      */
    public $product_create_date;

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
      * Product default price.
      */
    public $product_default_price;
    
    /**
      * Array of product special prices (discounts etc).
      */
    public $product_prices;
    
    /**
      * Array of categories, which the product belongs to.
      */
    public $product_categories;
    
    /**
      * Array of ids of products, related to the current one.
      */
    public $product_relations;
    
    /**
      * Whether the product is removed from the store or not.
      */
    public $product_is_removed;
    
    /**
      * Whether the product is activated.
      */
    public $product_is_active;
    
    /**
      * Date from which the product is active.
      */
    public $product_active_from;
    
    /**
      * Date to which the product is active.
      */
    public $product_active_to;
    
    /**
      * Date from which the product is shown as new.
      */
    public $product_show_as_new_from;
    
    /**
      * Date to which the product is shown as new.
      */
    public $product_show_as_new_to;
}