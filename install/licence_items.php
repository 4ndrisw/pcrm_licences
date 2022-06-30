<?php defined('BASEPATH') or exit('No direct script access allowed');


if (!$CI->db->table_exists(db_prefix() . 'licence_items')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "licence_items` (
  `id` int(11) NOT NULL,
  `licence_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `equipment_name` varchar(60) DEFAULT NULL,
  `licence_upt_number` varchar(40) DEFAULT NULL,
  `released` tinyint(1) DEFAULT NULL,
  `flag` tinyint(1) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');

    $CI->db->query('ALTER TABLE `' . db_prefix() . 'licence_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `licence_id_task_id` (`licence_id`,`task_id`) USING BTREE,
  ADD KEY `project_id` (`project_id`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `licence_id` (`licence_id`),
  ADD KEY `released` (`released`);
  ');

    $CI->db->query('ALTER TABLE `' . db_prefix() . 'licence_items`
      MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1');
}
