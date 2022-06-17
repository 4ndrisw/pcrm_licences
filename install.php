<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!$CI->db->table_exists(db_prefix() . 'licences')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "licences` (
      `id` int(11) NOT NULL,
      `staff_id` int(11) NOT NULL DEFAULT 0,
      `sent` tinyint(1) NOT NULL DEFAULT 0,
      `datesend` datetime DEFAULT NULL,
      `clientid` int(11) NOT NULL DEFAULT 0,
      `deleted_customer_name` varchar(100) DEFAULT NULL,
      `project_id` int(11) NOT NULL DEFAULT 0,
      `number` int(11) NOT NULL DEFAULT 0,
      `prefix` varchar(50) DEFAULT NULL,
      `number_format` int(11) NOT NULL DEFAULT 0,
      `formatted_number` varchar(20) DEFAULT NULL,
      `hash` varchar(32) DEFAULT NULL,
      `datecreated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `date` date DEFAULT NULL,
      `addedfrom` int(11) NOT NULL DEFAULT 0,
      `status` int(11) NOT NULL DEFAULT 1,
      `clientnote` text DEFAULT NULL,
      `adminnote` text DEFAULT NULL,
      `invoiceid` int(11) DEFAULT NULL,
      `invoiced_date` datetime DEFAULT NULL,
      `terms` text DEFAULT NULL,
      `reference_no` varchar(100) DEFAULT NULL,
      `assigned` int(11) NOT NULL DEFAULT 0,
      `billing_street` varchar(200) DEFAULT NULL,
      `billing_city` varchar(100) DEFAULT NULL,
      `billing_state` varchar(100) DEFAULT NULL,
      `billing_zip` varchar(100) DEFAULT NULL,
      `billing_country` int(11) DEFAULT NULL,
      `shipping_street` varchar(200) DEFAULT NULL,
      `shipping_city` varchar(100) DEFAULT NULL,
      `shipping_state` varchar(100) DEFAULT NULL,
      `shipping_zip` varchar(100) DEFAULT NULL,
      `shipping_country` int(11) DEFAULT NULL,
      `include_shipping` tinyint(1) NOT NULL DEFAULT 0,
      `show_shipping_on_licence` tinyint(1) NOT NULL DEFAULT 1,
      `show_quantity_as` int(11) NOT NULL DEFAULT 1,
      `pipeline_order` int(11) DEFAULT 1,
      `is_expiry_notified` int(11) NOT NULL DEFAULT 0,
      `signed` tinyint(1) NOT NULL DEFAULT 0,
      `acceptance_firstname` varchar(50) DEFAULT NULL,
      `acceptance_lastname` varchar(50) DEFAULT NULL,
      `acceptance_email` varchar(100) DEFAULT NULL,
      `acceptance_date` datetime DEFAULT NULL,
      `acceptance_ip` varchar(40) DEFAULT NULL,
      `signature` varchar(40) DEFAULT NULL,
      `short_link` varchar(100) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');

    $CI->db->query('ALTER TABLE `' . db_prefix() . 'licences`
      ADD PRIMARY KEY (`id`),
      ADD UNIQUE( `number`),
      ADD KEY `signed` (`signed`),
      ADD KEY `status` (`status`),
      ADD KEY `clientid` (`clientid`),
      ADD KEY `project_id` (`project_id`),
      ADD KEY `date` (`date`);');

    $CI->db->query('ALTER TABLE `' . db_prefix() . 'licences`
      MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1');
}


if (!$CI->db->table_exists(db_prefix() . 'licence_members')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "licence_members` (
      `id` int(11) NOT NULL,
      `licence_id` int(11) NOT NULL DEFAULT 0,
      `staff_id` int(11) NOT NULL DEFAULT 0
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');

    $CI->db->query('ALTER TABLE `' . db_prefix() . 'licence_members`
      ADD PRIMARY KEY (`id`),
      ADD KEY `staff_id` (`staff_id`),
      ADD KEY `licence_id` (`licence_id`) USING BTREE;');

    $CI->db->query('ALTER TABLE `' . db_prefix() . 'licence_members`
      MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1');
}

if (!$CI->db->table_exists(db_prefix() . 'licence_activity')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "licence_activity` (
  `id` int(11) NOT NULL,
  `rel_type` varchar(20) DEFAULT NULL,
  `rel_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `additional_data` text DEFAULT NULL,
  `staffid` varchar(11) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `date` datetime NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');

    $CI->db->query('ALTER TABLE `' . db_prefix() . 'licence_activity`
        ADD PRIMARY KEY (`id`),
        ADD KEY `date` (`date`);');

    $CI->db->query('ALTER TABLE `' . db_prefix() . 'licence_activity`
      MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1');
}

if (!$CI->db->table_exists(db_prefix() . 'licence_items')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "licence_items` (
      `id` int(11) NOT NULL,
      `rel_id` int(11) NOT NULL,
      `rel_type` varchar(15) NOT NULL,
      `description` mediumtext NOT NULL,
      `long_description` mediumtext DEFAULT NULL,
      `qty` decimal(15,2) NOT NULL,
      `unit` varchar(40) DEFAULT NULL,
      `item_order` int(11) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');

    $CI->db->query('ALTER TABLE `' . db_prefix() . 'licence_items`
      ADD PRIMARY KEY (`id`),
      ADD KEY `rel_id` (`rel_id`);');

    $CI->db->query('ALTER TABLE `' . db_prefix() . 'licence_items`
      MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1');
}

$CI->db->query("
INSERT INTO `tblemailtemplates` (`type`, `slug`, `language`, `name`, `subject`, `message`, `fromname`, `fromemail`, `plaintext`, `active`, `order`) VALUES
('licence', 'licence-send-to-client', 'english', 'Send licence to Customer', 'licence # {licence_number} created', '<span style=\"font-size: 12pt;\">Dear {contact_firstname} {contact_lastname}</span><br /><br /><span style=\"font-size: 12pt;\">Please find the attached licence <strong># {licence_number}</strong></span><br /><br /><span style=\"font-size: 12pt;\"><strong>licence status:</strong> {licence_status}</span><br /><br /><span style=\"font-size: 12pt;\">You can view the licence on the following link: <a href=\"{licence_link}\">{licence_number}</a></span><br /><br /><span style=\"font-size: 12pt;\">We look forward to your communication.</span><br /><br /><span style=\"font-size: 12pt;\">Kind Regards,</span><br /><span style=\"font-size: 12pt;\">{email_signature}<br /></span>', '{companyname} | CRM', '', 0, 1, 0),
('licence', 'licence-already-send', 'english', 'licence Already Sent to Customer', 'licence # {licence_number} ', '<span style=\"font-size: 12pt;\">Dear {contact_firstname} {contact_lastname}</span><br /> <br /><span style=\"font-size: 12pt;\">Thank you for your licence request.</span><br /> <br /><span style=\"font-size: 12pt;\">You can view the licence on the following link: <a href=\"{licence_link}\">{licence_number}</a></span><br /> <br /><span style=\"font-size: 12pt;\">Please contact us for more information.</span><br /> <br /><span style=\"font-size: 12pt;\">Kind Regards,</span><br /><span style=\"font-size: 12pt;\">{email_signature}</span>', '{companyname} | CRM', '', 0, 1, 0),
('licence', 'licence-declined-to-staff', 'english', 'licence Declined (Sent to Staff)', 'Customer Declined licence', '<span style=\"font-size: 12pt;\">Hi</span><br /> <br /><span style=\"font-size: 12pt;\">Customer ({client_company}) declined licence with number <strong># {licence_number}</strong></span><br /> <br /><span style=\"font-size: 12pt;\">You can view the licence on the following link: <a href=\"{licence_link}\">{licence_number}</a></span><br /> <br /><span style=\"font-size: 12pt;\">{email_signature}</span>', '{companyname} | CRM', '', 0, 1, 0),
('licence', 'licence-accepted-to-staff', 'english', 'licence Accepted (Sent to Staff)', 'Customer Accepted licence', '<span style=\"font-size: 12pt;\">Hi</span><br /> <br /><span style=\"font-size: 12pt;\">Customer ({client_company}) accepted licence with number <strong># {licence_number}</strong></span><br /> <br /><span style=\"font-size: 12pt;\">You can view the licence on the following link: <a href=\"{licence_link}\">{licence_number}</a></span><br /> <br /><span style=\"font-size: 12pt;\">{email_signature}</span>', '{companyname} | CRM', '', 0, 1, 0),
('licence', 'licence-thank-you-to-customer', 'english', 'Thank You Email (Sent to Customer After Accept)', 'Thank for you accepting licence', '<span style=\"font-size: 12pt;\">Dear {contact_firstname} {contact_lastname}</span><br /> <br /><span style=\"font-size: 12pt;\">Thank for for accepting the licence.</span><br /> <br /><span style=\"font-size: 12pt;\">We look forward to doing business with you.</span><br /> <br /><span style=\"font-size: 12pt;\">We will contact you as soon as possible.</span><br /> <br /><span style=\"font-size: 12pt;\">Kind Regards,</span><br /><span style=\"font-size: 12pt;\">{email_signature}</span>', '{companyname} | CRM', '', 0, 1, 0),
('licence', 'licence-expiry-reminder', 'english', 'licence Expiration Reminder', 'licence Expiration Reminder', '<p><span style=\"font-size: 12pt;\">Hello {contact_firstname} {contact_lastname}</span><br /><br /><span style=\"font-size: 12pt;\">The licence with <strong># {licence_number}</strong> will expire on <strong>{licence_expirydate}</strong></span><br /><br /><span style=\"font-size: 12pt;\">You can view the licence on the following link: <a href=\"{licence_link}\">{licence_number}</a></span><br /><br /><span style=\"font-size: 12pt;\">Kind Regards,</span><br /><span style=\"font-size: 12pt;\">{email_signature}</span></p>', '{companyname} | CRM', '', 0, 1, 0),
('licence', 'licence-send-to-client', 'english', 'Send licence to Customer', 'licence # {licence_number} created', '<span style=\"font-size: 12pt;\">Dear {contact_firstname} {contact_lastname}</span><br /><br /><span style=\"font-size: 12pt;\">Please find the attached licence <strong># {licence_number}</strong></span><br /><br /><span style=\"font-size: 12pt;\"><strong>licence status:</strong> {licence_status}</span><br /><br /><span style=\"font-size: 12pt;\">You can view the licence on the following link: <a href=\"{licence_link}\">{licence_number}</a></span><br /><br /><span style=\"font-size: 12pt;\">We look forward to your communication.</span><br /><br /><span style=\"font-size: 12pt;\">Kind Regards,</span><br /><span style=\"font-size: 12pt;\">{email_signature}<br /></span>', '{companyname} | CRM', '', 0, 1, 0),
('licence', 'licence-already-send', 'english', 'licence Already Sent to Customer', 'licence # {licence_number} ', '<span style=\"font-size: 12pt;\">Dear {contact_firstname} {contact_lastname}</span><br /> <br /><span style=\"font-size: 12pt;\">Thank you for your licence request.</span><br /> <br /><span style=\"font-size: 12pt;\">You can view the licence on the following link: <a href=\"{licence_link}\">{licence_number}</a></span><br /> <br /><span style=\"font-size: 12pt;\">Please contact us for more information.</span><br /> <br /><span style=\"font-size: 12pt;\">Kind Regards,</span><br /><span style=\"font-size: 12pt;\">{email_signature}</span>', '{companyname} | CRM', '', 0, 1, 0),
('licence', 'licence-declined-to-staff', 'english', 'licence Declined (Sent to Staff)', 'Customer Declined licence', '<span style=\"font-size: 12pt;\">Hi</span><br /> <br /><span style=\"font-size: 12pt;\">Customer ({client_company}) declined licence with number <strong># {licence_number}</strong></span><br /> <br /><span style=\"font-size: 12pt;\">You can view the licence on the following link: <a href=\"{licence_link}\">{licence_number}</a></span><br /> <br /><span style=\"font-size: 12pt;\">{email_signature}</span>', '{companyname} | CRM', '', 0, 1, 0),
('licence', 'licence-accepted-to-staff', 'english', 'licence Accepted (Sent to Staff)', 'Customer Accepted licence', '<span style=\"font-size: 12pt;\">Hi</span><br /> <br /><span style=\"font-size: 12pt;\">Customer ({client_company}) accepted licence with number <strong># {licence_number}</strong></span><br /> <br /><span style=\"font-size: 12pt;\">You can view the licence on the following link: <a href=\"{licence_link}\">{licence_number}</a></span><br /> <br /><span style=\"font-size: 12pt;\">{email_signature}</span>', '{companyname} | CRM', '', 0, 1, 0),
('licence', 'staff-added-as-project-member', 'english', 'Staff Added as Project Member', 'New project assigned to you', '<p>Hi <br /><br />New licence has been assigned to you.<br /><br />You can view the licence on the following link <a href=\"{licence_link}\">licence__number</a><br /><br />{email_signature}</p>', '{companyname} | CRM', '', 0, 1, 0),
('licence', 'licence-accepted-to-staff', 'english', 'licence Accepted (Sent to Staff)', 'Customer Accepted licence', '<span style=\"font-size: 12pt;\">Hi</span><br /> <br /><span style=\"font-size: 12pt;\">Customer ({client_company}) accepted licence with number <strong># {licence_number}</strong></span><br /> <br /><span style=\"font-size: 12pt;\">You can view the licence on the following link: <a href=\"{licence_link}\">{licence_number}</a></span><br /> <br /><span style=\"font-size: 12pt;\">{email_signature}</span>', '{companyname} | CRM', '', 0, 1, 0);
");
/*
 *
 */

// Add options for licences
add_option('delete_only_on_last_licence', 1);
add_option('licence_prefix', 'BAPP-');
add_option('next_licence_number', 1);
add_option('default_licence_assigned', 9);
add_option('licence_number_decrement_on_delete', 0);
add_option('licence_number_format', 4);
add_option('licence_year', date('Y'));
add_option('exclude_licence_from_client_area_with_draft_status', 1);
add_option('predefined_clientnote_licence', '');
add_option('predefined_terms_licence', '- Dokumen ini diterbitkan melalui Aplikasi CRM, tidak memerlukan tanda tangan basah dari PT. Cipta Mas Jaya.');
add_option('licence_due_after', 1);
add_option('allow_staff_view_licences_assigned', 1);
add_option('show_assigned_on_licences', 1);
add_option('require_client_logged_in_to_view_licence', 0);

add_option('show_project_on_licence', 1);
add_option('licences_pipeline_limit', 1);
add_option('default_licences_pipeline_sort', 1);
add_option('licence_accept_identity_confirmation', 1);
add_option('licence_qrcode_size', '160');


/*

DROP TABLE `tbllicences`;
DROP TABLE `tbllicence_activity`, `tbllicence_items`, `tbllicence_members`;
delete FROM `tbloptions` WHERE `name` LIKE '%licence%';
DELETE FROM `tblemailtemplates` WHERE `type` LIKE 'licence';



*/