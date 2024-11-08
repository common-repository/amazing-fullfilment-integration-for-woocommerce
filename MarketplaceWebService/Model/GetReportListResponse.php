<?php
/** 
 *  PHP Version 5
 *
 *  @category    Amazon
 *  @package     MarketplaceWebService
 *  @copyright   Copyright 2009 Amazon Technologies, Inc.
 *  @link        http://aws.amazon.com
 *  @license     http://aws.amazon.com/apache2.0  Apache License, Version 2.0
 *  @version     2009-01-01
 */
/******************************************************************************* 

 *  Marketplace Web Service PHP5 Library
 *  Generated: Thu May 07 13:07:36 PDT 2009
 * 
 */

/**
 *  @see MarketplaceWebService_Model
 */
//require_once ('../Model.php');  

    

/**
 * MarketplaceWebService_Model_GetReportListResponse
 * 
 * Properties:
 * <ul>
 * 
 * <li>GetReportListResult: MarketplaceWebService_Model_GetReportListResult</li>
 * <li>ResponseMetadata: MarketplaceWebService_Model_ResponseMetadata</li>
 *
 * </ul>
 */ 
class MarketplaceWebService_Model_GetReportListResponse extends MarketplaceWebService_Model
{


    /**
     * Construct new MarketplaceWebService_Model_GetReportListResponse
     * 
     * @param mixed $data DOMElement or Associative Array to construct from. 
     * 
     * Valid properties:
     * <ul>
     * 
     * <li>GetReportListResult: MarketplaceWebService_Model_GetReportListResult</li>
     * <li>ResponseMetadata: MarketplaceWebService_Model_ResponseMetadata</li>
     *
     * </ul>
     */
    public function __construct($data = null)
    {
        $this->fields = array (
        'GetReportListResult' => array('FieldValue' => null, 'FieldType' => 'MarketplaceWebService_Model_GetReportListResult'),
        'ResponseMetadata' => array('FieldValue' => null, 'FieldType' => 'MarketplaceWebService_Model_ResponseMetadata'),
        );
        parent::__construct($data);
    }

       
    /**
     * Construct MarketplaceWebService_Model_GetReportListResponse from XML string
     * 
     * @param string $xml XML string to construct from
     * @return MarketplaceWebService_Model_GetReportListResponse 
     */
    public static function fromXML($xml)
    {
        $dom = new DOMDocument();
        $dom->loadXML($xml);
        $xpath = new DOMXPath($dom);
    	$xpath->registerNamespace('a', 'http://mws.amazonaws.com/doc/2009-01-01/');
        $response = $xpath->query('//a:GetReportListResponse');
        if ($response->length == 1) {
            return new MarketplaceWebService_Model_GetReportListResponse(($response->item(0))); 
        } else {
            throw new Exception ("Unable to construct MarketplaceWebService_Model_GetReportListResponse from provided XML. 
                                  Make sure that GetReportListResponse is a root element");
        }
          
    }
    
    /**
     * Gets the value of the GetReportListResult.
     * 
     * @return GetReportListResult GetReportListResult
     */
    public function getGetReportListResult() 
    {
        return $this->fields['GetReportListResult']['FieldValue'];
    }

    /**
     * Sets the value of the GetReportListResult.
     * 
     * @param GetReportListResult GetReportListResult
     * @return void
     */
    public function setGetReportListResult($value) 
    {
        $this->fields['GetReportListResult']['FieldValue'] = $value;
        return;
    }

    /**
     * Sets the value of the GetReportListResult  and returns this instance
     * 
     * @param GetReportListResult $value GetReportListResult
     * @return MarketplaceWebService_Model_GetReportListResponse instance
     */
    public function withGetReportListResult($value)
    {
        $this->setGetReportListResult($value);
        return $this;
    }


    /**
     * Checks if GetReportListResult  is set
     * 
     * @return bool true if GetReportListResult property is set
     */
    public function isSetGetReportListResult()
    {
        return !is_null($this->fields['GetReportListResult']['FieldValue']);

    }

    /**
     * Gets the value of the ResponseMetadata.
     * 
     * @return ResponseMetadata ResponseMetadata
     */
    public function getResponseMetadata() 
    {
        return $this->fields['ResponseMetadata']['FieldValue'];
    }

    /**
     * Sets the value of the ResponseMetadata.
     * 
     * @param ResponseMetadata ResponseMetadata
     * @return void
     */
    public function setResponseMetadata($value) 
    {
        $this->fields['ResponseMetadata']['FieldValue'] = $value;
        return;
    }

    /**
     * Sets the value of the ResponseMetadata  and returns this instance
     * 
     * @param ResponseMetadata $value ResponseMetadata
     * @return MarketplaceWebService_Model_GetReportListResponse instance
     */
    public function withResponseMetadata($value)
    {
        $this->setResponseMetadata($value);
        return $this;
    }


    /**
     * Checks if ResponseMetadata  is set
     * 
     * @return bool true if ResponseMetadata property is set
     */
    public function isSetResponseMetadata()
    {
        return !is_null($this->fields['ResponseMetadata']['FieldValue']);

    }



    /**
     * XML Representation for this object
     * 
     * @return string XML for this object
     */
    public function toXML() 
    {
        $xml = "";
        $xml .= "<GetReportListResponse xmlns=\"http://mws.amazonaws.com/doc/2009-01-01/\">";
        $xml .= $this->_toXMLFragment();
        $xml .= "</GetReportListResponse>";
        return $xml;
    }

    private $_responseHeaderMetadata = null;

    public function getResponseHeaderMetadata() {
      return $this->_responseHeaderMetadata;
    }

    public function setResponseHeaderMetadata($responseHeaderMetadata) {
      return $this->_responseHeaderMetadata = $responseHeaderMetadata;
    }
}
