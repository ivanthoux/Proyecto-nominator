ALTER TABLE `client_packs` ADD `clientpack_import` INT NULL AFTER `clientpack_obs`;
ALTER TABLE `client_packs` ADD FOREIGN KEY (`clientpack_import`) REFERENCES `imports`(`import_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `payments` ADD `pay_import` INT NULL AFTER `pay_description`;
ALTER TABLE `payments` ADD FOREIGN KEY (`pay_import`) REFERENCES `imports`(`import_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;