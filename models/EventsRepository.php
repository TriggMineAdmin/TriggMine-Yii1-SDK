<?php
/**
* Contains a dictionary to get an event url by its class name.
*/
class EventsRepository
{
    /**
      * Gets event url by its class name.
      *
      * @param string $type with a class name of the event.
      *
      * @return string event url
      */
    public function GetEventUrl( $type )
    {
        $urlRepository = array(
                'CartEvent'             => '/api/events/cart',
                'OrderHistoryEvent'     => '/api/events/history',
                'CustomerHistoryEvent'  => '/api/events/history/prospects',
                'ProductHistoryEvent'   => '/api/products/import',
                'LoginEvent'            => '/api/events/prospect/login',
                'LogoutEvent'           => '/api/events/prospect/logout',
                'NavigationEvent'       => '/api/events/navigation',
                'OrderEvent'            => '/api/events/order',
                'ProspectEvent'         => '/api/events/prospect/registration',
                'PluginDiagnosticEvent' => '/control/api/plugin/onDiagnosticInformationUpdated'
            );
        
        return $urlRepository[$type];
    }
}