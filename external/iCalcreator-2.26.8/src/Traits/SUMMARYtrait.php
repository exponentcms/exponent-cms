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
 * SUMMARY property functions
 *
 * @author Kjell-Inge Gustafsson, kigkonsult <ical@kigkonsult.se>
 * @since  2.22.23 - 2017-02-02
 */
trait SUMMARYtrait
{
    /**
     * @var array component property SUMMARY value
     * @access protected
     */
    protected $summary = null;

    /**
     * Return formatted output for calendar component property summary
     *
     * @return string
     */
    public function createSummary() {
        if( empty( $this->summary )) {
            return null;
        }
        if( empty( $this->summary[Util::$LCvalue] )) {
            return ( $this->getConfig( Util::$ALLOWEMPTY )) ? Util::createElement( Util::$SUMMARY ) : null;
        }
        return Util::createElement(
            Util::$SUMMARY,
            Util::createParams(
                $this->summary[Util::$LCparams],
                Util::$ALTRPLANGARR,
                $this->getConfig( Util::$LANGUAGE )
            ),
            Util::strrep( $this->summary[Util::$LCvalue] )
        );
    }

    /**
     * Set calendar component property summary
     *
     * @param string $value
     * @param array  $params
     * @return bool
     */
    public function setSummary( $value, $params = null ) {
        if( empty( $value )) {
            if( $this->getConfig( Util::$ALLOWEMPTY )) {
                $value = Util::$SP0;
            }
            else {
                return false;
            }
        }
        $this->summary = [
            Util::$LCvalue  => $value,
            Util::$LCparams => Util::setParams( $params ),
        ];
        return true;
    }
}
