<?php

namespace PhpXmlRpc\Helper;

use PhpXmlRpc\PhpXmlRpc;

class Http
{
    /**
     * Decode a string that is encoded with "chunked" transfer encoding as defined in rfc2068 par. 19.4.6
     * Code shamelessly stolen from nusoap library by Dietrich Ayala.
     *
     * @param string $buffer the string to be decoded
     *
     * @return string
     */
    public static function decodeChunked($buffer)
    {
        // length := 0
        $length = 0;
        $new = '';

        // read chunk-size, chunk-extension (if any) and crlf
        // get the position of the linebreak
        $chunkEnd = strpos($buffer, "\r\n") + 2;
        $temp = substr($buffer, 0, $chunkEnd);
        $chunkSize = hexdec(trim($temp));
        $chunkStart = $chunkEnd;
        while ($chunkSize > 0) {
            $chunkEnd = strpos($buffer, "\r\n", $chunkStart + $chunkSize);

            // just in case we got a broken connection
            if ($chunkEnd == false) {
                $chunk = substr($buffer, $chunkStart);
                // append chunk-data to entity-body
                $new .= $chunk;
                $length += strlen($chunk);
                break;
            }

            // read chunk-data and crlf
            $chunk = substr($buffer, $chunkStart, $chunkEnd - $chunkStart);
            // append chunk-data to entity-body
            $new .= $chunk;
            // length := length + chunk-size
            $length += strlen($chunk);
            // read chunk-size and crlf
            $chunkStart = $chunkEnd + 2;

            $chunkEnd = strpos($buffer, "\r\n", $chunkStart) + 2;
            if ($chunkEnd == false) {
                break; //just in case we got a broken connection
            }
            $temp = substr($buffer, $chunkStart, $chunkEnd - $chunkStart);
            $chunkSize = hexdec(trim($temp));
            $chunkStart = $chunkEnd;
        }

        return $new;
    }

