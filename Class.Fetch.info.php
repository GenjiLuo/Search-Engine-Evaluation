<?php

/* php easy :: seo report script */

/* * **********Function Start******************* */

function clean($string) {
    $specialCharacters = array(
        "#" => "",
        "$" => "",
        "%" => "",
        "&" => "",
        "@" => "",
        "." => "",
        "€" => "",
        "+" => "",
        "=" => "",
        "§" => "",
        "/" => "",
        "'" => "",
        ";" => "",
        "`" => ""
    );
    while (list($character, $replacement) = each($specialCharacters)) {
        $string = str_replace($character, '-' . $replacement . '-', $string);
    }
    // Remove all remaining other unknown characters
    $string = preg_replace('/[^a-zA-Z0-9-]/', ' ', $string);
    $string = preg_replace('/^[-]+/', '', $string);
    $string = preg_replace('/[-]+$/', '', $string);
    $string = preg_replace('/[-]{2,}/', ' ', $string);
    return $string;
}

/* * **********Function Close******************* */
/* * **********Function Start******************* */

function get_match($regex, $content) {
    preg_match($regex, $content, $matches);
    return $matches[1];
}

/* * **********Function Close******************* */
/* * **********Function Start******************* */

function getPageTitle($host) {
    $fp = fopen($host, "r");
    $content = "";
    while (!feof($fp)) {
        $buffer = trim(fgets($fp, 4096));
        $content .= $buffer;
    } //!feof( $fp )
    $start = "<title>";
    $end = "<\/title>";
    $title = get_match("/$start(.*)$end/isU", $content);
    return $title;
}

/* * **********Function Close******************* */
/* * **********Function Start******************* */

function getMetaKeyword($host) {
    $metatagarray = get_meta_tags($host);
    $keywords = $metatagarray["keywords"];
    return $keywords;
}

/* * **********Function Close******************* */
/* * **********Function Start******************* */

function getMetaDescription($host) {
    $metatagarray = get_meta_tags($host);
    $description = $metatagarray["description"];
    return $description;
}

/* * **********Function Close******************* */
/* * **********Function Start******************* */

function getLastModified($host) {
    $head = get_headers($host, 1);
    return $head['Last-Modified'];
}

