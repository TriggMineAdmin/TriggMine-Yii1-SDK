<?php
/**
* TriggMine module for Yii 1, specially customized for Argilla CMS
* 
* TriggMine JS scripts location:
* /js/src/vendor/triggmine-scripts.js
* 
* CMS files modified with TriggMine code:
* Product Event - /protected/views/product/one/product.php
* Cart and Order Events - /protected/controllers/BasketController.php
* Registration, Login, Logout Events - /protected/controllers/UserController.php
* Admin Product Edit Event - /backend/protected/modules/product/models/BProduct.php
* 
* All the code added by TriggMine is marked with "TriggMine start" and  "TriggMine end" comments
*/

class TriggmineModule extends CWebModule
{
	/**
	 * Use it to quickly turn TriggMine on and off without editing the modified files
	 */
	public $_TriggMineEnabled;
	
	/**
	 * Instance of the ApiController class which sends events to TriggMine
	 */
	public $client;
	
	/**
      * API URL value from the Integration tab of you TriggMine dashboard.
      */
	private $apiUrl;
	
	/**
      * API key value from the Integration tab of you TriggMine dashboard.
      */
	private $apiKey;
	
	/**
	 * If set to 1 - logs TriggMine data to the corresponding (frontend or backend) runtime/application.log
	 * 
	 * To see TriggMine log messages please edit your corresponding (frontend or backend) config:
	 * in 'log'
	 * 1. add 'info' to 'levels'
	 * 2. add 'triggmine.*' to 'categories'
	 */
	public $debugMode;
	
	/**
	 * Flag (1 or 0) to show that TriggMine has been successfully installed and Diagnostic Event has been sent
	 */
	public $installed = 0;
	
	/**
	 * Provides correct file paths when called both from frontend and backend
	 */
	private $_root;
	
	public function __construct( $root = null )
	{
		$this->_TriggMineEnabled = 1;
		$this->apiUrl = 'cabinet1550458391.triggmine.com';
		$this->apiKey = 'c492324b80ba4527a57e7ef78d70fb26';
		$this->debugMode = 0;
		
		// add frontend scripts
		$this->registerScript();
		
		$this->_root = $root ? 'webroot' : 'webroot.backend';
		
		// import required module components
		Yii::import( $this->_root . '.protected.modules.triggmine.controllers.ApiController' );
		Yii::import( $this->_root . '.protected.modules.triggmine.models.ProspectEvent' );
		
		// create the client with corresponding api credentials to send events
		$this->client = new ApiController( $this->apiUrl, $this->apiKey );
	}
	
	/**
	 * Add JS scripts needed for TriggMine to gather user data
	 */
	protected function registerScript()
	{
	    $baseUrl = Yii::app()->getHomeUrl();
	    $js_arr = array('triggmine-scripts.js');
	    foreach($js_arr as $filename)
	    {
	        Yii::app()->getClientScript()->registerScriptFile('/js/vendor/'.$filename, CClientScript::POS_END);
	    }
	}
	
	/**
	 * Check who accessed the page to prevent robots from triggering the navigation event
	 */
	public function isBot()
	{
	   preg_match('/bot|curl|spider|google|facebook|yandex|bing|aol|duckduckgo|teoma|yahoo|twitter^$/i', $_SERVER['HTTP_USER_AGENT'], $matches);
	
	   return (empty($matches)) ? false : true;
	}
	
	/**
	 * Get name of order status by id
	 * @var $statusId - order status id
	 */
	public function getOrderStatus( $statusId )
	{
		$status = Yii::app()->db->createCommand()
	      ->select('s.sysname')
	      ->from('kalitniki_order_status AS s')
	      ->where('s.id = ' . $statusId);
	
	    $status = $status->queryAll();
	    $res = $status[0]['sysname'];

	    return $res;
	}
	
