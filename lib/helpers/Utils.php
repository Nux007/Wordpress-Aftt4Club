<?php 

/**
 * ColorMap helper.
 * 
 * Help defining color scheme for misc plugin tables.
 *
 * @category   Divisions helper.
 * @author     Nux007 <007.nux@gmail.com>
 * @link       https://github.com/Nux007/Wordpress-Aftt4Club
 * @copyright  2018
 * @since      Class available since Release 0.0.1
 */
class ColorMap
{
    public $colorsMap = array("header" => "#9cfff4", "th" => "#9cfff4", "borders" => "#f0f0f0", "even" => "#f0f0f0", "odd" => "#FFFFFF");
    
    
    /**
     * Sets the color maps for html and pdf generation.
     * @param string $th
     * @param string $border
     * @param string $even
     * @param string $odd
     */
    public function setColorsMap($head, $th=null, $border=null, $even=null, $odd=null)
    {
        if(is_array($head)) {
            $this->colorsMap = $head;
        } else {
            $this->colorsMap = array("header" => $head, "th" => $th, "borders" => $border, "even" => $even, "odd" => $odd);
        }
    }
    
    
    /**
     * Return the color map.
     * @return string[]
     */
    public function getColorsMap()
    {
        return (null !== $this->colorsMap) ? $this->colorsMap : false;
    }
}

?>