/* * **********Function Close******************* */
/* * **********Function Start******************* */
/* define( 'GOOGLE_MAGIC', 0xE6359A60 );
  function _zeroFill( $a, $b )
  {
  $z = hexdec( 80000000 );
  if ( $z & $a )
  {
  $a = ( $a >> 1 );
  $a &= ( ~$z );
  $a |= 0x40000000;
  $a = ( $a >> ( $b - 1 ) );
  } //$z & $a
  else
  $a = ( $a >> $b );
  return $a;
  }
  function _mix( $a, $b, $c )
  {
  $a -= $b;
  $a -= $c;
  $a ^= ( _zeroFill( $c, 13 ) );
  $b -= $c;
  $b -= $a;
  $b ^= ( $a << 8 );
  $c -= $a;
  $c -= $b;
  $c ^= ( _zeroFill( $b, 13 ) );
  $a -= $b;
  $a -= $c;
  $a ^= ( _zeroFill( $c, 12 ) );
  $b -= $c;
  $b -= $a;
  $b ^= ( $a << 16 );
  $c -= $a;
  $c -= $b;
  $c ^= ( _zeroFill( $b, 5 ) );
  $a -= $b;
  $a -= $c;
  $a ^= ( _zeroFill( $c, 3 ) );
  $b -= $c;
  $b -= $a;
  $b ^= ( $a << 10 );
  $c -= $a;
  $c -= $b;
  $c ^= ( _zeroFill( $b, 15 ) );
  return array(
  $a,
  $b,
  $c
  );
  }
  function _GoogleCH( $url, $length = null, $init = GOOGLE_MAGIC )
  {
  if ( is_null( $length ) )
  $length = sizeof( $url );
  $a   = $b = 0x9E3779B9;
  $c   = $init;
  $k   = 0;
  $len = $length;
  while ( $len >= 12 )
  {
  $a += ( $url[ $k + 0 ] + ( $url[ $k + 1 ] << 8 ) + ( $url[ $k + 2 ] << 16 ) + ( $url[ $k + 3 ] << 24 ) );
  $b += ( $url[ $k + 4 ] + ( $url[ $k + 5 ] << 8 ) + ( $url[ $k + 6 ] << 16 ) + ( $url[ $k + 7 ] << 24 ) );
  $c += ( $url[ $k + 8 ] + ( $url[ $k + 9 ] << 8 ) + ( $url[ $k + 10 ] << 16 ) + ( $url[ $k + 11 ] << 24 ) );
  $_mix = _mix( $a, $b, $c );
  $a    = $_mix[ 0 ];
  $b    = $_mix[ 1 ];
  $c    = $_mix[ 2 ];
  $k += 12;
  $len -= 12;
  } //$len >= 12
  $c += $length;
  switch ( $len )
  {
  case 11:
  $c += ( $url[ $k + 10 ] << 24 );
  case 10:
  $c += ( $url[ $k + 9 ] << 16 );
  case 9:
  $c += ( $url[ $k + 8 ] << 8 );
  case 8:
  $b += ( $url[ $k + 7 ] << 24 );
  case 7:
  $b += ( $url[ $k + 6 ] << 16 );
  case 6:
  $b += ( $url[ $k + 5 ] << 8 );
  case 5:
  $b += ( $url[ $k + 4 ] );
  case 4:
  $a += ( $url[ $k + 3 ] << 24 );
  case 3:
  $a += ( $url[ $k + 2 ] << 16 );
  case 2:
  $a += ( $url[ $k + 1 ] << 8 );
  case 1:
  $a += ( $url[ $k + 0 ] );
  } //$len
  $_mix = _mix( $a, $b, $c );
  return $_mix[ 2 ];
  }
  function _strord( $string )
  {
  for ( $i = 0; $i < strlen( $string ); $i++ )
  $result[ $i ] = ord( $string{$i} );
  return $result;
  }
  function getGooglePageRank( $url )
  {
  $pagerank = -1;
  $ch       = "6" . _GoogleCH( _strord( "info:" . $url ) );
  $fp       = fsockopen( "www.google.com", 80, $errno, $errstr, 180 );
  if ( $fp )
  {
  $out = "GET /search?client=navclient-auto&ch=" . $ch . "&features=Rank&q=info:" . $url . " HTTP/1.1\r\n";
  $out .= "Host: www.google.com\r\n";
  $out .= "Connection: Close\r\n\r\n";
  fwrite( $fp, $out );
  while ( !feof( $fp ) )
  {
  $data = fgets( $fp, 128 );
  $pos  = strpos( $data, "Rank_" );
  if ( $pos === false )
  {
  } //$pos === false
  else
  $pagerank = substr( $data, $pos + 9 );
  } //!feof( $fp )
  fclose( $fp );
  } //$fp

  return $pagerank;

  } */
/* * **********Function Close******************* */

/* * **********Function Start******************* */

/* Google Page Rank New Script */

//----------------------------------------------------------------------------
// Get Google Page Rank. Function: google_page_rank
function getGooglePageRank($url) {
    // URL or domain name
    if (strlen(trim($url)) > 0) {
        /* if (strstr($url, 'http://') == false) {
          $_url = 'http://' . $url;
          } */
        $pagerank = trim(/* $this-> */GooglePageRank($url));
        if (empty($pagerank))
            $pagerank = 0;
        return(int) ($pagerank);
    }
    return 0;
}

function GooglePageRank($url) {
    $fp = fsockopen("toolbarqueries.google.com", 80, $errno, $errstr, 30);
    if (!$fp) {
        echo "$errstr ($errno)<br />\n";
    } else {
        $out = "GET /search?client=navclient-auto&ch=" . /* $this-> */CheckHash(/* $this-> */HashURL($url)) . "&features=Rank&q=info:" . $url . "&num=100&filter=0 HTTP/1.1\r\n";
        $out .= "Host: toolbarqueries.google.com\r\n";
        $out .= "User-Agent: Mozilla/4.0 (compatible; GoogleToolbar 2.0.114-big; Windows XP 5.1)\r\n";
        $out .= "Connection: Close\r\n\r\n";
        fwrite($fp, $out);
        while (!feof($fp)) {
            $data = fgets($fp, 128);
            $pos = strpos($data, "Rank_");
            if ($pos === false) {
                
            } else {
                $pagerank = substr($data, $pos + 9);
            }
        }
        fclose($fp);
        return $pagerank;
    }
}

function StrToNum($Str, $Check, $Magic) {
    // 2^32
    $Int32Unit = 4294967296;
    $length = strlen($Str);
    for ($i = 0; $i < $length; $i++) {
        $Check *= $Magic;
        if ($Check >= $Int32Unit) {
            $Check = ($Check - $Int32Unit * (int) ($Check / $Int32Unit));
            $Check = ($Check < -2147483648) ? ($Check + $Int32Unit) : $Check;
        }
        $Check += ord($Str{$i});
    }
    return $Check;
}

