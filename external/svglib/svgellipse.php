<?php
/**
 *
 * Description: Implementation of Ellipse.
 *
 * Blog: http://trialforce.nostaljia.eng.br
 *
 * Started at Mar 11, 2011
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
class SVGEllipse extends SVGShapeEx
{
    /**
     * Construct a circle
     * 
     * @param integer $cx the center x
     * @param integer $cy the center y
     * @param integer $radius the radius of circle
     * @param string $id the id of element
     * @param SVGStyle $style style of element
     * 
     * @return SVGCircle 
     */
    public static function getInstance( $cx, $cy, $radiusX, $radiusY, $id = null, $style = null )
    {
        $circle = new SVGEllipse('<ellipse></ellipse>');

        $circle->setCx( $cx );
        $circle->setCy( $cy );
        $circle->setRadius($radiusX, $radiusY );
        $circle->setId( $id );
        $circle->setStyle($style);

        return $circle;
    }
    
    /**
     * Define the center x
     * 
     * @param integer $cx 
     */
    public function setCx( $cx )
    {
        $this->addAttribute('cx', $cx );
    }
    
    /**
     * Return the center x
     *
     * @return integer cx attribute
     */
    public function getCx()
    {
        return $this->getAttribute('cx');
    }
    
    /**
     * Define the center y
     * 
     * @param integer $cy 
     */
    public function setCy( $cy )
    {
        $this->addAttribute('cy', $cy );
    }
    
    /**
     * Return the center y
     *
     * @return integer cy attribute
     */
    public function getCy()
    {
        return $this->getAttribute('cy');
    }
    
    /**
     * Define the radius of circle
     * 
     * @param integer $radius 
     */
    public function setRadius( $radiusX, $radiusY )
    {
        $this->addAttribute('rx', $radiusX );
        $this->addAttribute('ry', $radiusY );
    }

    /**
     * Return the x radius of circle
     * 
     * @return integer the radius of circle
     */
    public function getRadiusX( )
    {
        return $this->getAttribute('rx');
    }
    
    /**
     * Return the y radius of circle
     * 
     * @return integer the radius of circle
     */
    public function getRadiusY( )
    {
        return $this->getAttribute('ry');
    }
}
?>