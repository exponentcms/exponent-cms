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

/**
 * DTEND property functions
 *
 * @author Kjell-Inge Gustafsson, kigkonsult <ical@kigkonsult.se>
 * @since  2.22.23 - 2017-02-02
 */
trait DTENDtrait
{
    /**
     * @var array component property DTEND value
     * @access protected
     */
    protected $dtend = null;

    /**
     * Return formatted output for calendar component property dtend
     *
     * @return string
     */
    public function createDtend() {
        if( empty( $this->dtend )) {
            return null;
        }
        if( Util::hasNodate( $this->dtend )) {
            return ( $this->getConfig( Util::$ALLOWEMPTY )) ? Util::createElement( Util::$DTEND ) : null;
        }
        $parno = Util::isParamsValueSet( $this->dtend, Util::$DATE ) ? 3 : null;
        return Util::createElement(
            Util::$DTEND,
            Util::createParams( $this->dtend[Util::$LCparams] ),
            Util::date2strdate( $this->dtend[Util::$LCvalue], $parno )
        );
    }

    /**
     * Set calendar component property dtend
     *
     * @param mixed  $year
     * @param mixed  $month
     * @param int    $day
     * @param int    $hour
     * @param int    $min
     * @param int    $sec
     * @param string $tz
     * @param array  $params
     * @return bool
     */
    public function setDtend(
        $year,
        $month = null,
        $day = null,
        $hour = null,
        $min = null,
        $sec = null,
        $tz = null,
        $params = null
    ) {
        if( empty( $year )) {
            if( $this->getConfig( Util::$ALLOWEMPTY )) {
                $this->dtend = [
                    Util::$LCvalue  => Util::$SP0,
                    Util::$LCparams => Util::setParams( $params ),
                ];
                return true;
            }
            else {
                return false;
            }
        }
        if( false === ( $tzid = $this->getConfig( Util::$TZID ))) {
            $tzid = null;
        }
        $this->dtend = Util::setDate( $year, $month, $day, $hour, $min, $sec, $tz,
                                      $params, null, null, $tzid
        );
        return true;
    }
}
