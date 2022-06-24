<?php defined('BASEPATH') or exit('No direct script access allowed');


if (!$CI->db->table_exists(db_prefix() . 'licences_related_tasks')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "licences_related_tasks` (
  `id` int(11) NOT NULL,
  `licence_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `licence_upt_number` varchar(40) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');

    $CI->db->query('ALTER TABLE `' . db_prefix() . 'licences_related_tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `licence_id` (`licence_id`);');

    $CI->db->query('ALTER TABLE `' . db_prefix() . 'licences_related_tasks`
      MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1');
}
