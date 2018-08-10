<?php

/**
 * @category   MonolithForge/OpenCart/PdfWizard
 * @package    monolithforge-opencart-pdf-wizard
 * @author     Original Author <support@monolithforge.com>
 * @copyright  2017-2018 Monolith Forge, LLC
 * @license    https://www.monolithforge.com/license/pdf-wizard-basic-license.txt
 * @version    3-6-beta2
 */

include_once(dirname(__FILE__)."/../vendor/tfpdf/font/unifont/ttfonts.php");

class BasePdf extends tFPDF {
    
    protected $default_font = "Arial";
    
    protected $fill_definitions = array(
        "default" => array(
            "R" => 255,
            "G" => 255,
            "B" => 255,
            "do_fill" => true,
            "cell_height" => 8 //no more than 8?
        ),
        "legend_cell" => array(
            "R" => 218,
            "G" => 242,
            "B" => 250,
            "do_fill" => true,
            "cell_height" => 12
        )
    );
    
    protected $font_definitions = array(
        /**
         * Example structure populated from PdfWizard Root Class:
         * "name" => array(
         *      "font_family" => 'Arial',
         *      "font_style" => 'B',
         *      "font_size" => 12,
         *      "font_color" => array(
         *          "R" => 150,
         *          "G" => 150,
         *          "B" => 150
         * )
         */
        "default_font" => array("font_color"),
        "header_title" => array("font_color"),
        "footer_text" => array("font_color"),
        "store_name" => array("font_color"),
        "store_address" => array("font_color"),
        "legend_cell_text" => array("font_color"),
        "default_label_text" => array("font_color"),
        "default_value_text" => array("font_color"),
    );
    
