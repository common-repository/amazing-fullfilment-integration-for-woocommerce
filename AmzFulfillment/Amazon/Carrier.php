<?php
class AmzFulfillment_Amazon_Carrier {
	public static $codes = array(
			"AFLFEDEX"				=> "FedEx",
			"AMAZON"				=> "Amazon",
			"AMZN"					=> "Amazon",
			"ASENDIA"				=> "Asendia",
			"ARAMEX"				=> "Aramex",
			"ATPOST"				=> "Post AT",
			"BARTOLINI"				=> "BRT",
			"BLUE PACKAGE"			=> "Blue Package",
			"BLUEDART"				=> "Blue Dart",
			"CANADAPOST"			=> "Canada Post",
			"CHRONOPOST"			=> "Chronopost",
			"CITYLINK"				=> "City Link",
			"DELHIVERY"				=> "Delhivery",
			"DEPOST"				=> "Deutsche Post",
			"DEUTSCHEPOST"			=> "Deutsche Post",
			"DHL"					=> "DHL",
			"DHL_CONNECT"			=> "DHL Connect",
			"DHLGLOBALMAIL"			=> "DHL Global Mail",
			"DP"					=> "Deutsche Post",
			"DPD"					=> "DPD",
			"DPDDE"					=> "DPD",
			"DTDC"					=> "DTDC",
			"FASTWAY"				=> "Fastway",
			"FEDEX"					=> "FedEx",
			"FEDEX_JP"				=> "FedEx JP",
			"FEDEXSMARTPOST"		=> "FedEx SmartPost",
			"FIRSTFLIGHT"			=> "First Flight",
			"GLS"					=> "GLS",
			"GLSIT"					=> "GLS",
			"GO"					=> "GO",
			"HERMES"				=> "Hermes Logistik Gruppe",
			"HLG"					=> "Hermes Logistik Gruppe",
			"INDIAPOST"				=> "India Post",
			"JP_EXPRESS"			=> "JP Express",
			"LAPOSTE"				=> "La Poste",
			"LASERSHIP"				=> "Lasership",
			"NEWGISTICS"			=> "Newgistics",
			"NIPPONEXPRESS"			=> "Nippon Express",
			"NITTSU"				=> "Nippon Express",
			"ONTRAC"				=> "On Trac",
			"OSM"					=> "OSM",
			"OVERNITEEXPRESS"		=> "Overnite Express",
			"PARCELFORCE"			=> "Parcelforce",
			"PARCELNET"				=> "Parcelnet",
			"PIN"					=> "PINTRILL",
			"POSTDK"				=> "Post Danmark",
			"POSTEITALIANE"			=> "Poste Italiane",
			"POSTE_ITALIANE"		=> "POSTE ITALIANE",
			"PROFESSIONAL"			=> "Professional",
			"ROYAL MAIL"			=> "Royal Mail",
			"PTLUX"					=> "POST Luxembourg",
			"SAGAWA"				=> "Sagawa",
			"SAGAWAEXPRESS"			=> "Sagawa Express",
			"SDA"					=> "SDA",
			"SEUR"					=> "Transporte de Paquetería",
			"SMARTMAIL"				=> "Smartmail",
			"STREAMLITE"			=> "Streamlite",
			"TARGET"				=> "Target",
			"TAXIPOST"				=> "Taxipost",
			"TNT"					=> "TNT",
			"TNTNL"					=> "TNT",
			"UPS"					=> "UPS",
			"UPSMAILINNOVATIONS"	=> "UPS Mail Innovations",
			"UPSMI"					=> "UPS Mail Innovations",
			"USPS"					=> "USPS",
			"YAMATO"				=> "YAMATO",
			"YAMATOTRANSPORT"		=> "Yamato Transport",
			"YODEL"					=> "Yodel",
	);

	public static function name($code) {
		$matches = array();
		if(isset(self::$codes[strtoupper($code)])) {
			return self::$codes[strtoupper($code)];
		} elseif(preg_match('/^AMZN_([a-z]+)$/i', $code, $matches)) {
			return 'Amazon';
		} else {
			return $code;
		}
	}
}