function HashURL($String) {
    $Check1 = /* $this-> */StrToNum($String, 0x1505, 0x21);
    $Check2 = /* $this-> */StrToNum($String, 0, 0x1003F);
    $Check1 >>= 2;
    $Check1 = (($Check1 >> 4) & 0x3FFFFC0) | ($Check1 & 0x3F);
    $Check1 = (($Check1 >> 4) & 0x3FFC00) | ($Check1 & 0x3FF);
    $Check1 = (($Check1 >> 4) & 0x3C000) | ($Check1 & 0x3FFF);
    $T1 = (((($Check1 & 0x3C0) << 4) | ($Check1 & 0x3C)) << 2) | ($Check2 & 0xF0F);
    $T2 = (((($Check1 & 0xFFFFC000) << 4) | ($Check1 & 0x3C00)) << 0xA) | ($Check2 & 0xF0F0000);
    return($T1 | $T2);
}

function CheckHash($Hashnum) {
    $CheckByte = 0;
    $Flag = 0;
    $HashStr = sprintf('%u', $Hashnum);
    $length = strlen($HashStr);
    for ($i = $length - 1; $i >= 0; $i--) {
        $Re = $HashStr{$i};
        if (1 === ($Flag % 2)) {
            $Re += $Re;
            $Re = (int) ($Re / 10) + ($Re % 10);
        }
        $CheckByte += $Re;
        $Flag++;
    }
    $CheckByte %= 10;
    if (0 !== $CheckByte) {
        $CheckByte = 10 - $CheckByte;
        if (1 === ($Flag % 2)) {
            if (1 === ($CheckByte % 2)) {
                $CheckByte += 9;
            }
            $CheckByte >>= 1;
        }
    }
    return '7' . $CheckByte . $HashStr;
}

/* * **********Function Close******************* */


/* * **********Function Start******************* */

function valid_domain($url) {
    if (strstr($url, 'http://') == false) {
        $url = 'http://' . $url;
    }
    /* elseif (strstr($url, 'www.') == false) {
      $url = 'www.' . $url;
      } */
    return $url;
}

/* * **********Function Close******************* */
/* * **********Function Start******************* */

function getGooglePages($host) {
    $request = "http://www.google.com/search?q=" . urlencode("site:" . $host) . "&amp;hl=en";
    $data = getPageData($request);
    preg_match('/<div id=resultStats>(About )?([\d,]+) result/si', $data, $p);
    $value = ( $p[2] ) ? $p[2] : "Not-Available";
    //$string = "<a href=\"" . $request . "\">" . $value . "</a>";
    $string = $value;
    return $string;
}

/* * **********Function Close******************* */
/* * **********Function Start******************* */

function getGoogleLinks($host) {
    $request = "http://www.google.com/search?q=" . urlencode("link:" . $host) . "&amp;hl=en";
    $data = getPageData($request);
    preg_match('/<div id=resultStats>(About )?([\d,]+) result/si', $data, $l);
    $value = ( $l[2] ) ? $l[2] : "Not-Available";
    //$string = "<a href=\"" . $request . "\">" . $value . "</a>";
    $string = $value;
    return $string;
}

/* * **********Function Close******************* */
/* * **********Function Start******************* */

function getYahooPages($host) {
    $request = "http://siteexplorer.search.yahoo.com/search?p=" . urlencode("http://" . $host);
    $data = getPageData($request);
    preg_match('/Pages \(([\d,]+)/si', $data, $p);
    $value = ( $p[1] ) ? $p[1] : "Not-Available";
    $string = "<a href=\"" . $request . "\">" . $value . "</a>";
    $string = $value;
    return $string;
}

/* * **********Function Close******************* */
/* * **********Function Start******************* */

function getYahooLinks($host) {
    $request = "http://siteexplorer.search.yahoo.com/search?p=" . urlencode("http://" . $host);
    $data = getPageData($request);
    preg_match('/Inlinks \(([\d,]+)/si', $data, $l);
    $value = ( $l[1] ) ? $l[1] : "Not-Available";
    //$string .= "<a href=\"" . $request . "&amp;bwm=i\">" . $value . "</a>";
    $string .= $value;
    return $string;
}

/* * **********Function Close******************* */
/* * **********Function Start******************* */

