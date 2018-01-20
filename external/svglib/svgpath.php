<?php
/**
 *
 * Description: Implementation of Path.
 *
 * Blog: http://trialforce.nostaljia.eng.br
 *
 * Started at Mar 18, 2010
 *
 * @version 0.1
 *
 * @author Eduardo Bonfandini
 *
 *-----------------------------------------------------------------------
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU Library General Public License as published
 *   by the Free Software Foundation; either version 3 of the License, or
 *   (at your option) any later version.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU Library General Public License for more details.
 *
 *   You should have received a copy of the GNU Library General Public
 *   License along with this program; if not, access
 *   http://www.fsf.org/licensing/licenses/lgpl.html or write to the
 *   Free Software Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
 *----------------------------------------------------------------------
 */

class SVGPath extends SVGShape
{
    /**
     * Get a instance of a Path.
     *
     * @param string or array $d the points
     * @param string $id of element
     * @param string or SVGStyle object $style of element
     * 
     * @return SVGPath
     */
    public static function getInstance( $d, $id, $style )
    {
        $path = new SVGPath('<path></path>');

        //if is as array make implode to glue it
        if ( is_array( $d ) )
        {
            $d = implode( ' ', $d);
        }

        $path->setAttribute('d', $d);
        $path->setId( $id );
        $path->setAttribute( 'style', $style );

        return $path;
    }
}
?>