    public function __construct($data = array()) {
        parent::__construct();
        $this->data = $data;
        $this->SetLineWidth(0.2);
        $this->SetDrawColor(200,200,200);
        
        $default_fpdf_font_family_and_style = $this->_getFpdfFontFamilyAndStyleByName($data["pdf_wizard_settings_default_font"]);
        
        ## update default font family on all $this->font_defintions...
        foreach ($this->font_definitions as $default_font_key => $default_font_data) {
            //let's quiet these with an @ because we're only providing the key above in (protected $font_definitions)
            @$this->font_definitions[$default_font_key]["font_family"] = $default_fpdf_font_family_and_style["font_family"];
            @$this->font_definitions[$default_font_key]["font_style"] = $default_fpdf_font_family_and_style["font_style"];
            @$this->font_definitions[$default_font_key]["font_size"] = $data["pdf_wizard_settings_default_font_fize"];
            $rgb = $this->hex2RGB($data["pdf_wizard_settings_default_legend_font_color"]);
            @$this->font_definitions[$default_font_key]["font_color"] = array("R","G","B");
            @$this->font_definitions[$default_font_key]["font_color"]["R"] = $rgb["red"];
            @$this->font_definitions[$default_font_key]["font_color"]["G"] = $rgb["green"];
            @$this->font_definitions[$default_font_key]["font_color"]["B"] = $rgb["blue"];
        }
        
        ## header
        # header_title
        if ($data["pdf_wizard_settings_default_header_title_font_family"] == "inherit") {
            $fpdf_font_family_and_style = $default_fpdf_font_family_and_style;
        }
        else {
            $fpdf_font_family_and_style = $this->_getFpdfFontFamilyAndStyleByName($data["pdf_wizard_settings_default_header_title_font_family"]);
        }
        $rgb = $this->hex2RGB($data["pdf_wizard_settings_default_header_title_font_color"]);
        $this->font_definitions["header_title"] = array(
            "font_family" => $fpdf_font_family_and_style["font_family"],
            "font_style" => $fpdf_font_family_and_style["font_style"],
            "font_size" => $data["pdf_wizard_settings_default_header_title_font_size"],
            "font_color" => array(
                "R" => $rgb["red"],
                "G" => $rgb["green"],
                "B" => $rgb["blue"]
            )
        );
        
        # store name
        if ($data["pdf_wizard_settings_default_header_store_name_font_family"] == "inherit") {
            $fpdf_font_family_and_style = $default_fpdf_font_family_and_style;
        }
        else {
            $fpdf_font_family_and_style = $this->_getFpdfFontFamilyAndStyleByName($data["pdf_wizard_settings_default_header_store_name_font_family"]);
        }
        $rgb = $this->hex2RGB($data["pdf_wizard_settings_default_header_store_name_font_color"]);
        $this->font_definitions["store_name"] = array(
            "font_family" => $fpdf_font_family_and_style["font_family"],
            "font_style" => $fpdf_font_family_and_style["font_style"],
            "font_size" => $data["pdf_wizard_settings_default_header_store_name_font_size"],
            "font_color" => array(
                "R" => $rgb["red"],
                "G" => $rgb["green"],
                "B" => $rgb["blue"]
            )
        );
        
        # store address
        if ($data["pdf_wizard_settings_default_header_store_address_font_family"] == "inherit") {
            $fpdf_font_family_and_style = $default_fpdf_font_family_and_style;
        }
        else {
            $fpdf_font_family_and_style = $this->_getFpdfFontFamilyAndStyleByName($data["pdf_wizard_settings_default_header_store_address_font_family"]);
        }
        $rgb = $this->hex2RGB($data["pdf_wizard_settings_default_header_store_address_font_color"]);
        $this->font_definitions["store_address"] = array(
            "font_family" => $fpdf_font_family_and_style["font_family"],
            "font_style" => $fpdf_font_family_and_style["font_style"],
            "font_size" => $data["pdf_wizard_settings_default_header_store_address_font_size"],
            "font_color" => array(
                "R" => $rgb["red"],
                "G" => $rgb["green"],
                "B" => $rgb["blue"]
            )
        );
        
        ## footer
        if ($data["pdf_wizard_settings_default_footer_font_family"] == "inherit") {
            $fpdf_font_family_and_style = $default_fpdf_font_family_and_style;
        }
        else {
            $fpdf_font_family_and_style = $this->_getFpdfFontFamilyAndStyleByName($data["pdf_wizard_settings_default_footer_font_family"]);
        }
        $rgb = $this->hex2RGB($data["pdf_wizard_settings_default_footer_font_color"]);
        $this->font_definitions["footer_text"] = array(
            "font_family" => $fpdf_font_family_and_style["font_family"],
            "font_style" => $fpdf_font_family_and_style["font_style"],
            "font_size" => $data["pdf_wizard_settings_default_footer_font_size"],
            "font_color" => array(
                "R" => $rgb["red"],
                "G" => $rgb["green"],
                "B" => $rgb["blue"]
            )
        );
        
        ## update default legend-cell
        $header_rgb = $this->hex2RGB($data["pdf_wizard_settings_default_legend_background_color"]);
        $this->fill_definitions["legend_cell"] = array(
            "R" => $header_rgb["red"],
            "G" => $header_rgb["green"],
            "B" => $header_rgb["blue"],
            "do_fill" => true,
            "cell_height" => 12
        );
        
        ## update default legend-font
        if ($data["pdf_wizard_settings_default_legend_font_family"] == "inherit") {
            $fpdf_font_family_and_style = $default_fpdf_font_family_and_style;
        }
        else {
            $fpdf_font_family_and_style = $this->_getFpdfFontFamilyAndStyleByName($data["pdf_wizard_settings_default_legend_font_family"]);
        }
        $rgb = $this->hex2RGB($data["pdf_wizard_settings_default_legend_font_color"]);
        $this->font_definitions["legend_cell_text"] = array(
            "font_family" => $fpdf_font_family_and_style["font_family"],
            "font_style" => $fpdf_font_family_and_style["font_style"],
            "font_size" => $data["pdf_wizard_settings_default_legend_font_size"],
            "font_color" => array(
                "R" => $rgb["red"],
                "G" => $rgb["green"],
                "B" => $rgb["blue"]
            )
        );
        
        #cell label
        if ($data["pdf_wizard_settings_default_cell_label_font_family"] == "inherit") {
            $fpdf_font_family_and_style = $default_fpdf_font_family_and_style;
        }
        else {
            $fpdf_font_family_and_style = $this->_getFpdfFontFamilyAndStyleByName($data["pdf_wizard_settings_default_cell_label_font_family"]);
        }
        $rgb = $this->hex2RGB($data["pdf_wizard_settings_default_cell_label_font_color"]);
        $this->font_definitions["default_label_text"] = array(
            "font_family" => $fpdf_font_family_and_style["font_family"],
            "font_style" => $fpdf_font_family_and_style["font_style"],
            "font_size" => $data["pdf_wizard_settings_default_cell_label_font_size"],
            "font_color" => array(
                "R" => $rgb["red"],
                "G" => $rgb["green"],
                "B" => $rgb["blue"]
            )
        );
        
        #cell text
        if ($data["pdf_wizard_settings_default_cell_text_font_family"] == "inherit") {
            $fpdf_font_family_and_style = $default_fpdf_font_family_and_style;
        }
        else {
            $fpdf_font_family_and_style = $this->_getFpdfFontFamilyAndStyleByName($data["pdf_wizard_settings_default_cell_text_font_family"]);
        }
        $rgb = $this->hex2RGB($data["pdf_wizard_settings_default_cell_text_font_color"]);
        $this->font_definitions["default_value_text"] = array(
            "font_family" => $fpdf_font_family_and_style["font_family"],
            "font_style" => $fpdf_font_family_and_style["font_style"],
            "font_size" => $data["pdf_wizard_settings_default_cell_text_font_size"],
            "font_color" => array(
                "R" => $rgb["red"],
                "G" => $rgb["green"],
                "B" => $rgb["blue"]
            )
        );
    }
    
