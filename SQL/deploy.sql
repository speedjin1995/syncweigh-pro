-- 25/02/2025 --

ALTER TABLE `Raw_Mat` ADD `type` VARCHAR(10) NULL AFTER `low`;

ALTER TABLE `Raw_Mat_Log` ADD `type` VARCHAR(10) NULL AFTER `low`;

ALTER TABLE `Raw_Mat_Log` CHANGE `type` `type` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;

ALTER TABLE `Raw_Mat` CHANGE `type` `type` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;

DROP TABLE Weight_Log;

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
  `plant_code` varchar(50) DEFAULT NULL,
  `plant_name` varchar(50) DEFAULT NULL,
  `site_code` varchar(50) DEFAULT NULL,
  `site_name` varchar(100) DEFAULT NULL,
  `agent_code` varchar(50) DEFAULT NULL,
  `agent_name` varchar(50) DEFAULT NULL,
  `customer_code` varchar(50) DEFAULT NULL,
  `customer_name` varchar(50) DEFAULT NULL,
  `supplier_code` varchar(50) DEFAULT NULL,
  `supplier_name` varchar(50) DEFAULT NULL,
  `product_code` varchar(50) DEFAULT NULL,
  `product_name` varchar(50) DEFAULT NULL,
  `product_description` varchar(150) DEFAULT NULL,
  `ex_del` varchar(5) DEFAULT NULL,
  `raw_mat_code` varchar(50) DEFAULT NULL,
  `raw_mat_name` varchar(100) DEFAULT NULL,
  `container_no` varchar(50) DEFAULT NULL,
  `invoice_no` varchar(50) DEFAULT NULL,
  `purchase_order` varchar(50) DEFAULT NULL,
  `delivery_no` varchar(50) DEFAULT NULL,
  `transporter_code` varchar(50) DEFAULT NULL,
  `transporter` varchar(50) DEFAULT NULL,
  `destination_code` varchar(50) DEFAULT NULL,
  `destination` varchar(100) DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
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
  `load_drum` varchar(4) DEFAULT NULL,
  `no_of_drum` int(100) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `approved_by` int(5) DEFAULT NULL,
  `approved_reason` text DEFAULT NULL,
  `action_id` int(11) NOT NULL,
  `action_by` varchar(50) NOT NULL,
  `event_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `Weight_Log` ADD PRIMARY KEY (`id`);

ALTER TABLE `Weight_Log` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=319;

CREATE TRIGGER `TRG_INS_WEIGHT` AFTER INSERT ON `Weight`
 FOR EACH ROW INSERT INTO Weight_Log (
    transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, order_weight, plant_code, plant_name, site_code, site_name, agent_code, agent_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, ex_del, raw_mat_code,raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, weight_different, is_complete, is_cancel, is_approved, manual_weight, indicator_id, weighbridge_id, indicator_id_2, sub_total, sst, total_price, load_drum, no_of_drum,status, approved_by, approved_reason, action_id, action_by, event_date
) 
VALUES (
    NEW.transaction_id, NEW.transaction_status, NEW.weight_type, NEW.transaction_date, NEW.lorry_plate_no1, NEW.lorry_plate_no2, NEW.supplier_weight, NEW.order_weight, NEW.plant_code, NEW.plant_name, NEW.site_code, NEW.site_name, NEW.agent_code, NEW.agent_name, NEW.customer_code, NEW.customer_name, NEW.supplier_code, NEW.supplier_name, NEW.product_code, NEW.product_name, NEW.product_description, NEW.ex_del, NEW.raw_mat_code, NEW.raw_mat_name, NEW.container_no, NEW.invoice_no, NEW.purchase_order, NEW.delivery_no, NEW.transporter_code, NEW.transporter, NEW.destination_code, NEW.destination, NEW.remarks, NEW.gross_weight1, NEW.gross_weight1_date, NEW.tare_weight1, NEW.tare_weight1_date, NEW.nett_weight1, NEW.gross_weight2, NEW.gross_weight2_date, NEW.tare_weight2, NEW.tare_weight2_date, NEW.nett_weight2, NEW.reduce_weight, NEW.final_weight, NEW.weight_different, NEW.is_complete, NEW.is_cancel, NEW.is_approved, NEW.manual_weight, NEW.indicator_id, NEW.weighbridge_id, NEW.indicator_id_2, NEW.sub_total, NEW.sst, NEW.total_price, NEW.load_drum, NEW.no_of_drum, NEW.status, NEW.approved_by, NEW.approved_reason, 1, NEW.created_by, NEW.created_date
)

CREATE TRIGGER `TRG_UPD_WEIGHT` BEFORE UPDATE ON `Weight`
 FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if status = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Weight_Log table
    INSERT INTO Weight_Log (
        transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, order_weight, plant_code, plant_name, site_code, site_name, agent_code, agent_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, ex_del, raw_mat_code,raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, weight_different, is_complete, is_cancel, is_approved, manual_weight, indicator_id, weighbridge_id, indicator_id_2, sub_total, sst, total_price, load_drum, no_of_drum,status, approved_by, approved_reason, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.transaction_id, NEW.transaction_status, NEW.weight_type, NEW.transaction_date, 
        NEW.lorry_plate_no1, NEW.lorry_plate_no2, NEW.supplier_weight, NEW.order_weight, 
        NEW.plant_code, NEW.plant_name, NEW.site_code, NEW.site_name, 
        NEW.agent_code, NEW.agent_name, NEW.customer_code, NEW.customer_name, 
        NEW.supplier_code, NEW.supplier_name, NEW.product_code, NEW.product_name, 
        NEW.product_description, NEW.ex_del, NEW.raw_mat_code, NEW.raw_mat_name, 
        NEW.container_no, NEW.invoice_no, NEW.purchase_order, NEW.delivery_no, 
        NEW.transporter_code, NEW.transporter, NEW.destination_code, NEW.destination, 
        NEW.remarks, NEW.gross_weight1, NEW.gross_weight1_date, NEW.tare_weight1, 
        NEW.tare_weight1_date, NEW.nett_weight1, NEW.gross_weight2, NEW.gross_weight2_date, 
        NEW.tare_weight2, NEW.tare_weight2_date, NEW.nett_weight2, NEW.reduce_weight, 
        NEW.final_weight, NEW.weight_different, NEW.is_complete, NEW.is_cancel, 
        NEW.is_approved, NEW.manual_weight, NEW.indicator_id, NEW.weighbridge_id, 
        NEW.indicator_id_2, NEW.sub_total, NEW.sst, NEW.total_price, NEW.load_drum, 
        NEW.no_of_drum, NEW.status, NEW.approved_by, NEW.approved_reason, action_value, NEW.modified_by, NEW.modified_date
    );
END

-- 01/03/2025 --

ALTER TABLE `Vehicle` ADD `transporter_code` VARCHAR(50) NULL AFTER `vehicle_weight`, ADD `transporter_name` VARCHAR(255) NULL AFTER `transporter_code`;

ALTER TABLE `Vehicle_Log` ADD `transporter_code` VARCHAR(50) NULL AFTER `vehicle_weight`, ADD `transporter_name` VARCHAR(255) NULL AFTER `transporter_code`;

ALTER TABLE `Weight` ADD `unit_price` VARCHAR(10) NULL AFTER `indicator_id_2`;

ALTER TABLE `Weight` ADD `customer_type` VARCHAR(100) NULL AFTER `weight_type`;

ALTER TABLE `Users` ADD `name` VARCHAR(255) NULL AFTER `username`;

-- 09/03/2025 --

CREATE TABLE `Purchase_Order` (
  `id` int(11) NOT NULL,
  `company_code` varchar(50) DEFAULT NULL,
  `company_name` varchar(100) DEFAULT NULL,
  `customer_code` varchar(50) DEFAULT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `site_code` varchar(50) DEFAULT NULL,
  `site_name` varchar(100) DEFAULT NULL,
  `order_date` datetime DEFAULT NULL,
  `order_no` varchar(50) DEFAULT NULL,
  `po_no` varchar(50) DEFAULT NULL,
  `delivery_date` datetime DEFAULT NULL,
  `agent_code` int(50) DEFAULT NULL,
  `agent_name` int(100) DEFAULT NULL,
  `deliver_to_name` varchar(50) DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `created_by` varchar(50) DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT current_timestamp(),
  `modified_by` varchar(50) DEFAULT NULL,
  `modified_date` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `Purchase_Order` ADD `deleted` INT(1) NOT NULL DEFAULT '0' AFTER `status`;

ALTER TABLE `Purchase_Order` ADD PRIMARY KEY (`id`);

ALTER TABLE `Purchase_Order` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE `Purchase_Order_Log` (
  `id` int(11) NOT NULL,
  `company_code` varchar(50) DEFAULT NULL,
  `company_name` varchar(100) DEFAULT NULL,
  `customer_code` varchar(50) DEFAULT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `site_code` varchar(50) DEFAULT NULL,
  `site_name` varchar(100) DEFAULT NULL,
  `order_date` datetime DEFAULT NULL,
  `order_no` varchar(50) DEFAULT NULL,
  `po_no` varchar(50) DEFAULT NULL,
  `delivery_date` datetime DEFAULT NULL,
  `agent_code` int(50) DEFAULT NULL,
  `agent_name` int(100) DEFAULT NULL,
  `deliver_to_name` varchar(50) DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `action_id` int(11) NOT NULL,
  `action_by` varchar(50) NOT NULL,
  `event_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `Purchase_Order_Log` ADD PRIMARY KEY (`id`);

