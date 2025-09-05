ALTER TABLE `clients` ADD `client_balance` DOUBLE NOT NULL AFTER `client_ref3_phone`;

ALTER TABLE `payments` ADD `pay_interest_2` DOUBLE NOT NULL AFTER `pay_interest`;