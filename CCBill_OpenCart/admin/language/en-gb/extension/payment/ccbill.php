<?php
// Heading
$_['heading_title']					= 'CCBill';

// Text
$_['text_payment']					= 'Payment';
$_['text_success']					= 'Success: You have modified CCBill account details!';
$_['text_edit']             = 'Edit CCBill Settings';
$_['text_ccbill']	          = '<a target="_blank" href="https://www.ccbill.com"><img src="view/image/payment/ccbill.png" alt="CCBill Website" title="CCBill Website" style="border: 0px;" /></a>';
$_['text_authorization']		= 'Authorization';
$_['text_sale']						  = 'Sale';

// Entry
$_['entry_client_account_no']	    = 'Client Account No';
$_['entry_client_subaccount_no']	= 'Client SubAccount No';
$_['entry_form_name']	            = 'Form Name';
$_['entry_flexform_id']             = 'FlexForm ID';
$_['entry_is_flexform']	            = 'Is Flexform';
$_['entry_currency_code']	        = 'Currency';
$_['entry_salt']	                = 'Salt';

$_['entry_test']					  = 'Sandbox Mode';
$_['entry_transaction']			= 'Transaction Method';
$_['entry_debug']					  = 'Debug Mode';
$_['entry_total']					  = 'Total';
$_['entry_canceled_reversal_status'] = 'Canceled Reversal Status';
$_['entry_completed_status']= 'Completed Status';
$_['entry_denied_status']		= 'Denied Status';
$_['entry_expired_status']	= 'Expired Status';
$_['entry_failed_status']		= 'Failed Status';
$_['entry_pending_status']	= 'Pending Status';
$_['entry_processed_status']= 'Processed Status';
$_['entry_refunded_status']	= 'Refunded Status';
$_['entry_reversed_status']	= 'Reversed Status';
$_['entry_voided_status']		= 'Voided Status';
$_['entry_geo_zone']				= 'Geo Zone';
$_['entry_status']					= 'Status';
$_['entry_sort_order']			= 'Sort Order';

// Tab
$_['tab_general']					= 'General';
$_['tab_status']					= 'Order status';

// Help
$_['help_test']						= 'Use the live or testing (sandbox) gateway server to process transactions?';
$_['help_debug']					= 'Logs additional information to the system log';
$_['help_total']					= 'The checkout total the order must reach before this payment method becomes active';

$_['help_client_account_no']    = 'Please enter your six-digit CCBill client account number; this is needed in order to take payment via CCBill.';
$_['help_client_subaccount_no'] = 'Please enter your four-digit CCBill client account number; this is needed in order to take payment via CCBill.';
$_['help_form_name']            = 'The name of the CCBill form used to collect payment';
$_['help_is_flexform']          = 'Select Yes if using a CCBill FlexForm.  Otherwise, select No.';
$_['help_currency_code']        = 'The currency in which payments will be made.';
$_['help_salt']                 = 'The salt value is used by CCBill to verify the hash and can be obtained in one of two ways: (1) Contact client support and receive the salt value, OR (2) Create your own salt value (up to 32 alphanumeric characters) and provide it to client support.';

// Error
$_['error_permission']				= 'Warning: You do not have permission to modify payment CCBill!';
$_['error_email']					= 'E-Mail required!';
