CREATE TABLE `mercado_pago` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `payment_id` bigint(20) NOT NULL,
  `date_created` DATETIME NOT NULL,
  `status` varchar(50) NOT NULL,
  `status_detail` varchar(100) NOT NULL,
  `transaction_amount` double NOT NULL,
  `payment_method_id` varchar(50) NOT NULL,
  `payment_type_id` varchar(50) NOT NULL,
  `json` text NOT NULL
) ENGINE=InnoDB ;

ALTER TABLE `mercado_pago`  ADD PRIMARY KEY (`id`),  ADD KEY `client_id` (`client_id`);

ALTER TABLE `mercado_pago`   MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `mercado_pago`   ADD CONSTRAINT `mercado_pago_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`);