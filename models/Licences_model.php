<?php

use app\services\AbstractKanban;
use app\services\licences\LicencesPipeline;
use modules\offices\models\Offices_model;
defined('BASEPATH') or exit('No direct script access allowed');

class Licences_model extends App_Model
{
    private $statuses;

    private $shipping_fields = ['shipping_street', 'shipping_city', 'shipping_city', 'shipping_state', 'shipping_zip', 'shipping_country'];

    public function __construct()
    {
        parent::__construct();

        $this->statuses = hooks()->apply_filters('before_set_licence_statuses', [
            1,
            2,
            5,
            3,
            4,
        ]);
    }
    /**
     * Get unique sale agent for licences / Used for filters
     * @return array
     */
    public function get_assigneds()
    {
        return $this->db->query("SELECT DISTINCT(assigned) as assigned, CONCAT(firstname, ' ', lastname) as full_name FROM " . db_prefix() . 'licences JOIN ' . db_prefix() . 'staff on ' . db_prefix() . 'staff.staffid=' . db_prefix() . 'licences.assigned WHERE assigned != 0')->result_array();
    }

    /**
     * Get licence/s
     * @param mixed $id licence id
     * @param array $where perform where
     * @return mixed
     */
    public function get($id = '', $where = [])
    {
//        $this->db->select('*,' . db_prefix() . 'currencies.id as currencyid, ' . db_prefix() . 'licences.id as id, ' . db_prefix() . 'currencies.name as currency_name');
        $this->db->select('*,' . db_prefix() . 'licences.id as id');
        $this->db->from(db_prefix() . 'licences');
        //$this->db->join(db_prefix() . 'currencies', db_prefix() . 'currencies.id = ' . db_prefix() . 'licences.currency', 'left');
        $this->db->where($where);
        if (is_numeric($id)) {
            $this->db->where(db_prefix() . 'licences.id', $id);
            $licence = $this->db->get()->row();
            if ($licence) {
                $licence->attachments                           = $this->get_attachments($id);
                $licence->visible_attachments_to_customer_found = false;

                foreach ($licence->attachments as $attachment) {
                    if ($attachment['visible_to_customer'] == 1) {
                        $licence->visible_attachments_to_customer_found = true;

                        break;
                    }
                }

                $licence->items = get_items_by_type('licence', $id);
                if(isset($licence->licence_id)){

                    if ($licence->licence_id != 0) {
                        $this->load->model('licences_model');
                        $licence->licence_data = $this->licences_model->get($licence->licence_id);
                    }

                }
                $licence->client = $this->clients_model->get($licence->clientid);

                if (!$licence->client) {
                    $licence->client          = new stdClass();
                    $licence->client->company = $licence->deleted_customer_name;
                }

                $this->load->model('email_schedule_model');
                $licence->scheduled_email = $this->email_schedule_model->get($id, 'licence');

                include_once(FCPATH . 'modules/offices/models/Offices_model.php');
                $this->load->model('offices_model');
                $licence->office = $this->offices_model->get($licence->office_id);

            }

            return $licence;
        }
        $this->db->order_by('number,YEAR(date)', 'desc');

        return $this->db->get()->result_array();
    }

    /**
     * Get licence statuses
     * @return array
     */
    public function get_statuses()
    {
        return $this->statuses;
    }


    /**
     * Get licence statuses
     * @return array
     */
    public function get_status($status,$id)
    {
        $this->db->where('status', $status);
        $this->db->where('id', $id);
        $licence = $this->db->get(db_prefix() . 'licences')->row();

        return $this->status;
    }

    public function clear_signature($id)
    {
        $this->db->select('signed','signature','status');
        $this->db->where('id', $id);
        $licence = $this->db->get(db_prefix() . 'licences')->row();

        if ($licence) {
            $this->db->where('id', $id);
            $this->db->update(db_prefix() . 'licences', ['signed'=>0,'signature' => null, 'status'=>2]);

            if (!empty($licence->signature)) {
                unlink(get_upload_path_by_type('licence') . $id . '/' . $licence->signature);
            }

            return true;
        }

        return false;
    }


    /**
     * Copy licence
     * @param mixed $id licence id to copy
     * @return mixed
     */
    public function copy($id)
    {
        $_licence                       = $this->get($id);
        $new_licence_data               = [];
        $new_licence_data['clientid']   = $_licence->clientid;
        $new_licence_data['project_id']   = $_licence->project_id;
        $new_licence_data['number']     = get_option('next_licence_number');
        $new_licence_data['proposed_date']       = _d(date('Y-m-d'));



        $number = get_option('next_licence_number');
        $format = get_option('licence_number_format');
        $prefix = get_option('licence_prefix');
        $date = date('Y-m-d');

        $new_licence_data['formatted_number'] = licence_number_format($number, $format, $prefix, $date);



        $new_licence_data['terms']            = $_licence->terms;
        $new_licence_data['assigned']       = $_licence->assigned;
        $new_licence_data['reference_no']     = $_licence->reference_no;
        $new_licence_data['office_id']     = $_licence->office_id;

        // Since version 1.0.6
        $new_licence_data['billing_street']   = clear_textarea_breaks($_licence->billing_street);
        $new_licence_data['billing_city']     = $_licence->billing_city;
        $new_licence_data['billing_state']    = $_licence->billing_state;
        $new_licence_data['billing_zip']      = $_licence->billing_zip;
        $new_licence_data['billing_country']  = $_licence->billing_country;
        $new_licence_data['shipping_street']  = clear_textarea_breaks($_licence->shipping_street);
        $new_licence_data['shipping_city']    = $_licence->shipping_city;
        $new_licence_data['shipping_state']   = $_licence->shipping_state;
        $new_licence_data['shipping_zip']     = $_licence->shipping_zip;
        $new_licence_data['shipping_country'] = $_licence->shipping_country;
        if ($_licence->include_shipping == 1) {
            $new_licence_data['include_shipping'] = $_licence->include_shipping;
        }
        $new_licence_data['show_shipping_on_licence'] = $_licence->show_shipping_on_licence;
        // Set to unpaid status automatically
        $new_licence_data['status']     = 1;
        $new_licence_data['clientnote'] = $_licence->clientnote;
        $new_licence_data['adminnote']  = '';
        $new_licence_data['newitems']   = [];
        $custom_fields_items             = get_custom_fields('items');
        $key                             = 1;
        foreach ($_licence->items as $item) {
            $new_licence_data['newitems'][$key]['description']      = $item['description'];
            $new_licence_data['newitems'][$key]['long_description'] = clear_textarea_breaks($item['long_description']);
            $new_licence_data['newitems'][$key]['qty']              = $item['qty'];
            $new_licence_data['newitems'][$key]['unit']             = $item['unit'];

            $new_licence_data['newitems'][$key]['order'] = $item['item_order'];
            $key++;
        }
        $id = $this->add($new_licence_data);
        if ($id) {

            $tags = get_tags_in($_licence->id, 'licence');
            handle_tags_save($tags, $id, 'licence');

            $this->log_licence_activity('Copied Licence ' . format_licence_number($_licence->id));

            hooks()->do_action('after_licence_copied', $id);
            return $id;
        }

        return false;
    }

