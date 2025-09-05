ALTER TABLE `payments` ADD `pay_canvas` VARCHAR(300) NULL AFTER `pay_description`, 
  ADD `pay_lat` VARCHAR(20) NULL AFTER `pay_canvas`, 
  ADD `pay_lng` VARCHAR(20) NULL AFTER `pay_lat`;