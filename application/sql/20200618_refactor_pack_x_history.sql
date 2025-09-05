ALTER TABLE `packs` ADD `pack_price` DECIMAL(9,2) NOT NULL AFTER `pack_phone_ref_validate`, 
ADD `pack_commision` DECIMAL(9,5) NOT NULL AFTER `pack_price`, 
ADD `pack_commision_2` DECIMAL(9,5) NOT NULL AFTER `pack_commision`, 
ADD `pack_expenses` DECIMAL(9,2) NOT NULL AFTER `pack_commision_2`, 
ADD `pack_daytask` DECIMAL(9,5) NOT NULL AFTER `pack_expenses`, 
ADD `pack_type` INT NOT NULL AFTER `pack_daytask`, 
ADD `pack_session_min` INT NOT NULL AFTER `pack_type`, 
ADD `pack_session_max` INT NOT NULL AFTER `pack_session_min`;

DROP TABLE packs_history;

ALTER TABLE `payments` ADD `pay_client` INT NULL AFTER `pay_clientperiod`;
ALTER TABLE `payments` ADD FOREIGN KEY (`pay_client`) REFERENCES `clients`(`client_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `client_packs` ADD `clientpack_sessions_2_price` DOUBLE NOT NULL AFTER `clientpack_sessions_price`;

ALTER TABLE `client_periods` ADD `clientperiod_date_2` DATETIME NOT NULL AFTER `clientperiod_date`;

ALTER TABLE `client_periods` ADD `clientperiod_amountinterest_2` DOUBLE NOT NULL AFTER `clientperiod_amountinterestfull`, 
ADD `clientperiod_amountinterestfull_2` DOUBLE NOT NULL AFTER `clientperiod_amountinterest_2`;