    /**
     * Performs licences totals status
     * @param array $data
     * @return array
     */
    public function get_licences_total($data)
    {
        $statuses            = $this->get_statuses();
        $has_permission_view = has_permission('licences', '', 'view');
        $this->load->model('currencies_model');
        if (isset($data['currency'])) {
            $currencyid = $data['currency'];
        } elseif (isset($data['customer_id']) && $data['customer_id'] != '') {
            $currencyid = $this->clients_model->get_customer_default_currency($data['customer_id']);
            if ($currencyid == 0) {
                $currencyid = $this->currencies_model->get_base_currency()->id;
            }
        } elseif (isset($data['licence_id']) && $data['licence_id'] != '') {
            $this->load->model('licences_model');
            $currencyid = $this->licences_model->get_currency($data['licence_id'])->id;
        } else {
            $currencyid = $this->currencies_model->get_base_currency()->id;
        }

        $currency = get_currency($currencyid);
        $where    = '';
        if (isset($data['customer_id']) && $data['customer_id'] != '') {
            $where = ' AND clientid=' . $data['customer_id'];
        }

        if (isset($data['licence_id']) && $data['licence_id'] != '') {
            $where .= ' AND licence_id=' . $data['licence_id'];
        }

        if (!$has_permission_view) {
            $where .= ' AND ' . get_licences_where_sql_for_staff(get_staff_user_id());
        }

        $sql = 'SELECT';
        foreach ($statuses as $licence_status) {
            $sql .= '(SELECT SUM(total) FROM ' . db_prefix() . 'licences WHERE status=' . $licence_status;
            $sql .= ' AND currency =' . $this->db->escape_str($currencyid);
            if (isset($data['years']) && count($data['years']) > 0) {
                $sql .= ' AND YEAR(date) IN (' . implode(', ', array_map(function ($year) {
                    return get_instance()->db->escape_str($year);
                }, $data['years'])) . ')';
            } else {
                $sql .= ' AND YEAR(date) = ' . date('Y');
            }
            $sql .= $where;
            $sql .= ') as "' . $licence_status . '",';
        }

        $sql     = substr($sql, 0, -1);
        $result  = $this->db->query($sql)->result_array();
        $_result = [];
        $i       = 1;
        foreach ($result as $key => $val) {
            foreach ($val as $status => $total) {
                $_result[$i]['total']         = $total;
                $_result[$i]['symbol']        = $currency->symbol;
                $_result[$i]['currency_name'] = $currency->name;
                $_result[$i]['status']        = $status;
                $i++;
            }
        }
        $_result['currencyid'] = $currencyid;

        return $_result;
    }

    /**
     * Insert new licence to database
     * @param array $data invoiec data
     * @return mixed - false if not insert, licence ID if succes
     */
    public function add($data)
    {
        $affectedRows = 0;

        $data['datecreated'] = date('Y-m-d H:i:s');

        $data['addedfrom'] = get_staff_user_id();

        $data['prefix'] = get_option('licence_prefix');

        $data['number_format'] = get_option('licence_number_format');

        $save_and_send = isset($data['save_and_send']);


        $data['hash'] = app_generate_hash();
        $tags         = isset($data['tags']) ? $data['tags'] : '';

        $items = [];
        if (isset($data['newitems'])) {
            $items = $data['newitems'];
            unset($data['newitems']);
        }

        $data = $this->map_shipping_columns($data);

        $data['billing_street'] = trim($data['billing_street']);
        $data['billing_street'] = nl2br($data['billing_street']);

        if (isset($data['shipping_street'])) {
            $data['shipping_street'] = trim($data['shipping_street']);
            $data['shipping_street'] = nl2br($data['shipping_street']);
        }

        $hook = hooks()->apply_filters('before_licence_added', [
            'data'  => $data,
            'items' => $items,
        ]);

        $data  = $hook['data'];
        $items = $hook['items'];

        unset($data['tags']);
        unset($data['allowed_payment_modes']);
        unset($data['save_as_draft']);
        unset($data['schedule_id']);
        unset($data['duedate']);

        try {
            $this->db->insert(db_prefix() . 'licences', $data);
        } catch (Exception $e) {
            $message = $e->getMessage();
            log_activity('Insert ERROR ' . $message);
        }

        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            // Update next licence number in settings
            $this->db->where('name', 'next_licence_number');
            $this->db->set('value', 'value+1', false);
            $this->db->update(db_prefix() . 'options');

            handle_tags_save($tags, $insert_id, 'licence');

            foreach ($items as $key => $item) {
                if ($new_item_added = add_new_licence_item_post($item, $insert_id, 'licence')) {
                    $affectedRows++;
                }
            }

            hooks()->do_action('after_licence_added', $insert_id);

            if ($save_and_send === true) {
                $this->send_licence_to_client($insert_id, '', true, '', true);
            }

            return $insert_id;
        }

