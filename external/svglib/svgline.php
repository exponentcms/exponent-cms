<?php
/**
 *
 * Description: Implementation of Line.
 *
 * Blog: http://trialforce.nostaljia.eng.br
 *
 * Started at nov 13, 2011
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
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.   See the
 *   GNU Library General Public License for more details.
 *
 *   You should have received a copy of the GNU Library General Public
 *   License along with this program; if not, access
 *   http://www.fsf.org/licensing/licenses/lgpl.html or write to the
 *   Free Software Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
 *----------------------------------------------------------------------
 */
class SVGLine extends SVGShapeEx
{
    public static function getInstance( $x1, $y1, $x2, $y2, $id = null , $style = null )
    {
        $rect = new SVGLine('<line></line>');

        $rect->setX1( $x1 );
        $rect->setX2( $x2 );
        $rect->setY1( $y1 );
        $rect->setY2( $y2 );
        $rect->setId( $id );
        $rect->setStyle( $style );

        return $rect;
    }

    /**
     * Define the x 1 of line
     * 
     * @param int $x1 
     */
    public function setX1( $x1 )
    {
        $this->addAttribute('x1', $x1 );
    }
    
    /**
     * Define the x 2 of line
     * 
     * @param int $x2
     */
    public function setX2( $x2 )
    {
        $this->addAttribute('x2', $x2 );
    }
    
    /**
     * Define the y 1 of line
     * 
     * @param int $y1 
     */
    public function setY1( $y1 )
    {
        $this->addAttribute('y1', $y1 );
    }
    
    /**
     * Define the y 2 of line
     * 
     * @param int $y2 
     */
    public function setY2( $y2 )
    {
        $this->addAttribute('y2', $y2 );
    }
    
    /**
     * Return x1 attribute
     * 
     * @return integer x1 attribute
     */
    public function getX1()
    {
        return $this->getAttribute('x1');
    }
    
    /**
     * Return x2 attribute
     * 
     * @return integer x2 attribute
     */
    public function getX2()
    {
        return $this->getAttribute('x2');
    }
    
    /**
     * Return y1 attribute
     * 
     * @return integer y1 attribute
     */
    public function getY1()
    {
        return $this->getAttribute('y1');
    }
    
    /**
     * Return y2  attribute
     * 
     * @return integer y2 attribute
     */
    public function getY2()
    {
        return $this->getAttribute('y2');
    }
}
?>