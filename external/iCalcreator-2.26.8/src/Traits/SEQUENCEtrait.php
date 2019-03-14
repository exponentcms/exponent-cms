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

use function is_numeric;

/**
 * SEQUENCE property functions
 *
 * @author Kjell-Inge Gustafsson, kigkonsult <ical@kigkonsult.se>
 * @since  2.22.23 - 2017-02-24
 */
trait SEQUENCEtrait
{
    /**
     * @var array component property SEQUENCE value
     * @access protected
     */
    protected $sequence = null;

    /**
     * Return formatted output for calendar component property sequence
     *
     * @return string
     */
    public function createSequence() {
        if( ! isset( $this->sequence ) ||
            ( empty( $this->sequence ) && ! is_numeric( $this->sequence ))) {
            return null;
        }
        if(( ! isset( $this->sequence[Util::$LCvalue] ) ||
                ( empty( $this->sequence[Util::$LCvalue] ) && ! is_numeric( $this->sequence[Util::$LCvalue] ))) &&
                ( Util::$ZERO != $this->sequence[Util::$LCvalue] )) {
            return ( $this->getConfig( Util::$ALLOWEMPTY )) ? Util::createElement( Util::$SEQUENCE ) : null;
        }
        return Util::createElement(
            Util::$SEQUENCE,
            Util::createParams( $this->sequence[Util::$LCparams] ),
            $this->sequence[Util::$LCvalue]
        );
    }

    /**
     * Set calendar component property sequence
     *
     * @param int   $value
     * @param array $params
     * @return bool
     */
    public function setSequence( $value = null, $params = null ) {
        if(( empty( $value ) && ! is_numeric( $value )) && ( Util::$ZERO != $value )) {
            $value = ( isset( $this->sequence[Util::$LCvalue] ) &&
                ( -1 < $this->sequence[Util::$LCvalue] ))
                ? $this->sequence[Util::$LCvalue] + 1
                : Util::$ZERO;
        }
        $this->sequence = [
            Util::$LCvalue  => $value,
            Util::$LCparams => Util::setParams( $params ),
        ];
        return true;
    }
}
