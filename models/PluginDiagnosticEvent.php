<?php
/**
  * PLugin diagnostic event class with a set of corresponding attributes.
  *
  * This data should be collected and sent when you have just started using TriggMine or changed its configuration.
  * It is needed to confirm your integration in your TriggMine dashboard.
  */
class PluginDiagnosticEvent
{
    /**
      * Date when diagnostic has been created.
      */
    public $dateCreated;
    
    /**
      * Type of diagnostic - Installation, Configuration etc.
      */
    public $diagnosticType;
    
    /**
      * Platform you are using, its version, SDK version, other important info.
      */
    public $description;
    
    /**
      * Whether TriggMine is enabled and active on your side - 0 if no, 1 if yes.
      */
    public $status;
}