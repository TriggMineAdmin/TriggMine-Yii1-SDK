<?php
/**
  * The main method which sends data to TriggMine.
  *
  */
if ( strpos( Yii::app()->basePath, 'backend' ) > 0 )
{
    require Yii::app()->basePath.'/modules/triggmine/components/vendor/autoload.php';
    Yii::import('webroot.protected.modules.triggmine.models.EventsRepository');
}
else
{
    require Yii::app()->basePath.'/../backend/protected/modules/triggmine/components/vendor/autoload.php';
    Yii::import('webroot.backend.protected.modules.triggmine.models.EventsRepository');
}


use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;

/**
  * The main class which contains API credentials and makes calls to TriggMine.
  */
class ApiController
{
    /**
      * Guzzle HTTP Client with TriggMine API credentials for making calls.
      */
    public $client;
    
    /**
      * API URL value from the Integration tab of you TriggMine dashboard.
      */
    protected $apiUrl;
    
    /**
      * API key value from the Integration tab of you TriggMine dashboard.
      */
    protected $apiKey;
    
    /**
      * Creates a TriggMine client with a certain API URL and API key, which you can find in your TriggMine Dashboard.
      *
      * @param string $apiUrl with API URL value from the Integration tab of you TriggMine dashboard.
      * @param string $apiKey with API key value from the Integration tab of you TriggMine dashboard.
      *
      */
    public function __construct( $apiUrl, $apiKey )
    {
        $this->client = new Client([
            'base_uri' => $apiUrl,
            'defaults' => [
                    'headers' => [
                            'ApiKey'        => $apiKey,
                            'Authorization' => "auth-code"
                        ]
                ]
            ]);
        $this->apiUrl = $apiUrl;
        $this->apiKey = $apiKey;
    }
    
    /**
      * Detects an appropriate TriggMine API URL and sends data to it.
      *
      * @param class $eventDto with a set of attributes (data to be sent to TriggMine).
      *
      * @return array $response with a status code and message (success or error) from TriggMine
      */
    public function SendEvent( $eventDto )
    {
        $response = "";
        
        $repository = new EventsRepository;
        
        $reflection = new \ReflectionClass( $eventDto );
        $method = $repository->GetEventUrl( $reflection->getShortName() );

        try
        {
            $request = $this->client->request(
                'POST',
                'https://' . $this->apiUrl . $method,
                [
                        'headers' => [
                                'ApiKey'        => $this->apiKey,
                                'Content-Type'  => 'application/json',
                                'Authorization' => 'auth-code'
                            ],
                        'body' => json_encode( $eventDto )
                    ]
            );
            $response = array(
                'status' => $request->getStatusCode(),
                'body'   => $request->getBody()->getContents()
            );
        }
        catch ( RequestException $e )
        {
            // uncomment this to see the request content
            // echo Psr7\str( $e->getRequest() );
            if ( $e->hasResponse() )
            {
                echo 'Error: ' . Psr7\str( $e->getResponse() );
            }
        }

        return $response;
    }
}