    // Page header
    public function Header()
    {
        $this->_setFont("header_title");
        
        if ($this->PageNo() == 1 && $this->data["logo"] != "" && $this->data["pdf_wizard_settings_default_header_use_logo"] == 1) {
            // Logo
            $this->Cell( 40, 40, $this->Image($this->data["logo"], $this->GetX(), $this->GetY(), 40), 0, 0, 'L', false );
        }
        
        // Title
        $this->_setFont("header_title");
        if ($this->PageNo() == 1 && $this->data["logo"] != "" && $this->data["pdf_wizard_settings_default_header_use_logo"] == 1) {
            $this->Cell($this->GetPageWidth()-60, 7, $this->_cleanText(str_replace(" (#%s)", "", $this->data["language"]->get("text_order")))." #".$this->_cleanText($this->data["order_id"]), '', 1, 'R');
        }
        else {
            $this->Cell($this->GetPageWidth()-20, 7, $this->_cleanText(str_replace(" (#%s)", "", $this->data["language"]->get("text_order")))." #".$this->_cleanText($this->data["order_id"]), '', 1, 'R');
        }
        
        if ($this->PageNo() != 1) {
            // Line break
            $this->Ln(8);
        }
    }
    
    protected function build()
    {
        $this->AliasNbPages();
        $this->AddPage();
        
        //extend header on first page with website address
        
        // Store Name
        if ($this->data["pdf_wizard_settings_default_header_use_store_name"] == "1") {
            $this->_setFont("store_name");
            $this->_setFill("default");
            $this->Cell($this->GetPageWidth()-20, $this->font_definitions["store_name"]["font_size"]/2, $this->_cleanText($this->data["store"]["name"]), '', 1, 'R', false); //set fill to false in case of large logo
        }
        
        // Store Address and Contact Info
        if ($this->data["pdf_wizard_settings_default_header_store_address_use"] == "1") {
            $this->_setFont("store_address");
            $this->_setFill("default");
            $this->MultiCell($this->GetPageWidth()-20, $this->font_definitions["store_address"]["font_size"]/2, $this->_cleanText($this->data["store"]["address"]), '', 'R', false); //set fill to false in case of large logo
            if (trim($this->data["store"]["email"]) != "") {
                $this->Cell($this->GetPageWidth()-20, $this->font_definitions["store_address"]["font_size"]/2, $this->_cleanText($this->data["store"]["email"]), '', 1, 'R', false); //set fill to false in case of large logo
            }
            if (trim($this->data["store"]["telephone"]) != "") {
                $this->Cell($this->GetPageWidth()-20, $this->font_definitions["store_address"]["font_size"]/2, $this->_cleanText($this->data["store"]["telephone"]), '', 1, 'R', false); //set fill to false in case of large logo
            }
            if (trim($this->data["store"]["fax"]) != "") {
                $this->Cell($this->GetPageWidth()-20, $this->font_definitions["store_address"]["font_size"]/2, "Fax: ".$this->_cleanText($this->data["store"]["fax"]), '', 1, 'R', false); ////set fill to false in case of large logo
            }
            if (trim($this->data["store"]["owner"]) != "") {
                $this->Cell($this->GetPageWidth()-20, $this->font_definitions["store_address"]["font_size"]/2, $this->_cleanText($this->data["store"]["owner"]), '', 1, 'R', false); //set fill to false in case of large logo
            }
        }
        
        // Line break
        $this->Ln(8);
    }
    