        return false;
    }

    /**
     * Get item by id
     * @param mixed $id item id
     * @return object
     */
    public function get_licence_item($id)
    {
        $this->db->where('rel_id', $id);
        $this->db->where('rel_type', 'licence');

        return $this->db->get(db_prefix() . 'licence_items')->result();
    }

    /**
     * Update licence data
     * @param array $data licence data
     * @param mixed $id licenceid
     * @return boolean
     */
    public function update($data, $id)
    {
        $affectedRows = 0;

        $data['number'] = trim($data['number']);

        $original_licence = $this->get($id);

        $original_status = $original_licence->status;

        $original_number = $original_licence->number;

        $original_number_formatted = format_licence_number($id);

        $save_and_send = isset($data['save_and_send']);

        $items = [];
        if (isset($data['items'])) {
            $items = $data['items'];
            unset($data['items']);
        }

        $newitems = [];
        if (isset($data['newitems'])) {
            $newitems = $data['newitems'];
            unset($data['newitems']);
        }

        if (isset($data['tags'])) {
            if (handle_tags_save($data['tags'], $id, 'licence')) {
                $affectedRows++;
            }
        }

        $data['billing_street'] = trim($data['billing_street']);
        $data['billing_street'] = nl2br($data['billing_street']);

        $data['shipping_street'] = trim($data['shipping_street']);
        $data['shipping_street'] = nl2br($data['shipping_street']);

        $data = $this->map_shipping_columns($data);

        $hook = hooks()->apply_filters('before_licence_updated', [
            'data'             => $data,
            'items'            => $items,
            'newitems'         => $newitems,
            'removed_items'    => isset($data['removed_items']) ? $data['removed_items'] : [],
        ], $id);

        $data                  = $hook['data'];
        $items                 = $hook['items'];
        $newitems              = $hook['newitems'];
        $data['removed_items'] = $hook['removed_items'];

        // Delete items checked to be removed from database
        foreach ($data['removed_items'] as $remove_item_id) {
            $original_item = $this->get_licence_item($remove_item_id);
            if (handle_removed_licence_item_post($remove_item_id, 'licence')) {
                $affectedRows++;
                $this->log_licence_activity($id, 'licence_activity_removed_item', false, serialize([
                    $original_item->description,
                ]));
            }
        }

        unset($data['removed_items']);

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'licences', $data);

        if ($this->db->affected_rows() > 0) {
            // Check for status change
            if ($original_status != $data['status']) {
                $this->log_licence_activity($original_licence->id, 'not_licence_status_updated', false, serialize([
                    '<original_status>' . $original_status . '</original_status>',
                    '<new_status>' . $data['status'] . '</new_status>',
                ]));
                if ($data['status'] == 2) {
                    $this->db->where('id', $id);
                    $this->db->update(db_prefix() . 'licences', ['sent' => 1, 'datesend' => date('Y-m-d H:i:s')]);
                }
            }
            if ($original_number != $data['number']) {
                $this->log_licence_activity($original_licence->id, 'licence_activity_number_changed', false, serialize([
                    $original_number_formatted,
                    format_licence_number($original_licence->id),
                ]));
            }
            $affectedRows++;
        }

        foreach ($items as $key => $item) {
            $original_item = $this->get_licence_item($item['itemid']);

            if (update_licence_item_post($item['itemid'], $item, 'item_order')) {
                $affectedRows++;
            }

            if (update_licence_item_post($item['itemid'], $item, 'unit')) {
                $affectedRows++;
            }


            if (update_licence_item_post($item['itemid'], $item, 'qty')) {
                $this->log_licence_activity($id, 'licence_activity_updated_qty_item', false, serialize([
                    $item['description'],
                    $original_item->qty,
                    $item['qty'],
                ]));
                $affectedRows++;
            }

            if (update_licence_item_post($item['itemid'], $item, 'description')) {
                $this->log_licence_activity($id, 'licence_activity_updated_item_short_description', false, serialize([
                    $original_item->description,
                    $item['description'],
                ]));
                $affectedRows++;
            }

            if (update_licence_item_post($item['itemid'], $item, 'long_description')) {
                $this->log_licence_activity($id, 'licence_activity_updated_item_long_description', false, serialize([
                    $original_item->long_description,
                    $item['long_description'],
                ]));
                $affectedRows++;
            }

        }

        foreach ($newitems as $key => $item) {
            if ($new_item_added = add_new_licence_item_post($item, $id, 'licence')) {
                $affectedRows++;
            }
        }

        if ($save_and_send === true) {
            $this->send_licence_to_client($id, '', true, '', true);
        }

        if ($affectedRows > 0) {
            hooks()->do_action('after_licence_updated', $id);
            return true;
        }

        return false;
    }

    public function mark_action_status($action, $id, $client = false)
    {

        $licence = $this->get($id);
        $licence_proposed = $this->get_licence_proposed_items($id, $licence->project_id);

        if(empty($licence_proposed)){
            return false;
        }

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'licences', [
            'status' => $action,
            'signed' => ($action == 4) ? 1 : 0,
            'released_date' => ($action == 5) ? date('Y-m-d H:i:s') : NULL,
        ]);

        $notifiedUsers = [];

        if ($this->db->affected_rows() > 0) {
            //$licence = $this->get($id);
            if ($client == true) {
                $this->db->where('staffid', $licence->addedfrom);
                $this->db->or_where('staffid', $licence->assigned);
                $staff_licence = $this->db->get(db_prefix() . 'staff')->result_array();

                $invoiceid = false;
                $invoiced  = false;

                $contact_id = !is_client_logged_in()
                    ? get_primary_contact_user_id($licence->clientid)
                    : get_contact_user_id();

                if ($action == 5) {
                    $this->db->where('id', $id);
                    $this->db->update(db_prefix() . 'licences', ['sent' => 1, 'datesend' => date('Y-m-d H:i:s')]);

                    $this->db->where('active', 1);
                    $staff_licence = $this->db->get(db_prefix() . 'staff')->result_array();
                    $contacts = $this->clients_model->get_contacts($licence->clientid, ['active' => 1, 'project_emails' => 1]);

                        foreach ($staff_licence as $member) {
                            $notified = add_notification([
                                'fromcompany'     => true,
                                'touserid'        => $member['staffid'],
                                'description'     => 'licence_released_already_sent',
                                'link'            => 'licences/release/' . $id,
                                'additional_data' => serialize([
                                    format_licence_number($licence->id),
                                ]),
                            ]);

                            if ($notified) {
                                array_push($notifiedUsers, $member['staffid']);
                            }
                            // Send staff email notification that customer declined licence
                            // (To fix merge field) send_mail_template('licence_declined_to_staff', 'licences',$licence, $member['email'], $contact_id);
                        }

                    // Admin marked licence
                    $this->log_licence_activity($id, 'licence_activity_marked', false, serialize([
                        '<status>' . $action . '</status>',
                    ]));
                    pusher_trigger_notification($notifiedUsers);
                    hooks()->do_action('licence_released_already_sent', $licence);

                    return true;
                }
                elseif ($action == 4) {
                    $this->log_licence_activity($id, 'licence_activity_client_accepted', true);

                    // Send thank you email to all contacts with permission licences
                    $contacts = $this->clients_model->get_contacts($licence->clientid, ['active' => 1, 'project_emails' => 1]);

                    foreach ($contacts as $contact) {
                        // (To fix merge field) send_mail_template('licence_accepted_to_customer','licences', $licence, $contact);
                    }

                    foreach ($staff_licence as $member) {
                        $notified = add_notification([
                            'fromcompany'     => true,
                            'touserid'        => $member['staffid'],
                            'description'     => 'licence_customer_accepted',
                            'link'            => 'licences/release/' . $id,
                            'additional_data' => serialize([
                                format_licence_number($licence->id),
                            ]),
                        ]);

                        if ($notified) {
                            array_push($notifiedUsers, $member['staffid']);
                        }

                        // (To fix merge field) send_mail_template('licence_accepted_to_staff','licences', $licence, $member['email'], $contact_id);
                    }

                    pusher_trigger_notification($notifiedUsers);
                    hooks()->do_action('licence_accepted', $licence);

                    return true;
                } elseif ($action == 3) {
                    foreach ($staff_licence as $member) {
                        $notified = add_notification([
                            'fromcompany'     => true,
                            'touserid'        => $member['staffid'],
                            'description'     => 'licence_customer_declined',
                            'link'            => 'licences/release/' . $id,
                            'additional_data' => serialize([
                                format_licence_number($licence->id),
                            ]),
                        ]);

                        if ($notified) {
                            array_push($notifiedUsers, $member['staffid']);
                        }
                        // Send staff email notification that customer declined licence
                        // (To fix merge field) send_mail_template('licence_declined_to_staff', 'licences',$licence, $member['email'], $contact_id);
                    }
                    pusher_trigger_notification($notifiedUsers);
                    $this->log_licence_activity($id, 'licence_activity_client_declined', true);
                    hooks()->do_action('licence_declined', $licence);

                    return true;
                }
            } else {
                if ($action == 2) {
                    $this->db->where('id', $id);
                    $this->db->update(db_prefix() . 'licences', ['sent' => 1, 'datesend' => date('Y-m-d H:i:s')]);

                    $this->db->where('active', 1);
                    $staff_licence = $this->db->get(db_prefix() . 'staff')->result_array();
                    $contacts = $this->clients_model->get_contacts($licence->clientid, ['active' => 1, 'project_emails' => 1]);

                        foreach ($staff_licence as $member) {
                            $notified = add_notification([
                                'fromcompany'     => true,
                                'touserid'        => $member['staffid'],
                                'description'     => 'licence_send_to_customer_already_sent',
                                'link'            => 'licences/release/' . $id,
                                'additional_data' => serialize([
                                    format_licence_number($licence->id),
                                ]),
                            ]);

                            if ($notified) {
                                array_push($notifiedUsers, $member['staffid']);
                            }
                            // Send staff email notification that customer declined licence
                            // (To fix merge field) send_mail_template('licence_declined_to_staff', 'licences',$licence, $member['email'], $contact_id);
                        }

                    // Admin marked licence
                    $this->log_licence_activity($id, 'licence_activity_marked', false, serialize([
                        '<status>' . $action . '</status>',
                    ]));
                    pusher_trigger_notification($notifiedUsers);
                    hooks()->do_action('licence_send_to_customer_already_sent', $licence);

                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get licence attachments
     * @param mixed $licence_id
     * @param string $id attachment id
     * @return mixed
     */
    public function get_attachments($licence_id, $id = '')
    {
        // If is passed id get return only 1 attachment
        if (is_numeric($id)) {
            $this->db->where('id', $id);
        } else {
            $this->db->where('rel_id', $licence_id);
        }
        $this->db->where('rel_type', 'licence');
        $result = $this->db->get(db_prefix() . 'files');
        if (is_numeric($id)) {
            return $result->row();
        }

        return $result->result_array();
    }

    /**
     *  Delete licence attachment
     * @param mixed $id attachmentid
     * @return  boolean
     */
    public function delete_attachment($id)
    {
        $attachment = $this->get_attachments('', $id);
        $deleted    = false;
        if ($attachment) {
            if (empty($attachment->external)) {
                unlink(get_upload_path_by_type('licence') . $attachment->rel_id . '/' . $attachment->file_name);
            }
            $this->db->where('id', $attachment->id);
            $this->db->delete(db_prefix() . 'files');
            if ($this->db->affected_rows() > 0) {
                $deleted = true;
                log_licence_activity('Licence Attachment Deleted [LicenceID: ' . $attachment->rel_id . ']');
            }

            if (is_dir(get_upload_path_by_type('licence') . $attachment->rel_id)) {
                // Check if no attachments left, so we can delete the folder also
                $other_attachments = list_files(get_upload_path_by_type('licence') . $attachment->rel_id);
                if (count($other_attachments) == 0) {
                    // okey only index.html so we can delete the folder also
                    delete_dir(get_upload_path_by_type('licence') . $attachment->rel_id);
                }
            }
        }

        return $deleted;
    }

    /**
     * Delete licence items and all connections
     * @param mixed $id licenceid
     * @return boolean
     */
    public function delete($id, $simpleDelete = false)
    {
        if (get_option('delete_only_on_last_licence') == 1 && $simpleDelete == false) {
            if (!is_last_licence($id)) {
                return false;
            }
        }
        $licence = $this->get($id);
        /*
        if (!is_null($licence->invoiceid) && $simpleDelete == false) {
            return [
                'is_invoiced_licence_delete_error' => true,
            ];
        }
        */
        hooks()->do_action('before_licence_deleted', $id);

        $number = format_licence_number($id);

        $this->clear_signature($id);

        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'licences');

        if ($this->db->affected_rows() > 0) {
            if (!is_null($licence->short_link)) {
                app_archive_short_link($licence->short_link);
            }

            if (get_option('licence_number_decrement_on_delete') == 1 && $simpleDelete == false) {
                $current_next_licence_number = get_option('next_licence_number');
                if ($current_next_licence_number > 1) {
                    // Decrement next licence number to
                    $this->db->where('name', 'next_licence_number');
                    $this->db->set('value', 'value-1', false);
                    $this->db->update(db_prefix() . 'options');
                }
            }

            delete_tracked_emails($id, 'licence');

            // Delete the items values

            $this->db->where('rel_id', $id);
            $this->db->where('rel_type', 'licence');
            $this->db->delete(db_prefix() . 'notes');

            $this->db->where('rel_type', 'licence');
            $this->db->where('rel_id', $id);
            $this->db->delete(db_prefix() . 'views_tracking');

            $this->db->where('rel_type', 'licence');
            $this->db->where('rel_id', $id);
            $this->db->delete(db_prefix() . 'taggables');

            $this->db->where('rel_type', 'licence');
            $this->db->where('rel_id', $id);
            $this->db->delete(db_prefix() . 'reminders');

            $this->db->where('licence_id', $id);
            $this->db->delete(db_prefix() . 'licence_items');

            $this->db->where('rel_id', $id);
            $this->db->where('rel_type', 'licence');
            $this->db->delete(db_prefix() . 'item_tax');

            $this->db->where('rel_id', $id);
            $this->db->where('rel_type', 'licence');
            $this->db->delete(db_prefix() . 'licence_activity');

            // Delete the items values
            $this->db->where('rel_id', $id);
            $this->db->where('rel_type', 'licence');
            $this->db->delete(db_prefix() . 'itemable');


            $attachments = $this->get_attachments($id);
            foreach ($attachments as $attachment) {
                $this->delete_attachment($attachment['id']);
            }

            $this->db->where('rel_id', $id);
            $this->db->where('rel_type', 'licence');
            $this->db->delete('scheduled_emails');

            // Get related tasks
            $this->db->where('rel_type', 'licence');
            $this->db->where('rel_id', $id);
            $tasks = $this->db->get(db_prefix() . 'tasks')->result_array();
            foreach ($tasks as $task) {
                $this->tasks_model->delete_task($task['id']);
            }
            if ($simpleDelete == false) {
                $this->log_licence_activity('Licences Deleted [Number: ' . $number . ']');
            }

            return true;
        }

        return false;
    }

    /**
     * Set licence to sent when email is successfuly sended to client
     * @param mixed $id licenceid
     */
    public function set_licence_sent($id, $emails_sent = [])
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'licences', [
            'sent'     => 1,
            'datesend' => date('Y-m-d H:i:s'),
        ]);

        $this->log_licence_activity($id, 'licence_activity_sent_to_client', false, serialize([
            '<custom_data>' . implode(', ', $emails_sent) . '</custom_data>',
        ]));

        // Update licence status to sent
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'licences', [
            'status' => 2,
        ]);

        $this->db->where('rel_id', $id);
        $this->db->where('rel_type', 'licence');
        $this->db->delete('licenced_emails');
    }

    /**
     * Send expiration reminder to customer
     * @param mixed $id licence id
     * @return boolean
     */
    public function send_expiry_reminder($id)
    {
        $licence        = $this->get($id);
        $licence_number = format_licence_number($licence->id);
        set_mailing_constant();
        $pdf              = licence_pdf($licence);
        $attach           = $pdf->Output($licence_number . '.pdf', 'S');
        $emails_sent      = [];
        $sms_sent         = false;
        $sms_reminder_log = [];

        // For all cases update this to prevent sending multiple reminders eq on fail
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'licences', [
            'is_expiry_notified' => 1,
        ]);

        $contacts = $this->clients_model->get_contacts($licence->clientid, ['active' => 1, 'project_emails' => 1]);

        foreach ($contacts as $contact) {
            $template = mail_template('licence_expiration_reminder', $licence, $contact);

            $merge_fields = $template->get_merge_fields();

            $template->add_attachment([
                'attachment' => $attach,
                'filename'   => str_replace('/', '-', $licence_number . '.pdf'),
                'type'       => 'application/pdf',
            ]);

            if ($template->send()) {
                array_push($emails_sent, $contact['email']);
            }

            if (can_send_sms_based_on_creation_date($licence->datecreated)
                && $this->app_sms->trigger(SMS_TRIGGER_LICENCE_EXP_REMINDER, $contact['phonenumber'], $merge_fields)) {
                $sms_sent = true;
                array_push($sms_reminder_log, $contact['firstname'] . ' (' . $contact['phonenumber'] . ')');
            }
        }

        if (count($emails_sent) > 0 || $sms_sent) {
            if (count($emails_sent) > 0) {
                $this->log_licence_activity($id, 'not_expiry_reminder_sent', false, serialize([
                    '<custom_data>' . implode(', ', $emails_sent) . '</custom_data>',
                ]));
            }

            if ($sms_sent) {
                $this->log_licence_activity($id, 'sms_reminder_sent_to', false, serialize([
                    implode(', ', $sms_reminder_log),
                ]));
            }

            return true;
        }

        return false;
    }

    /**
     * Send licence to client
     * @param mixed $id licenceid
     * @param string $template email template to sent
     * @param boolean $attachpdf attach licence pdf or not
     * @return boolean
     */
    public function send_licence_to_client($id, $template_name = '', $attachpdf = true, $cc = '', $manually = false)
    {
        $licence = $this->get($id);

        if ($template_name == '') {
            $template_name = $licence->sent == 0 ?
                'licence_send_to_customer' :
                'licence_send_to_customer_already_sent';
        }

        $licence_number = format_licence_number($licence->id);

        $emails_sent = [];
        $send_to     = [];

        // Manually is used when sending the licence via add/edit area button Save & Send
        if (!DEFINED('CRON') && $manually === false) {
            $send_to = $this->input->post('sent_to');
        } elseif (isset($GLOBALS['licenced_email_contacts'])) {
            $send_to = $GLOBALS['licenced_email_contacts'];
        } else {
            $contacts = $this->clients_model->get_contacts(
                $licence->clientid,
                ['active' => 1, 'project_emails' => 1]
            );

            foreach ($contacts as $contact) {
                array_push($send_to, $contact['id']);
            }
        }

        $status_auto_updated = false;
        $status_now          = $licence->status;

        if (is_array($send_to) && count($send_to) > 0) {
            $i = 0;

            // Auto update status to sent in case when user sends the licence is with status draft
            if ($status_now == 1) {
                $this->db->where('id', $licence->id);
                $this->db->update(db_prefix() . 'licences', [
                    'status' => 2,
                ]);
                $status_auto_updated = true;
            }

            if ($attachpdf) {
                $_pdf_licence = $this->get($licence->id);
                set_mailing_constant();
                $pdf = licence_pdf($_pdf_licence);

                $attach = $pdf->Output($licence_number . '.pdf', 'S');
            }

            foreach ($send_to as $contact_id) {
                if ($contact_id != '') {
                    // Send cc only for the first contact
                    if (!empty($cc) && $i > 0) {
                        $cc = '';
                    }

                    $contact = $this->clients_model->get_contact($contact_id);

                    if (!$contact) {
                        continue;
                    }

                    $template = mail_template($template_name, $licence, $contact, $cc);

                    if ($attachpdf) {
                        $hook = hooks()->apply_filters('send_licence_to_customer_file_name', [
                            'file_name' => str_replace('/', '-', $licence_number . '.pdf'),
                            'licence'  => $_pdf_licence,
                        ]);

                        $template->add_attachment([
                            'attachment' => $attach,
                            'filename'   => $hook['file_name'],
                            'type'       => 'application/pdf',
                        ]);
                    }

                    if ($template->send()) {
                        array_push($emails_sent, $contact->email);
                    }
                }
                $i++;
            }
        } else {
            return false;
        }

        if (count($emails_sent) > 0) {
            $this->set_licence_sent($id, $emails_sent);
            hooks()->do_action('licence_sent', $id);

            return true;
        }

        if ($status_auto_updated) {
            // Licence not send to customer but the status was previously updated to sent now we need to revert back to draft
            $this->db->where('id', $licence->id);
            $this->db->update(db_prefix() . 'licences', [
                'status' => 1,
            ]);
        }

        return false;
    }

    /**
     * All licence activity
     * @param mixed $id licenceid
     * @return array
     */
    public function get_licence_activity($id)
    {
        $this->db->where('rel_id', $id);
        $this->db->where('rel_type', 'licence');
        $this->db->order_by('date', 'desc');

        return $this->db->get(db_prefix() . 'licence_activity')->result_array();
    }

    /**
     * Log licence activity to database
     * @param mixed $id licenceid
     * @param string $description activity description
     */
    public function log_licence_activity($id, $description = '', $client = false, $additional_data = '')
    {
        $staffid   = get_staff_user_id();
        $full_name = get_staff_full_name(get_staff_user_id());
        if (DEFINED('CRON')) {
            $staffid   = '[CRON]';
            $full_name = '[CRON]';
        } elseif ($client == true) {
            $staffid   = null;
            $full_name = '';
        }

        $this->db->insert(db_prefix() . 'licence_activity', [
            'description'     => $description,
            'date'            => date('Y-m-d H:i:s'),
            'rel_id'          => $id,
            'rel_type'        => 'licence',
            'staffid'         => $staffid,
            'full_name'       => $full_name,
            'additional_data' => $additional_data,
        ]);
    }

    /**
     * Updates pipeline order when drag and drop
     * @param mixe $data $_POST data
     * @return void
     */
    public function update_pipeline($data)
    {
        $this->mark_action_status($data['status'], $data['licenceid']);
        AbstractKanban::updateOrder($data['order'], 'pipeline_order', 'licences', $data['status']);
    }

    /**
     * Get licence unique year for filtering
     * @return array
     */
    public function get_licences_years()
    {
        return $this->db->query('SELECT DISTINCT(YEAR(date)) as year FROM ' . db_prefix() . 'licences ORDER BY year DESC')->result_array();
    }

    private function map_shipping_columns($data)
    {
        if (!isset($data['include_shipping'])) {
            foreach ($this->shipping_fields as $_s_field) {
                if (isset($data[$_s_field])) {
                    $data[$_s_field] = null;
                }
            }
            $data['show_shipping_on_licence'] = 1;
            $data['include_shipping']          = 0;
        } else {
            $data['include_shipping'] = 1;
            // set by default for the next time to be checked
            if (isset($data['show_shipping_on_licence']) && ($data['show_shipping_on_licence'] == 1 || $data['show_shipping_on_licence'] == 'on')) {
                $data['show_shipping_on_licence'] = 1;
            } else {
                $data['show_shipping_on_licence'] = 0;
            }
        }

        return $data;
    }

    public function do_kanban_query($status, $search = '', $page = 1, $sort = [], $count = false)
    {
        _deprecated_function('Licences_model::do_kanban_query', '2.9.2', 'LicencesPipeline class');

        $kanBan = (new LicencesPipeline($status))
            ->search($search)
            ->page($page)
            ->sortBy($sort['sort'] ?? null, $sort['sort_by'] ?? null);

        if ($count) {
            return $kanBan->countAll();
        }

        return $kanBan->get();
    }

