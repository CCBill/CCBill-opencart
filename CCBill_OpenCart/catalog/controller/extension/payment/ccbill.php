<?php
class ControllerExtensionPaymentCCBill extends Controller {

    public function index() {

		$this->language->load('extension/payment/ccbill');

		$data['text_testmode'] = '';
		$data['button_confirm'] = 'Submit Order';//$this->language->get('button_confirm');

		$data['testmode'] = false;

	    // Set the form action.  Data will be sent via POST
	    $myFormAction = 'https://bill.ccbill.com/jpost/signup.cgi';

		$is_flexform    = $this->config->get('payment_ccbill_is_flexform') == 'yes';
	    $priceVarName   = 'formPrice';
	    $periodVarName  = 'formPeriod';

	    if($is_flexform){
			$myFormAction   = 'https://api.ccbill.com/wap-frontflex/flexforms/' . $this->config->get('payment_ccbill_form_name');
			$priceVarName   = 'initialPrice';
			$periodVarName  = 'initialPeriod';
	    }// end if

	    $data['action']      = $myFormAction;
	    $data['is_flexform'] = $is_flexform;

		$this->load->model('checkout/order');

    	// If order info is present in the session, proceed.
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		if ($order_info) {
            //$data['business'] = $this->config->get('payment_ccbill_email');

            // Throw an exception if the order total is not greater than zero
            if ( !($order_info['total'] > 0) )
                die("<script type=\"text/javascript\">alert('Invalid amount');location.reload();</script>");

			$data['item_name'] = html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8');
			$data['products'] = array();

			foreach ($this->cart->getProducts() as $product) {

				$option_data = array();

				foreach ($product['option'] as $option) {

                	if ($option['type'] != 'file') {

                		if(array_key_exists('option_value', $option))
							$value = $option['option_value'];
						else
							$value = 0;

                	} else {

                		$filename = $this->encryption->decrypt($option['option_value']);

						$value = utf8_substr($filename, 0, utf8_strrpos($filename, '.'));

                    }// end if/else

					$option_data[] = array(
						'name'  => $option['name'],
						'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
					);

				}// end foreach option

				$data['products'][] = array(
					'name'     => htmlspecialchars($product['name']),
					'model'    => htmlspecialchars($product['model']),
					'price'    => $this->currency->format($product['price'], $order_info['currency_code'], false, false),
					'quantity' => $product['quantity'],
					'option'   => $option_data,
					'weight'   => $product['weight']
				);

			}// end foreach product

			$data['discount_amount_cart'] = 0;

			$total = $this->currency->format($order_info['total'] - $this->cart->getSubTotal(), $order_info['currency_code'], false, false);

			if ($total > 0) {

				$data['products'][] = array(
					'name'     => $this->language->get('text_total'),
					'model'    => '',
					'price'    => $total,
					'quantity' => 1,
					'option'   => array(),
					'weight'   => 0
				);

			} else {

				$data['discount_amount_cart'] -= $total;

			}// end if/else

            // Generate verification hash
			$mySalt              = $this->config->get('payment_ccbill_salt');
			$myDigest            = '';
			$billingPeriodInDays = 2;
			$myCurrencyCode      = $this->config->get('payment_ccbill_currency_code');

            $wCartTotal   = '' . number_format($order_info['total'], 2, '.', '');

			$stringToHash = '' . $wCartTotal
			                   . $billingPeriodInDays
			                   . $myCurrencyCode
			                   . $mySalt;

		    $myDigest = md5($stringToHash);

            // Output form values
			$data['clientAccnum']  = $this->config->get('payment_ccbill_client_account_no');
			$data['clientSubacc']  = $this->config->get('payment_ccbill_client_subaccount_no');
			$data['formName']      = $this->config->get('payment_ccbill_form_name');
			$data[$priceVarName]   = $wCartTotal;
			$data[$periodVarName]  = $billingPeriodInDays;
			$data['currencyCode']  = $myCurrencyCode;
			$data['formDigest']    = $myDigest;


			$data['customer_fname']= html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8');
			$data['customer_lname']= html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8');
			$data['address1']      = html_entity_decode($order_info['payment_address_1'], ENT_QUOTES, 'UTF-8');
			$data['address2']      = html_entity_decode($order_info['payment_address_2'], ENT_QUOTES, 'UTF-8');
			$data['city']          = html_entity_decode($order_info['payment_city'], ENT_QUOTES, 'UTF-8');
			$data['state']         = $this->getStateCodeFromName($order_info['payment_zone']);
			$data['zipcode']       = html_entity_decode($order_info['payment_postcode'], ENT_QUOTES, 'UTF-8');
			$data['country']       = $order_info['payment_iso_code_2'];
			$data['email']         = $order_info['email'];
			$data['zc_orderid']    = $this->session->data['order_id'];

			// For testing direct submit
			//$data['Approval_Action'] = 'Approval_Post';
			//$data['action'] = 'http://localhost/opencart/index.php?route=payment/ccbill/callback';


			// Set order status to pending (1)
		    $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], 1);