    /**
     * Parses HTTP an http response headers and separates them from the body.
     *
     * @param string $data the http response,headers and body. It will be stripped of headers
     * @param bool $headersProcessed when true, we assume that response inflating and dechunking has been already carried out
     *
     * @return array with keys 'headers' and 'cookies'
     * @throws \Exception
     */
    public function parseResponseHeaders(&$data, $headersProcessed = false, $debug=0)
    {
        $httpResponse = array('raw_data' => $data, 'headers'=> array(), 'cookies' => array());

        // Support "web-proxy-tunnelling" connections for https through proxies
        if (preg_match('/^HTTP\/1\.[0-1] 200 Connection established/', $data)) {
            // Look for CR/LF or simple LF as line separator,
            // (even though it is not valid http)
            $pos = strpos($data, "\r\n\r\n");
            if ($pos || is_int($pos)) {
                $bd = $pos + 4;
            } else {
                $pos = strpos($data, "\n\n");
                if ($pos || is_int($pos)) {
                    $bd = $pos + 2;
                } else {
                    // No separation between response headers and body: fault?
                    $bd = 0;
                }
            }
            if ($bd) {
                // this filters out all http headers from proxy.
                // maybe we could take them into account, too?
                $data = substr($data, $bd);
            } else {
                error_log('XML-RPC: ' . __METHOD__ . ': HTTPS via proxy error, tunnel connection possibly failed');
                throw new \Exception(PhpXmlRpc::$xmlrpcstr['http_error'] . ' (HTTPS via proxy error, tunnel connection possibly failed)', PhpXmlRpc::$xmlrpcerr['http_error']);
            }
        }

        // Strip HTTP 1.1 100 Continue header if present
        while (preg_match('/^HTTP\/1\.1 1[0-9]{2} /', $data)) {
            $pos = strpos($data, 'HTTP', 12);
            // server sent a Continue header without any (valid) content following...
            // give the client a chance to know it
            if (!$pos && !is_int($pos)) {
                // works fine in php 3, 4 and 5

                break;
            }
            $data = substr($data, $pos);
        }

        // When using Curl to query servers using Digest Auth, we get back a double set of http headers.
        // We strip out the 1st...
        if ($headersProcessed && preg_match('/^HTTP\/[0-9.]+ 401 /', $data)) {
            if (preg_match('/(\r?\n){2}HTTP\/[0-9.]+ 200 /', $data)) {
                $data = preg_replace('/^HTTP\/[0-9.]+ 401 .+?(?:\r?\n){2}(HTTP\/[0-9.]+ 200 )/s', '$1', $data, 1);
            }
        }

        if (!preg_match('/^HTTP\/[0-9.]+ 200 /', $data)) {
            $errstr = substr($data, 0, strpos($data, "\n") - 1);
            error_log('XML-RPC: ' . __METHOD__ . ': HTTP error, got response: ' . $errstr);
            throw new \Exception(PhpXmlRpc::$xmlrpcstr['http_error'] . ' (' . $errstr . ')', PhpXmlRpc::$xmlrpcerr['http_error']);
        }

        // be tolerant to usage of \n instead of \r\n to separate headers and data
        // (even though it is not valid http)
        $pos = strpos($data, "\r\n\r\n");
        if ($pos || is_int($pos)) {
            $bd = $pos + 4;
        } else {
            $pos = strpos($data, "\n\n");
            if ($pos || is_int($pos)) {
                $bd = $pos + 2;
            } else {
                // No separation between response headers and body: fault?
                // we could take some action here instead of going on...
                $bd = 0;
            }
        }

        // be tolerant to line endings, and extra empty lines
        $ar = preg_split("/\r?\n/", trim(substr($data, 0, $pos)));

        foreach($ar as $line) {
            // take care of multi-line headers and cookies
            $arr = explode(':', $line, 2);
            if (count($arr) > 1) {
                $headerName = strtolower(trim($arr[0]));
                /// @todo some other headers (the ones that allow a CSV list of values)
                /// do allow many values to be passed using multiple header lines.
                /// We should add content to $xmlrpc->_xh['headers'][$headerName]
                /// instead of replacing it for those...
                if ($headerName == 'set-cookie' || $headerName == 'set-cookie2') {
                    if ($headerName == 'set-cookie2') {
                        // version 2 cookies:
                        // there could be many cookies on one line, comma separated
                        $cookies = explode(',', $arr[1]);
                    } else {
                        $cookies = array($arr[1]);
                    }
                    foreach ($cookies as $cookie) {
                        // glue together all received cookies, using a comma to separate them
                        // (same as php does with getallheaders())
                        if (isset($httpResponse['headers'][$headerName])) {
                            $httpResponse['headers'][$headerName] .= ', ' . trim($cookie);
                        } else {
                            $httpResponse['headers'][$headerName] = trim($cookie);
                        }
                        // parse cookie attributes, in case user wants to correctly honour them
                        // feature creep: only allow rfc-compliant cookie attributes?
                        // @todo support for server sending multiple time cookie with same name, but using different PATHs
                        $cookie = explode(';', $cookie);
                        foreach ($cookie as $pos => $val) {
                            $val = explode('=', $val, 2);
                            $tag = trim($val[0]);
                            $val = trim(@$val[1]);
                            /// @todo with version 1 cookies, we should strip leading and trailing " chars
                            if ($pos == 0) {
                                $cookiename = $tag;
                                $httpResponse['cookies'][$tag] = array();
                                $httpResponse['cookies'][$cookiename]['value'] = urldecode($val);
                            } else {
                                if ($tag != 'value') {
                                    $httpResponse['cookies'][$cookiename][$tag] = $val;
                                }
                            }
                        }
                    }
                } else {
                    $httpResponse['headers'][$headerName] = trim($arr[1]);
                }
            } elseif (isset($headerName)) {
                /// @todo version1 cookies might span multiple lines, thus breaking the parsing above
                $httpResponse['headers'][$headerName] .= ' ' . trim($line);
            }
        }

        $data = substr($data, $bd);

        if ($debug && count($httpResponse['headers'])) {
            $msg = '';
            foreach ($httpResponse['headers'] as $header => $value) {
                $msg .= "HEADER: $header: $value\n";
            }
            foreach ($httpResponse['cookies'] as $header => $value) {
                $msg .= "COOKIE: $header={$value['value']}\n";
            }
            Logger::instance()->debugMessage($msg);
        }

        // if CURL was used for the call, http headers have been processed,
        // and dechunking + reinflating have been carried out
        if (!$headersProcessed) {

            // Decode chunked encoding sent by http 1.1 servers
            if (isset($httpResponse['headers']['transfer-encoding']) && $httpResponse['headers']['transfer-encoding'] == 'chunked') {
                if (!$data = Http::decodeChunked($data)) {
                    error_log('XML-RPC: ' . __METHOD__ . ': errors occurred when trying to rebuild the chunked data received from server');
                    throw new \Exception(PhpXmlRpc::$xmlrpcstr['dechunk_fail'], PhpXmlRpc::$xmlrpcerr['dechunk_fail']);
                }
            }

            // Decode gzip-compressed stuff
            // code shamelessly inspired from nusoap library by Dietrich Ayala
            if (isset($httpResponse['headers']['content-encoding'])) {
                $httpResponse['headers']['content-encoding'] = str_replace('x-', '', $httpResponse['headers']['content-encoding']);
                if ($httpResponse['headers']['content-encoding'] == 'deflate' || $httpResponse['headers']['content-encoding'] == 'gzip') {
                    // if decoding works, use it. else assume data wasn't gzencoded
                    if (function_exists('gzinflate')) {
                        if ($httpResponse['headers']['content-encoding'] == 'deflate' && $degzdata = @gzuncompress($data)) {
                            $data = $degzdata;
                            if ($debug) {
                                Logger::instance()->debugMessage("---INFLATED RESPONSE---[" . strlen($data) . " chars]---\n$data\n---END---");
                            }
                        } elseif ($httpResponse['headers']['content-encoding'] == 'gzip' && $degzdata = @gzinflate(substr($data, 10))) {
                            $data = $degzdata;
                            if ($debug) {
                                Logger::instance()->debugMessage("---INFLATED RESPONSE---[" . strlen($data) . " chars]---\n$data\n---END---");
                            }
                        } else {
                            error_log('XML-RPC: ' . __METHOD__ . ': errors occurred when trying to decode the deflated data received from server');
                            throw new \Exception(PhpXmlRpc::$xmlrpcstr['decompress_fail'], PhpXmlRpc::$xmlrpcerr['decompress_fail']);
                        }
                    } else {
                        error_log('XML-RPC: ' . __METHOD__ . ': the server sent deflated data. Your php install must have the Zlib extension compiled in to support this.');
                        throw new \Exception(PhpXmlRpc::$xmlrpcstr['cannot_decompress'], PhpXmlRpc::$xmlrpcerr['cannot_decompress']);
                    }
                }
            }
        } // end of 'if needed, de-chunk, re-inflate response'

        return $httpResponse;
    }
}