	/**
	 * Get customer data (Prospect Event)
	 * @var $event - type of event (ProspectEvent by default, may also be LoginEvent, LogoutEvent)
	 * @var $userId - you may specify the user id if it can't be got from Yii::app()
	 */
	public function getCustomerData( $event = 'ProspectEvent', $userAttr = null )
	{
		if ( $event !== 'ProspectEvent' )
		{
			Yii::import( 'webroot.backend.protected.modules.triggmine.models.' . $event );
		}
		
		$dateCreate = null;
		
		if ( $userAttr )
		{
			$dateCreate = isset( $userAttr['date_create'] ) ? $userAttr['date_create'] : null;
		}
		else
		{
			$user = Yii::app()->user;
			if ( $user->data )
			{
				$userData = $user->data->attributes;
		        $loginData = Login::model()->findByPk($userData['user_id']);
		        
		        $dateCreate = $loginData->attributes['date_create'];
			}
			else
			{
				$userData = null;
				$dateCreate = null;
			}
		}

  		$dataCustomer = new $event;
        $dataCustomer->device_id             = array_key_exists( 'device_id', $_COOKIE ) ? $_COOKIE['device_id'] : "";
        $dataCustomer->device_id_1           = array_key_exists( 'device_id_1', $_COOKIE ) ? $_COOKIE['device_id_1'] : "";
        $dataCustomer->customer_id           = $userAttr ? $userAttr['id'] : $userData['user_id'];
        $dataCustomer->customer_first_name   = isset( $userData['name'] ) ? $userData['name'] : "";
        $dataCustomer->customer_last_name    = "";
        $dataCustomer->customer_email        = $userAttr ? $userAttr['email'] : $user->email;
        $dataCustomer->customer_date_created = $dateCreate;
        
        return $dataCustomer;
	}
	
	/**
	 * Send Diagnostic Event to inform TriggMine about the current integration
	 */
	public function sendDiagnosticEvent()
	{
		Yii::import( 'webroot.backend.protected.modules.triggmine.models.PluginDiagnosticEvent' );
		
		$pluginDiagnostic = new PluginDiagnosticEvent;
		$pluginDiagnostic->dateCreated = date("Y-m-d H:i:s");
    	$pluginDiagnostic->diagnosticType = "InstallPlugin";
    	$pluginDiagnostic->description = "Argilla CMS (kostis.ru) Yii1 TriggMine plugin for Yii (customized) v3.0.23";
    	$pluginDiagnostic->status = $this->installed;
    	
    	$response = $this->client->sendEvent( $pluginDiagnostic );
    	if ( $this->debugMode )
  		{
  		  Yii::log( json_encode( $pluginDiagnostic ), CLogger::LEVEL_INFO, 'triggmine' );
  		  Yii::log( CVarDumper::dumpAsString( $response ), CLogger::LEVEL_INFO, 'triggmine' );
  		}
	}
	
	/**
	 * Export products from the current shop to TriggMine
	 */
	public function exportProducts()
	{
		Yii::import( 'webroot.backend.protected.modules.triggmine.models.ProductExportEvent' );
		Yii::import( 'webroot.backend.protected.modules.triggmine.models.ProductHistoryEvent' );
		
		$baseURL = Yii::app()->request->hostInfo;
		$pageSize = 20;
		
		$allProducts = Yii::app()->db->createCommand()
	      ->select('p.id, p.parent, p.url, p.name, p.articul, p.price, p.notice, p.visible, p.discount, p.date_create, i.name AS img, s.name AS section')
	      ->from('kalitniki_product AS p')
	      ->join('kalitniki_product_img AS i', 'p.id = i.parent')
	      ->join('kalitniki_product_assignment AS a', 'p.id = a.product_id')
	      ->join('kalitniki_product_section AS s', 's.id = a.section_id')
	      ->group('p.id')
	      ->order('IF(p.position=0, 1, 0), p.position ASC ');
	
	    $allProducts = $allProducts->queryAll();
	    $productCount = count( $allProducts );
	    
	    $pages = ( $productCount % $pageSize ) > 0 ? floor( $productCount / $pageSize ) + 1 : $productCount / $pageSize;
	    
	    for ( $currentPage = 0; $currentPage <= $pages - 1; $currentPage++ )
	    {
	    	$dataExport = new ProductHistoryEvent;
	    	
	    	$offset = $currentPage * $pageSize;
	    	$collection = array_slice( $allProducts, $offset, $pageSize );
	    	
	    	foreach ( $collection as $productItem )
	    	{
	    		$productPrices = array();
				if ( $productItem['discount'] )
				{
				  $productPrice = array(
			      'price_id'             => "",
			      'price_value'          => Utils::calcDiscountPrice($productItem['price'], $productItem['discount']),
			      'price_priority'       => null,
			      'price_active_from'    => "",
			      'price_active_to'      => "",
			      'price_customer_group' => "",
			      'price_quantity'       => ""
			    );
			      
			    $productPrices[] = $productPrice;
				}
	    		
	    		$productCategories = array();
        		$productCategories[] = $productItem['section'];
	    		
	    		$product = new ProductExportEvent;
                $product->product_id               = $productItem['id'];
                $product->parent_id                = $productItem['parent'] ? $productItem['parent'] : "";
                $product->product_name             = isset( $productItem['name'] ) ? $productItem['name'] : "";
                $product->product_desc             = $productItem['notice'];
                $product->product_create_date      = $productItem['date_create'];
                $product->product_sku              = $productItem['articul'] ? $productItem['articul'] : "";
                $product->product_image            = $baseURL . '/' . $productItem['img'];
                $product->product_url              = $baseURL . '/' . $productItem['url'];
                $product->product_qty              = "";
                $product->product_default_price    = $productItem['price'];
                $product->product_prices           = $productPrices;
                $product->product_categories       = $productCategories;
                $product->product_relations        = "";
                $product->product_is_removed       = "";
                $product->product_is_active        = $productItem['visible'];
                $product->product_active_from      = "";
                $product->product_active_to        = "";
                $product->product_show_as_new_from = "";
                $product->product_show_as_new_to   = "";
                
                $dataExport->products[] = $product;
	    	}

	    	$this->client->sendEvent( $dataExport );
	    }
	}
	