function getBingPages($host) {
    $request = "http://www.bing.com/search?q=" . urlencode("site:" . $host) . "&amp;mkt=en-US";
    $data = getPageData($request);
    preg_match('/1-([\d]+) of ([\d,]+)/si', $data, $p);
    $value = ( $p[2] ) ? $p[2] : "Not-Available";
    //$string = "<a href=\"" . $request . "\">" . $value . "</a>";
    $string = $value;
    return $string;
}

/* * **********Function Close******************* */
/* * **********Function Start******************* */

function getBingLinks($host) {
    $request = "http://www.bing.com/search?q=" . urlencode("inbody:" . $host) . "&amp;mkt=en-US";
    $data = getPageData($request);
    preg_match('/1-([\d]+) of ([\d,]+)/si', $data, $p);
    $value = ( $p[2] ) ? $p[2] : "Not-Available";
    //$string = "<a href=\"" . $request . "\">" . $value . "</a>";
    $string = $value;
    return $string;
}

/* * **********Function Close******************* */
/* * **********Function Start******************* */

function getAskPages($host) {
    $request = "http://www.ask.com/web?q=" . urlencode($host . " site:" . $host);
    $data = getPageData($request);
    preg_match('/<span id=\'indexLast\' class=\'b\'>([\d]+)<\/span> of ([\d,]+)/si', $data, $p);
    $value = ( $p[2] ) ? $p[2] : "Not-Available";
    //$string = "<a href=\"" . $request . "\">" . $value . "</a>";
    $string = $value;
    return $string;
}

/* * **********Function Close******************* */
/* * **********Function Start******************* */

function getAlexaRank($domain) {
    $request = "http://data.alexa.com/data?cli=10&amp;dat=s&amp;url=" . $domain;
    $data = getPageData($request);
    preg_match('/<POPULARITY URL="(.*?)" TEXT="([\d]+)"\/>/si', $data, $p);
    $value = ( $p[2] ) ? $p[2] : "Not-Available";
    //$string = "<a href=\"http://www.alexa.com/siteinfo/" . $domain . "\">" . number_format($value) . "</a>";
    $string = number_format($value);
    return $string;
}

/* * **********Function Close******************* */
/* * **********Function Start******************* */

function getAlexaLinks($domain) {
    $request = "http://data.alexa.com/data?cli=10&amp;dat=s&amp;url=" . $domain;
    $data = getPageData($request);
    preg_match('/<LINKSIN NUM="([\d]+)"\/>/si', $data, $l);
    $value = ( $l[1] ) ? $l[1] : "Not-Available";
    //$string = "<a href=\"http://www.alexa.com/site/linksin/" . $domain . "\">" . number_format($value) . "</a>";
    $string = number_format($value);
    return $string;
}

/* * **********Function Close******************* */
/* * **********Function Start******************* */

function getDMOZListings($domain) {
    $request = "http://data.alexa.com/data?cli=10&amp;url=" . $domain;
    $data = getPageData($request);
    preg_match('/<SITE BASE="(.*?)" TITLE="(.*?)" DESC="(.*?)">/si', $data, $s);
    $value1 = ( $s[1] ) ? $s[1] : "";
    $value2 = ( $s[2] ) ? $s[2] : "";
    $value3 = ( $s[3] ) ? $s[3] : "";
    preg_match('/<CAT ID="(.*?)" TITLE="(.*?)" CID="(.*?)"\/>/si', $data, $c);
    $value4 = ( $c[1] ) ? $c[1] : "";
    $value5 = ( $c[2] ) ? $c[2] : "";
    $value6 = ( $c[3] ) ? $c[3] : "";
    $string = "";
    if ($value4) {
        //$string = "<a href=\"http://www.dmoz.org/" . str_replace("Top/", "", $value4) . "\" title=\"" . $value2 . " - " . $value3 . "\">" . $value5 . "</a>";
        $string = $value5;
    } //$value4
    else
        $string = "Not-Available";
    return $string;
}

/* * **********Function Close******************* */
/* * **********Function Start******************* */

function getSiteAdvisorRating($domain) {
    $request = "http://www.siteadvisor.com/sites/" . $domain . "?ref=safe&amp;locale=en-US";
    $data = getPageData($request);
    preg_match('/(green|yellow|red)-xbg2\.gif/si', $data, $r);
    $value = ( $r[1] ) ? $r[1] : "grey";
    //$string = "<a href=\"" . $request . "\">" . $value . "</a>";
    $string = $value;
    return $string;
}

