--18/03/2025--

DROP TABLE Driver;

CREATE TABLE `Driver` (
  `id` int(11) NOT NULL,
  `driver_code` varchar(50) NOT NULL,
  `driver_name` varchar(255) NOT NULL,
  `driver_ic` varchar(255) DEFAULT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  `modified_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `modified_by` varchar(50) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `Driver` ADD PRIMARY KEY (`id`);

ALTER TABLE `Driver` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

DROP TABLE Driver_Log;

CREATE TABLE `Driver_Log` (
  `id` int(11) NOT NULL,
  `driver_id` varchar(100) NOT NULL,
  `driver_code` varchar(50) NOT NULL,
  `driver_name` varchar(255) NOT NULL,
  `driver_ic` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `action_id` int(11) NOT NULL,
  `action_by` varchar(50) NOT NULL,
  `event_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `Driver_Log` ADD PRIMARY KEY (`id`);

ALTER TABLE `Driver_Log` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- CREATE TRIGGER `TRG_INS_DV` AFTER INSERT ON `Driver`
--  FOR EACH ROW INSERT INTO Driver_Log (
--     company_code, company_name, customer_code, customer_name, site_code, site_name, order_date, order_no, po_no, delivery_date, agent_code,
--     agent_name, deliver_to_name, remarks, status, action_id, action_by, event_date
-- ) 
-- VALUES (
--     NEW.company_code, NEW.company_name, NEW.customer_code, NEW.customer_name, NEW.site_code, NEW.site_name, NEW.order_date, NEW.order_no, NEW.po_no, NEW.delivery_date, NEW.agent_code, NEW.agent_name, NEW.deliver_to_name, NEW.remarks, NEW.status, 1, NEW.created_by, NEW.created_date
-- )

-- CREATE TRIGGER `TRG_UPD_DV` BEFORE UPDATE ON `Driver`
-- FOR EACH ROW 
-- BEGIN
--     DECLARE action_value INT;

--     -- Check if deleted = 1, set action_id to 3, otherwise set to 2
--     IF NEW.deleted = 1 THEN
--         SET action_value = 3;
--     ELSE
--         SET action_value = 2;
--     END IF;

--     -- Insert into Purchase_Order table
--     INSERT INTO Purchase_Order_Log (
--         company_code, company_name, customer_code, customer_name, site_code, site_name, order_date, order_no, po_no, delivery_date, agent_code,
--         agent_name, deliver_to_name, remarks, status, action_id, action_by, event_date
--     ) 
--     VALUES (
--         NEW.company_code, NEW.company_name, NEW.customer_code, NEW.customer_name, NEW.site_code, NEW.site_name, NEW.order_date, NEW.order_no, NEW.po_no, NEW.delivery_date, NEW.agent_code, NEW.agent_name, NEW.deliver_to_name, NEW.remarks, NEW.status, action_value, NEW.modified_by, NEW.modified_date
--     );
-- END

-- 20/03/2025 --

ALTER TABLE `Driver` ADD `driver_phone` VARCHAR(50) NULL AFTER `driver_ic`;

ALTER TABLE `Driver_Log` ADD `driver_phone` VARCHAR(50) NULL AFTER `driver_ic`;

ALTER TABLE `Weight` ADD `driver_phone` VARCHAR(50) NULL AFTER `driver_ic`;
