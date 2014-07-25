﻿<?php/* * i18n * Translator in en & fr for all php pages */class translator {	private $_id;	private $_lang;	public $currentLanguage = "en";	public $validLanguages = array("en", "fr");	public $file = "lang.xml";	function __construct($currentLanguage = "en", $file = "lang.xml") {		// Set the current language		if(in_array($currentLanguage, $this->validLanguages)) {			$this->currentLanguage = $currentLanguage;		} else {			$this->currentLanguage = "en";		}				// Set the translations file		$this->file = $file;				// Create an xml parser		$xmlParser = xml_parser_create();		xml_set_element_handler($xmlParser, Array(&$this, "startElement"), Array(&$this, "endElement"));		xml_set_character_data_handler($xmlParser, Array(&$this, "getDate"));				// Open and parse the xml file		if (!($fp = fopen($this->file, "r"))) {			die("Can not open XML file!");		}		while($xmlLine = fgets($fp)) {			xml_parse($xmlParser, $xmlLine, feof($fp)) or				die("Erreur XML");		}				// Cleaning		fclose($fp);		xml_parser_free($xmlParser);	}		function startElement($parser, $name, $attrs) {		global $_id, $_lang;		if($name == "XMLDATA") {			$_id = trim($attrs["ID"]);		}		if($name == "TRANSLATION") {			$_lang = trim($attrs["LANG"]);		}	}		function endElement($parser, $name) {    }		function getDate($parser, $cdata) {		global $_id, $_lang;		$cdata = trim($cdata);		if(strlen($_id) != 0 AND strlen($_lang) != 0 AND strlen($cdata) != 0 AND $_lang == $this->currentLanguage) {			$this->$_id = $cdata;		}	}}?>