/*
    public function get_licence_members($id, $with_name = false)
    {
        if ($with_name) {
            $this->db->select('firstname,lastname,email,licence_id,staff_id');
        } else {
            $this->db->select('email,licence_id,staff_id');
        }
        $this->db->join(db_prefix() . 'staff', db_prefix() . 'staff.staffid=' . db_prefix() . 'licence_members.staff_id');
        $this->db->where('licence_id', $id);

        return $this->db->get(db_prefix() . 'licence_members')->result_array();
    }
*/

    /**
     * Update canban licence status when drag and drop
     * @param  array $data licence data
     * @return boolean
     */
    public function update_licence_status($data)
    {
        $this->db->select('status');
        $this->db->where('id', $data['licenceid']);
        $_old = $this->db->get(db_prefix() . 'licences')->row();

        $old_status = '';

        if ($_old) {
            $old_status = format_licence_status($_old->status);
        }

        $affectedRows   = 0;
        $current_status = format_licence_status($data['status']);


        $this->db->where('id', $data['licenceid']);
        $this->db->update(db_prefix() . 'licences', [
            'status' => $data['status'],
        ]);

        $_log_message = '';

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
            if ($current_status != $old_status && $old_status != '') {
                $_log_message    = 'not_licence_activity_status_updated';
                $additional_data = serialize([
                    get_staff_full_name(),
                    $old_status,
                    $current_status,
                ]);

                hooks()->do_action('licence_status_changed', [
                    'licence_id'    => $data['licenceid'],
                    'old_status' => $old_status,
                    'new_status' => $current_status,
                ]);
            }
            $this->db->where('id', $data['licenceid']);

            $this->db->update(db_prefix() . 'licences', [
                'last_status_change' => date('Y-m-d H:i:s'),
            ]);


        }

        if ($affectedRows > 0) {
            if ($_log_message == '') {
                return true;
            }
            $this->log_licence_activity($data['licenceid'], $_log_message, false, $additional_data);

            return true;
        }

        return false;
    }



    /**
     * Get the licences about to expired in the given days
     *
     * @param  integer|null $staffId
     * @param  integer $days
     *
     * @return array
     */
    public function get_licence_proposed_this_week($staffId = null, $days = 7)
    {
        $diff1 = date('Y-m-d', strtotime('-' . $days . ' days'));
        $diff2 = date('Y-m-d', strtotime('+' . $days . ' days'));

        if ($staffId && ! staff_can('view', 'licences', $staffId)) {
            $this->db->where(db_prefix() . 'licences.addedfrom', $staffId);
        }

        $this->db->select([db_prefix() . 'licences.id', db_prefix() . 'licences.number', db_prefix() . 'clients.userid', db_prefix() . 'clients.company', db_prefix() . 'projects.id AS project_id', db_prefix() . 'projects.name', 'COUNT('. db_prefix() . 'licence_items.task_id) AS count_task', db_prefix() . 'licences.proposed_date']);
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid = ' . db_prefix() . 'licences.clientid', 'left');
        $this->db->join(db_prefix() . 'projects', db_prefix() . 'projects.id = ' . db_prefix() . 'licences.project_id', 'left');
        $this->db->join(db_prefix() . 'licence_items', db_prefix() . 'licences.id = ' . db_prefix() . 'licence_items.licence_id', 'left');

        $this->db->group_by([db_prefix() . 'licences.id',db_prefix() . 'clients.userid', db_prefix() . 'projects.id']);

        $this->db->where('proposed_date IS NOT NULL');
        $this->db->where('proposed_date >=', $diff1);
        $this->db->where('proposed_date <=', $diff2);

        return $this->db->get(db_prefix() . 'licences')->result_array();
    }

    /**
     * Get the licences for the client given
     *
     * @param  integer|null $staffId
     * @param  integer $days
     *
     * @return array
     */
    public function get_client_licences($client = null)
    {
        /*
        if ($staffId && ! staff_can('view', 'licences', $staffId)) {
            $this->db->where('addedfrom', $staffId);
        }
        */

        $this->db->select(db_prefix() . 'licences.id,' . db_prefix() . 'licences.number,' . db_prefix() . 'licences.status,' . db_prefix() . 'clients.userid,' . db_prefix() . 'licences.hash,' . db_prefix() . 'projects.name,' . db_prefix() . 'licences.proposed_date');
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid = ' . db_prefix() . 'licences.clientid', 'left');
        $this->db->join(db_prefix() . 'projects', db_prefix() . 'projects.id = ' . db_prefix() . 'licences.project_id', 'left');
        $this->db->where('proposed_date IS NOT NULL');
        $this->db->where(db_prefix() . 'licences.status > ',1);
        $this->db->where(db_prefix() . 'licences.clientid =', $client->userid);

        return $this->db->get(db_prefix() . 'licences')->result_array();
    }


    /**
     * Get the licences about to expired in the given days
     *
     * @param  integer|null $staffId
     * @param  integer $days
     *
     * @return array
     */
    public function get_licences_between($staffId = null, $days = 7)
    {
        $diff1 = date('Y-m-d', strtotime('-' . $days . ' days'));
        $diff2 = date('Y-m-d', strtotime('+' . $days . ' days'));

        if ($staffId && ! staff_can('view', 'licences', $staffId)) {
            $this->db->where('addedfrom', $staffId);
        }

        $this->db->select(db_prefix() . 'licences.id,' . db_prefix() . 'licences.number,' . db_prefix() . 'clients.userid,' . db_prefix() . 'clients.company,' . db_prefix() . 'projects.name,' . db_prefix() . 'licences.proposed_date');
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid = ' . db_prefix() . 'licences.clientid', 'left');
        $this->db->join(db_prefix() . 'projects', db_prefix() . 'projects.id = ' . db_prefix() . 'licences.project_id', 'left');
        $this->db->where('expirydate IS NOT NULL');
        $this->db->where('expirydate >=', $diff1);
        $this->db->where('expirydate <=', $diff2);

        //return $this->db->get_compiled_select(db_prefix() . 'licences');
        //return $this->db->get(db_prefix() . 'licences')->get_compiled_select();
        return $this->db->get(db_prefix() . 'licences')->result_array();
    }



    public function get_related_tasks($licence_id, $project_id, $proposed = false, $released = false){
        $this->db->select([db_prefix() . 'tasks.id',db_prefix() . 'tasks.name', db_prefix() . 'tasks.rel_id', db_prefix() . 'licence_items.licence_upt_number', db_prefix() . 'tasks.dateadded']);
        $this->db->where(db_prefix() . 'tasks.rel_id =' . $project_id);

        $this->db->join(db_prefix() . 'licence_items', db_prefix() . 'licence_items.task_id = ' . db_prefix() . 'tasks.id', 'left');

        $this->db->where(db_prefix() . 'tasks.rel_type = ' . "'project'");
        $this->db->where(db_prefix() . 'licence_items.licence_id = ' . $licence_id);

        if($proposed){
            $this->db->where(db_prefix() . 'licence_items.task_id IS NOT NULL');
        }

        if($released){
            $this->db->where(db_prefix() . 'licence_items.licence_upt_number IS NOT NULL');
        }

        //return $this->db->get_compiled_select(db_prefix() . 'tasks');
        return $this->db->get(db_prefix() . 'tasks')->result_array();

    }

    public function licence_add_proposed_item($data){

        $this->db->insert(db_prefix() . 'licence_items', [
                'licence_id'      => $data['licence_id'],
                'project_id'      => $data['project_id'],
                'task_id'         => $data['task_id'],
                'equipment_name'  => $data['equipment_name'],
            ]
            );

        hooks()->do_action('after_licence_item_added', $data);
    }



    public function licence_remove_proposed_item($data)
    {

        $affectedRows   = 0;

        //$this->db->where('licence_id', $data['licence_id']);
        //$this->db->where('task_id', $data['task_id']);
        //$id = $this->db->get(db_prefix() . 'licence_items')->row();
        //if(isset($id)){
            $this->db->delete(db_prefix() . 'licence_items', [
                'licence_id' => $data['licence_id'],
                'task_id' => $data['task_id'],
            ]);
        //}

        $_log_message = '';

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
                $_log_message    = 'not_licence_remove_proposed_item';
                $additional_data = serialize([
                    get_staff_full_name(),
                    $data['licence_id'],
                    $data['task_id'],
                ]);

                hooks()->do_action('licence_remove_proposed_item', [
                    'licence_id' => $data['licence_id'],
                    'task_id' => $data['task_id'],
                ]);

        }

        if ($affectedRows > 0) {
            if ($_log_message == '') {
                return true;
            }
            $this->log_licence_activity($data['licence_id'], $_log_message, false, $additional_data);

            return true;
        }

        return false;
    }


    public function licence_add_released_item($data){

        $this->db->where(array('licence_id'=> $data['licence_id'], 'project_id' => $data['project_id'],'task_id' => $data['task_id']));
        $this->db->update(db_prefix() . 'licence_items', [
                'released'              => $data['released']]);
    }

    public function licence_remove_released_item($data){

        $this->db->where(array('licence_id'=> $data['licence_id'], 'task_id' => $data['task_id']));
        $this->db->update(db_prefix() . 'licence_items', [
                'released'              => NULL]);
    }


    public function update_licence_data($licence_id, $project_id, $tasks_data){

        foreach($tasks_data as $key => $task){
            if($task !== ''){

                $search = 'task_id_' ;
                $task_id = str_replace($search, '', $key) ;

                $this->db->where('licence_id', $licence_id);
                $this->db->where('project_id', $project_id);
                $this->db->where('task_id', $task_id);
                $this->db->update(db_prefix() . 'licence_items',
                            [
                                'licence_upt_number'=> $task,
                            ]
                );
            }
        }
    }

    public function get_available_tasks($licence_id, $project_id){

        $this->db->select([db_prefix() . 'projects.id AS project_id', db_prefix() . 'tasks.id AS task_id']);

        $this->db->join(db_prefix() . 'projects', db_prefix() . 'tasks.rel_id = ' . db_prefix() . 'projects.id', 'left');
        $this->db->join(db_prefix() . 'licence_items', db_prefix() . 'tasks.id = ' . db_prefix() . 'licence_items.task_id', 'left');
        $this->db->join(db_prefix() . 'inspection_items', db_prefix() . 'tasks.id = ' . db_prefix() . 'inspection_items.task_id', 'left');

        $this->db->where(db_prefix() . 'tasks.rel_id =' . $project_id);
        $this->db->where(db_prefix() . 'tasks.rel_type = ' . "'project'");
        $this->db->where(db_prefix() . 'inspection_items.equipment_name IS NOT NULL');

        //$this->db->where(db_prefix() . 'licence_items.task_id IS NULL');

        //return $this->db->get_compiled_select(db_prefix() . 'tasks');
        return $this->db->get(db_prefix() . 'tasks')->result_array();
    }


  public function get_licence_proposed_items($licence_id, $project_id){

        $this->db->select([db_prefix() . 'tasks.id AS task_id', db_prefix() . 'tasks.name AS task_name']);
        $this->db->select([db_prefix() . 'licence_items.licence_id', db_prefix() . 'projects.id AS project_id', db_prefix() . 'tags.name AS tags_name', 'COUNT('.db_prefix() . 'tasks.id) AS count']);

        $this->db->join(db_prefix() . 'tasks', db_prefix() . 'tasks.id = ' . db_prefix() . 'licence_items.task_id', 'left');
        $this->db->join(db_prefix() . 'projects', db_prefix() . 'tasks.rel_id = ' . db_prefix() . 'projects.id', 'left');
        $this->db->join(db_prefix() . 'taggables', db_prefix() . 'taggables.rel_id = ' . db_prefix() . 'tasks.id', 'left');
        $this->db->join(db_prefix() . 'tags', db_prefix() . 'taggables.tag_id = ' . db_prefix() . 'tags.id', 'left');

        $this->db->group_by(db_prefix() . 'licence_items.licence_id');
        $this->db->group_by(db_prefix() . 'licence_items.project_id');
        $this->db->group_by(db_prefix() . 'licence_items.task_id');
        $this->db->group_by(db_prefix() . 'tasks.name');
        $this->db->group_by(db_prefix() . 'tags.name');

        $this->db->where(db_prefix() . 'tasks.rel_id =' . $project_id);
        $this->db->where(db_prefix() . 'tasks.rel_type = ' . "'project'");
        $this->db->where(db_prefix() . 'licence_items.licence_id=' . $licence_id);

        //return $this->db->get_compiled_select(db_prefix() . 'licence_items');
        return $this->db->get(db_prefix() . 'licence_items')->result_array();
    }


  public function get_licence_proposed_taggable_items($licence_id, $project_id){

        $this->db->select([db_prefix() . 'tags.name AS tags_name', 'COUNT('.db_prefix() . 'tags.name) AS count']);

        $this->db->join(db_prefix() . 'tasks', db_prefix() . 'tasks.id = ' . db_prefix() . 'licence_items.task_id', 'left');
        $this->db->join(db_prefix() . 'projects', db_prefix() . 'tasks.rel_id = ' . db_prefix() . 'projects.id', 'left');
        $this->db->join(db_prefix() . 'taggables', db_prefix() . 'taggables.rel_id = ' . db_prefix() . 'tasks.id', 'left');
        $this->db->join(db_prefix() . 'tags', db_prefix() . 'taggables.tag_id = ' . db_prefix() . 'tags.id', 'left');

        $this->db->group_by(db_prefix() . 'tags.name');

        $this->db->where(db_prefix() . 'tasks.rel_id =' . $project_id);
        $this->db->where(db_prefix() . 'tasks.rel_type = ' . "'project'");
        $this->db->where(db_prefix() . 'licence_items.licence_id=' . $licence_id);

        //return $this->db->get_compiled_select(db_prefix() . 'licence_items');
        return $this->db->get(db_prefix() . 'licence_items')->result_array();
    }


    /**
     * Get the licences about to expired in the given days
     *
     * @param  integer|null $staffId
     * @param  integer $days
     *
     * @return array
     */
    public function get_project_not_licenced($staffId = null)
    {
        $days = get_option('licence_number_of_date');
        $diff1 = date('Y-m-d', strtotime('-' . $days . ' days'));
        $diff2 = date('Y-m-d', strtotime('+' . $days . ' days'));
        $start = date(get_option('licence_start_date'));

        if ($staffId && ! staff_can('view', 'licences', $staffId)) {
            $this->db->where(db_prefix() . 'licences.addedfrom', $staffId);
        }

        $this->db->select(db_prefix() . 'licences.id,' . db_prefix() . 'licences.formatted_number,' . db_prefix() . 'clients.userid,' . db_prefix() . 'clients.company,' . db_prefix() . 'projects.id,' . db_prefix() . 'projects.name,' . db_prefix() . 'projects.start_date');
        $this->db->join(db_prefix() . 'projects', db_prefix() . 'projects.id = ' . db_prefix() . 'licences.project_id', 'right');
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid = ' . db_prefix() . 'projects.clientid', 'left');

        $this->db->where('project_id IS NULL');
        if($days !='0'){
            $this->db->where(db_prefix() . 'projects.start_date >=', $diff1);
            $this->db->where(db_prefix() . 'projects.start_date <=', $diff2);
        }

        $this->db->where(db_prefix() . 'projects.start_date >=', $start);

        //return $this->db->get_compiled_select(db_prefix() . 'licences');

        return $this->db->get(db_prefix() . 'licences')->result_array();
    }


    public function get_available_tags($task_id=NULL){

        $this->db->select([db_prefix() . 'tags.id AS tag_id', db_prefix() . 'tags.name AS tag_name']);
        $this->db->select(['COUNT('.db_prefix() . 'tasks.id) AS count_task']);

        $this->db->join(db_prefix() . 'taggables', db_prefix() . 'taggables.rel_id = ' . db_prefix() . 'tasks.id', 'left');
        $this->db->join(db_prefix() . 'tags', db_prefix() . 'taggables.tag_id = ' . db_prefix() . 'tags.id', 'left');
        $this->db->group_by(db_prefix() . 'tags.id');
        $this->db->where(db_prefix() . 'tasks.rel_type = ' . "'project'");
        if(is_numeric($task_id)){
            $this->db->where(db_prefix() . 'tasks.id = ' . $task_id);
        }
        $this->db->where(db_prefix() . 'tags.id is NOT NULL', NULL, true);

        //return $this->db->get_compiled_select(db_prefix() . 'tasks');
        return $this->db->get(db_prefix() . 'tasks')->result_array();
    }

    public function get_inspection_id($id, $task_id){
        $this->db->select([db_prefix() . 'inspections.id']);
        $this->db->join(db_prefix() . 'licence_items', db_prefix() . 'inspection_items.task_id = ' . db_prefix() . 'licence_items.task_id');
        $this->db->join(db_prefix() . 'inspections', db_prefix() . 'inspection_items.inspection_id = ' . db_prefix() . 'inspections.id');
        //$this->db->where(db_prefix() . 'licence_items.licence_id = ' . $id);
        $this->db->where(db_prefix() . 'licence_items.task_id = ' . $task_id);
        //return $this->db->get_compiled_select(db_prefix() . 'inspection_items');
        return $this->db->get(db_prefix() . 'inspection_items')->row();
    }
    /*
    public function get_office_id($id){
        $this->db->select([db_prefix() . 'offices.id']);
        $this->db->join(db_prefix() . 'schedules', db_prefix() . 'licences.project_id = ' . db_prefix() . 'schedules.project_id');
        $this->db->join(db_prefix() . 'offices', db_prefix() . 'offices.id = ' . db_prefix() . 'schedules.office_id');
        $this->db->where(db_prefix() . 'licences.id = ' . $id);
        //return $this->db->get_compiled_select(db_prefix() . 'licences');
        return $this->db->get(db_prefix() . 'licences')->row();
    }
    */
    public function update_licence_item_data($data, $licence_id, $task_id){
        $field = $data['field'];
        unset($data['field']);
        //$data_text = htmlspecialchars($data['text'], ENT_QUOTES);
        $data_text = strip_tags($data['text'], '<div><p><br>');
        //$data_text = $data['text'];
        unset($data['text']);
        $data[$field] = $data_text;

        $this->db->select('id');
        $this->db->where('licence_id', $licence_id);
        $this->db->where('task_id', $task_id);
        $this->db->update(db_prefix() . 'licence_items', $data);
    }

  public function get_licence_item_data($licence_id, $task_id = ''){
        $this->db->select('*');

        $this->db->where(db_prefix() . 'licence_items.licence_id =' . $licence_id);
        if(isset($task_id)){
            $this->db->where(db_prefix() . 'licence_items.task_id=' . $task_id);
        }
        //return $this->db->get_compiled_select(db_prefix() . 'licence_items');
        return $this->db->get(db_prefix() . 'licence_items')->result_array();
    }

    /**
     * Get item by id
     * @param mixed $id item id
     * @return object
     */
    public function get_licence_items($id, $task_id='')
    {
        $this->db->where('licence_id', $id);
        if(isset($task_id)){
            $this->db->where('task_id', $task_id);
        }
        $licence_items = $this->db->get(db_prefix() . 'licence_items')->result();
        return $licence_items;
    }
}
