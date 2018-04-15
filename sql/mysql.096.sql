ALTER TABLE `art_category`
  ADD `cat_entry` INT(11) UNSIGNED NOT NULL DEFAULT '0'
  AFTER `cat_template`;
