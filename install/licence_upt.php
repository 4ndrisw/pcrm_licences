<?php defined('BASEPATH') or exit('No direct script access allowed');

if (!$CI->db->table_exists(db_prefix() . 'licence_upt')) {

    $CI->db->query('CREATE TABLE ' . db_prefix() . "licence_upt (
      id int(11) NOT NULL,
      short_name varchar(30) NOT NULL,
      full_name varchar(60) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');

    $CI->db->query('ALTER TABLE ' . db_prefix() . 'licence_upt
      ADD PRIMARY KEY (id),
      ADD KEY full_name (full_name),
      ADD KEY short_name (short_name);
    ');

    $CI->db->query('ALTER TABLE ' . db_prefix() . 'licence_upt
      MODIFY id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1');

}

$CI->db->query('
INSERT INTO  '. db_prefix() .'licence_upt (`short_name`, `full_name`) VALUES 
("seragon", "UPT Serang Cilegon"),
("sepale", "UPT Serang Pandeglang Lebak");
');

