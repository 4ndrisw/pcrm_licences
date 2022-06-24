<?php defined('BASEPATH') or exit('No direct script access allowed');


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
