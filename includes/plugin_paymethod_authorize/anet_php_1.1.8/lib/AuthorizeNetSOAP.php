<?php 
/** 
  * Copyright: dtbaker 2012
  * Licence: Please check CodeCanyon.net for licence details. 
  * More licence clarification available here:  http://codecanyon.net/wiki/support/legal-terms/licensing-terms/ 
  * Deploy: 6525 ae190c43422d2b2a7c6934e00e8b8686
  * Envato: 82b7b3b1-2e56-457f-aac2-ff4d410e1e54
  * Package Date: 2014-10-02 15:05:08 
  * IP Address: 203.125.187.204
  */
/**
 * A simple wrapper for the SOAP API as well as a helper function
 * to generate a documentation file from the WSDL.
 *
 * @package    AuthorizeNet
 * @subpackage AuthorizeNetSoap
 */

/**
 * A simple wrapper for the SOAP API as well as a helper function
 * to generate a documentation file from the WSDL.
 *
 * @package    AuthorizeNet
 * @subpackage AuthorizeNetSoap
 * @todo       Make the doc file a usable class.
 */
class AuthorizeNetSOAP extends SoapClient
{
    const WSDL_URL = "https://api.authorize.net/soap/v1/Service.asmx?WSDL";
    const LIVE_URL = "https://api.authorize.net/soap/v1/Service.asmx";
    const SANDBOX_URL = "https://apitest.authorize.net/soap/v1/Service.asmx";
    
    public $sandbox;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct(self::WSDL_URL);
        $this->__setLocation(self::SANDBOX_URL);
    }
    
    /**
     * Switch between the sandbox or production gateway.
     *
     * @param bool
     */
    public function setSandbox($bool)
    {
        $this->__setLocation(($bool ? self::SANDBOX_URL : self::LIVE_URL));
    }

    /**
     * Get all types as PHP Code.
     * @return string
     */
    public function getSoapTypes()
    {
        $string = "";
        $types = $this->__getTypes();
        foreach ($types as $type) {
            if (preg_match("/struct /",$type)) {
                $type = preg_replace("/struct /","class ",$type);
                $type = preg_replace("/ (\w+) (\w+);/","    // $1\n    public \$$2;",$type);
                $string .= $type ."\n";
            }
        }
        return $string;
    }
    
    /**
     * Get all methods as PHP Code.
     * @return string
     */
    public function getSoapMethods()
    {
        $string = "";
        $functions = array();
        $methods = $this->__getFunctions();
        foreach ($methods as $index => $method) {
            $sig = explode(" ", $method, 2);
            if (!isset($functions[$sig[1]])) {
                $string .= "    /**\n     * @return {$sig[0]}\n    */\n    public function {$sig[1]} {}\n\n";
                $functions[$sig[1]] = true;
            }
        }
        return $string;
    }
    
    /**
     * Create a file from the WSDL for reference.
     */
    public function saveSoapDocumentation($path)
    {
        $string =  "<?php\n";
        $string .= "/**\n";
        $string .= " * Auto generated documentation for the AuthorizeNetSOAP API.\n";
        $string .= " * Generated " . date("m/d/Y") . "\n";
        $string .= " */\n";
        $string .= "class AuthorizeNetSOAP\n";
        $string .= "{\n" . $this->getSoapMethods() . "\n}\n\n" . $this->getSoapTypes() ."\n\n ?>";
        return file_put_contents($path, $string);
    }
    
    
    
}