/* * **********Function Close******************* */
/* * **********Function Start******************* */

function getWOTRating($domain) {
    $request = "http://api.mywot.com/0.4/public_query2?target=" . $domain;
    $data = getPageData($request);
    preg_match_all('/<application name="(\d+)" r="(\d+)" c="(\d+)"\/>/si', $data, $regs);
    $trustworthiness = ( $regs[2][0] ) ? $regs[2][0] : -1;
    $values = array(
        "Trustworthy",
        "Mostly",
        "Suspicious",
        "Untrustworthy",
        "Dangerous",
        "Unknown"
    );
    if ($trustworthiness >= 80)
        $value = $values[0];
    elseif ($trustworthiness >= 60)
        $value = $values[1];
    elseif ($trustworthiness >= 40)
        $value = $values[2];
    elseif ($trustworthiness >= 20)
        $value = $values[3];
    elseif ($trustworthiness >= 0)
        $value = $values[4];
    else
        $value = $values[5];
    //$string = "<a href=\"http://www.mywot.com/en/scorecard/" . $domain . "\">" . $value . "</a>";
    $string = $value;
    return $string;
}

/* * **********Function Close******************* */
/* * **********Function Start******************* */

function getDomainAge($domain) {
    $request = "http://reports.internic.net/cgi/whois?whois_nic=" . $domain . "&type=domain";
    $data = getPageData($request);
    preg_match('/Creation Date: ([a-z0-9-]+)/si', $data, $p);
    if (!$p[1]) {
        $value = "Unknown";
    } //!$p[ 1 ]
    else {
        $time = time() - strtotime($p[1]);
        $years = floor($time / 31556926);
        $days = floor(( $time % 31556926 ) / 86400);
        if ($years == "1") {
            $y = "1 year";
        } //$years == "1"
        else {
            $y = $years . " years";
        }
        if ($days == "1") {
            $d = "1 day";
        } //$days == "1"
        else {
            $d = $days . " days";
        }
        $value = "$y, $d";
    }
    //$string = "<a href=\"" . $request . "\">" . $value . "</a>";
    if ($value == 'Unknown') {
        //$domain = explode('.',$domain);
        $domain = substr($domain, 0, -1);
        //echo $domain.': if';
        return getDomainAge($domain);
    } //$value == 'Unknown'
    else {
    ///echo $domain.': else';
        $string = $value;
        return $string;
    }
}

/* * **********Function Close******************* */
/* * **********Function Start******************* */

function getPageData($url) {
    return file_get_contents($url);
}

/* * **********Function Close******************* */
/* * **********Function Start******************* */

function getDomainName($host) {
    // split host name to parts
    $hostparts = explode('.', $host);
    // get parts number
    $num = count($hostparts);
    if (preg_match('/^(ac|arpa|biz|co|com|edu|gov|info|int|me|mil|mobi|museum|name|net|org|pp|tv)$/i', $hostparts[$num - 2])) {
        // for ccTLDs like .co.uk etc.
        $domain = $hostparts[$num - 3] . '.' . $hostparts[$num - 2] . '.' . $hostparts[$num - 1];
    } //preg_match( '/^(ac|arpa|biz|co|com|edu|gov|info|int|me|mil|mobi|museum|name|net|org|pp|tv)$/i', $hostparts[ $num - 2 ] )
    else {
        $domain = $hostparts[$num - 2] . '.' . $hostparts[$num - 1];
    }
    return $domain;
}

/* * **********Function Close******************* */
/* * **********Function Start******************* */
/* function getValidUrl( $realurl )
  {
  $realurl = strstr($realurl, '.com/', true);
  $realurl = $realurl.'.com/';
  return $realurl;
  } */

function get_tld_from_url($url) {
    $tld = '';

    $url = valid_domain($url);
    $url_parts = parse_url((string) $url);
    if (is_array($url_parts) && isset($url_parts['host'])) {
        $host_parts = explode('.', $url_parts['host']);
        if (is_array($host_parts) && count($host_parts) > 0) {
            $tld = array_pop($host_parts);
            $tld = strstr($url, $tld, true) . $tld;
        }
    }

    return $tld;
}

/* * **********Function Close******************* */
/* * **********Function Start******************* */

function getShortUrl($realurl) {
    $realurl = str_replace('http://', '', $realurl);
    $realurl = str_replace('/', '', $realurl);
    return $realurl;
}

/************Function Close********************/