	/**
	 * Export orders from the current shop to TriggMine
	 */
	public function exportOrders()
	{
		Yii::import( 'webroot.backend.protected.modules.triggmine.models.OrderEvent' );
		Yii::import( 'webroot.backend.protected.modules.triggmine.models.ProductEvent' );
		Yii::import( 'webroot.backend.protected.modules.triggmine.models.ProspectExportEvent' );
		Yii::import( 'webroot.backend.protected.modules.triggmine.models.OrderHistoryEvent' );
		
		$baseURL = Yii::app()->request->hostInfo;
		$pageSize = 100;

		$allOrders = Yii::app()->db->createCommand()
		      ->select('o.id, o.user_id, o.name, o.email, o.sum, o.date_create, o.status_id')
		      ->from('kalitniki_order AS o')
		      ->group('o.id');
	
	    $allOrders = $allOrders->queryAll();

	    $orderCount = count( $allOrders );
	    
	    $pages = ( $orderCount % $pageSize ) > 0 ? floor( $orderCount / $pageSize ) + 1 : $orderCount / $pageSize;
	    
	    for ( $currentPage = 0; $currentPage <= 0; $currentPage++ )
	    {
	    	$dataExport = new OrderHistoryEvent;
	    	
	    	$offset = $currentPage * $pageSize;
	    	$collection = array_slice( $allOrders, $offset, $pageSize );
	    	
		    foreach ( $collection as $order )
		    {
		    	$orderProducts = Yii::app()->db->createCommand()
			      ->select('op.name AS product_name, op.price, op.count, op.sum,
			    			p.id, p.url, p.articul, p.notice, i.name AS img, s.name AS section')
			      ->from('kalitniki_order_product AS op')
			      ->where('op.order_id = ' . $order['id'])
			      ->join('kalitniki_product AS p', 'op.name = p.name')
			      ->join('kalitniki_product_img AS i', 'p.id = i.parent')
			      ->join('kalitniki_product_assignment AS a', 'p.id = a.product_id')
			      ->join('kalitniki_product_section AS s', 's.id = a.section_id')
			      ->group('p.id');
			    $orderProducts = $orderProducts->queryAll();
			    
			    $qtyTotal = 0;
			    
			    $customer = Yii::app()->db->createCommand()
			      ->select('c.user_id AS id, c.name, c.email')
			      ->from('kalitniki_order AS c')
			      ->where('c.id = ' . $order['id']);
			    $customer = $customer->queryAll();
			    $customer = $customer[0];
				
				$customer['date_create'] = null;
				
				if ( $customer['id'] )
				{
					$dateCreate = Yii::app()->db->createCommand()
				      ->select('u.date_create')
				      ->from('kalitniki_user AS u')
				      ->where('u.id = ' . $customer['id']);
				    $dateCreate = $dateCreate->queryAll();
				    $dateCreate = $dateCreate[0];
				    $customer['date_create'] = $dateCreate['date_create'];
				}
			    
			    $dataCustomer = new ProspectExportEvent;
			    $dataCustomer->customer_id              = $customer['id'];
		        $dataCustomer->customer_first_name      = $customer['name'];
		        $dataCustomer->customer_last_name       = "";
		        $dataCustomer->customer_email           = $customer['email'];
		        $dataCustomer->customer_date_created    = $customer['date_create'];
		        $dataCustomer->customer_last_login_date = "";
    
			    $data = new OrderEvent;
			    $data->customer     = $dataCustomer;
			    $data->order_id     = $order['id'];
			    $data->date_created = $order['date_create'];
			    $data->status       = $this->getOrderStatus( $order['status_id'] );
			    $data->price_total  = $order['sum'];
			    $data->qty_total    = $qtyTotal;
			    $data->products     = array();
			    
			    foreach ($orderProducts as $orderProduct)
			    {
			    	$productCategories = array();
        			$productCategories[] = $orderProduct['section'];
			    	
			    	$dataProduct = new ProductEvent;
		        	$dataProduct->product_id         = $orderProduct['id'];
		        	$dataProduct->product_name       = $orderProduct['product_name'];
		        	$dataProduct->product_desc       = $orderProduct['notice'];
		        	$dataProduct->product_sku        = $orderProduct['articul'];
		        	$dataProduct->product_image      = $baseURL . '/' . $orderProduct['img'];
		        	$dataProduct->product_url        = $baseURL . '/' . $orderProduct['url'];
		        	$dataProduct->product_qty        = (int) $orderProduct['count'];
		        	$dataProduct->product_price      = $orderProduct['price'];
		        	$dataProduct->product_total_val  = $orderProduct['sum'];
		        	$dataProduct->product_categories = $productCategories;
		        	
		        	$data->products[] = $dataProduct;
        			$data->qty_total += $dataProduct->product_qty;
			    }
			    
			    $dataExport->orders[] = $data;
		    }
		    
		    Yii::log(json_encode($dataExport), CLogger::LEVEL_INFO, 'triggmine');
		    $this->client->sendEvent( $dataExport );
	    }
	}
	
	/**
	 * Export customers from the current shop to TriggMine
	 */
	public function exportCustomers()
	{
		Yii::import( 'webroot.backend.protected.modules.triggmine.models.ProspectExportEvent' );
		Yii::import( 'webroot.backend.protected.modules.triggmine.models.CustomerHistoryEvent' );
		
		$pageSize = 100;
		
		$allCustomers = Yii::app()->db->createCommand()
	      ->select('u.id, u.date_create, u.email, e.name')
	      ->from('kalitniki_user AS u')
	      ->join('kalitniki_user_data_extended AS e', 'u.id = e.user_id')
	      ->group('u.id');
	    
	    $allCustomers = $allCustomers->queryAll();
	    
	    $customerCount = count( $allCustomers );
	    $pages = ( $customerCount % $pageSize ) > 0 ? floor( $customerCount / $pageSize ) + 1 : $customerCount / $pageSize;
	    
	    for ( $currentPage = 0; $currentPage <= $pages - 1; $currentPage++ )
	    {
	    	$dataExport = new CustomerHistoryEvent;
	    	
	    	$offset = $currentPage * $pageSize;
	    	$collection = array_slice( $allCustomers, $offset, $pageSize );
	    	
	    	foreach ( $collection as $customer )
	    	{
	    		$dataCustomer = new ProspectExportEvent;
		        $dataCustomer->customer_id              = $customer['id'];
		        $dataCustomer->customer_first_name      = $customer['name'];
		        $dataCustomer->customer_last_name       = "";
		        $dataCustomer->customer_email           = $customer['email'];
		        $dataCustomer->customer_date_created    = $customer['date_create'];
		        $dataCustomer->customer_last_login_date = "";
                
                $dataExport->prospects[] = $dataCustomer;
	    	}

	    	$this->client->sendEvent( $dataExport );
	    }
	}
}
