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

-- 08/04/2025 --
DELIMITER $$

CREATE OR REPLACE TRIGGER `TRG_INS_PLANT` AFTER INSERT ON `Plant`
 FOR EACH ROW INSERT INTO Plant_Log (
    plant_id, plant_code, name, address_line_1, address_line_2, address_line_3, phone_no, fax_no, sales, purchase, locals, do_no, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.plant_code, NEW.name, NEW.address_line_1, NEW.address_line_2, NEW.address_line_3, NEW.phone_no, NEW.fax_no, NEW.sales, NEW.purchase, NEW.locals, NEW.do_no, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_PLANT` BEFORE UPDATE ON `Plant`
FOR EACH ROW 
BEGIN
    DECLARE action_value INT;

    -- Check if deleted = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Plant_Log table
    INSERT INTO Plant_Log (
        plant_id, plant_code, name, address_line_1, address_line_2, address_line_3, phone_no, fax_no, sales, purchase, locals, do_no, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.plant_code, NEW.name, NEW.address_line_1, NEW.address_line_2, NEW.address_line_3, NEW.phone_no, NEW.fax_no, NEW.sales, NEW.purchase, NEW.locals, NEW.do_no, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ; 

-- 09/04/2025 -- 
DROP TABLE `Weight_Log`;

CREATE TABLE `Weight_Log` (
  `id` int(11) NOT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `transaction_status` varchar(100) DEFAULT NULL,
  `weight_type` varchar(100) DEFAULT NULL,
  `transaction_date` datetime DEFAULT NULL,
  `lorry_plate_no1` varchar(100) DEFAULT NULL,
  `lorry_plate_no2` varchar(100) DEFAULT NULL,
  `supplier_weight` varchar(100) DEFAULT NULL,
  `order_weight` varchar(100) DEFAULT NULL,
  `customer_code` varchar(50) DEFAULT NULL,
  `customer_name` varchar(50) DEFAULT NULL,
  `supplier_code` varchar(50) DEFAULT NULL,
  `supplier_name` varchar(50) DEFAULT NULL,
  `product_code` varchar(50) DEFAULT NULL,
  `product_name` varchar(50) DEFAULT NULL,
  `product_description` varchar(150) DEFAULT NULL,
  `container_no` varchar(50) DEFAULT NULL,
  `invoice_no` varchar(50) DEFAULT NULL,
  `purchase_order` varchar(50) DEFAULT NULL,
  `delivery_no` varchar(50) DEFAULT NULL,
  `transporter_code` varchar(50) DEFAULT NULL,
  `transporter` varchar(50) DEFAULT NULL,
  `destination_code` varchar(50) DEFAULT NULL,
  `destination` varchar(100) DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `driver_code` varchar(50) DEFAULT NULL,
  `driver_name` varchar(100) DEFAULT NULL,
  `driver_ic` varchar(50) DEFAULT NULL,
  `driver_phone` varchar(50) DEFAULT NULL,
  `plant_code` varchar(50) DEFAULT NULL,
  `plant_name` varchar(100) DEFAULT NULL,
  `estimate_loading` varchar(10) DEFAULT NULL,
  `gross_weight1` varchar(100) DEFAULT NULL,
  `gross_weight1_date` datetime DEFAULT NULL,
  `tare_weight1` varchar(100) DEFAULT NULL,
  `tare_weight1_date` datetime DEFAULT NULL,
  `nett_weight1` varchar(100) DEFAULT NULL,
  `gross_weight2` varchar(100) DEFAULT NULL,
  `gross_weight2_date` datetime DEFAULT NULL,
  `tare_weight2` varchar(100) DEFAULT NULL,
  `tare_weight2_date` datetime DEFAULT NULL,
  `nett_weight2` varchar(100) DEFAULT NULL,
  `reduce_weight` varchar(100) DEFAULT NULL,
  `final_weight` varchar(150) DEFAULT NULL,
  `weight_different` varchar(100) DEFAULT NULL,
  `is_complete` varchar(100) DEFAULT NULL,
  `is_cancel` varchar(100) DEFAULT NULL,
  `is_approved` varchar(3) DEFAULT NULL,
  `manual_weight` varchar(100) DEFAULT NULL,
  `indicator_id` varchar(100) DEFAULT NULL,
  `weighbridge_id` varchar(100) DEFAULT NULL,
  `indicator_id_2` varchar(50) DEFAULT NULL,
  `sub_total` varchar(10) DEFAULT NULL,
  `sst` varchar(10) DEFAULT NULL,
  `total_price` varchar(10) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `approved_by` int(5) DEFAULT NULL,
  `approved_reason` text DEFAULT NULL,
  `action_id` int(11) NOT NULL,
  `action_by` varchar(50) NOT NULL,
  `event_date` varchar(50) NOT NULL DEFAULT current_timestamp(),
);

ALTER TABLE `Weight_Log` ADD PRIMARY KEY (`id`);

ALTER TABLE `Weight_Log` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

DELIMITER $$

CREATE OR REPLACE TRIGGER `TRG_INS_WEIGHT` AFTER INSERT ON `Weight`
 FOR EACH ROW INSERT INTO Weight_Log (
    transaction_id, transaction_status, weight_type, transaction_date,
    lorry_plate_no1, lorry_plate_no2, supplier_weight, order_weight,
    customer_code, customer_name, supplier_code, supplier_name,
    product_code, product_name, product_description, container_no,
    invoice_no, purchase_order, delivery_no, transporter_code,
    transporter, destination_code, destination, remarks,
    driver_code, driver_name, driver_ic, driver_phone,
    plant_code, plant_name, estimate_loading,
    gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1,
    gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2,
    reduce_weight, final_weight, weight_different,
    is_complete, is_cancel, is_approved, manual_weight,
    indicator_id, weighbridge_id, indicator_id_2,
    sub_total, sst, total_price,
    status, approved_by, approved_reason, action_id, action_by, event_date
) 
VALUES (
    NEW.transaction_id, NEW.transaction_status, NEW.weight_type, NEW.transaction_date,
    NEW.lorry_plate_no1, NEW.lorry_plate_no2, NEW.supplier_weight, NEW.order_weight,
    NEW.customer_code, NEW.customer_name, NEW.supplier_code, NEW.supplier_name,
    NEW.product_code, NEW.product_name, NEW.product_description, NEW.container_no,
    NEW.invoice_no, NEW.purchase_order, NEW.delivery_no, NEW.transporter_code,
    NEW.transporter, NEW.destination_code, NEW.destination, NEW.remarks,
    NEW.driver_code, NEW.driver_name, NEW.driver_ic, NEW.driver_phone,
    NEW.plant_code, NEW.plant_name, NEW.estimate_loading,
    NEW.gross_weight1, NEW.gross_weight1_date, NEW.tare_weight1, NEW.tare_weight1_date, NEW.nett_weight1,
    NEW.gross_weight2, NEW.gross_weight2_date, NEW.tare_weight2, NEW.tare_weight2_date, NEW.nett_weight2,
    NEW.reduce_weight, NEW.final_weight, NEW.weight_different,
    NEW.is_complete, NEW.is_cancel, NEW.is_approved, NEW.manual_weight,
    NEW.indicator_id, NEW.weighbridge_id, NEW.indicator_id_2,
    NEW.sub_total, NEW.sst, NEW.total_price,
    NEW.status, NEW.approved_by, NEW.approved_reason, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_WEIGHT` BEFORE UPDATE ON `Weight`
FOR EACH ROW 
BEGIN
    DECLARE action_value INT;

    -- Check if deleted = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Plant_Log table
    INSERT INTO Weight_Log (
        transaction_id, transaction_status, weight_type, transaction_date,
        lorry_plate_no1, lorry_plate_no2, supplier_weight, order_weight,
        customer_code, customer_name, supplier_code, supplier_name,
        product_code, product_name, product_description, container_no,
        invoice_no, purchase_order, delivery_no, transporter_code,
        transporter, destination_code, destination, remarks,
        driver_code, driver_name, driver_ic, driver_phone,
        plant_code, plant_name, estimate_loading,
        gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1,
        gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2,
        reduce_weight, final_weight, weight_different,
        is_complete, is_cancel, is_approved, manual_weight,
        indicator_id, weighbridge_id, indicator_id_2,
        sub_total, sst, total_price,
        status, approved_by, approved_reason, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.transaction_id, NEW.transaction_status, NEW.weight_type, NEW.transaction_date,
        NEW.lorry_plate_no1, NEW.lorry_plate_no2, NEW.supplier_weight, NEW.order_weight,
        NEW.customer_code, NEW.customer_name, NEW.supplier_code, NEW.supplier_name,
        NEW.product_code, NEW.product_name, NEW.product_description, NEW.container_no,
        NEW.invoice_no, NEW.purchase_order, NEW.delivery_no, NEW.transporter_code,
        NEW.transporter, NEW.destination_code, NEW.destination, NEW.remarks,
        NEW.driver_code, NEW.driver_name, NEW.driver_ic, NEW.driver_phone,
        NEW.plant_code, NEW.plant_name, NEW.estimate_loading,
        NEW.gross_weight1, NEW.gross_weight1_date, NEW.tare_weight1, NEW.tare_weight1_date, NEW.nett_weight1,
        NEW.gross_weight2, NEW.gross_weight2_date, NEW.tare_weight2, NEW.tare_weight2_date, NEW.nett_weight2,
        NEW.reduce_weight, NEW.final_weight, NEW.weight_different,
        NEW.is_complete, NEW.is_cancel, NEW.is_approved, NEW.manual_weight,
        NEW.indicator_id, NEW.weighbridge_id, NEW.indicator_id_2,
        NEW.sub_total, NEW.sst, NEW.total_price,
        NEW.status, NEW.approved_by, NEW.approved_reason, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ; 

-- 27/04/25 --
ALTER TABLE `product_log` ADD `plant` INT(5) NULL AFTER `low`;

-- 13/05/25 -- 
ALTER TABLE `Customer` ADD `new_reg_no` VARCHAR(100) NULL AFTER `company_reg_no`;

ALTER TABLE `Customer` ADD `contact_name` VARCHAR(100) NULL AFTER `fax_no`, ADD `ic_no` VARCHAR(100) NULL AFTER `contact_name`, ADD `tin_no` VARCHAR(100) NULL AFTER `ic_no`;

ALTER TABLE `Customer_Log` ADD `new_reg_no` VARCHAR(100) NULL AFTER `company_reg_no`;

ALTER TABLE `Customer_Log` ADD `contact_name` VARCHAR(100) NULL AFTER `fax_no`, ADD `ic_no` VARCHAR(100) NULL AFTER `contact_name`, ADD `tin_no` VARCHAR(100) NULL AFTER `ic_no`;

ALTER TABLE `Supplier` ADD `new_reg_no` VARCHAR(100) NULL AFTER `company_reg_no`;

ALTER TABLE `Supplier` ADD `contact_name` VARCHAR(100) NULL AFTER `fax_no`, ADD `ic_no` VARCHAR(100) NULL AFTER `contact_name`, ADD `tin_no` VARCHAR(100) NULL AFTER `ic_no`;

ALTER TABLE `Supplier_Log` ADD `new_reg_no` VARCHAR(100) NULL AFTER `company_reg_no`;

ALTER TABLE `Supplier_Log` ADD `contact_name` VARCHAR(100) NULL AFTER `fax_no`, ADD `ic_no` VARCHAR(100) NULL AFTER `contact_name`, ADD `tin_no` VARCHAR(100) NULL AFTER `ic_no`;

ALTER TABLE `Transporter` ADD `new_reg_no` VARCHAR(100) NULL AFTER `company_reg_no`;

ALTER TABLE `Transporter` ADD `contact_name` VARCHAR(100) NULL AFTER `fax_no`, ADD `ic_no` VARCHAR(100) NULL AFTER `contact_name`, ADD `tin_no` VARCHAR(100) NULL AFTER `ic_no`;

ALTER TABLE `Transporter_Log` ADD `new_reg_no` VARCHAR(100) NULL AFTER `company_reg_no`;

ALTER TABLE `Transporter_Log` ADD `contact_name` VARCHAR(100) NULL AFTER `fax_no`, ADD `ic_no` VARCHAR(100) NULL AFTER `contact_name`, ADD `tin_no` VARCHAR(100) NULL AFTER `ic_no`;

ALTER TABLE `Company` CHANGE `created_date` `created_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;

ALTER TABLE `Company` CHANGE `modified_date` `modified_date` DATETIME on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;

ALTER TABLE `Company` ADD `new_reg_no` VARCHAR(100) NULL AFTER `company_reg_no`;

ALTER TABLE `Company` ADD `tin_no` VARCHAR(100) NULL AFTER `fax_no`, ADD `mobile_no` VARCHAR(50) NULL AFTER `tin_no`;

ALTER TABLE `Company_Log` ADD `new_reg_no` VARCHAR(100) NULL AFTER `company_reg_no`;

ALTER TABLE `Company_Log` ADD `tin_no` VARCHAR(100) NULL AFTER `fax_no`, ADD `mobile_no` VARCHAR(50) NULL AFTER `tin_no`;

-- 17/05/2025 --
ALTER TABLE `Customer` ADD `plant` INT(5) NULL AFTER `tin_no`;

ALTER TABLE `Customer_Log` ADD `plant` INT(5) NULL AFTER `tin_no`;

ALTER TABLE `Driver` ADD `plant` INT(5) NULL AFTER `driver_phone`;

ALTER TABLE `Driver_Log` ADD `plant` INT(5) NULL AFTER `driver_phone`;

ALTER TABLE `Destination` ADD `plant` INT(5) NULL AFTER `description`;

ALTER TABLE `Destination_Log` ADD `plant` INT(5) NULL AFTER `description`;

ALTER TABLE `Supplier` ADD `plant` INT(5) NULL AFTER `tin_no`;

ALTER TABLE `Supplier_Log` ADD `plant` INT(5) NULL AFTER `tin_no`;

ALTER TABLE `Vehicle` ADD `plant` INT(5) NULL AFTER `vehicle_weight`;

ALTER TABLE `Vehicle_Log` ADD `plant` INT(5) NULL AFTER `vehicle_weight`;

ALTER TABLE `Transporter` ADD `plant` INT(5) NULL AFTER `tin_no`;

ALTER TABLE `Transporter_Log` ADD `plant` INT(5) NULL AFTER `tin_no`;

-- 20/05/2025 --
ALTER TABLE `Weight` ADD `cancel_reason` TEXT NULL AFTER `approved_reason`;

ALTER TABLE `Weight_Log` ADD `cancel_reason` TEXT NULL AFTER `approved_reason`;

DELIMITER $$

CREATE OR REPLACE TRIGGER `TRG_INS_WEIGHT` AFTER INSERT ON `Weight`
 FOR EACH ROW INSERT INTO Weight_Log (
    transaction_id, transaction_status, weight_type, transaction_date,
    lorry_plate_no1, lorry_plate_no2, supplier_weight, order_weight,
    customer_code, customer_name, supplier_code, supplier_name,
    product_code, product_name, product_description, container_no,
    invoice_no, purchase_order, delivery_no, transporter_code,
    transporter, destination_code, destination, remarks,
    driver_code, driver_name, driver_ic, driver_phone,
    plant_code, plant_name, estimate_loading,
    gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1,
    gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2,
    reduce_weight, final_weight, weight_different,
    is_complete, is_cancel, is_approved, manual_weight,
    indicator_id, weighbridge_id, indicator_id_2,
    sub_total, sst, total_price,
    status, approved_by, approved_reason, cancel_reason, action_id, action_by, event_date
) 
VALUES (
    NEW.transaction_id, NEW.transaction_status, NEW.weight_type, NEW.transaction_date,
    NEW.lorry_plate_no1, NEW.lorry_plate_no2, NEW.supplier_weight, NEW.order_weight,
    NEW.customer_code, NEW.customer_name, NEW.supplier_code, NEW.supplier_name,
    NEW.product_code, NEW.product_name, NEW.product_description, NEW.container_no,
    NEW.invoice_no, NEW.purchase_order, NEW.delivery_no, NEW.transporter_code,
    NEW.transporter, NEW.destination_code, NEW.destination, NEW.remarks,
    NEW.driver_code, NEW.driver_name, NEW.driver_ic, NEW.driver_phone,
    NEW.plant_code, NEW.plant_name, NEW.estimate_loading,
    NEW.gross_weight1, NEW.gross_weight1_date, NEW.tare_weight1, NEW.tare_weight1_date, NEW.nett_weight1,
    NEW.gross_weight2, NEW.gross_weight2_date, NEW.tare_weight2, NEW.tare_weight2_date, NEW.nett_weight2,
    NEW.reduce_weight, NEW.final_weight, NEW.weight_different,
    NEW.is_complete, NEW.is_cancel, NEW.is_approved, NEW.manual_weight,
    NEW.indicator_id, NEW.weighbridge_id, NEW.indicator_id_2,
    NEW.sub_total, NEW.sst, NEW.total_price,
    NEW.status, NEW.approved_by, NEW.approved_reason, NEW.cancel_reason, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_WEIGHT` BEFORE UPDATE ON `Weight`
FOR EACH ROW 
BEGIN
    DECLARE action_value INT;

    -- Check if deleted = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Plant_Log table
    INSERT INTO Weight_Log (
        transaction_id, transaction_status, weight_type, transaction_date,
        lorry_plate_no1, lorry_plate_no2, supplier_weight, order_weight,
        customer_code, customer_name, supplier_code, supplier_name,
        product_code, product_name, product_description, container_no,
        invoice_no, purchase_order, delivery_no, transporter_code,
        transporter, destination_code, destination, remarks,
        driver_code, driver_name, driver_ic, driver_phone,
        plant_code, plant_name, estimate_loading,
        gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1,
        gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2,
        reduce_weight, final_weight, weight_different,
        is_complete, is_cancel, is_approved, manual_weight,
        indicator_id, weighbridge_id, indicator_id_2,
        sub_total, sst, total_price,
        status, approved_by, approved_reason, cancel_reason, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.transaction_id, NEW.transaction_status, NEW.weight_type, NEW.transaction_date,
        NEW.lorry_plate_no1, NEW.lorry_plate_no2, NEW.supplier_weight, NEW.order_weight,
        NEW.customer_code, NEW.customer_name, NEW.supplier_code, NEW.supplier_name,
        NEW.product_code, NEW.product_name, NEW.product_description, NEW.container_no,
        NEW.invoice_no, NEW.purchase_order, NEW.delivery_no, NEW.transporter_code,
        NEW.transporter, NEW.destination_code, NEW.destination, NEW.remarks,
        NEW.driver_code, NEW.driver_name, NEW.driver_ic, NEW.driver_phone,
        NEW.plant_code, NEW.plant_name, NEW.estimate_loading,
        NEW.gross_weight1, NEW.gross_weight1_date, NEW.tare_weight1, NEW.tare_weight1_date, NEW.nett_weight1,
        NEW.gross_weight2, NEW.gross_weight2_date, NEW.tare_weight2, NEW.tare_weight2_date, NEW.nett_weight2,
        NEW.reduce_weight, NEW.final_weight, NEW.weight_different,
        NEW.is_complete, NEW.is_cancel, NEW.is_approved, NEW.manual_weight,
        NEW.indicator_id, NEW.weighbridge_id, NEW.indicator_id_2,
        NEW.sub_total, NEW.sst, NEW.total_price,
        NEW.status, NEW.approved_by, NEW.approved_reason, NEW.cancel_reason, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ; 

-- 22/05/2025 --
ALTER TABLE `Weight` ADD `manual_price` VARCHAR(100) NULL AFTER `manual_weight`;

ALTER TABLE `Weight_Log` ADD `manual_price` VARCHAR(100) NULL AFTER `manual_weight`;

DELIMITER $$

CREATE OR REPLACE TRIGGER `TRG_INS_WEIGHT` AFTER INSERT ON `Weight`
 FOR EACH ROW INSERT INTO Weight_Log (
    transaction_id, transaction_status, weight_type, transaction_date,
    lorry_plate_no1, lorry_plate_no2, supplier_weight, order_weight,
    customer_code, customer_name, supplier_code, supplier_name,
    product_code, product_name, product_description, container_no,
    invoice_no, purchase_order, delivery_no, transporter_code,
    transporter, destination_code, destination, remarks,
    driver_code, driver_name, driver_ic, driver_phone,
    plant_code, plant_name, estimate_loading,
    gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1,
    gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2,
    reduce_weight, final_weight, weight_different,
    is_complete, is_cancel, is_approved, manual_weight, manual_price,
    indicator_id, weighbridge_id, indicator_id_2,
    sub_total, sst, total_price,
    status, approved_by, approved_reason, cancel_reason, action_id, action_by, event_date
) 
VALUES (
    NEW.transaction_id, NEW.transaction_status, NEW.weight_type, NEW.transaction_date,
    NEW.lorry_plate_no1, NEW.lorry_plate_no2, NEW.supplier_weight, NEW.order_weight,
    NEW.customer_code, NEW.customer_name, NEW.supplier_code, NEW.supplier_name,
    NEW.product_code, NEW.product_name, NEW.product_description, NEW.container_no,
    NEW.invoice_no, NEW.purchase_order, NEW.delivery_no, NEW.transporter_code,
    NEW.transporter, NEW.destination_code, NEW.destination, NEW.remarks,
    NEW.driver_code, NEW.driver_name, NEW.driver_ic, NEW.driver_phone,
    NEW.plant_code, NEW.plant_name, NEW.estimate_loading,
    NEW.gross_weight1, NEW.gross_weight1_date, NEW.tare_weight1, NEW.tare_weight1_date, NEW.nett_weight1,
    NEW.gross_weight2, NEW.gross_weight2_date, NEW.tare_weight2, NEW.tare_weight2_date, NEW.nett_weight2,
    NEW.reduce_weight, NEW.final_weight, NEW.weight_different,
    NEW.is_complete, NEW.is_cancel, NEW.is_approved, NEW.manual_weight, NEW.manual_price,
    NEW.indicator_id, NEW.weighbridge_id, NEW.indicator_id_2,
    NEW.sub_total, NEW.sst, NEW.total_price,
    NEW.status, NEW.approved_by, NEW.approved_reason, NEW.cancel_reason, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_WEIGHT` BEFORE UPDATE ON `Weight`
FOR EACH ROW 
BEGIN
    DECLARE action_value INT;

    -- Check if deleted = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Plant_Log table
    INSERT INTO Weight_Log (
        transaction_id, transaction_status, weight_type, transaction_date,
        lorry_plate_no1, lorry_plate_no2, supplier_weight, order_weight,
        customer_code, customer_name, supplier_code, supplier_name,
        product_code, product_name, product_description, container_no,
        invoice_no, purchase_order, delivery_no, transporter_code,
        transporter, destination_code, destination, remarks,
        driver_code, driver_name, driver_ic, driver_phone,
        plant_code, plant_name, estimate_loading,
        gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1,
        gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2,
        reduce_weight, final_weight, weight_different,
        is_complete, is_cancel, is_approved, manual_weight, manual_price,
        indicator_id, weighbridge_id, indicator_id_2,
        sub_total, sst, total_price,
        status, approved_by, approved_reason, cancel_reason, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.transaction_id, NEW.transaction_status, NEW.weight_type, NEW.transaction_date,
        NEW.lorry_plate_no1, NEW.lorry_plate_no2, NEW.supplier_weight, NEW.order_weight,
        NEW.customer_code, NEW.customer_name, NEW.supplier_code, NEW.supplier_name,
        NEW.product_code, NEW.product_name, NEW.product_description, NEW.container_no,
        NEW.invoice_no, NEW.purchase_order, NEW.delivery_no, NEW.transporter_code,
        NEW.transporter, NEW.destination_code, NEW.destination, NEW.remarks,
        NEW.driver_code, NEW.driver_name, NEW.driver_ic, NEW.driver_phone,
        NEW.plant_code, NEW.plant_name, NEW.estimate_loading,
        NEW.gross_weight1, NEW.gross_weight1_date, NEW.tare_weight1, NEW.tare_weight1_date, NEW.nett_weight1,
        NEW.gross_weight2, NEW.gross_weight2_date, NEW.tare_weight2, NEW.tare_weight2_date, NEW.nett_weight2,
        NEW.reduce_weight, NEW.final_weight, NEW.weight_different,
        NEW.is_complete, NEW.is_cancel, NEW.is_approved, NEW.manual_weight, NEW.manual_price,
        NEW.indicator_id, NEW.weighbridge_id, NEW.indicator_id_2,
        NEW.sub_total, NEW.sst, NEW.total_price,
        NEW.status, NEW.approved_by, NEW.approved_reason, NEW.cancel_reason, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ; 

ALTER TABLE `Weight_Product` ADD `deleted` INT(1) NOT NULL DEFAULT '0' AFTER `total_price`;

-- 26/05/2025 --
ALTER TABLE `Vehicle` ADD `customer_code` VARCHAR(50) NULL AFTER `plant`, ADD `customer_name` VARCHAR(100) NULL AFTER `customer_code`, ADD `supplier_code` VARCHAR(50) NULL AFTER `customer_name`, ADD `supplier_name` VARCHAR(100) NULL AFTER `supplier_code`;

ALTER TABLE `Vehicle_Log` ADD `customer_code` VARCHAR(50) NULL AFTER `plant`, ADD `customer_name` VARCHAR(100) NULL AFTER `customer_code`, ADD `supplier_code` VARCHAR(50) NULL AFTER `customer_name`, ADD `supplier_name` VARCHAR(100) NULL AFTER `supplier_code`;

-- 27/05/2025 --
ALTER TABLE `Vehicle` CHANGE `vehicle_weight` `vehicle_weight` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL;

ALTER TABLE `Vehicle_Log` CHANGE `vehicle_weight` `vehicle_weight` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL;

ALTER TABLE `Weight_product` ADD `reduce_weight` INT(20) NULL AFTER `item_weight`, ADD `total_weight` INT(20) NULL AFTER `reduce_weight`;