    // Page footer
    public function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        $this->SetX(-50);
        $this->_setFont("footer_text");
        // Page number
        $this->Cell(0,10,'Page '.$this->PageNo().' of {nb}',0,0,'C');
    }
    
    
    
    protected function _getFpdfFontFamilyAndStyleByName($name)
    {
        if (strpos($name, "(unifont) ") > -1) {
            $name = substr_replace($name, "", 0, 9);
        }
        
        $return_val = array(
            "font_family" => "Arial",
            "font_style" => "",
        );
        
        $stripped_name = str_ireplace("-BoldOblique", "", $name);
        $stripped_name = str_ireplace("-Bold", "", $stripped_name);
        $stripped_name = str_ireplace("-BoldOblique", "", $stripped_name);
        $stripped_name = str_ireplace("-Oblique", "", $stripped_name);
        $stripped_name = str_ireplace("-Sans", "", $name);
        $stripped_name = str_ireplace("TimesItalic", "Times", $stripped_name);
        $stripped_name = str_ireplace("Times-Italic", "Times", $stripped_name);
        $stripped_name = str_ireplace("Times-Roman", "Times", $stripped_name);
        $style = "";
        if (stripos($name, "Bold") > -1) {
            $style .= "B";
        }
        if (stripos($name, "Oblique") > -1) {
            $style .= "I";
        }
        if (stripos($name, "Italic") > -1) {
            $style .= "I";
        }
        
        $return_val["font_family"] = $stripped_name;
        $return_val["font_style"] = $style;
        
        return $return_val;
    }
    
    protected function _setFont($font_definition)
    {
        if (strpos($this->font_definitions[$font_definition]["font_family"], "DejaVuSans") > -1) {
            $font_family = "DejaVuSans";
            $this->AddFont($font_family, '', trim($this->font_definitions[$font_definition]["font_family"]).".ttf", true);
            $this->SetFont($font_family,'',$this->font_definitions[$font_definition]["font_size"]);
            $this->SetTextColor($this->font_definitions[$font_definition]["font_color"]["R"],$this->font_definitions[$font_definition]["font_color"]["G"],$this->font_definitions[$font_definition]["font_color"]["B"]);
        }
        else {
            $this->SetFont($this->font_definitions[$font_definition]["font_family"],$this->font_definitions[$font_definition]["font_style"],$this->font_definitions[$font_definition]["font_size"]);
            $this->SetTextColor($this->font_definitions[$font_definition]["font_color"]["R"],$this->font_definitions[$font_definition]["font_color"]["G"],$this->font_definitions[$font_definition]["font_color"]["B"]);
        }
    }
    
    protected function _setFill($fill_definition = null)
    {
        $fd = $fill_definition;
        if (is_null($fd)) {
            $this->setFillColor(255, 255, 255); //default reset to white
        }
        else {
            $fd = $this->fill_definitions[$fill_definition];
            $this->setFillColor($fd["R"], $fd["G"], $fd["B"]); //default reset to white
        }
    }
    
    protected function _cleanText($txt)
    {
        return html_entity_decode($txt);
    }
    
    
    //PDF_MC_Table class from FPDF.org/scripts
    //Author: Olivier
    
    protected $PDF_MC_Table_widths;
    protected $PDF_MC_Table_aligns;
    protected $PDF_MC_Table_borders;
    protected $PDF_MC_Table_fills;
    protected $PDF_MC_Table_heights;
    protected $PDF_MC_Table_font_styles;

    protected function PDF_MC_Table_SetWidths($w)
    {
        //Set the array of column widths
        $this->PDF_MC_Table_widths=$w;
    }

    protected function PDF_MC_Table_SetAligns($a)
    {
        //Set the array of column alignments
        $this->PDF_MC_Table_aligns=$a;
    }

    protected function PDF_MC_Table_SetBorders($b)
    {
        //Set the array of column borders
        $this->PDF_MC_Table_borders=$b;
    }

    protected function PDF_MC_Table_SetFills($f)
    {
        //Set the array of column fills
        $this->PDF_MC_Table_fills=$f;
    }

    protected function PDF_MC_Table_SetHeights($h)
    {
        //Set the array of column heights
        $this->PDF_MC_Table_heights=$h;
    }

    protected function PDF_MC_Table_SetFontStyles($f_keys_arr = array())
    {
        //Set the array of column font key to $this->font_definitions[$f_key] (eg. "legend_cell_text", "default_label_text", "default_value_text")
        $this->PDF_MC_Table_font_styles=$f_keys_arr;
    }

    protected function PDF_MC_Table_Row($data)
    {
        //Calculate the height of the row
        #$nb=0;
        #for($i=0;$i<count($data);$i++)
        #    $nb=max($nb,$this->PDF_MC_Table_NbLines($this->PDF_MC_Table_widths[$i],$data[$i]));
        #$h=5*$nb;
        $nb=0;
        $h=0;
        $lh=0;
        $sl=0;
        $br=0;
        for($i=0;$i<count($data);$i++) {
            $nb=max($nb,$this->PDF_MC_Table_NbLines($this->PDF_MC_Table_widths[$i],$data[$i]));
            $h=max($h,$this->PDF_MC_Table_heights[$i]*$nb);
            $lh=max($lh, $this->PDF_MC_Table_heights[$i]);
            $sl=max($sl, strlen($data[$i]));
            $br=max($br, (count(explode("\n", $data[$i]))-1));
        }
        //Issue a page break first if needed
        $this->PDF_MC_Table_CheckPageBreak($h);
        //Draw the cells of the row
        for($i=0;$i<count($data);$i++)
        {
            $w=$this->PDF_MC_Table_widths[$i];
            $a=isset($this->PDF_MC_Table_aligns[$i]) ? $this->PDF_MC_Table_aligns[$i] : 'L';
            //Save the current position
            $x=$this->GetX();
            $y=$this->GetY();
            //Draw the border
            #$this->Rect($x,$y,$w,$h);
            $b=isset($this->PDF_MC_Table_borders[$i]) && trim($this->PDF_MC_Table_borders[$i]) !== "" ? $this->PDF_MC_Table_borders[$i] : 0;
            //Set the font
            if (isset($this->PDF_MC_Table_font_styles[$i])) {
                $this->_setFont($this->PDF_MC_Table_font_styles[$i]);
            }
            if (isset($this->PDF_MC_Table_fills)) {
                $this->setFillColor($this->PDF_MC_Table_fills[$i]["R"], $this->PDF_MC_Table_fills[$i]["G"], $this->PDF_MC_Table_fills[$i]["B"]);
                $fill = $this->PDF_MC_Table_fills[$i]["do_fill"];
            }
            else {
                //background white default
                $this->setFillColor(255, 255, 255);
                $fill = true;
            }
            //Print the text
            #$tsl = $sl - strlen($data[$i]);
            $tsl = $sl - strlen($data[$i]);
            $tsl = ($tsl/($h/$nb));
            $extra = str_repeat(" ", $tsl);
            $current_breaks = count(explode("\n", $data[$i]))-1;
            $do_breaks = str_repeat("\n", ($br-$current_breaks+1));
            
            //find out if current row had line breaks based of textlength (not actual \newlines)
            $current_nb = $this->PDF_MC_Table_NbLines($w,$data[$i]);
            if (($nb-$current_nb-$current_breaks) > 0) {
                $do_breaks .= str_repeat("\n", $current_nb);
            }
            
            $this->MultiCell($w,$lh,($a == "L" ? $data[$i].$extra.$do_breaks : $extra.$data[$i].$do_breaks),$b,$a,$fill);
            //Put the position to the right of the cell
            $this->SetXY($x+$w,$y);
        }
        //Go to the next line
        $this->Ln($h);
    }

    protected function PDF_MC_Table_CheckPageBreak($h)
    {
        //If the height h would cause an overflow, add a new page immediately
        if($this->GetY()+$h>$this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    protected function PDF_MC_Table_NbLines($w,$txt)
    {
        //Computes the number of lines a MultiCell of width w will take
        $cw=&$this->CurrentFont['cw'];
        if($w==0)
            $w=$this->w-$this->rMargin-$this->x;
        $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
        $s=str_replace("\r",'',$txt);
        $nb=strlen($s);
        if($nb>0 and $s[$nb-1]=="\n")
            $nb--;
        $sep=-1;
        $i=0;
        $j=0;
        $l=0;
        $nl=1;
        while($i<$nb)
        {
            $c=$s[$i];
            if($c=="\n")
            {
                $i++;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
                continue;
            }
            if($c==' ')
                $sep=$i;
            $l+=@$cw[$c];
            if($l>$wmax)
            {
                if($sep==-1)
                {
                    if($i==$j)
                        $i++;
                }
                else
                    $i=$sep+1;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
            }
            else
                $i++;
        }
        return $nl;
    }
    
    /**
    * Convert a hexa decimal color code to its RGB equivalent
    *
    * OUTPUT:
    * 
    * hex2RGB("#FF0") -> array( red =>255, green => 255, blue => 0)
    * hex2RGB("#FFFF00) -> Same as above
    * hex2RGB("#FF0", true) -> 255,255,0
    * hex2RGB("#FF0", true, ":") -> 255:255:0
    *
    * @param string $hexStr (hexadecimal color value)
    * @param boolean $returnAsString (if set true, returns the value separated by the separator character. Otherwise returns associative array)
    * @param string $seperator (to separate RGB values. Applicable only if second parameter is true.)
    * @return array or string (depending on second parameter. Returns False if invalid hex color value)
    */                                                                                                 
    public function hex2RGB($hexStr, $returnAsString = false, $seperator = ',')
    {
        $hexStr = preg_replace("/[^0-9A-Fa-f]/", '', $hexStr); // Gets a proper hex string
        $rgbArray = array();
        if (strlen($hexStr) == 6) { //If a proper hex code, convert using bitwise operation. No overhead... faster
            $colorVal = hexdec($hexStr);
            $rgbArray['red'] = 0xFF & ($colorVal >> 0x10);
            $rgbArray['green'] = 0xFF & ($colorVal >> 0x8);
            $rgbArray['blue'] = 0xFF & $colorVal;
        } elseif (strlen($hexStr) == 3) { //if shorthand notation, need some string manipulations
            $rgbArray['red'] = hexdec(str_repeat(substr($hexStr, 0, 1), 2));
            $rgbArray['green'] = hexdec(str_repeat(substr($hexStr, 1, 1), 2));
            $rgbArray['blue'] = hexdec(str_repeat(substr($hexStr, 2, 1), 2));
        } else {
            return false; //Invalid hex color code
        }
        return $returnAsString ? implode($seperator, $rgbArray) : $rgbArray; // returns the rgb string or the associative array
    }
}