		    // Clear Cart
		    $this->cart->clear();

			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/extension/payment/ccbill')) {
				return $this->load->view($this->config->get('config_template') . '/template/extension/payment/ccbill', $data);
			} else {
				return $this->load->view('extension/payment/ccbill', $data);
			}// end if/else

		}// end if order_info exists

    }// end index function

    // Return a state code given a state name
	function getStateCodeFromName($stateName){

        $rVal = $stateName;

        switch($rVal){
            case 'Alabama':         $rVal = 'AL';
                break;
            case 'Alaska':          $rVal = 'AK';
                break;
            case 'Arizona':         $rVal = 'AZ';
                break;
            case 'Arkansas':        $rVal = 'AR';
                break;
            case 'California':      $rVal = 'CA';
                break;
            case 'Colorado':        $rVal = 'CO';
                break;
            case 'Connecticut':     $rVal = 'CT';
                break;
            case 'Delaware':        $rVal = 'DE';
                break;
            case 'Florida':         $rVal = 'FL';
                break;
            case 'Georgia':         $rVal = 'GA';
                break;
            case 'Hawaii':          $rVal = 'HI';
                break;
            case 'Idaho':           $rVal = 'ID';
                break;
            case 'Illinois':        $rVal = 'IL';
                break;
            case 'Indiana':         $rVal = 'IN';
                break;
            case 'Iowa':            $rVal = 'IA';
                break;
            case 'Kansas':          $rVal = 'KS';
                break;
            case 'Kentucky':        $rVal = 'KY';
                break;
            case 'Louisiana':       $rVal = 'LA';
                break;
            case 'Maine':           $rVal = 'ME';
                break;
            case 'Maryland':        $rVal = 'MD';
                break;
            case 'Massachusetts':   $rVal = 'MA';
                break;
            case 'Michigan':        $rVal = 'MI';
                break;
            case 'Minnesota':       $rVal = 'MN';
                break;
            case 'Mississippi':     $rVal = 'MS';
                break;
            case 'Missouri':        $rVal = 'MO';
                break;
            case 'Montana':         $rVal = 'MT';
                break;
            case 'Nebraska':        $rVal = 'NE';
                break;
            case 'Nevada':          $rVal = 'NV';
                break;
            case 'New York':        $rVal = 'NY';
                break;
            case 'Ohio':            $rVal = 'OH';
                break;
            case 'Oklahoma':        $rVal = 'OK';
                break;
            case 'Oregon':          $rVal = 'OR';
                break;
            case 'Pennsylvania':    $rVal = 'PN';
                break;
            case 'Rhode Island':    $rVal = 'RI';
                break;
            case 'South Carolina':  $rVal = 'SC';
                break;
            case 'South Dakota':    $rVal = 'SD';
                break;
            case 'Tennessee':       $rVal = 'TN';
                break;
            case 'Texas':           $rVal = 'TX';
                break;
            case 'Utah':            $rVal = 'UT';
                break;
            case 'Virginia':        $rVal = 'VA';
                break;
            case 'Vermont':         $rVal = 'VT';
                break;
            case 'Washington':      $rVal = 'WA';
                break;
            case 'Wisconsin':       $rVal = 'WI';
                break;
            case 'West Virginia':   $rVal = 'WV';
                break;
            case 'Wyoming':         $rVal = 'WY';
                break;
        }// end switch

        return $rVal;

    }// end getStateCodeFromName

    private function responseDigestIsValid($subscriptionId, $responseDigest, $isFlexForm, $salt)
    {
        if (strlen($subscriptionId . '') < 1 || strlen($responseDigest . '') < 1) {
            return false;
        }// end if

        // If using FlexForms, remove leading zeroes from subscription id before computing the hash
        if ($isFlexForm) {
            $subscriptionId = ltrim($subscriptionId, '0');
        }// end if

        $stringToHash = $subscriptionId . 1 . $salt;

        $myDigest = md5($stringToHash);

        $this->log('274 stringToHash: ' . $stringToHash . '; myDigest: ' . $myDigest . '; responseDigest: ' . $responseDigest);

        if ($myDigest == $responseDigest) {
            return true;
        }// end if

        return false;

    }// end responseDigestIsValid

    // Process an approval notification
    public function callback_approval(){

        $this->log('285 callback approval');

		$myOrderId    = -1;
		$order_info   = null;
		$txId         = -1;

		$myFirstName    = '';
		$myLastName     = '';
		$myEmail        = '';
		$myAmount       = -1;
		$myCurrencyCode = -1;
		$cardType       = '';

        $isFlexForm            = $this->config->get('payment_ccbill_is_flexform') == 'yes';
        $salt                  = $this->config->get('payment_ccbill_salt');
        $billingPeriodInDays   = 2;
        $responseDigest        = '';
        $responseDigestIsValid = false;

        $this->log('307 isFlexForm: ' . $isFlexForm . '; salt: ' . $salt);

		if(isset($this->request->post['customer_fname']))    $myFirstName    = $this->request->post['customer_fname'];
        if(isset($this->request->post['customer_lname']))    $myLastName     = $this->request->post['customer_lname'];
        if(isset($this->request->post['email']))             $myEmail        = $this->request->post['email'];
        if(isset($this->request->post['accountingAmount']))  $myAmount       = $this->request->post['accountingAmount'];
        if(isset($this->request->post['currencyCode']))      $myCurrencyCode = $this->request->post['currencyCode'];
        if(isset($this->request->post['zc_orderid']))        $myOrderId      = $this->request->post['zc_orderid'];
        if(isset($this->request->post['cardType']))          $cardType       = $this->request->post['cardType'];
        if(isset($this->request->post['subscription_id']))   $txId           = $this->request->post['subscription_id'];
        if(isset($this->request->post['responseDigest']))    $responseDigest = $this->request->post['responseDigest'];

        $this->log('321 myorderId: ' . $myOrderId . '; myEmail: ' . $myEmail . '; subscriptionId: ' . $txId . '; responseDigest: ' . $responseDigest);

		if($myOrderId > 0 && strlen($myEmail) > 0 && $txId > 0){

            $this->log('327 about to verify the response');

            $responseDigestIsValid = $this->responseDigestIsValid($txId, $responseDigest, $isFlexForm, $salt);

            $validText = "false";

            if($responseDigestIsValid)
                $validText = "true";

            $this->log('336 responseDigestIsValid: ' . $validText);

            $order_query = $this->db->query('SELECT `order_id` FROM `' . DB_PREFIX . 'order` WHERE `ip` = "' . $this->request->post['ip_address'] . '" AND `total` = "' . $myAmount . '" ORDER BY `order_id` DESC');

		    if($order_query->num_rows){
		        $myOrderId = $order_query->row['order_id'];
		    }// end if

		}// end if

		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($myOrderId);

		if ($order_info && $responseDigestIsValid) {

            $order_status_id = 2; // Processing

            $this->model_checkout_order->addOrderHistory($myOrderId, $order_status_id);

            print_r('<html><head><title>done</title></head><body>done</body></html>');

		}// end if order info

    }// end callback_approval

    public function callback_denial(){

        $this->log('353 callback denial');

        $order_id = 0;
		$order_info = null;

		if (isset($this->request->post['zc_orderid'])) {
			$order_id = $this->request->post['zc_orderid'];
		}// end if

		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($order_id);

		if ($order_info) {

            $txId      = 0;
            $order_status_id = 1;

            if(isset($this->request->post['denialId']))
                $txId = $this->request->post['denialId'];

            $order_status_id = 10;// Failed

            $this->model_checkout_order->addOrderHistory($order_id, $order_status_id);

            print_r('<html><head><title>done</title></head><body>done</body></html>');

		}// end if order info

    }// end callback_denial

	public function callback($myAction) {

        $this->log('386 callback');

        if($myAction == 'Approval_Post')
            $this->callback_approval();
        else if($myAction == 'Denial_Post')
            $this->callback_denial();

	}// end callback

    private function log($message)
    {
        $logIsActive = false;

        if (!$logIsActive) {
            return false;
        }// end if/else

        if (!isset($this->logWritten)) {
            $this->logWritten = true;
            file_put_contents(
                "ccbill_log.txt",
                "\r\n\r\n\r\n=============== " . date("Y-m-d | h:i:sa") . " ==========\r\n\r\n",
                FILE_APPEND
            );
        }// end if

        file_put_contents("ccbill_log.txt", "\r\n" . $message, FILE_APPEND);
    }// end log

}// end class
