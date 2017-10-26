<?php
/**
  * Prospect (customer) event class with a set of attributes needed in Customer History Export.
  */
class ProspectExportEvent
{
    /**
      * ID of the customer in your DB.
      */
    public $customer_id;

    /**
      * Customer's first name.
      */
    public $customer_first_name;

    /**
      * Customer's last name.
      */
    public $customer_last_name;

    /**
      * Customer's email address.
      */
    public $customer_email;

    /**
      * Customer's registration date.
      */
    public $customer_date_created;
    
    /**
      * Customer's registration date.
      */
    public $customer_last_login_date;
}