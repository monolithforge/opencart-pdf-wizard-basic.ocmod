<?php

require_once("BasePdf.php");

/**
 * @category   MonolithForge/OpenCart/PdfWizard
 * @package    monolithforge-opencart-pdf-wizard
 * @author     Original Author <support@monolithforge.com>
 * @copyright  2017-2018 Monolith Forge, LLC
 * @license    https://www.monolithforge.com/license/pdf-wizard-basic-license.txt
 * @version    3-5-dev
 */
class OrderInfoPdf extends BasePdf {
    
    public function __construct($data = array()) {
        parent::__construct($data);
        $this->data = $data;
    }
    
    public function build()
    {
        parent::build();
        
        $this->_setFont("legend_cell_text");
        $this->_setFill("legend_cell");
        $this->Cell($this->GetPageWidth()-20, $this->fill_definitions["legend_cell"]["cell_height"], $this->_cleanText($this->data["language"]->get("text_order_detail")), '', 1, 'L', $this->fill_definitions["legend_cell"]["do_fill"]);
        
        $this->_setFill("default");
        $fill = $this->fill_definitions["default"]["do_fill"];
        $this->_setFont("default_label_text");
        
        $this->Cell(($this->GetPageWidth()-20) / 4, $this->fill_definitions["default"]["cell_height"], $this->_cleanText($this->data["language"]->get("text_order_id")), '', 0, 'R', $fill);
        $this->_setFont("default_value_text");
        $this->Cell(($this->GetPageWidth()-20) / 4, $this->fill_definitions["default"]["cell_height"], "#".$this->_cleanText($this->data["order_id"]), 'R', 0, 'L', $fill);
        
        $this->_setFont("default_label_text");
        $this->Cell(($this->GetPageWidth()-20) / 4, $this->fill_definitions["default"]["cell_height"], $this->_cleanText($this->data["language"]->get("text_payment_method")), '', 0, 'R', $fill);
        $this->_setFont("default_value_text");
        $this->Cell(($this->GetPageWidth()-20) / 4, $this->fill_definitions["default"]["cell_height"], $this->_cleanText($this->data["payment_method"]), '', 1, 'L', $fill);
        
        $this->_setFont("default_label_text");
        $this->Cell(($this->GetPageWidth()-20) / 4, $this->fill_definitions["default"]["cell_height"], $this->_cleanText($this->data["language"]->get("text_date_added")), 'B', 0, 'R', $fill);
        $this->_setFont("default_value_text");
        $this->Cell(($this->GetPageWidth()-20) / 4, $this->fill_definitions["default"]["cell_height"], $this->_cleanText($this->data["date_added"]), 'BR', 0, 'L', $fill);
        
        $this->_setFont("default_label_text");
        $this->Cell(($this->GetPageWidth()-20) / 4, $this->fill_definitions["default"]["cell_height"], $this->_cleanText($this->data["language"]->get("text_shipping_method")), 'B', 0, 'R', $fill);
        $this->_setFont("default_value_text");
        $this->Cell(($this->GetPageWidth()-20) / 4, $this->fill_definitions["default"]["cell_height"], $this->_cleanText($this->data["shipping_method"]), 'B', 1, 'L', $fill);
        
        //line break
        $this->Ln(12);
        
        $this->_setFont("legend_cell_text");
        $this->_setFill("legend_cell");
        $this->Cell(($this->GetPageWidth()-20) / 2, $this->fill_definitions["legend_cell"]["cell_height"], $this->_cleanText($this->data["language"]->get("text_shipping_address")), 'R', 0, 'L', $this->fill_definitions["legend_cell"]["do_fill"]);
        $this->Cell(($this->GetPageWidth()-20) / 2, $this->fill_definitions["legend_cell"]["cell_height"], $this->_cleanText($this->data["language"]->get("text_payment_address")), '', 1, 'L', $this->fill_definitions["legend_cell"]["do_fill"]);
        
        $this->_setFont("default_value_text");
        $shipping_address_split = explode("<br />", $this->data["shipping_address"]);
        $payment_address_split = explode("<br />", $this->data["payment_address"]);
        $looper = $shipping_address_split;
        if (count($payment_address_split) > count($shipping_address_split)) {
            $looper = $payment_address_split;
        }
        $cnt = 0;
        foreach ($looper as $loop) {
            $bottom_border = "";
            $this->_setFill("default");
            $fill = $this->fill_definitions["default"]["do_fill"];
            if (count($looper)-1 == $cnt) {
                $bottom_border = "B";
            }
            if (isset($shipping_address_split[$cnt])) {
                $this->Cell(($this->GetPageWidth()-20) / 2, 7, $this->_cleanText($shipping_address_split[$cnt]), $bottom_border.'R', 0, 'L', $fill);
            }
            else {
                $this->Cell(($this->GetPageWidth()-20) / 2, 7, "", $bottom_border.'R', 0, 'L', $fill);
            }
            
            if (isset($payment_address_split[$cnt])) {
                $this->Cell(($this->GetPageWidth()-20) / 2, 7, $this->_cleanText($payment_address_split[$cnt]), $bottom_border, 1, 'L', $fill);
            }
            else {
                $this->Cell(($this->GetPageWidth()-20) / 2, 7, "", $bottom_border, 1, 'L', $fill);
            }
            $cnt++;
        }
        
        // Line break
        $this->Ln(12);
        
        //BEGIN build product MultiCell Header Row
        $this->_setFont("legend_cell_text");
        $this->_setFill("legend_cell");
        $this->PDF_MC_Table_SetWidths(array(
            (($this->GetPageWidth()-20) / 12) * 3,
            (($this->GetPageWidth()-20) / 12) * 2,
            (($this->GetPageWidth()-20) / 12) * 2,
            (($this->GetPageWidth()-20) / 12) * 3,
            (($this->GetPageWidth()-20) / 12) * 2
        ));
        $this->PDF_MC_Table_SetAligns(array(
            "L",
            "L",
            "R",
            "R",
            "R"
        ));
        $this->PDF_MC_Table_SetBorders(array(
            "",
            "",
            "",
            "",
            ""
        ));
        $this->PDF_MC_Table_SetHeights(array(
            $this->fill_definitions["legend_cell"]["cell_height"],
            $this->fill_definitions["legend_cell"]["cell_height"],
            $this->fill_definitions["legend_cell"]["cell_height"],
            $this->fill_definitions["legend_cell"]["cell_height"],
            $this->fill_definitions["legend_cell"]["cell_height"]
        ));
        $this->PDF_MC_Table_SetFills(array(
            $this->fill_definitions["legend_cell"],
            $this->fill_definitions["legend_cell"],
            $this->fill_definitions["legend_cell"],
            $this->fill_definitions["legend_cell"],
            $this->fill_definitions["legend_cell"]
        ));
        if (trim($this->data["language"]->get("column_name")) == "column_name") {
            $column_name = $this->data["language"]->get("column_product"); //admin model calls this column_product
        }
        else {
            $column_name = $this->data["language"]->get("column_name"); //catalog model calls this column_name
        }
        $this->PDF_MC_Table_Row(array(
            $this->_cleanText($column_name),
            $this->_cleanText($this->data["language"]->get("column_model")),
            $this->_cleanText($this->data["language"]->get("column_quantity")),
            $this->_cleanText($this->data["language"]->get("column_price")),
            $this->_cleanText($this->data["language"]->get("column_total"))
        ));
        //END build product MultiCell Header Row
        
        
        //BEGIN build product MultiCell Data Row
        $this->_setFont("default_value_text");
        $product_cnt = 0;
        foreach ($this->data["products"] as $key => $product) {
            
            $this->PDF_MC_Table_SetWidths(array(
                (($this->GetPageWidth()-20) / 12) * 3,
                (($this->GetPageWidth()-20) / 12) * 2,
                (($this->GetPageWidth()-20) / 12) * 2,
                (($this->GetPageWidth()-20) / 12) * 3,
                (($this->GetPageWidth()-20) / 12) * 2
            ));
            
            $this->PDF_MC_Table_SetAligns(array(
                "L",
                "L",
                "R",
                "R",
                "R"
            ));
            
            $row_borders = array(
                "BR",
                "BR",
                "BR",
                "BR",
                "B"
            );
            
            $this->PDF_MC_Table_SetHeights(array(
                $this->fill_definitions["default"]["cell_height"],
                $this->fill_definitions["default"]["cell_height"],
                $this->fill_definitions["default"]["cell_height"],
                $this->fill_definitions["default"]["cell_height"],
                $this->fill_definitions["default"]["cell_height"]
            ));
            
            $this->PDF_MC_Table_SetFills(array(
                $this->fill_definitions["default"],
                $this->fill_definitions["default"],
                $this->fill_definitions["default"],
                $this->fill_definitions["default"],
                $this->fill_definitions["default"]
            ));
            
            $row_font_styles = array("default_value_text", "default_value_text", "default_value_text", "default_value_text", "default_value_text");
            
            $row_data = array(
                $this->_cleanText($product["name"]),
                $this->_cleanText($product["model"]),
                $this->_cleanText($product["quantity"]),
                $this->_cleanText($product["price"]),
                $this->_cleanText($product["total"]),
            );
            
            $bottom = "B";
            $option_cnt = 0;
            if (count($product["option"]) > 0) {
                $bottom = "";
                $option_cnt = count($product["option"]);
            }
            
            $this->PDF_MC_Table_SetBorders($row_borders);
            $this->PDF_MC_Table_SetFontStyles($row_font_styles);
            $this->PDF_MC_Table_Row($row_data);
            
            //BEGIN build option MultiCell Data Row
            if (count($product["option"]) > 0) {
                foreach ($product["option"] as $option_key => $option_data) {
                    
                    $option_row_widths = array(
                        (($this->GetPageWidth()-20) / 12) * 3,
                        (($this->GetPageWidth()-20) / 12) * 9,
                    );
                    $option_row_aligns = array(
                        "R",
                        "L",
                    );
                    $option_row_heights = array(
                        $this->fill_definitions["default"]["cell_height"],
                        $this->fill_definitions["default"]["cell_height"],
                    );
                    $option_row_data = array(
                        "",
                        "",
                    );
                    
                    //inherit (product) $row_widths
                    
                    $option_bottom = "";
                    if (count($product["option"])-1 == $option_cnt) {
                        $option_bottom = "B";
                    }
                    $option_row_borders = array(
                        $option_bottom."R",
                        $option_bottom."",
                    );
                    $option_row_data[0] = $this->_cleanText($option_data["name"]).": ";
                    $option_row_data[1] = $this->_cleanText($option_data["value"]);
                    
                    $this->PDF_MC_Table_SetAligns($option_row_aligns);
                    $this->PDF_MC_Table_SetBorders($option_row_borders);
                    $this->PDF_MC_Table_SetHeights($option_row_heights);
                    $this->PDF_MC_Table_SetWidths($option_row_widths);
                    $this->PDF_MC_Table_SetFontStyles(array("default_label_text", "default_value_text"));
                    $this->PDF_MC_Table_Row($option_row_data);
                    $option_cnt++;
                }
            }
            //END build option MultiCell Data Row
            
            $product_cnt++;
        }
        
        //END build product MultiCell Data Row
        
        $this->_setFill("default");
        $fill = $this->fill_definitions["default"]["do_fill"];
        
        foreach ($this->data["totals"] as $total_data) {
            //BEGIN build totals MultiCell Row
            $this->PDF_MC_Table_SetWidths(array(
                (($this->GetPageWidth()-20) / 12) * 7,
                (($this->GetPageWidth()-20) / 12) * 3,
                (($this->GetPageWidth()-20) / 12) * 2
            ));
            $this->PDF_MC_Table_SetAligns(array(
                "",
                "R",
                "R"
            ));
            $this->PDF_MC_Table_SetBorders(array(
                "",
                "BR",
                "B",
            ));
            $this->PDF_MC_Table_SetHeights(array(
                $this->fill_definitions["default"]["cell_height"],
                $this->fill_definitions["default"]["cell_height"],
                $this->fill_definitions["default"]["cell_height"]
            ));
            $this->PDF_MC_Table_SetFills(array(
                $this->fill_definitions["default"],
                $this->fill_definitions["default"],
                $this->fill_definitions["default"]
            ));
            $this->PDF_MC_Table_SetFontStyles(array("default_value_text", "default_label_text", "default_value_text"));
            $this->PDF_MC_Table_Row(array(
                '',
                $this->_cleanText($total_data["title"]),
                $this->_cleanText($total_data["text"]),
            ));
            //BEGIN build totals MultiCell Row
        }
        
        // Line break
        $this->Ln(12);
        
        if ($this->data["comment"] != "") {
            $this->_setFont("legend_cell_text");
            $this->_setFill("legend_cell");
            $this->Cell($this->GetPageWidth()-20, $this->fill_definitions["legend_cell"]["cell_height"], $this->_cleanText($this->data["language"]->get("text_comment")), '', 1, 'L', $this->fill_definitions["legend_cell"]["do_fill"]);
            $this->_setFont("default_value_text");
            $this->_setFill("default");
            $fill = $this->fill_definitions["default"]["do_fill"];
            $this->MultiCell($this->GetPageWidth()-20, 7, $this->_cleanText($this->data["comment"]), 'B', 'L', $fill);
        }
    }
}