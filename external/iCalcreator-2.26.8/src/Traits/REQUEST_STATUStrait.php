<?php
/**
 * iCalcreator, a PHP rfc2445/rfc5545 solution.
 *
 * This file is a part of iCalcreator.
 *
 * Copyright (c) 2007-2018 Kjell-Inge Gustafsson, kigkonsult, All rights reserved
 * Link      http://kigkonsult.se/iCalcreator/index.php
 * Package   iCalcreator
 * Version   2.26
 * License   Subject matter of licence is the software iCalcreator.
 *           The above copyright, link, package and version notices,
 *           this licence notice and the [rfc5545] PRODID as implemented and
 *           invoked in iCalcreator shall be included in all copies or
 *           substantial portions of the iCalcreator.
 *           iCalcreator can be used either under the terms of
 *           a proprietary license, available from iCal_at_kigkonsult_dot_se
 *           or the GNU Affero General Public License, version 3:
 *           iCalcreator is free software: you can redistribute it and/or
 *           modify it under the terms of the GNU Affero General Public License
 *           as published by the Free Software Foundation, either version 3 of
 *           the License, or (at your option) any later version.
 *           iCalcreator is distributed in the hope that it will be useful,
 *           but WITHOUT ANY WARRANTY; without even the implied warranty of
 *           MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *           GNU Affero General Public License for more details.
 *           You should have received a copy of the GNU Affero General Public
 *           License along with this program.
 *           If not, see <http://www.gnu.org/licenses/>.
 */

namespace Kigkonsult\Icalcreator\Traits;

use Kigkonsult\Icalcreator\Util\Util;

use function number_format;

/**
 * REQUEST-STATUS property functions
 *
 * @author Kjell-Inge Gustafsson, kigkonsult <ical@kigkonsult.se>
 * @since  2.22.23 - 2017-02-19
 */
trait REQUEST_STATUStrait
{
    /**
     * @var array component property REQUEST-STATUS value
     * @access protected
     */
    protected $requeststatus = null;

    /**
     * @var string Request-status properties
     * @access private
     * @static
     */
    private static $STATCODE = 'statcode';
    private static $TEXT     = 'text';
    private static $EXTDATA  = 'extdata';
    /**
     * Return formatted output for calendar component property request-status
     *
     * @return string
     */
    public function createRequestStatus() {
        if( empty( $this->requeststatus )) {
            return null;
        }
        $output = null;
        $lang   = $this->getConfig( Util::$LANGUAGE );
        foreach( $this->requeststatus as $rx => $rStat ) {
            if( empty( $rStat[Util::$LCvalue][self::$STATCODE] )) {
                if( $this->getConfig( Util::$ALLOWEMPTY )) {
                    $output .= Util::createElement( Util::$REQUEST_STATUS );
                }
                continue;
            }
            $content = number_format(
                (float) $rStat[Util::$LCvalue][self::$STATCODE],
                2,
                Util::$DOT,
                null
            );
            $content .= Util::$SEMIC . Util::strrep( $rStat[Util::$LCvalue][self::$TEXT] );
            if( isset( $rStat[Util::$LCvalue][self::$EXTDATA] )) {
                $content .= Util::$SEMIC . Util::strrep( $rStat[Util::$LCvalue][self::$EXTDATA] );
            }
            $output .= Util::createElement(
                Util::$REQUEST_STATUS,
                Util::createParams( $rStat[Util::$LCparams], [ Util::$LANGUAGE ], $lang ),
                $content
            );
        }
        return $output;
    }

    /**
     * Set calendar component property request-status
     *
     * @param float   $statcode
     * @param string  $text
     * @param string  $extdata
     * @param array   $params
     * @param integer $index
     * @return bool
     */
    public function setRequestStatus( $statcode, $text, $extdata = null, $params = null, $index = null ) {
        if( empty( $statcode ) || empty( $text )) {
            if( $this->getConfig( Util::$ALLOWEMPTY )) {
                $statcode = $text = Util::$SP0;
            }
            else {
                return false;
            }
        }
        $input = [
            self::$STATCODE => $statcode,
            self::$TEXT     => Util::trimTrailNL( $text ),
        ];
        if( $extdata ) {
            $input[self::$EXTDATA] = Util::trimTrailNL( $extdata );
        }
        Util::setMval( $this->requeststatus, $input, $params, false, $index );
        return true;
    }
}