ALTER TABLE `Purchase_Order_Log` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TRIGGER `TRG_INS_PO` AFTER INSERT ON `Purchase_Order`
 FOR EACH ROW INSERT INTO Purchase_Order_Log (
    company_code, company_name, customer_code, customer_name, site_code, site_name, order_date, order_no, po_no, delivery_date, agent_code,
    agent_name, deliver_to_name, remarks, status, action_id, action_by, event_date
) 
VALUES (
    NEW.company_code, NEW.company_name, NEW.customer_code, NEW.customer_name, NEW.site_code, NEW.site_name, NEW.order_date, NEW.order_no, NEW.po_no, NEW.delivery_date, NEW.agent_code, NEW.agent_name, NEW.deliver_to_name, NEW.remarks, NEW.status, 1, NEW.created_by, NEW.created_date
)

CREATE TRIGGER `TRG_UPD_PO` BEFORE UPDATE ON `Purchase_Order`
FOR EACH ROW 
BEGIN
    DECLARE action_value INT;

    -- Check if deleted = 1, set action_id to 3, otherwise set to 2
    IF NEW.deleted = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Purchase_Order table
    INSERT INTO Purchase_Order_Log (
        company_code, company_name, customer_code, customer_name, site_code, site_name, order_date, order_no, po_no, delivery_date, agent_code,
        agent_name, deliver_to_name, remarks, status, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.company_code, NEW.company_name, NEW.customer_code, NEW.customer_name, NEW.site_code, NEW.site_name, NEW.order_date, NEW.order_no, NEW.po_no, NEW.delivery_date, NEW.agent_code, NEW.agent_name, NEW.deliver_to_name, NEW.remarks, NEW.status, action_value, NEW.modified_by, NEW.modified_date
    );
END

ALTER TABLE `Purchase_Order` CHANGE `agent_code` `agent_code` VARCHAR(50) NULL DEFAULT NULL;

ALTER TABLE `Purchase_Order` CHANGE `agent_name` `agent_name` VARCHAR(100) NULL DEFAULT NULL;

CREATE TABLE `Site_Log` (
  `id` int(11) NOT NULL,
  `site_code` varchar(50) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `address_line_1` varchar(255) DEFAULT NULL,
  `address_line_2` varchar(255) DEFAULT NULL,
  `address_line_3` varchar(255) DEFAULT NULL,
  `phone_no` varchar(50) DEFAULT NULL,
  `fax_no` varchar(50) DEFAULT NULL,
  `action_id` int(11) NOT NULL,
  `action_by` varchar(50) NOT NULL,
  `event_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `Site_Log` ADD PRIMARY KEY (`id`);

ALTER TABLE `Site_Log` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `Site_Log` ADD `site_id` INT(11) NOT NULL AFTER `id`;

ALTER TABLE `Company_Log` ADD `company_code` VARCHAR(50) NOT NULL AFTER `company_id`;
