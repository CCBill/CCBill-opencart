<?php
class ControllerExtensionPaymentCCBill extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/payment/ccbill');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('payment_ccbill', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'], true));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_all_zones'] = $this->language->get('text_all_zones');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_authorization'] = $this->language->get('text_authorization');
		$data['text_sale'] = $this->language->get('text_sale');

		$data['entry_client_account_no']    = $this->language->get('entry_client_account_no');
		$data['entry_client_subaccount_no'] = $this->language->get('entry_client_subaccount_no');
		$data['entry_form_name']            = $this->language->get('entry_form_name');
		$data['entry_is_flexform']          = $this->language->get('entry_is_flexform');
		$data['entry_currency_code']        = $this->language->get('entry_currency_code');
		$data['entry_salt']                 = $this->language->get('entry_salt');

		$data['entry_test'] = $this->language->get('entry_test');
		$data['entry_transaction'] = $this->language->get('entry_transaction');
		$data['entry_debug'] = $this->language->get('entry_debug');
		$data['entry_total'] = $this->language->get('entry_total');
		$data['entry_canceled_reversal_status'] = $this->language->get('entry_canceled_reversal_status');
		$data['entry_completed_status'] = $this->language->get('entry_completed_status');
		$data['entry_denied_status'] = $this->language->get('entry_denied_status');
		$data['entry_expired_status'] = $this->language->get('entry_expired_status');
		$data['entry_failed_status'] = $this->language->get('entry_failed_status');
		$data['entry_pending_status'] = $this->language->get('entry_pending_status');
		$data['entry_processed_status'] = $this->language->get('entry_processed_status');
		$data['entry_refunded_status'] = $this->language->get('entry_refunded_status');
		$data['entry_reversed_status'] = $this->language->get('entry_reversed_status');
		$data['entry_voided_status'] = $this->language->get('entry_voided_status');
		$data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');

		$data['help_test']  = $this->language->get('help_test');
		$data['help_debug'] = $this->language->get('help_debug');
		$data['help_total'] = $this->language->get('help_total');

		$data['help_client_account_no']     = $this->language->get('help_client_account_no');
		$data['help_client_subaccount_no']  = $this->language->get('help_client_subaccount_no');
		$data['help_form_name']             = $this->language->get('help_form_name');
		$data['help_is_flexform']           = $this->language->get('help_is_flexform');
		$data['help_currency_code']         = $this->language->get('help_currency_code');
		$data['help_salt']                  = $this->language->get('help_salt');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		$data['tab_general'] = $this->language->get('tab_general');
		$data['tab_status'] = $this->language->get('tab_status');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['email'])) {
			$data['error_email'] = $this->error['email'];
		} else {
			$data['error_email'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_payment'),
			'href' => $this->url->link('extension/payment', 'user_token=' . $this->session->data['user_token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/payment/ccbill', 'user_token=' . $this->session->data['user_token'], 'SSL')
		);

		$data['action'] = $this->url->link('extension/payment/ccbill', 'user_token=' . $this->session->data['user_token'], 'SSL');

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'], true);

		if (isset($this->request->post['payment_ccbill_client_account_no'])) {
			$data['payment_ccbill_client_account_no'] = $this->request->post['payment_ccbill_client_account_no'];
		} else {
			$data['payment_ccbill_client_account_no'] = $this->config->get('payment_ccbill_client_account_no');
		}

		if (isset($this->request->post['payment_ccbill_client_subaccount_no'])) {
			$data['payment_ccbill_client_subaccount_no'] = $this->request->post['payment_ccbill_client_subaccount_no'];
		} else {
			$data['payment_ccbill_client_subaccount_no'] = $this->config->get('payment_ccbill_client_subaccount_no');
		}

		if (isset($this->request->post['payment_ccbill_form_name'])) {
			$data['payment_ccbill_form_name'] = $this->request->post['payment_ccbill_form_name'];
		} else {
			$data['payment_ccbill_form_name'] = $this->config->get('payment_ccbill_form_name');
		}

		if (isset($this->request->post['payment_ccbill_is_flexform'])) {
			$data['payment_ccbill_is_flexform'] = $this->request->post['payment_ccbill_is_flexform'];
		} else {
			$data['payment_ccbill_is_flexform'] = $this->config->get('payment_ccbill_is_flexform');
		}

		if (isset($this->request->post['payment_ccbill_currency_code'])) {
			$data['payment_ccbill_currency_code'] = $this->request->post['payment_ccbill_currency_code'];
		} else {
			$data['payment_ccbill_currency_code'] = $this->config->get('payment_ccbill_currency_code');
		}

		if (isset($this->request->post['payment_ccbill_salt'])) {
			$data['payment_ccbill_salt'] = $this->request->post['payment_ccbill_salt'];
		} else {
			$data['payment_ccbill_salt'] = $this->config->get('payment_ccbill_salt');
		}

		if (isset($this->request->post['payment_ccbill_test'])) {
			$data['payment_ccbill_test'] = $this->request->post['payment_ccbill_test'];
		} else {
			$data['payment_ccbill_test'] = $this->config->get('payment_ccbill_test');
		}

		if (isset($this->request->post['payment_ccbill_transaction'])) {
			$data['payment_ccbill_transaction'] = $this->request->post['payment_ccbill_transaction'];
		} else {
			$data['payment_ccbill_transaction'] = $this->config->get('payment_ccbill_transaction');
		}

		if (isset($this->request->post['payment_ccbill_debug'])) {
			$data['payment_ccbill_debug'] = $this->request->post['payment_ccbill_debug'];
		} else {
			$data['payment_ccbill_debug'] = $this->config->get('payment_ccbill_debug');
		}

		if (isset($this->request->post['payment_ccbill_total'])) {
			$data['payment_ccbill_total'] = $this->request->post['payment_ccbill_total'];
		} else {
			$data['payment_ccbill_total'] = $this->config->get('payment_ccbill_total');
		}

		if (isset($this->request->post['payment_ccbill_canceled_reversal_status_id'])) {
			$data['payment_ccbill_canceled_reversal_status_id'] = $this->request->post['payment_ccbill_canceled_reversal_status_id'];
		} else {
			$data['payment_ccbill_canceled_reversal_status_id'] = $this->config->get('payment_ccbill_canceled_reversal_status_id');
		}

		if (isset($this->request->post['payment_ccbill_completed_status_id'])) {
			$data['payment_ccbill_completed_status_id'] = $this->request->post['payment_ccbill_completed_status_id'];
		} else {
			$data['payment_ccbill_completed_status_id'] = $this->config->get('payment_ccbill_completed_status_id');
		}

		if (isset($this->request->post['payment_ccbill_denied_status_id'])) {
			$data['payment_ccbill_denied_status_id'] = $this->request->post['payment_ccbill_denied_status_id'];
		} else {
			$data['payment_ccbill_denied_status_id'] = $this->config->get('payment_ccbill_denied_status_id');
		}

		if (isset($this->request->post['payment_ccbill_expired_status_id'])) {
			$data['payment_ccbill_expired_status_id'] = $this->request->post['payment_ccbill_expired_status_id'];
		} else {
			$data['payment_ccbill_expired_status_id'] = $this->config->get('payment_ccbill_expired_status_id');
		}

		if (isset($this->request->post['payment_ccbill_failed_status_id'])) {
			$data['payment_ccbill_failed_status_id'] = $this->request->post['payment_ccbill_failed_status_id'];
		} else {
			$data['payment_ccbill_failed_status_id'] = $this->config->get('payment_ccbill_failed_status_id');
		}

		if (isset($this->request->post['payment_ccbill_pending_status_id'])) {
			$data['payment_ccbill_pending_status_id'] = $this->request->post['payment_ccbill_pending_status_id'];
		} else {
			$data['payment_ccbill_pending_status_id'] = $this->config->get('payment_ccbill_pending_status_id');
		}

		if (isset($this->request->post['payment_ccbill_processed_status_id'])) {
			$data['payment_ccbill_processed_status_id'] = $this->request->post['payment_ccbill_processed_status_id'];
		} else {
			$data['payment_ccbill_processed_status_id'] = $this->config->get('payment_ccbill_processed_status_id');
		}

		if (isset($this->request->post['payment_ccbill_refunded_status_id'])) {
			$data['payment_ccbill_refunded_status_id'] = $this->request->post['payment_ccbill_refunded_status_id'];
		} else {
			$data['payment_ccbill_refunded_status_id'] = $this->config->get('payment_ccbill_refunded_status_id');
		}

		if (isset($this->request->post['payment_ccbill_reversed_status_id'])) {
			$data['payment_ccbill_reversed_status_id'] = $this->request->post['payment_ccbill_reversed_status_id'];
		} else {
			$data['payment_ccbill_reversed_status_id'] = $this->config->get('payment_ccbill_reversed_status_id');
		}

		if (isset($this->request->post['payment_ccbill_voided_status_id'])) {
			$data['payment_ccbill_voided_status_id'] = $this->request->post['payment_ccbill_voided_status_id'];
		} else {
			$data['payment_ccbill_voided_status_id'] = $this->config->get('payment_ccbill_voided_status_id');
		}

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['payment_ccbill_geo_zone_id'])) {
			$data['payment_ccbill_geo_zone_id'] = $this->request->post['payment_ccbill_geo_zone_id'];
		} else {
			$data['payment_ccbill_geo_zone_id'] = $this->config->get('payment_ccbill_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['payment_ccbill_status'])) {
			$data['payment_ccbill_status'] = $this->request->post['payment_ccbill_status'];
		} else {
			$data['payment_ccbill_status'] = $this->config->get('payment_ccbill_status');
		}

		if (isset($this->request->post['payment_ccbill_sort_order'])) {
			$data['payment_ccbill_sort_order'] = $this->request->post['payment_ccbill_sort_order'];
		} else {
			$data['payment_ccbill_sort_order'] = $this->config->get('payment_ccbill_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/payment/ccbill', $data));
	}

	protected function validate() {

		$check_credentials = true;

		if (!$this->user->hasPermission('modify', 'extension/payment/ccbill')) {
			$this->error['warning'] = $this->language->get('error_permission');
			$check_credentials = false;
		}
/*
		if (!$this->request->post['payment_ccbill_email']) {
			$this->error['email'] = $this->language->get('error_email');
		}
*/
		return !$this->error;
	}
}
