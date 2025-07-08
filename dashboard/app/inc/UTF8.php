<?php // class_UTF8.php
/**
 * This class can determine if a string is UTF-8 compatible.
 *
 * The constructor receives a string and returns an object containing the
 * string and a validity indicator.  If the string fails UTF-8 validation,
 * the offset location of the failures will be provided in an array in the
 * "error" property.
 *
 * References and further explanation are in the "readme.txt" file.
 */
error_reporting(E_ALL);


Class UTF8
{
    const ASCII_GOOD_REGEX = '/^[[:ascii:]]*$/';      // ASCII character class
    const UTF8_GOOD_REGEX  = '//u';                   // http://php.net/manual/en/function.mb-detect-encoding.php#112391
    const UTF8_BOM_REGEX   = '/^\x{EF}\x{BB}\x{BF}/'; // UTF-8 Byte-Order Mark
    const UTF8_ERROR_REGEX
    = '/(
      [\xC0-\xC1] # Invalid UTF-8 Bytes
    | [\xF5-\xFF] # Invalid UTF-8 Bytes
    | \xE0[\x80-\x9F] # Overlong encoding of prior code point
    | \xF0[\x80-\x8F] # Overlong encoding of prior code point
    | [\xC2-\xDF](?![\x80-\xBF])    # Invalid UTF-8 Sequence Start
    | [\xE0-\xEF](?![\x80-\xBF]{2}) # Invalid UTF-8 Sequence Start
    | [\xF0-\xF4](?![\x80-\xBF]{3}) # Invalid UTF-8 Sequence Start
    | (?<=[\x0-\x7F\xF5-\xFF])[\x80-\xBF] # Invalid UTF-8 Sequence Middle
    | (?<![\xC2-\xDF]|[\xE0-\xEF]|[\xE0-\xEF][\x80-\xBF]|[\xF0-\xF4]|[\xF0-\xF4][\x80-\xBF]|[\xF0-\xF4][\x80-\xBF]{2})[\x80-\xBF] # Overlong Sequence
    | (?<=[\xE0-\xEF])[\x80-\xBF](?![\x80-\xBF])            # Short 3 byte sequence
    | (?<=[\xF0-\xF4])[\x80-\xBF](?![\x80-\xBF]{2})         # Short 4 byte sequence
    | (?<=[\xF0-\xF4][\x80-\xBF])[\x80-\xBF](?![\x80-\xBF]) # Short 4 byte sequence (2)
    )/x';

    public $error, $bytes, $chars, $str;

    public function __construct($str, $repair=FALSE, $remove_BOM=TRUE) {
        if ($remove_BOM) {
            $str = preg_replace(self::UTF8_BOM_REGEX, NULL, $str);
        }
        $this->str   = $str;
        $this->bytes = strlen($str);
        $this->chars = mb_strlen($str);
        $this->error = NULL;

        // If ASCII or valid UTF8, we are done
        if ($this->is_ascii($this->str)) return $this;
        if ($this->is_utf8($this->str)) return $this;

        // Try to "repair" ISO-8859-1
        if ($repair) {
            $new = $this->extended_ascii_to_utf8($this->str);
            if ($this->is_utf8($new)) {
                $this->str   = $new;
                $this->bytes = strlen($new);
                $this->chars = mb_strlen($new);
                return $this;
            }
        }

        // If we get this far, we may have a bad UTF8 string
        $this->locate_utf8_errors($this->str);
        return $this;
    }

    public function is_ascii($str) {
        return preg_match(self::ASCII_GOOD_REGEX, $str);
    }

    public function is_utf8($str) {
        return preg_match(self::UTF8_GOOD_REGEX, $str);
    }

    public function locate_utf8_errors($str) {
        $error_count = preg_match_all(self::UTF8_ERROR_REGEX, $str, $hits, PREG_OFFSET_CAPTURE);
        if ($error_count == 0) return FALSE; // This should never occur
        foreach ($hits[0] as $error_points) {
            $this->error[] = $error_points[1];
        }
    }

    function extended_ascii_to_utf8($text) {
        // SEE: http://php.net/manual/en/function.mb-convert-encoding.php#112547
        $map =
        [ chr(0x80) => '&euro;'   // (128)
        , chr(0x82) => '&sbquo;'  // (130)
        , chr(0x84) => '&bdquo;'  // (132)
        , chr(0x85) => '&hellip;' // (133)
        , chr(0x86) => '&dagger;' // (134)
        , chr(0x87) => '&Dagger;' // (135)
        , chr(0x89) => '&permil;' // (137)
        , chr(0x8B) => '&lsaquo;' // (139)
        , chr(0x8C) => '&OElig;'  // (140)
        , chr(0x91) => '&lsquo;'  // (145)
        , chr(0x92) => '&rsquo;'  // (146)
        , chr(0x93) => '&ldquo;'  // (147)
        , chr(0x94) => '&rdquo;'  // (148)
        , chr(0x95) => '&bull;'   // (149)
        , chr(0x96) => '&ndash;'  // (150)
        , chr(0x97) => '&mdash;'  // (151)
        , chr(0x99) => '&trade;'  // (153)
        , chr(0x9B) => '&rsquo;'  // (155)
        , chr(0x9C) => '&oelig;'  // (156)
        , chr(0x9F) => '&fnof;'   // (159)
        , chr(0xC6) => '&AElig;'  // (198)

        /**
         * The following appear to translate correctly with the native function
         *
        , chr(0xA0) => ' '        // (160) NBSP
        , chr(0xA1) => '&iexcl;'  // (161)
        , chr(0xA2) => '&cent;'   // (162)
        , chr(0xA3) => '&pound;'  // (163)
        , chr(0xA4) => '&curren;' // (164)
        , chr(0xA5) => '&yen;'    // (165)
        , chr(0xA6) => '&brvbar;' // (166)
        , chr(0xA7) => '&sect;'   // (167)
        , chr(0xA8) => '&uml;'    // (168)
        , chr(0xA9) => '&copy;'   // (169)
        , chr(0xAA) => '&ordf;'   // (170)
        , chr(0xAB) => '&laquo;'  // (171)
        , chr(0xAC) => '&not;'    // (172)
        , chr(0xAB) => '&shy;'    // (173) Soft Hyphen
        , chr(0xAE) => '&reg;'    // (174)
        , chr(0xAF) => '&macr;'   // (175)
        , chr(0xB0) => '&deg;'    // (176)
        , chr(0xB1) => '&plusmn;' // (177)
        , chr(0xB2) => '&sup2;'   // (178)
        , chr(0xB3) => '&sup3;'   // (179)
        , chr(0xB4) => '&acute;'  // (180)
        , chr(0xB5) => '&micro;'  // (181)
        , chr(0xB6) => '&para;'   // (182)
        , chr(0xB7) => '&middot;' // (183)
        , chr(0xB8) => '&cedil;'  // (184)
        , chr(0xB9) => '&sup1;'   // (185)
        , chr(0xBA) => '&ordm;'   // (186)
        , chr(0xBB) => '&raquo;'  // (187)
        , chr(0xBC) => '&frac14;' // (188)
        , chr(0xBD) => '&frac12;' // (189)
        , chr(0xBE) => '&frac34;' // (190)
        , chr(0xBF) => '&iquest;' // (191)
        , chr(0xC0) => '&Agrave;' // (192)
        , chr(0xC1) => '&Aacute;' // (193)
        , chr(0xC2) => '&Acirc;'  // (194)
        , chr(0xC3) => '&Atilde;' // (195)
        , chr(0xC4) => '&Auml;'   // (196)
        , chr(0xC5) => '&Aring;'  // (197)
        , chr(0xC6) => '&AElig;'  // (198)
        , chr(0xC7) => '&Ccedil;' // (199)
        , chr(0xC8) => '&Egrave;' // (200)
        , chr(0xC9) => '&Eacute;' // (201)
        , chr(0xCA) => '&Ecirc;'  // (202)
        , chr(0xCB) => '&Euml;'   // (203)
        , chr(0xCC) => '&Igrave;' // (204)
        , chr(0xCD) => '&Iacute;' // (205)
        , chr(0xCE) => '&Icirc;'  // (206)
        , chr(0xCF) => '&Iuml;'   // (207)
        , chr(0xD0) => '&ETH;'    // (208)
        , chr(0xD1) => '&Ntilde;' // (209)
        , chr(0xD2) => '&Ograve;' // (210)
        , chr(0xD3) => '&Oacute;' // (211)
        , chr(0xD4) => '&Ocirc;'  // (212)
        , chr(0xD5) => '&Otilde;' // (213)
        , chr(0xD6) => '&Ouml;'   // (214)
        , chr(0xD7) => '&times;'  // (215)
        , chr(0xD8) => '&Oslash;' // (216)
        , chr(0xD9) => '&Ugrave;' // (217)
        , chr(0xDA) => '&Uacute;' // (218)
        , chr(0xDB) => '&Ucirc;'  // (219)
        , chr(0xDC) => '&Uuml;'   // (220)
        , chr(0xDD) => '&Yacute;' // (221)
        , chr(0xDE) => '&THORN;'  // (222)
        , chr(0xDF) => '&szlig;'  // (223)
        , chr(0xE0) => '&agrave;' // (224)
        , chr(0xE1) => '&aacute;' // (225)
        , chr(0xE2) => '&acirc;'  // (226)
        , chr(0xE3) => '&atilde;' // (227)
        , chr(0xE4) => '&auml;'   // (228)
        , chr(0xE5) => '&aring;'  // (229)
        , chr(0xE6) => '&aelig;'  // (230)
        , chr(0xE7) => '&ccedil;' // (231)
        , chr(0xE8) => '&egrave;' // (232)
        , chr(0xE9) => '&eacute;' // (233)
        , chr(0xEA) => '&ecirc;'  // (234)
        , chr(0xEB) => '&euml;'   // (235)
        , chr(0xEC) => '&igrave;' // (236)
        , chr(0xED) => '&iacute;' // (237)
        , chr(0xEE) => '&icirc;'  // (238)
        , chr(0xEF) => '&iuml;'   // (239)
        , chr(0xF0) => '&eth;'    // (240)
        , chr(0xF1) => '&ntilde;' // (241)
        , chr(0xF2) => '&ograve;' // (242)
        , chr(0xF3) => '&oacute;' // (243)
        , chr(0xF4) => '&ocirc;'  // (244)
        , chr(0xF5) => '&otilde;' // (245)
        , chr(0xF6) => '&ouml;'   // (246)
        , chr(0xF7) => '&divide;' // (247)
        , chr(0xF8) => '&oslash;' // (248)
        , chr(0xF9) => '&ugrave;' // (249)
        , chr(0xFA) => '&uacute;' // (250)
        , chr(0xFB) => '&ucirc;'  // (251)
        , chr(0xFC) => '&uuml;'   // (252)
        , chr(0xFD) => '&yacute;' // (253)
        , chr(0xFE) => '&thorn;'  // (254)
        , chr(0xFF) => '&yuml;'   // (255)
         *
         */
        ];

        $translated = strtr($text, $map);
        $encoding = mb_detect_encoding($translated, 'UTF-8, ISO-8859-1');
        $recoded = mb_convert_encoding($translated, 'UTF-8', $encoding);

        return html_entity_decode($recoded, ENT_QUOTES, 'UTF-8');
    }

}

