<?php

/**
 * @category   MonolithForge/OpenCart/PdfWizard
 * @package    monolithforge-opencart-pdf-wizard
 * @author     Original Author <support@monolithforge.com>
 * @copyright  2017-2018 Monolith Forge, LLC
 * @license    https://www.monolithforge.com/license/pdf-wizard-basic-license.txt
 * @version    3-5-dev
 */
class ControllerExtensionModulePdfWizard extends Controller {
    
    public function orderinfo() {
        $this->load->language('account/order');
        $this->load->language('information/contact');
        
        
        $cwd = getcwd();
        $dir = (strcmp(VERSION,'3.0.0.0')>=0) ? 'library/pdf_wizard' : 'fpdf181';
        chdir( DIR_SYSTEM.$dir );
        require_once( DIR_SYSTEM.'library/pdf_wizard/PdfWizard.php' );
        $pdf_wizard = new PdfWizard;
        chdir( $cwd );
        
        //load post/config/default into form
        foreach ($pdf_wizard->default_data as $k => $v) {
            if ($this->config->get($k)) {
                $data[$k] = $this->config->get($k);
            }
            else {
                $data[$k] = $v;
            }
        }

        if (isset($this->request->get['order_id'])) {
            $order_id = $this->request->get['order_id'];
        } else {
            $order_id = 0;
        }
        
        if (is_numeric($order_id) && $order_id > 0) {
            
            if (!$this->customer->isLogged()) {
                $this->session->data['redirect'] = $this->url->link('extension/module/pdf_wizard/orderinfo', 'order_id=' . $order_id, true);
                
                $this->response->redirect($this->url->link('account/login', '', true));
            }
            
            $this->load->model('account/order');
            
            $order_info = $this->model_account_order->getOrder($order_id);
            
            if ($order_info) {
                
                // Use the FPDF package from http://www.fpdf.org/
                $cwd = getcwd();
                $dir = (strcmp(VERSION,'3.0.0.0')>=0) ? 'library/pdf_wizard' : 'fpdf181';
                chdir( DIR_SYSTEM.$dir );
                require_once( 'src/vendor/fpdf181/fpdf.php' );
                chdir( $cwd );
                
                if ($order_info['invoice_no']) {
                    $data['invoice_no'] = $order_info['invoice_prefix'] . $order_info['invoice_no'];
                } else {
                    $data['invoice_no'] = '';
                }
                
                $data['order_id'] = $this->request->get['order_id'];
                $data['date_added'] = date($this->language->get('date_format_short'), strtotime($order_info['date_added']));
                
                if ($order_info['payment_address_format']) {
                    $format = $order_info['payment_address_format'];
                } else {
                    $format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
                }
                
                $find = array(
                    '{firstname}',
                    '{lastname}',
                    '{company}',
                    '{address_1}',
                    '{address_2}',
                    '{city}',
                    '{postcode}',
                    '{zone}',
                    '{zone_code}',
                    '{country}'
                );
                
                $replace = array(
                    'firstname' => $order_info['payment_firstname'],
                    'lastname'  => $order_info['payment_lastname'],
                    'company'   => $order_info['payment_company'],
                    'address_1' => $order_info['payment_address_1'],
                    'address_2' => $order_info['payment_address_2'],
                    'city'      => $order_info['payment_city'],
                    'postcode'  => $order_info['payment_postcode'],
                    'zone'      => $order_info['payment_zone'],
                    'zone_code' => $order_info['payment_zone_code'],
                    'country'   => $order_info['payment_country']
                );
                
                $data['payment_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));
                
                $data['payment_method'] = $order_info['payment_method'];
                
                if ($order_info['shipping_address_format']) {
                    $format = $order_info['shipping_address_format'];
                } else {
                    $format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
                }
                
                $find = array(
                    '{firstname}',
                    '{lastname}',
                    '{company}',
                    '{address_1}',
                    '{address_2}',
                    '{city}',
                    '{postcode}',
                    '{zone}',
                    '{zone_code}',
                    '{country}'
                );
                
                $replace = array(
                    'firstname' => $order_info['shipping_firstname'],
                    'lastname'  => $order_info['shipping_lastname'],
                    'company'   => $order_info['shipping_company'],
                    'address_1' => $order_info['shipping_address_1'],
                    'address_2' => $order_info['shipping_address_2'],
                    'city'      => $order_info['shipping_city'],
                    'postcode'  => $order_info['shipping_postcode'],
                    'zone'      => $order_info['shipping_zone'],
                    'zone_code' => $order_info['shipping_zone_code'],
                    'country'   => $order_info['shipping_country']
                );
                
                $data['shipping_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));
                
                $data['shipping_method'] = $order_info['shipping_method'];
                
                $this->load->model('catalog/product');
                $this->load->model('tool/upload');
                
                // Products
                $data['products'] = array();
                
                $products = $this->model_account_order->getOrderProducts($this->request->get['order_id']);
                
                foreach ($products as $product) {
                    $option_data = array();
                    
                    $options = $this->model_account_order->getOrderOptions($this->request->get['order_id'], $product['order_product_id']);
                    
                    foreach ($options as $option) {
                        if ($option['type'] != 'file') {
                            $value = $option['value'];
                        } else {
                            $upload_info = $this->model_tool_upload->getUploadByCode($option['value']);
                            
                            if ($upload_info) {
                                $value = $upload_info['name'];
                            } else {
                                $value = '';
                            }
                        }
                        
                        $option_data[] = array(
                            'name'  => $option['name'],
                            # In case this is a textarea or long value, we want to show this in full on the invoice...
                            #'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
                            'value' => $value
                        );
                    }
                    
                    $product_info = $this->model_catalog_product->getProduct($product['product_id']);
                    
                    if ($product_info) {
                        $reorder = $this->url->link('account/order/reorder', 'order_id=' . $order_id . '&order_product_id=' . $product['order_product_id'], true);
                    } else {
                        $reorder = '';
                    }
                    
                    $data['products'][] = array(
                        'name'     => $product['name'],
                        'model'    => $product['model'],
                        'option'   => $option_data,
                        'quantity' => $product['quantity'],
                        'price'    => $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
                        'total'    => $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value']),
                        'reorder'  => $reorder,
                        'return'   => $this->url->link('account/return/add', 'order_id=' . $order_info['order_id'] . '&product_id=' . $product['product_id'], true)
                    );
                }
                
                // Voucher
                $data['vouchers'] = array();
                
                $vouchers = $this->model_account_order->getOrderVouchers($this->request->get['order_id']);
                
                foreach ($vouchers as $voucher) {
                    $data['vouchers'][] = array(
                        'description' => $voucher['description'],
                        'amount'      => $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value'])
                    );
                }
                
                // Totals
                $data['totals'] = array();
                
                $totals = $this->model_account_order->getOrderTotals($this->request->get['order_id']);
                
                foreach ($totals as $total) {
                    $data['totals'][] = array(
                        'title' => $total['title'],
                        'text'  => $this->currency->format($total['value'], $order_info['currency_code'], $order_info['currency_value']),
                    );
                }
                
                $data['comment'] = nl2br($order_info['comment']);
                
                if ($this->request->server['HTTPS']) {
                    $server = $this->config->get('config_ssl');
                } else {
                    $server = $this->config->get('config_url');
                }
                
                $data["store"] = array(
                    "name" => $this->config->get('config_name'), // store name
                    "title" => $this->config->get('config_title'), // store title
                    "owner" => $this->config->get('config_owner'), // store owner name
                    "email" => $this->config->get('config_email'), // store email
                    "address" => $this->config->get('config_address'), // store address
                    "telephone" => $this->config->get('config_telephone'), // phone
                    "fax" => $this->config->get('config_fax'), // fax
                );
                
                if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
                    //$data['logo'] = $server . 'image/' . $this->config->get('config_logo');
                    $data['logo'] = DIR_IMAGE.$this->config->get('config_logo');
                } else {
                    $data['logo'] = '';
                }
                
                $data["language"] = $this->language;
                
                // Instanciation of inherited class
                $cwd = getcwd();
                $dir = (strcmp(VERSION,'3.0.0.0')>=0) ? 'library/pdf_wizard' : 'fpdf181';
                chdir( DIR_SYSTEM.$dir );
                require_once( 'src/pdfs/OrderInfoPdf.php' );
                chdir( $cwd );
                #possible override here? @TODO (maybe must easier to let main PdfWizard handle it with db settings?)
                #$data["default_font"] = array(
                #   "font_family" => $data['pdf_wizard_settings_default_font'],
                #   "font_style" => '',
                #   "font_size" => 12,
                #   "font_color" => array(
                #       "R" => 180,
                #       "G" => 180,
                #       "B" => 180
                #   )
                #);
                $pdf = new OrderInfoPdf($data);
                $pdf->build();
                $pdf->Output();
                
            } else {
                return new Action('error/not_found');
            }
        }
    }
}