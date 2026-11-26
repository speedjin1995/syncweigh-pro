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

-- 11/03/2025 --

CREATE TABLE `Purchase_Order` (
  `id` int(11) NOT NULL,
  `company_code` varchar(50) DEFAULT NULL,
  `company_name` varchar(100) DEFAULT NULL,
  `supplier_code` varchar(50) DEFAULT NULL,
  `supplier_name` varchar(100) DEFAULT NULL,
  `site_code` varchar(50) DEFAULT NULL,
  `site_name` varchar(100) DEFAULT NULL,
  `order_date` datetime DEFAULT NULL,
  `order_no` varchar(50) DEFAULT NULL,
  `po_no` varchar(50) DEFAULT NULL,
  `delivery_date` datetime DEFAULT NULL,
  `agent_code` varchar(50) DEFAULT NULL,
  `agent_name` varchar(100) DEFAULT NULL,
  `destination_code` varchar(50) DEFAULT NULL,
  `destination_name` varchar(100) DEFAULT NULL,
  `deliver_to_name` varchar(255) DEFAULT NULL,
  `raw_mat_code` varchar(50) DEFAULT NULL,
  `raw_mat_name` varchar(100) DEFAULT NULL,
  `order_load` varchar(100)	DEFAULT NULL,
  `order_quantity` varchar(100)	DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `created_by` varchar(50) DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT current_timestamp(),
  `modified_by` varchar(50) DEFAULT NULL,
  `modified_date` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` varchar(50) DEFAULT NULL,
  `deleted` int(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DELIMITER $$
CREATE TRIGGER `TRG_INS_PO` AFTER INSERT ON `Purchase_Order` FOR EACH ROW INSERT INTO Purchase_Order_Log (
    company_code, company_name, supplier_code, supplier_name, site_code, site_name, order_date, order_no, po_no, delivery_date, agent_code, agent_name, destination_code, destination_name, deliver_to_name, raw_mat_code, raw_mat_name, order_load, order_quantity, remarks, status, action_id, action_by, event_date
) 
VALUES (
    NEW.company_code, NEW.company_name, NEW.supplier_code, NEW.supplier_name, NEW.site_code, NEW.site_name, NEW.order_date, NEW.order_no, NEW.po_no, NEW.delivery_date, NEW.agent_code, NEW.agent_name, NEW.destination_code, NEW.destination_name, NEW.deliver_to_name, NEW.raw_mat_code, NEW.raw_mat_name, NEW.order_load, NEW.order_quantity, NEW.remarks, NEW.status, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `TRG_UPD_PO` BEFORE UPDATE ON `Purchase_Order` FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if deleted = 1, set action_id to 3, otherwise set to 2
    IF NEW.deleted = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Purchase_Order table
    INSERT INTO Purchase_Order_Log (
        company_code, company_name, supplier_code, supplier_name, site_code, site_name, order_date, order_no, po_no, delivery_date, agent_code,
        agent_name, destination_code, destination_name, deliver_to_name, raw_mat_code, raw_mat_name, order_load, order_quantity, remarks, status, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.company_code, NEW.company_name, NEW.supplier_code, NEW.supplier_name, NEW.site_code, NEW.site_name, NEW.order_date, NEW.order_no, NEW.po_no, NEW.delivery_date, NEW.agent_code, NEW.agent_name, NEW.destination_code, NEW.destination_name, NEW.deliver_to_name, NEW.raw_mat_code, NEW.raw_mat_name, NEW.order_load, NEW.order_quantity, NEW.remarks, NEW.status, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

ALTER TABLE `Purchase_Order` ADD PRIMARY KEY (`id`);

ALTER TABLE `Purchase_Order` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE `Purchase_Order_Log` (
  `id` int(11) NOT NULL,
  `company_code` varchar(50) DEFAULT NULL,
  `company_name` varchar(100) DEFAULT NULL,
  `supplier_code` varchar(50) DEFAULT NULL,
  `supplier_name` varchar(100) DEFAULT NULL,
  `site_code` varchar(50) DEFAULT NULL,
  `site_name` varchar(100) DEFAULT NULL,
  `order_date` datetime DEFAULT NULL,
  `order_no` varchar(50) DEFAULT NULL,
  `po_no` varchar(50) DEFAULT NULL,
  `delivery_date` datetime DEFAULT NULL,
  `agent_code` varchar(50) DEFAULT NULL,
  `agent_name` varchar(100) DEFAULT NULL,
  `destination_code` varchar(50) DEFAULT NULL,
  `destination_name` varchar(100) DEFAULT NULL,
  `deliver_to_name` varchar(255) DEFAULT NULL,
  `raw_mat_code` varchar(50) DEFAULT NULL,
  `raw_mat_name` varchar(100) DEFAULT NULL,
  `order_load` varchar(100)	DEFAULT NULL,
  `order_quantity` varchar(100)	DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `action_id` int(11) NOT NULL,
  `action_by` varchar(50) NOT NULL,
  `event_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `Purchase_Order_Log` ADD PRIMARY KEY (`id`); 

ALTER TABLE `Purchase_Order_Log` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE `Sales_Order` (
  `id` int(11) NOT NULL,
  `company_code` varchar(50) DEFAULT NULL,
  `company_name` varchar(100) DEFAULT NULL,
  `customer_code` varchar(50) DEFAULT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `site_code` varchar(50) DEFAULT NULL,
  `site_name` varchar(100) DEFAULT NULL,
  `order_date` datetime DEFAULT NULL,
  `order_no` varchar(50) DEFAULT NULL,
  `so_no` varchar(50) DEFAULT NULL,
  `delivery_date` datetime DEFAULT NULL,
  `agent_code` varchar(50) DEFAULT NULL,
  `agent_name` varchar(100) DEFAULT NULL,
  `destination_code` varchar(50) DEFAULT NULL,
  `destination_name` varchar(100) DEFAULT NULL,
  `deliver_to_name` varchar(255) DEFAULT NULL,
  `product_code` varchar(50) DEFAULT NULL,
  `product_name` varchar(100) DEFAULT NULL,
  `order_load` varchar(100)	DEFAULT NULL,
  `order_quantity` varchar(100)	DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `created_by` varchar(50) DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT current_timestamp(),
  `modified_by` varchar(50) DEFAULT NULL,
  `modified_date` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` varchar(50) DEFAULT NULL,
  `deleted` int(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DELIMITER $$
CREATE TRIGGER `TRG_INS_SO` AFTER INSERT ON `Sales_Order` FOR EACH ROW INSERT INTO Sales_Order_Log (
    company_code, company_name, customer_code, customer_name, site_code, site_name, order_date, order_no, so_no, delivery_date, agent_code,
    agent_name, destination_code, destination_name, deliver_to_name, product_code, product_name, order_load, order_quantity, remarks, status, action_id, action_by, event_date
) 
VALUES (
    NEW.company_code, NEW.company_name, NEW.customer_code, NEW.customer_name, NEW.site_code, NEW.site_name, NEW.order_date, NEW.order_no, NEW.so_no, NEW.delivery_date, NEW.agent_code, NEW.agent_name, NEW.destination_code, NEW.destination_name, NEW.deliver_to_name, NEW.product_code, NEW.product_name, NEW.order_load, NEW.order_quantity, NEW.remarks, NEW.status, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `TRG_UPD_SO` BEFORE UPDATE ON `Sales_Order` FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if deleted = 1, set action_id to 3, otherwise set to 2
    IF NEW.deleted = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Sales_Order table
    INSERT INTO Sales_Order_Log (
        company_code, company_name, customer_code, customer_name, site_code, site_name, order_date, order_no, so_no, delivery_date, agent_code,
        agent_name, destination_code, destination_name, deliver_to_name, product_code, product_name, order_load, order_quantity, remarks, status, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.company_code, NEW.company_name, NEW.customer_code, NEW.customer_name, NEW.site_code, NEW.site_name, NEW.order_date, NEW.order_no, NEW.so_no, NEW.delivery_date, NEW.agent_code, NEW.agent_name, NEW.destination_code, NEW.destination_name, NEW.deliver_to_name, NEW.product_code, NEW.product_name, NEW.order_load, NEW.order_quantity, NEW.remarks, NEW.status, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

ALTER TABLE `Sales_Order` ADD PRIMARY KEY (`id`);

ALTER TABLE `Sales_Order` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE `Sales_Order_Log` (
  `id` int(11) NOT NULL,
  `company_code` varchar(50) DEFAULT NULL,
  `company_name` varchar(100) DEFAULT NULL,
  `customer_code` varchar(50) DEFAULT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `site_code` varchar(50) DEFAULT NULL,
  `site_name` varchar(100) DEFAULT NULL,
  `order_date` datetime DEFAULT NULL,
  `order_no` varchar(50) DEFAULT NULL,
  `so_no` varchar(50) DEFAULT NULL,
  `delivery_date` datetime DEFAULT NULL,
  `agent_code` varchar(50) DEFAULT NULL,
  `agent_name` varchar(100) DEFAULT NULL,
  `destination_code` varchar(50) DEFAULT NULL,
  `destination_name` varchar(100) DEFAULT NULL,
  `deliver_to_name` varchar(255) DEFAULT NULL,
  `product_code` varchar(50) DEFAULT NULL,
  `product_name` varchar(100) DEFAULT NULL,
  `order_load` varchar(100)	DEFAULT NULL,
  `order_quantity` varchar(100)	DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `action_id` int(11) NOT NULL,
  `action_by` varchar(50) NOT NULL,
  `event_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `Sales_Order_Log` ADD PRIMARY KEY (`id`); 

ALTER TABLE `Sales_Order_Log` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- 15/03/2025 --
ALTER TABLE `Vehicle` ADD `ex_del` VARCHAR(10) NULL AFTER `transporter_name`, ADD `customer_code` VARCHAR(50) NULL AFTER `ex_del`, ADD `customer_name` VARCHAR(100) NULL AFTER `customer_code`;

ALTER TABLE `Vehicle_Log` ADD `ex_del` VARCHAR(10) NULL AFTER `transporter_name`, ADD `customer_code` VARCHAR(50) NULL AFTER `ex_del`, ADD `customer_name` VARCHAR(100) NULL AFTER `customer_code`;


-- 17/03/2025 --
CREATE TABLE `Plant_Log` (
  `id` int(11) NOT NULL,
  `plant_id` int(11) NOT NULL,
  `plant_code` varchar(50) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `address_line_1` varchar(255) DEFAULT NULL,
  `address_line_2` varchar(255) DEFAULT NULL,
  `address_line_3` varchar(255) DEFAULT NULL,
  `phone_no` varchar(50) DEFAULT NULL,
  `fax_no` varchar(50) DEFAULT NULL,
  `sales` varchar(5) DEFAULT NULL,
  `purchase` varchar(5) DEFAULT NULL,
  `locals` varchar(5) DEFAULT NULL,
  `do_no` varchar(5) DEFAULT NULL,
  `action_id` int(11) NOT NULL,
  `action_by` varchar(50) NOT NULL,
  `event_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `Plant_Log`
ADD PRIMARY KEY (`id`);

ALTER TABLE `Plant_Log`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- 19/03/2025 --
ALTER TABLE `Sales_Order` ADD `plant_code` VARCHAR(50) NULL AFTER `product_name`, ADD `plant_name` VARCHAR(100) NULL AFTER `plant_code`;

ALTER TABLE `Sales_Order` ADD `balance` VARCHAR(100) NULL AFTER `order_quantity`;

ALTER TABLE `Sales_Order_Log` ADD `plant_code` VARCHAR(50) NULL AFTER `product_name`, ADD `plant_name` VARCHAR(100) NULL AFTER `plant_code`;

ALTER TABLE `Sales_Order_Log` ADD `balance` VARCHAR(100) NULL AFTER `order_quantity`;

ALTER TABLE `Sales_Order_Log` CHANGE `agent_code` `agent_code` VARCHAR(50) NULL DEFAULT NULL;

ALTER TABLE `Sales_Order_Log` CHANGE `agent_name` `agent_name` VARCHAR(100) NULL DEFAULT NULL;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_SO` AFTER INSERT ON `Sales_Order`
 FOR EACH ROW INSERT INTO Sales_Order_Log (
    company_code, company_name, customer_code, customer_name, site_code, site_name, order_date, order_no, so_no, delivery_date, agent_code,
    agent_name, destination_code, destination_name, deliver_to_name, product_code, product_name, plant_code, plant_name, order_load, order_quantity, balance, remarks, status, action_id, action_by, event_date
) 
VALUES (
    NEW.company_code, NEW.company_name, NEW.customer_code, NEW.customer_name, NEW.site_code, NEW.site_name, NEW.order_date, NEW.order_no, NEW.so_no, NEW.delivery_date, NEW.agent_code, NEW.agent_name, NEW.destination_code, NEW.destination_name, NEW.deliver_to_name, NEW.product_code, NEW.product_name, NEW.plant_code, NEW.plant_name, NEW.order_load, NEW.order_quantity, NEW.balance, NEW.remarks, NEW.status, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_SO` BEFORE UPDATE ON `Sales_Order`
 FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if deleted = 1, set action_id to 3, otherwise set to 2
    IF NEW.deleted = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Sales_Order table
    INSERT INTO Sales_Order_Log (
        company_code, company_name, customer_code, customer_name, site_code, site_name, order_date, order_no, so_no, delivery_date, agent_code,
        agent_name, destination_code, destination_name, deliver_to_name, product_code, product_name, plant_code, plant_name, order_load, order_quantity, balance, remarks, status, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.company_code, NEW.company_name, NEW.customer_code, NEW.customer_name, NEW.site_code, NEW.site_name, NEW.order_date, NEW.order_no, NEW.so_no, NEW.delivery_date, NEW.agent_code, NEW.agent_name, NEW.destination_code, NEW.destination_name, NEW.deliver_to_name, NEW.product_code, NEW.product_name, NEW.plant_code, NEW.plant_name, NEW.order_load, NEW.order_quantity, NEW.balance, NEW.remarks, NEW.status, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

ALTER TABLE `Purchase_Order` ADD `plant_code` VARCHAR(50) NULL AFTER `raw_mat_name`, ADD `plant_name` VARCHAR(100) NULL AFTER `plant_code`;

ALTER TABLE `Purchase_Order` ADD `balance` VARCHAR(100) NULL AFTER `order_quantity`;

ALTER TABLE `Purchase_Order` CHANGE `order_load` `order_load` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL;

ALTER TABLE `Purchase_Order_Log` ADD `plant_code` VARCHAR(50) NULL AFTER `raw_mat_name`, ADD `plant_name` VARCHAR(100) NULL AFTER `plant_code`;

ALTER TABLE `Purchase_Order_Log` ADD `balance` VARCHAR(100) NULL AFTER `order_quantity`;


DELIMITER $$

CREATE OR REPLACE TRIGGER `TRG_INS_PO` AFTER INSERT ON `Purchase_Order`
 FOR EACH ROW INSERT INTO Purchase_Order_Log (
    company_code, company_name, supplier_code, supplier_name, site_code, site_name, order_date, order_no, po_no, delivery_date, agent_code, agent_name, destination_code, destination_name, deliver_to_name, raw_mat_code, raw_mat_name, plant_code, plant_name, order_load, order_quantity, balance, remarks, status, action_id, action_by, event_date
) 
VALUES (
    NEW.company_code, NEW.company_name, NEW.supplier_code, NEW.supplier_name, NEW.site_code, NEW.site_name, NEW.order_date, NEW.order_no, NEW.po_no, NEW.delivery_date, NEW.agent_code, NEW.agent_name, NEW.destination_code, NEW.destination_name, NEW.deliver_to_name, NEW.raw_mat_code, NEW.raw_mat_name, NEW.plant_code, NEW.plant_name, NEW.order_load, NEW.order_quantity, NEW.balance, NEW.remarks, NEW.status, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_PO` BEFORE UPDATE ON `Purchase_Order`
 FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if deleted = 1, set action_id to 3, otherwise set to 2
    IF NEW.deleted = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Purchase_Order table
    INSERT INTO Purchase_Order_Log (
        company_code, company_name, supplier_code, supplier_name, site_code, site_name, order_date, order_no, po_no, delivery_date, agent_code,
        agent_name, destination_code, destination_name, deliver_to_name, raw_mat_code, raw_mat_name, plant_code, plant_name, order_load, order_quantity, balance, remarks, status, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.company_code, NEW.company_name, NEW.supplier_code, NEW.supplier_name, NEW.site_code, NEW.site_name, NEW.order_date, NEW.order_no, NEW.po_no, NEW.delivery_date, NEW.agent_code, NEW.agent_name, NEW.destination_code, NEW.destination_name, NEW.deliver_to_name, NEW.raw_mat_code, NEW.raw_mat_name, NEW.plant_code, NEW.plant_name, NEW.order_load, NEW.order_quantity, NEW.balance, NEW.remarks, NEW.status, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;


-- 28/03/2025 --
ALTER TABLE `Sales_Order` ADD `transporter_code` VARCHAR(50) NULL AFTER `plant_name`, ADD `transporter_name` VARCHAR(100) NULL AFTER `transporter_code`, ADD `veh_number` VARCHAR(50) NULL AFTER `transporter_name`, ADD `exquarry_or_delivered` VARCHAR(3) NULL DEFAULT 'E' AFTER `balance`;

ALTER TABLE `Sales_Order_Log` ADD `transporter_code` VARCHAR(50) NULL AFTER `plant_name`, ADD `transporter_name` VARCHAR(100) NULL AFTER `transporter_code`, ADD `veh_number` VARCHAR(50) NULL AFTER `transporter_name`, ADD `exquarry_or_delivered` VARCHAR(3) NULL DEFAULT 'E' AFTER `veh_number`;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_SO` AFTER INSERT ON `Sales_Order`
 FOR EACH ROW INSERT INTO Sales_Order_Log (
    company_code, company_name, customer_code, customer_name, site_code, site_name, order_date, order_no, so_no, delivery_date, agent_code, agent_name, destination_code, destination_name, deliver_to_name, product_code, product_name, plant_code, plant_name, transporter_code, transporter_name, veh_number, exquarry_or_delivered, order_load, order_quantity, balance, remarks, status, action_id, action_by, event_date
) 
VALUES (
    NEW.company_code, NEW.company_name, NEW.customer_code, NEW.customer_name, NEW.site_code, NEW.site_name, NEW.order_date, NEW.order_no, NEW.so_no, NEW.delivery_date, NEW.agent_code, NEW.agent_name, NEW.destination_code, NEW.destination_name, NEW.deliver_to_name, NEW.product_code, NEW.product_name, NEW.plant_code, NEW.plant_name, NEW.transporter_code, NEW.transporter_name, NEW.veh_number, NEW.exquarry_or_delivered, NEW.order_load, NEW.order_quantity, NEW.balance, NEW.remarks, NEW.status, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_SO` BEFORE UPDATE ON `Sales_Order`
 FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if deleted = 1, set action_id to 3, otherwise set to 2
    IF NEW.deleted = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Sales_Order table
    INSERT INTO Sales_Order_Log (
        company_code, company_name, customer_code, customer_name, site_code, site_name, order_date, order_no, so_no, delivery_date, agent_code, agent_name, destination_code, destination_name, deliver_to_name, product_code, product_name, plant_code, plant_name, transporter_code, transporter_name, veh_number, exquarry_or_delivered, order_load, order_quantity, balance, remarks, status, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.company_code, NEW.company_name, NEW.customer_code, NEW.customer_name, NEW.site_code, NEW.site_name, NEW.order_date, NEW.order_no, NEW.so_no, NEW.delivery_date, NEW.agent_code, NEW.agent_name, NEW.destination_code, NEW.destination_name, NEW.deliver_to_name, NEW.product_code, NEW.product_name, NEW.plant_code, NEW.plant_name, NEW.transporter_code, NEW.transporter_name, NEW.veh_number, NEW.exquarry_or_delivered, NEW.order_load, NEW.order_quantity, NEW.balance, NEW.remarks, NEW.status, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

ALTER TABLE `Purchase_Order` ADD `transporter_code` VARCHAR(50) NULL AFTER `plant_name`, ADD `transporter_name` VARCHAR(100) NULL AFTER `transporter_code`, ADD `veh_number` VARCHAR(50) NULL AFTER `transporter_name`, ADD `exquarry_or_delivered` VARCHAR(3) NULL DEFAULT 'E' AFTER `veh_number`;

ALTER TABLE `Purchase_Order_Log` ADD `transporter_code` VARCHAR(50) NULL AFTER `plant_name`, ADD `transporter_name` VARCHAR(100) NULL AFTER `transporter_code`, ADD `veh_number` VARCHAR(50) NULL AFTER `transporter_name`, ADD `exquarry_or_delivered` VARCHAR(3) NULL DEFAULT 'E' AFTER `veh_number`;

DELIMITER $$

CREATE OR REPLACE TRIGGER `TRG_INS_PO` AFTER INSERT ON `Purchase_Order`
 FOR EACH ROW INSERT INTO Purchase_Order_Log (
    company_code, company_name, supplier_code, supplier_name, site_code, site_name, order_date, order_no, po_no, delivery_date, agent_code, agent_name, destination_code, destination_name, deliver_to_name, raw_mat_code, raw_mat_name, plant_code, plant_name, transporter_code, transporter_name, veh_number, exquarry_or_delivered, order_load, order_quantity, balance, remarks, status, action_id, action_by, event_date
) 
VALUES (
    NEW.company_code, NEW.company_name, NEW.supplier_code, NEW.supplier_name, NEW.site_code, NEW.site_name, NEW.order_date, NEW.order_no, NEW.po_no, NEW.delivery_date, NEW.agent_code, NEW.agent_name, NEW.destination_code, NEW.destination_name, NEW.deliver_to_name, NEW.raw_mat_code, NEW.raw_mat_name, NEW.plant_code, NEW.plant_name, NEW.transporter_code, NEW.transporter_name, NEW.veh_number, NEW.exquarry_or_delivered, NEW.order_load, NEW.order_quantity, NEW.balance, NEW.remarks, NEW.status, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_PO` BEFORE UPDATE ON `Purchase_Order`
 FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if deleted = 1, set action_id to 3, otherwise set to 2
    IF NEW.deleted = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Purchase_Order table
    INSERT INTO Purchase_Order_Log (
        company_code, company_name, supplier_code, supplier_name, site_code, site_name, order_date, order_no, po_no, delivery_date, agent_code,
        agent_name, destination_code, destination_name, deliver_to_name, raw_mat_code, raw_mat_name, plant_code, plant_name, transporter_code, transporter_name, veh_number, exquarry_or_delivered, order_load, order_quantity, balance, remarks, status, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.company_code, NEW.company_name, NEW.supplier_code, NEW.supplier_name, NEW.site_code, NEW.site_name, NEW.order_date, NEW.order_no, NEW.po_no, NEW.delivery_date, NEW.agent_code, NEW.agent_name, NEW.destination_code, NEW.destination_name, NEW.deliver_to_name, NEW.raw_mat_code, NEW.raw_mat_name, NEW.plant_code, NEW.plant_name, NEW.transporter_code, NEW.transporter_name, NEW.veh_number, NEW.exquarry_or_delivered, NEW.order_load, NEW.order_quantity, NEW.balance, NEW.remarks, NEW.status, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

-- 31/03/2025 --
ALTER TABLE `Users_Log` ADD `name` VARCHAR(255) NULL AFTER `username`, ADD `useremail` VARCHAR(255) NULL AFTER `name`;

-- 02/04/2025 --
ALTER TABLE `Users_Log` ADD `plant_id` TEXT NULL AFTER `password`;

-- 04/04/2025 --
ALTER TABLE `Weight_Log` ADD `unit_price` VARCHAR(10) NULL AFTER `indicator_id_2`;

DELIMITER $$

CREATE OR REPLACE TRIGGER `TRG_INS_WEIGHT` AFTER INSERT ON `Weight`
 FOR EACH ROW INSERT INTO Weight_Log (
    transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, order_weight, plant_code, plant_name, site_code, site_name, agent_code, agent_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, ex_del, raw_mat_code,raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, weight_different, is_complete, is_cancel, is_approved, manual_weight, indicator_id, weighbridge_id, indicator_id_2, unit_price, sub_total, sst, total_price, load_drum, no_of_drum, status, approved_by, approved_reason, action_id, action_by, event_date
) 
VALUES (
    NEW.transaction_id, NEW.transaction_status, NEW.weight_type, NEW.transaction_date, NEW.lorry_plate_no1, NEW.lorry_plate_no2, NEW.supplier_weight, NEW.order_weight, NEW.plant_code, NEW.plant_name, NEW.site_code, NEW.site_name, NEW.agent_code, NEW.agent_name, NEW.customer_code, NEW.customer_name, NEW.supplier_code, NEW.supplier_name, NEW.product_code, NEW.product_name, NEW.product_description, NEW.ex_del, NEW.raw_mat_code, NEW.raw_mat_name, NEW.container_no, NEW.invoice_no, NEW.purchase_order, NEW.delivery_no, NEW.transporter_code, NEW.transporter, NEW.destination_code, NEW.destination, NEW.remarks, NEW.gross_weight1, NEW.gross_weight1_date, NEW.tare_weight1, NEW.tare_weight1_date, NEW.nett_weight1, NEW.gross_weight2, NEW.gross_weight2_date, NEW.tare_weight2, NEW.tare_weight2_date, NEW.nett_weight2, NEW.reduce_weight, NEW.final_weight, NEW.weight_different, NEW.is_complete, NEW.is_cancel, NEW.is_approved, NEW.manual_weight, NEW.indicator_id, NEW.weighbridge_id, NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.total_price, NEW.load_drum, NEW.no_of_drum, NEW.status, NEW.approved_by, NEW.approved_reason, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_WEIGHT` BEFORE UPDATE ON `Weight`
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
        transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, order_weight, plant_code, plant_name, site_code, site_name, agent_code, agent_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, ex_del, raw_mat_code,raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, weight_different, is_complete, is_cancel, is_approved, manual_weight, indicator_id, weighbridge_id, indicator_id_2, unit_price, sub_total, sst, total_price, load_drum, no_of_drum, status, approved_by, approved_reason, action_id, action_by, event_date
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
        NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.total_price, NEW.load_drum, 
        NEW.no_of_drum, NEW.status, NEW.approved_by, NEW.approved_reason, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ; 

-- 13/04/25 --
ALTER TABLE `Sales_Order` ADD `unit_price` VARCHAR(50) NULL AFTER `balance`, ADD `total_price` VARCHAR(50) NULL AFTER `unit_price`;

ALTER TABLE `Purchase_Order` ADD `unit_price` VARCHAR(50) NULL AFTER `balance`, ADD `total_price` VARCHAR(50) NULL AFTER `unit_price`;

-- 29/04/2025 --
ALTER TABLE `miscellaneous` ADD `code` VARCHAR(50) NULL AFTER `value`;

INSERT INTO `miscellaneous` (`code`, `name`, `value`) VALUES
('destination', 'A', '1'),
('destination', 'B', '1'),
('destination', 'C', '1'),
('destination', 'D', '1'),
('destination', 'E', '1'),
('destination', 'F', '1'),
('destination', 'G', '1'),
('destination', 'H', '1'),
('destination', 'I', '1'),
('destination', 'J', '1'),
('destination', 'K', '1'),
('destination', 'L', '1'),
('destination', 'M', '1'),
('destination', 'N', '1'),
('destination', 'O', '1'),
('destination', 'P', '1'),
('destination', 'Q', '1'),
('destination', 'R', '1'),
('destination', 'S', '1'),
('destination', 'T', '1'),
('destination', 'U', '1'),
('destination', 'V', '1'),
('destination', 'W', '1'),
('destination', 'X', '1'),
('destination', 'Y', '1'),
('destination', 'Z', '1'),
('destination', '0', '1'),
('destination', '1', '1'),
('destination', '2', '1'),
('destination', '3', '1'),
('destination', '4', '1'),
('destination', '5', '1'),
('destination', '6', '1'),
('destination', '7', '1'),
('destination', '8', '1'),
('destination', '9', '1')

-- 06/05/2025 --
ALTER TABLE `Product` ADD `basic_uom` INT(5) NULL AFTER `low`;

ALTER TABLE `Product_Log` ADD `basic_uom` INT(5) NULL AFTER `low`;

CREATE TABLE `Product_UOM` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `unit_id` int(11) DEFAULT NULL,
  `rate` varchar(50) DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `Product_UOM` ADD PRIMARY KEY (`id`);

ALTER TABLE `Product_UOM` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `Raw_Mat` ADD `basic_uom` INT(5) NULL AFTER `low`;

ALTER TABLE `Raw_Mat_Log` ADD `basic_uom` INT(5) NULL AFTER `low`;

CREATE TABLE `Raw_Mat_UOM` (
  `id` int(11) NOT NULL,
  `raw_mat_id` int(11) DEFAULT NULL,
  `unit_id` int(11) DEFAULT NULL,
  `rate` varchar(50) DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `Raw_Mat_UOM` ADD PRIMARY KEY (`id`);

ALTER TABLE `Raw_Mat_UOM` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- 07/05/2025 --
ALTER TABLE `Sales_Order` ADD `converted_order_qty` VARCHAR(100) NULL AFTER `balance`, ADD `converted_balance` VARCHAR(100) NULL AFTER `converted_order_qty`;

ALTER TABLE `Sales_Order` ADD `converted_unit` INT(11) NULL AFTER `converted_balance`;

ALTER TABLE `Sales_Order_Log` ADD `converted_order_qty` VARCHAR(100) NULL AFTER `balance`, ADD `converted_balance` VARCHAR(100) NULL AFTER `converted_order_qty`;

ALTER TABLE `Sales_Order_Log` ADD `converted_unit` INT(11) NULL AFTER `converted_balance`;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_SO` AFTER INSERT ON `Sales_Order`
 FOR EACH ROW INSERT INTO Sales_Order_Log (
    company_code, company_name, customer_code, customer_name, site_code, site_name, order_date, order_no, so_no, delivery_date, agent_code, agent_name, destination_code, destination_name, deliver_to_name, product_code, product_name, plant_code, plant_name, transporter_code, transporter_name, veh_number, exquarry_or_delivered, order_load, order_quantity, balance, converted_order_qty, converted_balance, converted_unit, remarks, status, action_id, action_by, event_date
) 
VALUES (
    NEW.company_code, NEW.company_name, NEW.customer_code, NEW.customer_name, NEW.site_code, NEW.site_name, NEW.order_date, NEW.order_no, NEW.so_no, NEW.delivery_date, NEW.agent_code, NEW.agent_name, NEW.destination_code, NEW.destination_name, NEW.deliver_to_name, NEW.product_code, NEW.product_name, NEW.plant_code, NEW.plant_name, NEW.transporter_code, NEW.transporter_name, NEW.veh_number, NEW.exquarry_or_delivered, NEW.order_load, NEW.order_quantity, NEW.balance, NEW.converted_order_qty, NEW.converted_balance, NEW.converted_unit, NEW.remarks, NEW.status, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_SO` BEFORE UPDATE ON `Sales_Order`
 FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if deleted = 1, set action_id to 3, otherwise set to 2
    IF NEW.deleted = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Sales_Order table
    INSERT INTO Sales_Order_Log (
        company_code, company_name, customer_code, customer_name, site_code, site_name, order_date, order_no, so_no, delivery_date, agent_code, agent_name, destination_code, destination_name, deliver_to_name, product_code, product_name, plant_code, plant_name, transporter_code, transporter_name, veh_number, exquarry_or_delivered, order_load, order_quantity, balance, converted_order_qty, converted_balance, converted_unit, remarks, status, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.company_code, NEW.company_name, NEW.customer_code, NEW.customer_name, NEW.site_code, NEW.site_name, NEW.order_date, NEW.order_no, NEW.so_no, NEW.delivery_date, NEW.agent_code, NEW.agent_name, NEW.destination_code, NEW.destination_name, NEW.deliver_to_name, NEW.product_code, NEW.product_name, NEW.plant_code, NEW.plant_name, NEW.transporter_code, NEW.transporter_name, NEW.veh_number, NEW.exquarry_or_delivered, NEW.order_load, NEW.order_quantity, NEW.balance, NEW.converted_order_qty, NEW.converted_balance, NEW.converted_unit, NEW.remarks, NEW.status, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

ALTER TABLE `Purchase_Order` ADD `converted_order_qty` VARCHAR(100) NULL AFTER `balance`, ADD `converted_balance` VARCHAR(100) NULL AFTER `converted_order_qty`;

ALTER TABLE `Purchase_Order` ADD `converted_unit` INT(11) NULL AFTER `converted_balance`;

ALTER TABLE `Purchase_Order_Log` ADD `converted_order_qty` VARCHAR(100) NULL AFTER `balance`, ADD `converted_balance` VARCHAR(100) NULL AFTER `converted_order_qty`;

ALTER TABLE `Purchase_Order_Log` ADD `converted_unit` INT(11) NULL AFTER `converted_balance`;

DELIMITER $$

CREATE OR REPLACE TRIGGER `TRG_INS_PO` AFTER INSERT ON `Purchase_Order`
 FOR EACH ROW INSERT INTO Purchase_Order_Log (
    company_code, company_name, supplier_code, supplier_name, site_code, site_name, order_date, order_no, po_no, delivery_date, agent_code, agent_name, destination_code, destination_name, deliver_to_name, raw_mat_code, raw_mat_name, plant_code, plant_name, transporter_code, transporter_name, veh_number, exquarry_or_delivered, order_load, order_quantity, balance, converted_order_qty, converted_balance, converted_unit, remarks, status, action_id, action_by, event_date
) 
VALUES (
    NEW.company_code, NEW.company_name, NEW.supplier_code, NEW.supplier_name, NEW.site_code, NEW.site_name, NEW.order_date, NEW.order_no, NEW.po_no, NEW.delivery_date, NEW.agent_code, NEW.agent_name, NEW.destination_code, NEW.destination_name, NEW.deliver_to_name, NEW.raw_mat_code, NEW.raw_mat_name, NEW.plant_code, NEW.plant_name, NEW.transporter_code, NEW.transporter_name, NEW.veh_number, NEW.exquarry_or_delivered, NEW.order_load, NEW.order_quantity, NEW.balance, NEW.converted_order_qty, NEW.converted_balance, NEW.converted_unit, NEW.remarks, NEW.status, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_PO` BEFORE UPDATE ON `Purchase_Order`
 FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if deleted = 1, set action_id to 3, otherwise set to 2
    IF NEW.deleted = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Purchase_Order table
    INSERT INTO Purchase_Order_Log (
        company_code, company_name, supplier_code, supplier_name, site_code, site_name, order_date, order_no, po_no, delivery_date, agent_code,
        agent_name, destination_code, destination_name, deliver_to_name, raw_mat_code, raw_mat_name, plant_code, plant_name, transporter_code, transporter_name, veh_number, exquarry_or_delivered, order_load, order_quantity, balance, converted_order_qty, converted_balance, converted_unit, remarks, status, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.company_code, NEW.company_name, NEW.supplier_code, NEW.supplier_name, NEW.site_code, NEW.site_name, NEW.order_date, NEW.order_no, NEW.po_no, NEW.delivery_date, NEW.agent_code, NEW.agent_name, NEW.destination_code, NEW.destination_name, NEW.deliver_to_name, NEW.raw_mat_code, NEW.raw_mat_name, NEW.plant_code, NEW.plant_name, NEW.transporter_code, NEW.transporter_name, NEW.veh_number, NEW.exquarry_or_delivered, NEW.order_load, NEW.order_quantity, NEW.balance, NEW.converted_order_qty, NEW.converted_balance, NEW.converted_unit, NEW.remarks, NEW.status, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;


ALTER TABLE `Weight` ADD `converted_nett_weight1` VARCHAR(100) NULL AFTER `nett_weight1`;

ALTER TABLE `Weight` ADD `converted_order_weight` VARCHAR(100) NULL AFTER `order_weight`, ADD `converted_order_weight_unit` INT(11) NULL AFTER `converted_order_weight`;

ALTER TABLE `Weight` ADD `converted_supplier_weight` VARCHAR(100) NULL AFTER `supplier_weight`, ADD `converted_supplier_weight_unit` INT(11) NULL AFTER `converted_supplier_weight`;

ALTER TABLE `Weight_Log` ADD `converted_nett_weight1` VARCHAR(100) NULL AFTER `nett_weight1`;

ALTER TABLE `Weight_Log` ADD `converted_order_weight` VARCHAR(100) NULL AFTER `order_weight`, ADD `converted_order_weight_unit` INT(11) NULL AFTER `converted_order_weight`;

ALTER TABLE `Weight_Log` ADD `converted_supplier_weight` VARCHAR(100) NULL AFTER `supplier_weight`, ADD `converted_supplier_weight_unit` INT(11) NULL AFTER `converted_supplier_weight`;

DELIMITER $$

CREATE OR REPLACE TRIGGER `TRG_INS_WEIGHT` AFTER INSERT ON `Weight`
 FOR EACH ROW INSERT INTO Weight_Log (
    transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, converted_supplier_weight, converted_supplier_weight_unit, order_weight, converted_order_weight, converted_order_weight_unit, plant_code, plant_name, site_code, site_name, agent_code, agent_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, ex_del, raw_mat_code,raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1, converted_nett_weight1, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, weight_different, is_complete, is_cancel, is_approved, manual_weight, indicator_id, weighbridge_id, indicator_id_2, unit_price, sub_total, sst, total_price, load_drum, no_of_drum, status, approved_by, approved_reason, action_id, action_by, event_date
) 
VALUES (
    NEW.transaction_id, NEW.transaction_status, NEW.weight_type, NEW.transaction_date, NEW.lorry_plate_no1, NEW.lorry_plate_no2, NEW.supplier_weight, NEW.converted_supplier_weight, NEW.converted_supplier_weight_unit, NEW.order_weight, NEW.converted_order_weight, NEW.converted_order_weight_unit, NEW.plant_code, NEW.plant_name, NEW.site_code, NEW.site_name, NEW.agent_code, NEW.agent_name, NEW.customer_code, NEW.customer_name, NEW.supplier_code, NEW.supplier_name, NEW.product_code, NEW.product_name, NEW.product_description, NEW.ex_del, NEW.raw_mat_code, NEW.raw_mat_name, NEW.container_no, NEW.invoice_no, NEW.purchase_order, NEW.delivery_no, NEW.transporter_code, NEW.transporter, NEW.destination_code, NEW.destination, NEW.remarks, NEW.gross_weight1, NEW.gross_weight1_date, NEW.tare_weight1, NEW.tare_weight1_date, NEW.nett_weight1, NEW.converted_nett_weight1, NEW.gross_weight2, NEW.gross_weight2_date, NEW.tare_weight2, NEW.tare_weight2_date, NEW.nett_weight2, NEW.reduce_weight, NEW.final_weight, NEW.weight_different, NEW.is_complete, NEW.is_cancel, NEW.is_approved, NEW.manual_weight, NEW.indicator_id, NEW.weighbridge_id, NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.total_price, NEW.load_drum, NEW.no_of_drum, NEW.status, NEW.approved_by, NEW.approved_reason, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_WEIGHT` BEFORE UPDATE ON `Weight`
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
        transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, converted_supplier_weight, converted_supplier_weight_unit, order_weight, converted_order_weight, converted_order_weight_unit, plant_code, plant_name, site_code, site_name, agent_code, agent_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, ex_del, raw_mat_code,raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1, converted_nett_weight1, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, weight_different, is_complete, is_cancel, is_approved, manual_weight, indicator_id, weighbridge_id, indicator_id_2, unit_price, sub_total, sst, total_price, load_drum, no_of_drum, status, approved_by, approved_reason, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.transaction_id, NEW.transaction_status, NEW.weight_type, NEW.transaction_date, 
        NEW.lorry_plate_no1, NEW.lorry_plate_no2, NEW.supplier_weight, NEW.converted_supplier_weight, 
        NEW.converted_supplier_weight_unit, NEW.order_weight, NEW.converted_order_weight, NEW.converted_order_weight_unit, 
        NEW.plant_code, NEW.plant_name, NEW.site_code, NEW.site_name, 
        NEW.agent_code, NEW.agent_name, NEW.customer_code, NEW.customer_name, 
        NEW.supplier_code, NEW.supplier_name, NEW.product_code, NEW.product_name, 
        NEW.product_description, NEW.ex_del, NEW.raw_mat_code, NEW.raw_mat_name, 
        NEW.container_no, NEW.invoice_no, NEW.purchase_order, NEW.delivery_no, 
        NEW.transporter_code, NEW.transporter, NEW.destination_code, NEW.destination, 
        NEW.remarks, NEW.gross_weight1, NEW.gross_weight1_date, NEW.tare_weight1, 
        NEW.tare_weight1_date, NEW.nett_weight1, NEW.converted_nett_weight1, NEW.gross_weight2, NEW.gross_weight2_date, 
        NEW.tare_weight2, NEW.tare_weight2_date, NEW.nett_weight2, NEW.reduce_weight, 
        NEW.final_weight, NEW.weight_different, NEW.is_complete, NEW.is_cancel, 
        NEW.is_approved, NEW.manual_weight, NEW.indicator_id, NEW.weighbridge_id, 
        NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.total_price, NEW.load_drum, 
        NEW.no_of_drum, NEW.status, NEW.approved_by, NEW.approved_reason, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

-- 12/05/25 --
ALTER TABLE `Weight` DROP COLUMN `converted_nett_weight1`;

ALTER TABLE `Weight` DROP COLUMN `converted_order_weight`;

ALTER TABLE `Weight` DROP COLUMN `converted_order_weight_unit`;

ALTER TABLE `Weight` DROP COLUMN `converted_supplier_weight`;

ALTER TABLE `Weight`DROP COLUMN `converted_supplier_weight_unit`;

ALTER TABLE `Weight_Log` DROP COLUMN `converted_nett_weight1`;

ALTER TABLE `Weight_Log` DROP COLUMN `converted_order_weight`;

ALTER TABLE `Weight_Log` DROP COLUMN `converted_order_weight_unit`;

ALTER TABLE `Weight_Log` DROP COLUMN `converted_supplier_weight`;

ALTER TABLE `Weight_Log` DROP COLUMN `converted_supplier_weight_unit`;

ALTER TABLE `Weight` ADD `po_supply_weight` VARCHAR(100) NULL AFTER `supplier_weight`;

ALTER TABLE `Weight_Log` ADD `po_supply_weight` VARCHAR(100) NULL AFTER `supplier_weight`;

DELIMITER $$

CREATE OR REPLACE TRIGGER `TRG_INS_WEIGHT` AFTER INSERT ON `Weight`
 FOR EACH ROW INSERT INTO Weight_Log (
    transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, po_supply_weight, order_weight, plant_code, plant_name, site_code, site_name, agent_code, agent_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, ex_del, raw_mat_code,raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, weight_different, is_complete, is_cancel, is_approved, manual_weight, indicator_id, weighbridge_id, indicator_id_2, unit_price, sub_total, sst, total_price, load_drum, no_of_drum, status, approved_by, approved_reason, action_id, action_by, event_date
) 
VALUES (
    NEW.transaction_id, NEW.transaction_status, NEW.weight_type, NEW.transaction_date, NEW.lorry_plate_no1, NEW.lorry_plate_no2, NEW.supplier_weight, NEW.po_supply_weight, NEW.order_weight, NEW.plant_code, NEW.plant_name, NEW.site_code, NEW.site_name, NEW.agent_code, NEW.agent_name, NEW.customer_code, NEW.customer_name, NEW.supplier_code, NEW.supplier_name, NEW.product_code, NEW.product_name, NEW.product_description, NEW.ex_del, NEW.raw_mat_code, NEW.raw_mat_name, NEW.container_no, NEW.invoice_no, NEW.purchase_order, NEW.delivery_no, NEW.transporter_code, NEW.transporter, NEW.destination_code, NEW.destination, NEW.remarks, NEW.gross_weight1, NEW.gross_weight1_date, NEW.tare_weight1, NEW.tare_weight1_date, NEW.nett_weight1, NEW.gross_weight2, NEW.gross_weight2_date, NEW.tare_weight2, NEW.tare_weight2_date, NEW.nett_weight2, NEW.reduce_weight, NEW.final_weight, NEW.weight_different, NEW.is_complete, NEW.is_cancel, NEW.is_approved, NEW.manual_weight, NEW.indicator_id, NEW.weighbridge_id, NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.total_price, NEW.load_drum, NEW.no_of_drum, NEW.status, NEW.approved_by, NEW.approved_reason, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_WEIGHT` BEFORE UPDATE ON `Weight`
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
        transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, po_supply_weight, order_weight, plant_code, plant_name, site_code, site_name, agent_code, agent_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, ex_del, raw_mat_code,raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, weight_different, is_complete, is_cancel, is_approved, manual_weight, indicator_id, weighbridge_id, indicator_id_2, unit_price, sub_total, sst, total_price, load_drum, no_of_drum, status, approved_by, approved_reason, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.transaction_id, NEW.transaction_status, NEW.weight_type, NEW.transaction_date, 
        NEW.lorry_plate_no1, NEW.lorry_plate_no2, NEW.supplier_weight, NEW.po_supply_weight, 
        NEW.order_weight, NEW.plant_code, NEW.plant_name, NEW.site_code, NEW.site_name, 
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
        NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.total_price, NEW.load_drum, 
        NEW.no_of_drum, NEW.status, NEW.approved_by, NEW.approved_reason, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

-- 18/05/2025 -- 
ALTER TABLE `Plant` ADD `default_type` VARCHAR(5) NULL AFTER `do_no`;

ALTER TABLE `Plant_Log` ADD `default_type` VARCHAR(5) NULL AFTER `do_no`;

ALTER TABLE `Weight` ADD `batch_drum` VARCHAR(5) NULL AFTER `no_of_drum`;

ALTER TABLE `Weight_Log` ADD `batch_drum` VARCHAR(5) NULL AFTER `no_of_drum`;

DELIMITER $$

CREATE OR REPLACE TRIGGER `TRG_INS_WEIGHT` AFTER INSERT ON `Weight`
 FOR EACH ROW INSERT INTO Weight_Log (
    transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, po_supply_weight, order_weight, plant_code, plant_name, site_code, site_name, agent_code, agent_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, ex_del, raw_mat_code,raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, weight_different, is_complete, is_cancel, is_approved, manual_weight, indicator_id, weighbridge_id, indicator_id_2, unit_price, sub_total, sst, total_price, load_drum, no_of_drum, batch_drum, status, approved_by, approved_reason, action_id, action_by, event_date
) 
VALUES (
    NEW.transaction_id, NEW.transaction_status, NEW.weight_type, NEW.transaction_date, NEW.lorry_plate_no1, NEW.lorry_plate_no2, NEW.supplier_weight, NEW.po_supply_weight, NEW.order_weight, NEW.plant_code, NEW.plant_name, NEW.site_code, NEW.site_name, NEW.agent_code, NEW.agent_name, NEW.customer_code, NEW.customer_name, NEW.supplier_code, NEW.supplier_name, NEW.product_code, NEW.product_name, NEW.product_description, NEW.ex_del, NEW.raw_mat_code, NEW.raw_mat_name, NEW.container_no, NEW.invoice_no, NEW.purchase_order, NEW.delivery_no, NEW.transporter_code, NEW.transporter, NEW.destination_code, NEW.destination, NEW.remarks, NEW.gross_weight1, NEW.gross_weight1_date, NEW.tare_weight1, NEW.tare_weight1_date, NEW.nett_weight1, NEW.gross_weight2, NEW.gross_weight2_date, NEW.tare_weight2, NEW.tare_weight2_date, NEW.nett_weight2, NEW.reduce_weight, NEW.final_weight, NEW.weight_different, NEW.is_complete, NEW.is_cancel, NEW.is_approved, NEW.manual_weight, NEW.indicator_id, NEW.weighbridge_id, NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.total_price, NEW.load_drum, NEW.no_of_drum, NEW.batch_drum, NEW.status, NEW.approved_by, NEW.approved_reason, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_WEIGHT` BEFORE UPDATE ON `Weight`
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
        transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, po_supply_weight, order_weight, plant_code, plant_name, site_code, site_name, agent_code, agent_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, ex_del, raw_mat_code,raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, weight_different, is_complete, is_cancel, is_approved, manual_weight, indicator_id, weighbridge_id, indicator_id_2, unit_price, sub_total, sst, total_price, load_drum, no_of_drum, batch_drum, status, approved_by, approved_reason, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.transaction_id, NEW.transaction_status, NEW.weight_type, NEW.transaction_date, 
        NEW.lorry_plate_no1, NEW.lorry_plate_no2, NEW.supplier_weight, NEW.po_supply_weight, 
        NEW.order_weight, NEW.plant_code, NEW.plant_name, NEW.site_code, NEW.site_name, 
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
        NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.total_price, NEW.load_drum, 
        NEW.no_of_drum, NEW.batch_drum, NEW.status, NEW.approved_by, NEW.approved_reason, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

-- 04/06/2025 --
ALTER TABLE `Inventory` ADD `raw_mat_basic_uom` VARCHAR(10) NOT NULL DEFAULT '0' AFTER `raw_mat_id`;

ALTER TABLE `Product_RawMat` ADD `raw_mat_basic_uom` VARCHAR(10) NOT NULL DEFAULT '0' AFTER `raw_mat_code`;

CREATE TABLE `Api_Log` (
  `id` int(15) NOT NULL,
  `request` longtext DEFAULT NULL,
  `response` longtext DEFAULT NULL,
  `error_message` longtext DEFAULT NULL,
  `services` varchar(50) DEFAULT NULL,
  `created_datetime` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `Api_Log` ADD PRIMARY KEY (`id`);

ALTER TABLE `Api_Log` MODIFY `id` int(15) NOT NULL AUTO_INCREMENT;

CREATE TABLE `Cronjob_Table` (
  `id` int(11) NOT NULL,
  `cronjob_name` varchar(50) NOT NULL,
  `cronjob_file` text NOT NULL,
  `duration` varchar(10) NOT NULL,
  `unit` varchar(30) NOT NULL,
  `status` int(2) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `Cronjob_Table` ADD PRIMARY KEY (`id`);

ALTER TABLE `Cronjob_Table` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- 07/06/2025 --
ALTER TABLE `Purchase_Order_Log` ADD `unit_price` VARCHAR(50) NULL AFTER `converted_unit`, ADD `total_price` VARCHAR(50) NULL AFTER `unit_price`;

ALTER TABLE `Sales_Order_Log` ADD `unit_price` VARCHAR(50) NULL AFTER `converted_unit`, ADD `total_price` VARCHAR(50) NULL AFTER `unit_price`;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_SO` AFTER INSERT ON `Sales_Order`
 FOR EACH ROW INSERT INTO Sales_Order_Log (
    company_code, company_name, customer_code, customer_name, site_code, site_name, order_date, order_no, so_no, delivery_date, agent_code, agent_name, destination_code, destination_name, deliver_to_name, product_code, product_name, plant_code, plant_name, transporter_code, transporter_name, veh_number, exquarry_or_delivered, order_load, order_quantity, balance, converted_order_qty, converted_balance, converted_unit, unit_price, total_price, remarks, status, action_id, action_by, event_date
) 
VALUES (
    NEW.company_code, NEW.company_name, NEW.customer_code, NEW.customer_name, NEW.site_code, NEW.site_name, NEW.order_date, NEW.order_no, NEW.so_no, NEW.delivery_date, NEW.agent_code, NEW.agent_name, NEW.destination_code, NEW.destination_name, NEW.deliver_to_name, NEW.product_code, NEW.product_name, NEW.plant_code, NEW.plant_name, NEW.transporter_code, NEW.transporter_name, NEW.veh_number, NEW.exquarry_or_delivered, NEW.order_load, NEW.order_quantity, NEW.balance, NEW.converted_order_qty, NEW.converted_balance, NEW.converted_unit, NEW.remarks, NEW.converted_unit, NEW.unit_price, NEW.total_price, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_SO` BEFORE UPDATE ON `Sales_Order`
 FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if deleted = 1, set action_id to 3, otherwise set to 2
    IF NEW.deleted = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Sales_Order table
    INSERT INTO Sales_Order_Log (
        company_code, company_name, customer_code, customer_name, site_code, site_name, order_date, order_no, so_no, delivery_date, agent_code, agent_name, destination_code, destination_name, deliver_to_name, product_code, product_name, plant_code, plant_name, transporter_code, transporter_name, veh_number, exquarry_or_delivered, order_load, order_quantity, balance, converted_order_qty, converted_balance, converted_unit, unit_price, total_price, remarks, status, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.company_code, NEW.company_name, NEW.customer_code, NEW.customer_name, NEW.site_code, NEW.site_name, NEW.order_date, NEW.order_no, NEW.so_no, NEW.delivery_date, NEW.agent_code, NEW.agent_name, NEW.destination_code, NEW.destination_name, NEW.deliver_to_name, NEW.product_code, NEW.product_name, NEW.plant_code, NEW.plant_name, NEW.transporter_code, NEW.transporter_name, NEW.veh_number, NEW.exquarry_or_delivered, NEW.order_load, NEW.order_quantity, NEW.balance, NEW.converted_order_qty, NEW.converted_balance, NEW.converted_unit, NEW.unit_price, NEW.total_price, NEW.remarks, NEW.status, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

DELIMITER $$

CREATE OR REPLACE TRIGGER `TRG_INS_PO` AFTER INSERT ON `Purchase_Order`
 FOR EACH ROW INSERT INTO Purchase_Order_Log (
    company_code, company_name, supplier_code, supplier_name, site_code, site_name, order_date, order_no, po_no, delivery_date, agent_code, agent_name, destination_code, destination_name, deliver_to_name, raw_mat_code, raw_mat_name, plant_code, plant_name, transporter_code, transporter_name, veh_number, exquarry_or_delivered, order_load, order_quantity, balance, converted_order_qty, converted_balance, converted_unit, unit_price, total_price, remarks, status, action_id, action_by, event_date
) 
VALUES (
    NEW.company_code, NEW.company_name, NEW.supplier_code, NEW.supplier_name, NEW.site_code, NEW.site_name, NEW.order_date, NEW.order_no, NEW.po_no, NEW.delivery_date, NEW.agent_code, NEW.agent_name, NEW.destination_code, NEW.destination_name, NEW.deliver_to_name, NEW.raw_mat_code, NEW.raw_mat_name, NEW.plant_code, NEW.plant_name, NEW.transporter_code, NEW.transporter_name, NEW.veh_number, NEW.exquarry_or_delivered, NEW.order_load, NEW.order_quantity, NEW.balance, NEW.converted_order_qty, NEW.converted_balance, NEW.converted_unit, NEW.unit_price, NEW.total_price, NEW.remarks, NEW.status, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_PO` BEFORE UPDATE ON `Purchase_Order`
 FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if deleted = 1, set action_id to 3, otherwise set to 2
    IF NEW.deleted = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Purchase_Order table
    INSERT INTO Purchase_Order_Log (
        company_code, company_name, supplier_code, supplier_name, site_code, site_name, order_date, order_no, po_no, delivery_date, agent_code,
        agent_name, destination_code, destination_name, deliver_to_name, raw_mat_code, raw_mat_name, plant_code, plant_name, transporter_code, transporter_name, veh_number, exquarry_or_delivered, order_load, order_quantity, balance, converted_order_qty, converted_balance, converted_unit, unit_price, total_price, remarks, status, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.company_code, NEW.company_name, NEW.supplier_code, NEW.supplier_name, NEW.site_code, NEW.site_name, NEW.order_date, NEW.order_no, NEW.po_no, NEW.delivery_date, NEW.agent_code, NEW.agent_name, NEW.destination_code, NEW.destination_name, NEW.deliver_to_name, NEW.raw_mat_code, NEW.raw_mat_name, NEW.plant_code, NEW.plant_name, NEW.transporter_code, NEW.transporter_name, NEW.veh_number, NEW.exquarry_or_delivered, NEW.order_load, NEW.order_quantity, NEW.balance, NEW.converted_order_qty, NEW.converted_balance, NEW.converted_unit, NEW.unit_price, NEW.total_price, NEW.remarks, NEW.status, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

ALTER TABLE `Inventory` ADD `created_by` VARCHAR(50) NULL DEFAULT 'SYSTEM' AFTER `plant_code`, ADD `created_date` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP AFTER `created_by`, ADD `modified_by` VARCHAR(50) NULL DEFAULT 'SYSTEM' AFTER `created_date`, ADD `modified_date` TIMESTAMP on update CURRENT_TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP AFTER `modified_by`;


DELIMITER $$

CREATE OR REPLACE TRIGGER TRG_INS_INV
AFTER INSERT ON Raw_Mat
FOR EACH ROW
BEGIN
    -- Insert one inventory row for each plant
    INSERT INTO Inventory (raw_mat_id, plant_code, created_by, modified_by)
    SELECT NEW.id, p.plant_code, NEW.created_by, NEW.modified_by
    FROM Plant p WHERE status = '0';
END$$

DELIMITER $$

CREATE OR REPLACE TRIGGER TRG_INS_INV AFTER INSERT ON Raw_Mat
 FOR EACH ROW BEGIN
    -- Insert one inventory row for each plant
    INSERT INTO Inventory (raw_mat_id, plant_code, created_by, modified_by)
    SELECT NEW.id, p.plant_code, NEW.created_by, NEW.modified_by
    FROM Plant p WHERE status = '0';
END
$$
DELIMITER ;

CREATE TABLE `Inventory_Log` (
  `id` int(5) NOT NULL,
  `inventory_id` int(11) NOT NULL,
  `raw_mat_id` int(5) NOT NULL,
  `raw_mat_basic_uom` varchar(10) DEFAULT NULL,
  `raw_mat_weight` varchar(10) DEFAULT NULL,
  `raw_mat_count` varchar(10) DEFAULT NULL,
  `plant_code` varchar(15) DEFAULT NULL,
  `action_id` int(11) NOT NULL,
  `action_by` varchar(50) NOT NULL,
  `event_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `Inventory_Log` ADD PRIMARY KEY (`id`);
  
ALTER TABLE `Inventory_Log` MODIFY `id` int(5) NOT NULL AUTO_INCREMENT;

DELIMITER $$

CREATE OR REPLACE TRIGGER `TRG_INS_INV_LOG` AFTER INSERT ON `Inventory`
 FOR EACH ROW INSERT INTO Inventory_Log (
    inventory_id, raw_mat_id, raw_mat_basic_uom, raw_mat_weight, raw_mat_count, plant_code, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.raw_mat_id, NEW.raw_mat_basic_uom, NEW.raw_mat_weight, NEW.raw_mat_count, NEW.plant_code, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_INV_LOG` BEFORE UPDATE ON `Inventory`
 FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if deleted = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Sales_Order table
    INSERT INTO Inventory_Log (
        inventory_id, raw_mat_id, raw_mat_basic_uom, raw_mat_weight, raw_mat_count, plant_code, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.raw_mat_id, NEW.raw_mat_basic_uom, NEW.raw_mat_weight, NEW.raw_mat_count, NEW.plant_code, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

-- 22/06/2025 --
ALTER TABLE `Product_Rawmat` ADD `raw_mat_id` INT(11) NULL AFTER `product_id`;

ALTER TABLE `Product_Rawmat` ADD `plant_id` INT(11) NULL AFTER `raw_mat_weight`, ADD `batch_drum` VARCHAR(5) NULL AFTER `plant_id`;

ALTER TABLE `Product_Rawmat` ADD `basic_uom_unit_id` INT(5) NULL AFTER `raw_mat_basic_uom`;

-- 27/06/2025 --
ALTER TABLE `Bitumen` CHANGE `60/70` `60/70` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;

ALTER TABLE `Bitumen` CHANGE `pg76` `pg76` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;

ALTER TABLE `Bitumen` CHANGE `crmb` `crmb` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;

ALTER TABLE `Bitumen` CHANGE `lfo` `lfo` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;

ALTER TABLE `Bitumen` CHANGE `diesel` `diesel` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;

ALTER TABLE `Bitumen` ADD `hotoil` LONGTEXT NULL AFTER `diesel`, ADD `data` LONGTEXT NULL AFTER `hotoil`, ADD `declaration_datetime` DATETIME NULL AFTER `data`, ADD `plant_id` INT(11) NULL AFTER `declaration_datetime`;

ALTER TABLE `Bitumen` ADD `created_by` VARCHAR(100) NULL AFTER `created_datetime`;

ALTER TABLE `Bitumen` ADD `modified_datetime` DATETIME on update CURRENT_TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP AFTER `created_by`, ADD `modified_by` VARCHAR(100) NULL DEFAULT CURRENT_TIMESTAMP AFTER `modified_datetime`;

ALTER TABLE `Bitumen` CHANGE `modified_by` `modified_by` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL;

CREATE TABLE `Bitumen_Log` (
  `id` int(10) NOT NULL,
  `bitumen_id` INT(11) NOT NULL,
  `60/70` longtext DEFAULT NULL,
  `pg76` longtext DEFAULT NULL,
  `crmb` longtext DEFAULT NULL,
  `lfo` longtext DEFAULT NULL,
  `diesel` longtext DEFAULT NULL,
  `hotoil` longtext DEFAULT NULL,
  `data` longtext DEFAULT NULL,
  `declaration_datetime` datetime DEFAULT NULL,
  `plant_id` int(11) DEFAULT NULL,
  `plant_code` varchar(15) DEFAULT NULL,
  `action_id` int(11) NOT NULL,
  `action_by` varchar(50) NOT NULL,
  `event_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `Bitumen_Log` ADD PRIMARY KEY (`id`);

ALTER TABLE `Bitumen_Log` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

DELIMITER $$

CREATE OR REPLACE TRIGGER `TRG_INS_BITUMEN` AFTER INSERT ON `Bitumen`
 FOR EACH ROW INSERT INTO Bitumen_Log (
    bitumen_id, `60/70`, pg76, crmb, lfo, diesel, hotoil, data, declaration_datetime, plant_id, plant_code, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.`60/70`, NEW.pg76, NEW.crmb, NEW.lfo, NEW.diesel, NEW.hotoil, NEW.data, NEW.declaration_datetime, NEW.plant_id, NEW.plant_code, 1, NEW.created_by, NEW.created_datetime
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_BITUMEN` BEFORE UPDATE ON `Bitumen`
 FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if deleted = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Sales_Order table
    INSERT INTO Bitumen_Log (
        bitumen_id, `60/70`, pg76, crmb, lfo, diesel, hotoil, data, declaration_datetime, plant_id, plant_code, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.`60/70`, NEW.pg76, NEW.crmb, NEW.lfo, NEW.diesel, NEW.hotoil, NEW.data, NEW.declaration_datetime, NEW.plant_id, NEW.plant_code, action_value, NEW.modified_by, NEW.modified_datetime
    );
END
$$
DELIMITER ;

-- 29/06/2025 --
CREATE TABLE `Stock_Take_Log` (
  `id` int(11) NOT NULL,
  `declaration_datetime` datetime NOT NULL,
  `plant_id` int(11) DEFAULT NULL,
  `sixty_seventy_production` varchar(50) DEFAULT NULL,
  `sixty_seventy_os` varchar(50) DEFAULT NULL,
  `sixty_seventy_incoming` varchar(50) DEFAULT NULL,
  `sixty_seventy_usage` varchar(50) DEFAULT NULL,
  `sixty_seventy_bookstock` varchar(50) DEFAULT NULL,
  `sixty_seventy_ps` varchar(50) DEFAULT NULL,
  `sixty_seventy_diffstock` varchar(50) DEFAULT NULL,
  `sixty_seventy_actual_usage` varchar(50) DEFAULT NULL,
  `lfo_production` varchar(50) DEFAULT NULL,
  `lfo_os` varchar(50) DEFAULT NULL,
  `lfo_incoming` varchar(50) DEFAULT NULL,
  `lfo_ps` varchar(50) DEFAULT NULL,
  `lfo_usage` varchar(50) DEFAULT NULL,
  `lfo_actual_usage` varchar(50) DEFAULT NULL,
  `diesel_production` varchar(50) DEFAULT NULL,
  `diesel_os` varchar(50) DEFAULT NULL,
  `diesel_incoming` varchar(50) DEFAULT NULL,
  `diesel_mreading` varchar(50) DEFAULT NULL,
  `diesel_transport` varchar(50) DEFAULT NULL,
  `diesel_ps` varchar(50) DEFAULT NULL,
  `diesel_usage` varchar(50) DEFAULT NULL,
  `diesel_actual_usage` varchar(50) DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `Stock_Take_Log`  ADD PRIMARY KEY (`id`);

ALTER TABLE `Stock_Take_Log` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `Inventory` ADD `plant_id` INT(11) NOT NULL AFTER `raw_mat_count`;

ALTER TABLE `Inventory_Log` ADD `plant_id` INT(11) NOT NULL AFTER `raw_mat_count`;

DELIMITER $$

CREATE OR REPLACE TRIGGER TRG_INS_INV
AFTER INSERT ON Raw_Mat
FOR EACH ROW
BEGIN
    -- Insert one inventory row for each plant
    INSERT INTO Inventory (raw_mat_id, plant_id, plant_code, created_by, modified_by)
    SELECT NEW.id, p.id, p.plant_code, NEW.created_by, NEW.modified_by
    FROM Plant p WHERE status = '0';
END
$$
DELIMITER ;

DELIMITER $$

CREATE OR REPLACE TRIGGER `TRG_INS_INV_LOG` AFTER INSERT ON `Inventory`
 FOR EACH ROW INSERT INTO Inventory_Log (
    inventory_id, raw_mat_id, raw_mat_basic_uom, raw_mat_weight, raw_mat_count, plant_id, plant_code, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.raw_mat_id, NEW.raw_mat_basic_uom, NEW.raw_mat_weight, NEW.raw_mat_count, NEW.plant_id, NEW.plant_code, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_INV_LOG` BEFORE UPDATE ON `Inventory`
 FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if deleted = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Sales_Order table
    INSERT INTO Inventory_Log (
        inventory_id, raw_mat_id, raw_mat_basic_uom, raw_mat_weight, raw_mat_count, plant_id, plant_code, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.raw_mat_id, NEW.raw_mat_basic_uom, NEW.raw_mat_weight, NEW.raw_mat_count, NEW.plant_id, NEW.plant_code, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

UPDATE Inventory i
LEFT JOIN Plant p ON i.plant_code = p.plant_code
SET i.plant_id = p.id
WHERE i.plant_code IS NOT NULL;

UPDATE Inventory_Log i
LEFT JOIN Plant p ON i.plant_code COLLATE utf8mb4_unicode_ci = p.plant_code COLLATE utf8mb4_unicode_ci
SET i.plant_id = p.id
WHERE i.plant_code IS NOT NULL;

ALTER TABLE `Stock_Take_Log` RENAME TO `Stock_Take`;

-- 13/07/2025 --
CREATE TABLE `Stock_Take_Log` (
    `id` int(11) NOT NULL,
    `stock_take_id` int(11) NOT NULL,
    `declaration_datetime` datetime NOT NULL,
    `plant_id` int(11) DEFAULT NULL,
    `sixty_seventy_production` varchar(50) DEFAULT NULL,
    `sixty_seventy_os` varchar(50) DEFAULT NULL,
    `sixty_seventy_incoming` varchar(50) DEFAULT NULL,
    `sixty_seventy_usage` varchar(50) DEFAULT NULL,
    `sixty_seventy_bookstock` varchar(50) DEFAULT NULL,
    `sixty_seventy_ps` varchar(50) DEFAULT NULL,
    `sixty_seventy_diffstock` varchar(50) DEFAULT NULL,
    `sixty_seventy_actual_usage` varchar(50) DEFAULT NULL,
    `lfo_production` varchar(50) DEFAULT NULL,
    `lfo_os` varchar(50) DEFAULT NULL,
    `lfo_incoming` varchar(50) DEFAULT NULL,
    `lfo_ps` varchar(50) DEFAULT NULL,
    `lfo_usage` varchar(50) DEFAULT NULL,
    `lfo_actual_usage` varchar(50) DEFAULT NULL,
    `diesel_production` varchar(50) DEFAULT NULL,
    `diesel_os` varchar(50) DEFAULT NULL,
    `diesel_incoming` varchar(50) DEFAULT NULL,
    `diesel_mreading` varchar(50) DEFAULT NULL,
    `diesel_transport` varchar(50) DEFAULT NULL,
    `diesel_ps` varchar(50) DEFAULT NULL,
    `diesel_usage` varchar(50) DEFAULT NULL,
    `diesel_actual_usage` varchar(50) DEFAULT NULL,
    `action_id` int(11) NOT NULL,
    `action_by` varchar(50) NOT NULL,
    `event_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `Stock_Take_Log`  ADD PRIMARY KEY (`id`);

ALTER TABLE `Stock_Take_Log` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

DELIMITER $$

-- INSERT TRIGGER
CREATE OR REPLACE TRIGGER `TRG_INS_STK_TAKE_LOG` AFTER INSERT ON `Stock_Take`
FOR EACH ROW
INSERT INTO Stock_Take_Log (
    stock_take_id,
    declaration_datetime,
    plant_id,
    sixty_seventy_production,
    sixty_seventy_os,
    sixty_seventy_incoming,
    sixty_seventy_usage,
    sixty_seventy_bookstock,
    sixty_seventy_ps,
    sixty_seventy_diffstock,
    sixty_seventy_actual_usage,
    lfo_production,
    lfo_os,
    lfo_incoming,
    lfo_ps,
    lfo_usage,
    lfo_actual_usage,
    diesel_production,
    diesel_os,
    diesel_incoming,
    diesel_mreading,
    diesel_transport,
    diesel_ps,
    diesel_usage,
    diesel_actual_usage,
    action_id,
    action_by
)
VALUES (
    NEW.id,
    NEW.declaration_datetime,
    NEW.plant_id,
    NEW.sixty_seventy_production,
    NEW.sixty_seventy_os,
    NEW.sixty_seventy_incoming,
    NEW.sixty_seventy_usage,
    NEW.sixty_seventy_bookstock,
    NEW.sixty_seventy_ps,
    NEW.sixty_seventy_diffstock,
    NEW.sixty_seventy_actual_usage,
    NEW.lfo_production,
    NEW.lfo_os,
    NEW.lfo_incoming,
    NEW.lfo_ps,
    NEW.lfo_usage,
    NEW.lfo_actual_usage,
    NEW.diesel_production,
    NEW.diesel_os,
    NEW.diesel_incoming,
    NEW.diesel_mreading,
    NEW.diesel_transport,
    NEW.diesel_ps,
    NEW.diesel_usage,
    NEW.diesel_actual_usage,
    1,
    'SYSTEM'
);
$$

-- UPDATE TRIGGER
CREATE OR REPLACE TRIGGER `TRG_UPD_STK_TAKE_LOG` BEFORE UPDATE ON `Stock_Take`
FOR EACH ROW
BEGIN
    DECLARE action_value INT;

    -- Check if deleted = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    INSERT INTO Stock_Take_Log (
        stock_take_id,
        declaration_datetime,
        plant_id,
        sixty_seventy_production,
        sixty_seventy_os,
        sixty_seventy_incoming,
        sixty_seventy_usage,
        sixty_seventy_bookstock,
        sixty_seventy_ps,
        sixty_seventy_diffstock,
        sixty_seventy_actual_usage,
        lfo_production,
        lfo_os,
        lfo_incoming,
        lfo_ps,
        lfo_usage,
        lfo_actual_usage,
        diesel_production,
        diesel_os,
        diesel_incoming,
        diesel_mreading,
        diesel_transport,
        diesel_ps,
        diesel_usage,
        diesel_actual_usage,
        action_id,
        action_by
    )
    VALUES (
        NEW.id,
        NEW.declaration_datetime,
        NEW.plant_id,
        NEW.sixty_seventy_production,
        NEW.sixty_seventy_os,
        NEW.sixty_seventy_incoming,
        NEW.sixty_seventy_usage,
        NEW.sixty_seventy_bookstock,
        NEW.sixty_seventy_ps,
        NEW.sixty_seventy_diffstock,
        NEW.sixty_seventy_actual_usage,
        NEW.lfo_production,
        NEW.lfo_os,
        NEW.lfo_incoming,
        NEW.lfo_ps,
        NEW.lfo_usage,
        NEW.lfo_actual_usage,
        NEW.diesel_production,
        NEW.diesel_os,
        NEW.diesel_incoming,
        NEW.diesel_mreading,
        NEW.diesel_transport,
        NEW.diesel_ps,
        NEW.diesel_usage,
        NEW.diesel_actual_usage,
        action_value,
        'SYSTEM'
    );
END
$$

DELIMITER ;

-- 27/07/2025 --
ALTER TABLE `Weight` ADD `tin_no` VARCHAR(100) NULL AFTER `order_weight`, ADD `id_no` VARCHAR(100) NULL AFTER `tin_no`, ADD `id_type` VARCHAR(100) NULL AFTER `id_no`;

ALTER TABLE `Weight_Log` ADD `tin_no` VARCHAR(100) NULL AFTER `order_weight`, ADD `id_no` VARCHAR(100) NULL AFTER `tin_no`, ADD `id_type` VARCHAR(100) NULL AFTER `id_no`;

DELIMITER $$

CREATE OR REPLACE TRIGGER `TRG_INS_WEIGHT` AFTER INSERT ON `Weight`
 FOR EACH ROW INSERT INTO Weight_Log (
    transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, po_supply_weight, order_weight, tin_no, id_no, id_type, plant_code, plant_name, site_code, site_name, agent_code, agent_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, ex_del, raw_mat_code,raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, weight_different, is_complete, is_cancel, is_approved, manual_weight, indicator_id, weighbridge_id, indicator_id_2, unit_price, sub_total, sst, total_price, load_drum, no_of_drum, batch_drum, status, approved_by, approved_reason, action_id, action_by, event_date
) 
VALUES (
    NEW.transaction_id, NEW.transaction_status, NEW.weight_type, NEW.transaction_date, NEW.lorry_plate_no1, NEW.lorry_plate_no2, NEW.supplier_weight, NEW.po_supply_weight, NEW.order_weight, NEW.tin_no, NEW.id_no, NEW.id_type, NEW.plant_code, NEW.plant_name, NEW.site_code, NEW.site_name, NEW.agent_code, NEW.agent_name, NEW.customer_code, NEW.customer_name, NEW.supplier_code, NEW.supplier_name, NEW.product_code, NEW.product_name, NEW.product_description, NEW.ex_del, NEW.raw_mat_code, NEW.raw_mat_name, NEW.container_no, NEW.invoice_no, NEW.purchase_order, NEW.delivery_no, NEW.transporter_code, NEW.transporter, NEW.destination_code, NEW.destination, NEW.remarks, NEW.gross_weight1, NEW.gross_weight1_date, NEW.tare_weight1, NEW.tare_weight1_date, NEW.nett_weight1, NEW.gross_weight2, NEW.gross_weight2_date, NEW.tare_weight2, NEW.tare_weight2_date, NEW.nett_weight2, NEW.reduce_weight, NEW.final_weight, NEW.weight_different, NEW.is_complete, NEW.is_cancel, NEW.is_approved, NEW.manual_weight, NEW.indicator_id, NEW.weighbridge_id, NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.total_price, NEW.load_drum, NEW.no_of_drum, NEW.batch_drum, NEW.status, NEW.approved_by, NEW.approved_reason, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_WEIGHT` BEFORE UPDATE ON `Weight`
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
        transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, po_supply_weight, order_weight, tin_no, id_no, id_type, plant_code, plant_name, site_code, site_name, agent_code, agent_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, ex_del, raw_mat_code,raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, weight_different, is_complete, is_cancel, is_approved, manual_weight, indicator_id, weighbridge_id, indicator_id_2, unit_price, sub_total, sst, total_price, load_drum, no_of_drum, batch_drum, status, approved_by, approved_reason, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.transaction_id, NEW.transaction_status, NEW.weight_type, NEW.transaction_date, 
        NEW.lorry_plate_no1, NEW.lorry_plate_no2, NEW.supplier_weight, NEW.po_supply_weight, 
        NEW.order_weight, NEW.tin_no, NEW.id_no, NEW.id_type, NEW.plant_code, NEW.plant_name, NEW.site_code, NEW.site_name, 
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
        NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.total_price, NEW.load_drum, 
        NEW.no_of_drum, NEW.batch_drum, NEW.status, NEW.approved_by, NEW.approved_reason, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

-- 29/07/2025--
ALTER TABLE `Bitumen` ADD `fibre` LONGTEXT NULL AFTER `hotoil`;

ALTER TABLE `Bitumen_Log` ADD `fibre` LONGTEXT NULL AFTER `hotoil`;

DELIMITER $$

CREATE OR REPLACE TRIGGER `TRG_INS_BITUMEN` AFTER INSERT ON `Bitumen`
 FOR EACH ROW INSERT INTO Bitumen_Log (
    bitumen_id, `60/70`, pg76, crmb, lfo, diesel, hotoil, fibre, data, declaration_datetime, plant_id, plant_code, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.`60/70`, NEW.pg76, NEW.crmb, NEW.lfo, NEW.diesel, NEW.hotoil, NEW.fibre, NEW.data, NEW.declaration_datetime, NEW.plant_id, NEW.plant_code, 1, NEW.created_by, NEW.created_datetime
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_BITUMEN` BEFORE UPDATE ON `Bitumen`
 FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if deleted = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Bitumen_Log table
    INSERT INTO Bitumen_Log (
        bitumen_id, `60/70`, pg76, crmb, lfo, diesel, hotoil, fibre, data, declaration_datetime, plant_id, plant_code, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.`60/70`, NEW.pg76, NEW.crmb, NEW.lfo, NEW.diesel, NEW.hotoil, NEW.fibre, NEW.data, NEW.declaration_datetime, NEW.plant_id, NEW.plant_code, action_value, NEW.modified_by, NEW.modified_datetime
    );
END
$$
DELIMITER ;

-- 10/08/2025 --
-- Agent Log Trigger Creation
DELIMITER $$

CREATE OR REPLACE TRIGGER `TRG_INS_AGENT` AFTER INSERT ON `Agents`
 FOR EACH ROW INSERT INTO Agents_Log (
    agent_id, agent_code, name, description, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.agent_code, NEW.name, NEW.description, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_AGENT` BEFORE UPDATE ON `Agents`
 FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if deleted = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Agents_Log table
    INSERT INTO Agents_Log (
        agent_id, agent_code, name, description, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.agent_code, NEW.name, NEW.description, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

-- Customer Log Trigger Creation
DELIMITER $$

CREATE OR REPLACE TRIGGER `TRG_INS_CUSTOMER` AFTER INSERT ON `Customer`
 FOR EACH ROW INSERT INTO Customer_Log (
    customer_id, customer_code, company_reg_no, name, address_line_1, address_line_2, address_line_3, address_line_4, phone_no, fax_no, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.customer_code, NEW.company_reg_no, NEW.name, NEW.address_line_1, NEW.address_line_2, NEW.address_line_3, NEW.address_line_4, NEW.phone_no, NEW.fax_no, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_CUSTOMER` BEFORE UPDATE ON `Customer`
 FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if deleted = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Customer_Log table
    INSERT INTO Customer_Log (
        customer_id,  customer_code, company_reg_no, name, address_line_1, address_line_2, address_line_3, address_line_4, phone_no, fax_no, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.customer_code, NEW.company_reg_no, NEW.name, NEW.address_line_1, NEW.address_line_2, NEW.address_line_3, NEW.address_line_4, NEW.phone_no, NEW.fax_no, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

-- Destination Log Trigger Creation
DELIMITER $$

CREATE OR REPLACE TRIGGER `TRG_INS_DESTINATION` AFTER INSERT ON `Destination`
 FOR EACH ROW INSERT INTO Destination_Log (
    destination_id, destination_code, name, description, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.destination_code, NEW.name, NEW.description, 1, NEW.created_by, NEW.created_date
)

$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_DESTINATION` BEFORE UPDATE ON `Destination`
 FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if deleted = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Destination_Log table
    INSERT INTO Destination_Log (
        destination_id, destination_code, name, description, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.destination_code, NEW.name, NEW.description, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

-- Product Log Trigger Creation
DELIMITER $$

CREATE OR REPLACE TRIGGER `TRG_INS_PRODUCT` AFTER INSERT ON `Product`
 FOR EACH ROW INSERT INTO Product_Log (
    product_id, product_code, name, description, price, variance, high, low, basic_uom, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.product_code, NEW.name, NEW.description, NEW.price, NEW.variance, NEW.high, NEW.low, NEW.basic_uom, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_PRODUCT` BEFORE UPDATE ON `Product`
 FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if deleted = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Product_Log table
    INSERT INTO Product_Log (
        product_id, product_code, name, description, price, variance, high, low, basic_uom, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.product_code, NEW.name, NEW.description, NEW.price, NEW.variance, NEW.high, NEW.low, NEW.basic_uom, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

-- Raw Material Log Trigger Creation
DELIMITER $$

CREATE OR REPLACE TRIGGER `TRG_INS_RAWMAT` AFTER INSERT ON `Raw_Mat`
 FOR EACH ROW INSERT INTO Raw_Mat_Log (
    raw_mat_id, raw_mat_code, name, description, price, variance, high, low, basic_uom, type, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.raw_mat_code, NEW.name, NEW.description, NEW.price, NEW.variance, NEW.high, NEW.low, NEW.basic_uom, NEW.type, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_RAWMAT` BEFORE UPDATE ON `Raw_Mat`
 FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if deleted = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Raw_Mat_Log table
    INSERT INTO Raw_Mat_Log (
        raw_mat_id, raw_mat_code, name, description, price, variance, high, low, basic_uom, type, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.raw_mat_code, NEW.name, NEW.description, NEW.price, NEW.variance, NEW.high, NEW.low, NEW.basic_uom, NEW.type, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

-- Supplier Log Trigger Creation
DELIMITER $$

CREATE OR REPLACE TRIGGER `TRG_INS_SUPPLIER` AFTER INSERT ON `Supplier`
 FOR EACH ROW INSERT INTO Supplier_Log (
    supplier_id, supplier_code, company_reg_no, name, address_line_1, address_line_2, address_line_3, address_line_4, phone_no, fax_no, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.supplier_code, NEW.company_reg_no, NEW.name, NEW.address_line_1, NEW.address_line_2, NEW.address_line_3, NEW.address_line_4, NEW.phone_no, NEW.fax_no, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_SUPPLIER` BEFORE UPDATE ON `Supplier`
 FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if deleted = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Supplier_Log table
    INSERT INTO Supplier_Log (
        supplier_id, supplier_code, company_reg_no, name, address_line_1, address_line_2, address_line_3, address_line_4, phone_no, fax_no, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.supplier_code, NEW.company_reg_no, NEW.name, NEW.address_line_1, NEW.address_line_2, NEW.address_line_3, NEW.address_line_4, NEW.phone_no, NEW.fax_no, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

-- Vehicle Log Trigger Creation
DELIMITER $$

CREATE OR REPLACE TRIGGER `TRG_INS_VEHICLE` AFTER INSERT ON `Vehicle`
 FOR EACH ROW INSERT INTO Vehicle_Log (
    vehicle_id, veh_number, vehicle_weight, transporter_code, transporter_name, ex_del, customer_code, customer_name, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.veh_number, NEW.vehicle_weight, NEW.transporter_code, NEW.transporter_name, NEW.ex_del, NEW.customer_code, NEW.customer_name, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_VEHICLE` BEFORE UPDATE ON `Vehicle`
 FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if deleted = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Vehicle_Log table
    INSERT INTO Vehicle_Log (
        vehicle_id, veh_number, vehicle_weight, transporter_code, transporter_name, ex_del, customer_code, customer_name, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.veh_number, NEW.vehicle_weight, NEW.transporter_code, NEW.transporter_name, NEW.ex_del, NEW.customer_code, NEW.customer_name, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

-- Unit Log Trigger Creation
DELIMITER $$

CREATE OR REPLACE TRIGGER `TRG_INS_UNIT` AFTER INSERT ON `Unit`
 FOR EACH ROW INSERT INTO Unit_Log (
    unit_id, unit, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.unit, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_UNIT` BEFORE UPDATE ON `Unit`
 FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if deleted = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Unit_Log table
    INSERT INTO Unit_Log (
        unit_id, unit, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.unit, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

-- User Log Trigger Creation
DELIMITER $$

CREATE OR REPLACE TRIGGER `TRG_INS_USERS` AFTER INSERT ON `Users`
 FOR EACH ROW INSERT INTO Users_Log (
    user_id, employee_code, username, name, useremail, status, password, plant_id, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.employee_code, NEW.username, NEW.name, NEW.useremail, NEW.status, NEW.password, NEW.plant_id, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_USERS` BEFORE UPDATE ON `Users`
 FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if deleted = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Users_Log table
    INSERT INTO Users_Log (
        user_id, employee_code, username, name, useremail, status, password, plant_id, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.employee_code, NEW.username, NEW.name, NEW.useremail, NEW.status, NEW.password, NEW.plant_id, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

-- Plant Log Trigger Creation
DELIMITER $$

CREATE OR REPLACE TRIGGER `TRG_INS_PLANT` AFTER INSERT ON `Plant`
 FOR EACH ROW INSERT INTO Plant_Log (
    plant_id, plant_code, name, address_line_1, address_line_2, address_line_3, phone_no, fax_no, sales, purchase, locals, do_no, default_type, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.plant_code, NEW.name, NEW.address_line_1, NEW.address_line_2, NEW.address_line_3, NEW.phone_no, NEW.fax_no, NEW.sales, NEW.purchase, NEW.locals, NEW.do_no, NEW.default_type, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_PLANT` BEFORE UPDATE ON `Plant`
 FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if deleted = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Plant_Log table
    INSERT INTO Plant_Log (
        plant_id, plant_code, name, address_line_1, address_line_2, address_line_3, phone_no, fax_no, sales, purchase, locals, do_no, default_type, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.plant_code, NEW.name, NEW.address_line_1, NEW.address_line_2, NEW.address_line_3, NEW.phone_no, NEW.fax_no, NEW.sales, NEW.purchase, NEW.locals, NEW.do_no, NEW.default_type, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

-- 16/08/2025 --
ALTER TABLE `Bitumen` ADD `batch_drum` VARCHAR(10) NULL AFTER `plant_code`;

ALTER TABLE `Bitumen_Log` ADD `batch_drum` LONGTEXT NULL AFTER `plant_code`;

DELIMITER $$

CREATE OR REPLACE TRIGGER `TRG_INS_BITUMEN` AFTER INSERT ON `Bitumen`
 FOR EACH ROW INSERT INTO Bitumen_Log (
    bitumen_id, `60/70`, pg76, crmb, lfo, diesel, hotoil, fibre, data, declaration_datetime, plant_id, plant_code, batch_drum, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.`60/70`, NEW.pg76, NEW.crmb, NEW.lfo, NEW.diesel, NEW.hotoil, NEW.fibre, NEW.data, NEW.declaration_datetime, NEW.plant_id, NEW.plant_code, NEW.batch_drum, 1, NEW.created_by, NEW.created_datetime
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_BITUMEN` BEFORE UPDATE ON `Bitumen`
 FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if deleted = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Bitumen_Log table
    INSERT INTO Bitumen_Log (
        bitumen_id, `60/70`, pg76, crmb, lfo, diesel, hotoil, fibre, data, declaration_datetime, plant_id, plant_code, batch_drum, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.`60/70`, NEW.pg76, NEW.crmb, NEW.lfo, NEW.diesel, NEW.hotoil, NEW.fibre, NEW.data, NEW.declaration_datetime, NEW.plant_id, NEW.plant_code, NEW.batch_drum, action_value, NEW.modified_by, NEW.modified_datetime
    );
END
$$
DELIMITER ;

-- 28/08/2025 --
CREATE TABLE `Reasons` (
    `id` int(5) NOT NULL,
    `reason` varchar(100) NOT NULL,
    `created_date` datetime NOT NULL DEFAULT current_timestamp(),
    `created_by` varchar(50) NOT NULL,
    `modified_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `modified_by` varchar(50) NOT NULL,
    `status` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `Reasons` ADD PRIMARY KEY (`id`);

ALTER TABLE `Reasons` MODIFY `id` int(5) NOT NULL AUTO_INCREMENT;

CREATE TABLE `Reasons_Log` (
    `id` int(5) NOT NULL,
    `reason_id` int(11) NOT NULL,
    `reason` varchar(100) NOT NULL,
    `action_id` int(11) NOT NULL,
    `action_by` varchar(50) NOT NULL,
    `event_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `Reasons_Log` ADD PRIMARY KEY (`id`);

ALTER TABLE `Reasons_Log` MODIFY `id` int(5) NOT NULL AUTO_INCREMENT;

-- Reason Log Trigger Creation
DELIMITER $$

CREATE OR REPLACE TRIGGER `TRG_INS_REASON` AFTER INSERT ON `Reasons`
 FOR EACH ROW INSERT INTO Reasons_Log (
    reason_id, reason, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.reason, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_REASON` BEFORE UPDATE ON `Reasons`
 FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if deleted = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Reasons_Log table
    INSERT INTO Reasons_Log (
        reason_id, reason, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.reason, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

ALTER TABLE `Weight` ADD `cancel_id` INT(11) NULL AFTER `approved_reason`;

ALTER TABLE `Weight_Log` ADD `cancel_id` INT(11) NULL AFTER `approved_reason`;

ALTER TABLE `Weight_Log` ADD `cancelled_reason` text NULL AFTER `cancel_id`;

DELIMITER $$

CREATE OR REPLACE TRIGGER `TRG_INS_WEIGHT` AFTER INSERT ON `Weight`
 FOR EACH ROW INSERT INTO Weight_Log (
    transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, po_supply_weight, order_weight, tin_no, id_no, id_type, plant_code, plant_name, site_code, site_name, agent_code, agent_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, ex_del, raw_mat_code,raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, is_complete, is_cancel, is_approved, manual_weight, indicator_id, weighbridge_id, indicator_id_2, unit_price, sub_total, sst, total_price, load_drum, no_of_drum, batch_drum, status, approved_by, approved_reason, cancel_id, cancelled_reason, action_id, action_by, event_date
) 
VALUES (
    NEW.transaction_id, NEW.transaction_status, NEW.weight_type, NEW.transaction_date, NEW.lorry_plate_no1, NEW.lorry_plate_no2, NEW.supplier_weight, NEW.po_supply_weight, NEW.order_weight, NEW.tin_no, NEW.id_no, NEW.id_type, NEW.plant_code, NEW.plant_name, NEW.site_code, NEW.site_name, NEW.agent_code, NEW.agent_name, NEW.customer_code, NEW.customer_name, NEW.supplier_code, NEW.supplier_name, NEW.product_code, NEW.product_name, NEW.product_description, NEW.ex_del, NEW.raw_mat_code, NEW.raw_mat_name, NEW.container_no, NEW.invoice_no, NEW.purchase_order, NEW.delivery_no, NEW.transporter_code, NEW.transporter, NEW.destination_code, NEW.destination, NEW.remarks, NEW.gross_weight1, NEW.gross_weight1_date, NEW.tare_weight1, NEW.tare_weight1_date, NEW.nett_weight1, NEW.gross_weight2, NEW.gross_weight2_date, NEW.tare_weight2, NEW.tare_weight2_date, NEW.nett_weight2, NEW.reduce_weight, NEW.final_weight, NEW.is_complete, NEW.is_cancel, NEW.is_approved, NEW.manual_weight, NEW.indicator_id, NEW.weighbridge_id, NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.total_price, NEW.load_drum, NEW.no_of_drum, NEW.batch_drum, NEW.status, NEW.approved_by, NEW.approved_reason, NEW.cancel_id, NEW.cancelled_reason, 1, NEW.created_by, NEW.created_date
)

$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_WEIGHT` BEFORE UPDATE ON `Weight`
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
        transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, po_supply_weight, order_weight, tin_no, id_no, id_type, plant_code, plant_name, site_code, site_name, agent_code, agent_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, ex_del, raw_mat_code,raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, weight_different, is_complete, is_cancel, is_approved, manual_weight, indicator_id, weighbridge_id, indicator_id_2, unit_price, sub_total, sst, total_price, load_drum, no_of_drum, batch_drum, status, approved_by, approved_reason, cancel_id, cancelled_reason, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.transaction_id, NEW.transaction_status, NEW.weight_type, NEW.transaction_date, 
        NEW.lorry_plate_no1, NEW.lorry_plate_no2, NEW.supplier_weight, NEW.po_supply_weight, 
        NEW.order_weight, NEW.tin_no, NEW.id_no, NEW.id_type, NEW.plant_code, NEW.plant_name, NEW.site_code, NEW.site_name, 
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
        NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.total_price, NEW.load_drum, 
        NEW.no_of_drum, NEW.batch_drum, NEW.status, NEW.approved_by, NEW.approved_reason, NEW.cancel_id, NEW.cancelled_reason, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

-- 16/09/2025 --
ALTER TABLE `Product` ADD `type` VARCHAR(10) NOT NULL DEFAULT 'Premix' AFTER `basic_uom`;

ALTER TABLE `Product_Log` ADD `type` VARCHAR(10) NULL AFTER `basic_uom`;

DELIMITER $$

CREATE OR REPLACE TRIGGER `TRG_INS_PRODUCT` AFTER INSERT ON `Product`
 FOR EACH ROW INSERT INTO Product_Log (
    product_id, product_code, name, description, price, variance, high, low, basic_uom, type, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.product_code, NEW.name, NEW.description, NEW.price, NEW.variance, NEW.high, NEW.low, NEW.basic_uom, NEW.type, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_PRODUCT` BEFORE UPDATE ON `Product`
 FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if deleted = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Product_Log table
    INSERT INTO Product_Log (
        product_id, product_code, name, description, price, variance, high, low, basic_uom, type, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.product_code, NEW.name, NEW.description, NEW.price, NEW.variance, NEW.high, NEW.low, NEW.basic_uom, NEW.type, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

-- 27/09/2025 --
ALTER TABLE `Weight` ADD `balance` VARCHAR(100) NULL AFTER `weight_different`;

ALTER TABLE `Weight_Log` ADD `balance` VARCHAR(100) NULL AFTER `weight_different`;

DELIMITER $$

CREATE OR REPLACE TRIGGER `TRG_INS_WEIGHT` AFTER INSERT ON `Weight`
 FOR EACH ROW INSERT INTO Weight_Log (
    transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, po_supply_weight, order_weight, tin_no, id_no, id_type, plant_code, plant_name, site_code, site_name, agent_code, agent_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, ex_del, raw_mat_code,raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, weight_different, balance, is_complete, is_cancel, is_approved, manual_weight, indicator_id, weighbridge_id, indicator_id_2, unit_price, sub_total, sst, total_price, load_drum, no_of_drum, batch_drum, status, approved_by, approved_reason, cancel_id, cancelled_reason, action_id, action_by, event_date
) 
VALUES (
    NEW.transaction_id, NEW.transaction_status, NEW.weight_type, NEW.transaction_date, NEW.lorry_plate_no1, NEW.lorry_plate_no2, NEW.supplier_weight, NEW.po_supply_weight, NEW.order_weight, NEW.tin_no, NEW.id_no, NEW.id_type, NEW.plant_code, NEW.plant_name, NEW.site_code, NEW.site_name, NEW.agent_code, NEW.agent_name, NEW.customer_code, NEW.customer_name, NEW.supplier_code, NEW.supplier_name, NEW.product_code, NEW.product_name, NEW.product_description, NEW.ex_del, NEW.raw_mat_code, NEW.raw_mat_name, NEW.container_no, NEW.invoice_no, NEW.purchase_order, NEW.delivery_no, NEW.transporter_code, NEW.transporter, NEW.destination_code, NEW.destination, NEW.remarks, NEW.gross_weight1, NEW.gross_weight1_date, NEW.tare_weight1, NEW.tare_weight1_date, NEW.nett_weight1, NEW.gross_weight2, NEW.gross_weight2_date, NEW.tare_weight2, NEW.tare_weight2_date, NEW.nett_weight2, NEW.reduce_weight, NEW.final_weight, NEW.weight_different, NEW.balance, NEW.is_complete, NEW.is_cancel, NEW.is_approved, NEW.manual_weight, NEW.indicator_id, NEW.weighbridge_id, NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.total_price, NEW.load_drum, NEW.no_of_drum, NEW.batch_drum, NEW.status, NEW.approved_by, NEW.approved_reason, NEW.cancel_id, NEW.cancelled_reason, 1, NEW.created_by, NEW.created_date
)

$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_WEIGHT` BEFORE UPDATE ON `Weight`
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
        transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, po_supply_weight, order_weight, tin_no, id_no, id_type, plant_code, plant_name, site_code, site_name, agent_code, agent_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, ex_del, raw_mat_code,raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, weight_different, balance, is_complete, is_cancel, is_approved, manual_weight, indicator_id, weighbridge_id, indicator_id_2, unit_price, sub_total, sst, total_price, load_drum, no_of_drum, batch_drum, status, approved_by, approved_reason, cancel_id, cancelled_reason, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.transaction_id, NEW.transaction_status, NEW.weight_type, NEW.transaction_date, 
        NEW.lorry_plate_no1, NEW.lorry_plate_no2, NEW.supplier_weight, NEW.po_supply_weight, 
        NEW.order_weight, NEW.tin_no, NEW.id_no, NEW.id_type, NEW.plant_code, NEW.plant_name, NEW.site_code, NEW.site_name, 
        NEW.agent_code, NEW.agent_name, NEW.customer_code, NEW.customer_name, 
        NEW.supplier_code, NEW.supplier_name, NEW.product_code, NEW.product_name, 
        NEW.product_description, NEW.ex_del, NEW.raw_mat_code, NEW.raw_mat_name, 
        NEW.container_no, NEW.invoice_no, NEW.purchase_order, NEW.delivery_no, 
        NEW.transporter_code, NEW.transporter, NEW.destination_code, NEW.destination, 
        NEW.remarks, NEW.gross_weight1, NEW.gross_weight1_date, NEW.tare_weight1, 
        NEW.tare_weight1_date, NEW.nett_weight1, NEW.gross_weight2, NEW.gross_weight2_date, 
        NEW.tare_weight2, NEW.tare_weight2_date, NEW.nett_weight2, NEW.reduce_weight, 
        NEW.final_weight, NEW.weight_different, NEW.balance, NEW.is_complete, NEW.is_cancel, 
        NEW.is_approved, NEW.manual_weight, NEW.indicator_id, NEW.weighbridge_id, 
        NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.total_price, NEW.load_drum, 
        NEW.no_of_drum, NEW.batch_drum, NEW.status, NEW.approved_by, NEW.approved_reason, NEW.cancel_id, NEW.cancelled_reason, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

-- 13/12/2025 --
ALTER TABLE `Weight` ADD `order_weight_uom` VARCHAR(100) NULL AFTER `po_supply_weight`;

ALTER TABLE `Weight_Log` ADD `order_weight_uom` VARCHAR(100) NULL AFTER `po_supply_weight`;

ALTER TABLE `Weight` ADD `supplier_weight_uom` VARCHAR(100) NULL AFTER `lorry_plate_no2`;

ALTER TABLE `Weight_Log` ADD `supplier_weight_uom` VARCHAR(100) NULL AFTER `lorry_plate_no2`;

DELIMITER $$

CREATE OR REPLACE TRIGGER `TRG_INS_WEIGHT` AFTER INSERT ON `Weight`
 FOR EACH ROW INSERT INTO Weight_Log (
    transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight_uom, supplier_weight, po_supply_weight, order_weight_uom, order_weight, tin_no, id_no, id_type, plant_code, plant_name, site_code, site_name, agent_code, agent_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, ex_del, raw_mat_code,raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, weight_different, balance, is_complete, is_cancel, is_approved, manual_weight, indicator_id, weighbridge_id, indicator_id_2, unit_price, sub_total, sst, total_price, load_drum, no_of_drum, batch_drum, status, approved_by, approved_reason, cancel_id, cancelled_reason, action_id, action_by, event_date
) 
VALUES (
    NEW.transaction_id, NEW.transaction_status, NEW.weight_type, NEW.transaction_date, NEW.lorry_plate_no1, NEW.lorry_plate_no2, NEW.supplier_weight_uom, NEW.supplier_weight, NEW.po_supply_weight, NEW.order_weight_uom, NEW.order_weight, NEW.tin_no, NEW.id_no, NEW.id_type, NEW.plant_code, NEW.plant_name, NEW.site_code, NEW.site_name, NEW.agent_code, NEW.agent_name, NEW.customer_code, NEW.customer_name, NEW.supplier_code, NEW.supplier_name, NEW.product_code, NEW.product_name, NEW.product_description, NEW.ex_del, NEW.raw_mat_code, NEW.raw_mat_name, NEW.container_no, NEW.invoice_no, NEW.purchase_order, NEW.delivery_no, NEW.transporter_code, NEW.transporter, NEW.destination_code, NEW.destination, NEW.remarks, NEW.gross_weight1, NEW.gross_weight1_date, NEW.tare_weight1, NEW.tare_weight1_date, NEW.nett_weight1, NEW.gross_weight2, NEW.gross_weight2_date, NEW.tare_weight2, NEW.tare_weight2_date, NEW.nett_weight2, NEW.reduce_weight, NEW.final_weight, NEW.weight_different, NEW.balance, NEW.is_complete, NEW.is_cancel, NEW.is_approved, NEW.manual_weight, NEW.indicator_id, NEW.weighbridge_id, NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.total_price, NEW.load_drum, NEW.no_of_drum, NEW.batch_drum, NEW.status, NEW.approved_by, NEW.approved_reason, NEW.cancel_id, NEW.cancelled_reason, 1, NEW.created_by, NEW.created_date
)

$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_WEIGHT` BEFORE UPDATE ON `Weight`
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
        transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight_uom, supplier_weight, po_supply_weight, order_weight_uom, order_weight, tin_no, id_no, id_type, plant_code, plant_name, site_code, site_name, agent_code, agent_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, ex_del, raw_mat_code,raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, weight_different, balance, is_complete, is_cancel, is_approved, manual_weight, indicator_id, weighbridge_id, indicator_id_2, unit_price, sub_total, sst, total_price, load_drum, no_of_drum, batch_drum, status, approved_by, approved_reason, cancel_id, cancelled_reason, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.transaction_id, NEW.transaction_status, NEW.weight_type, NEW.transaction_date, 
        NEW.lorry_plate_no1, NEW.lorry_plate_no2, NEW.supplier_weight_uom, NEW.supplier_weight, NEW.po_supply_weight, 
        NEW.order_weight_uom, NEW.order_weight, NEW.tin_no, NEW.id_no, NEW.id_type, NEW.plant_code, NEW.plant_name, NEW.site_code, NEW.site_name, 
        NEW.agent_code, NEW.agent_name, NEW.customer_code, NEW.customer_name, 
        NEW.supplier_code, NEW.supplier_name, NEW.product_code, NEW.product_name, 
        NEW.product_description, NEW.ex_del, NEW.raw_mat_code, NEW.raw_mat_name, 
        NEW.container_no, NEW.invoice_no, NEW.purchase_order, NEW.delivery_no, 
        NEW.transporter_code, NEW.transporter, NEW.destination_code, NEW.destination, 
        NEW.remarks, NEW.gross_weight1, NEW.gross_weight1_date, NEW.tare_weight1, 
        NEW.tare_weight1_date, NEW.nett_weight1, NEW.gross_weight2, NEW.gross_weight2_date, 
        NEW.tare_weight2, NEW.tare_weight2_date, NEW.nett_weight2, NEW.reduce_weight, 
        NEW.final_weight, NEW.weight_different, NEW.balance, NEW.is_complete, NEW.is_cancel, 
        NEW.is_approved, NEW.manual_weight, NEW.indicator_id, NEW.weighbridge_id, 
        NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.total_price, NEW.load_drum, 
        NEW.no_of_drum, NEW.batch_drum, NEW.status, NEW.approved_by, NEW.approved_reason, NEW.cancel_id, NEW.cancelled_reason, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

-- 16/12/2025 --
ALTER TABLE `Sales_Order` ADD `batch_drum` VARCHAR(5) NULL AFTER `veh_number`;

ALTER TABLE `Sales_Order_Log` ADD `batch_drum` VARCHAR(5) NULL AFTER `veh_number`;

ALTER TABLE `Purchase_Order` ADD `batch_drum` VARCHAR(5) NULL AFTER `veh_number`;

ALTER TABLE `Purchase_Order_Log` ADD `batch_drum` VARCHAR(5) NULL AFTER `veh_number`;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_SO` AFTER INSERT ON `Sales_Order`
 FOR EACH ROW INSERT INTO Sales_Order_Log (
    company_code, company_name, customer_code, customer_name, site_code, site_name, order_date, order_no, so_no, delivery_date, agent_code, agent_name, destination_code, destination_name, deliver_to_name, product_code, product_name, plant_code, plant_name, transporter_code, transporter_name, veh_number, batch_drum, exquarry_or_delivered, order_load, order_quantity, balance, converted_order_qty, converted_balance, converted_unit, unit_price, total_price, remarks, status, action_id, action_by, event_date
) 
VALUES (
    NEW.company_code, NEW.company_name, NEW.customer_code, NEW.customer_name, NEW.site_code, NEW.site_name, NEW.order_date, NEW.order_no, NEW.so_no, NEW.delivery_date, NEW.agent_code, NEW.agent_name, NEW.destination_code, NEW.destination_name, NEW.deliver_to_name, NEW.product_code, NEW.product_name, NEW.plant_code, NEW.plant_name, NEW.transporter_code, NEW.transporter_name, NEW.veh_number, NEW.batch_drum, NEW.exquarry_or_delivered, NEW.order_load, NEW.order_quantity, NEW.balance, NEW.converted_order_qty, NEW.converted_balance, NEW.converted_unit, NEW.remarks, NEW.converted_unit, NEW.unit_price, NEW.total_price, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_SO` BEFORE UPDATE ON `Sales_Order`
 FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if deleted = 1, set action_id to 3, otherwise set to 2
    IF NEW.deleted = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Sales_Order table
    INSERT INTO Sales_Order_Log (
        company_code, company_name, customer_code, customer_name, site_code, site_name, order_date, order_no, so_no, delivery_date, agent_code, agent_name, destination_code, destination_name, deliver_to_name, product_code, product_name, plant_code, plant_name, transporter_code, transporter_name, veh_number, batch_drum, exquarry_or_delivered, order_load, order_quantity, balance, converted_order_qty, converted_balance, converted_unit, unit_price, total_price, remarks, status, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.company_code, NEW.company_name, NEW.customer_code, NEW.customer_name, NEW.site_code, NEW.site_name, NEW.order_date, NEW.order_no, NEW.so_no, NEW.delivery_date, NEW.agent_code, NEW.agent_name, NEW.destination_code, NEW.destination_name, NEW.deliver_to_name, NEW.product_code, NEW.product_name, NEW.plant_code, NEW.plant_name, NEW.transporter_code, NEW.transporter_name, NEW.veh_number, NEW.batch_drum, NEW.exquarry_or_delivered, NEW.order_load, NEW.order_quantity, NEW.balance, NEW.converted_order_qty, NEW.converted_balance, NEW.converted_unit, NEW.unit_price, NEW.total_price, NEW.remarks, NEW.status, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

DELIMITER $$

CREATE OR REPLACE TRIGGER `TRG_INS_PO` AFTER INSERT ON `Purchase_Order`
 FOR EACH ROW INSERT INTO Purchase_Order_Log (
    company_code, company_name, supplier_code, supplier_name, site_code, site_name, order_date, order_no, po_no, delivery_date, agent_code, agent_name, destination_code, destination_name, deliver_to_name, raw_mat_code, raw_mat_name, plant_code, plant_name, transporter_code, transporter_name, veh_number, batch_drum, exquarry_or_delivered, order_load, order_quantity, balance, converted_order_qty, converted_balance, converted_unit, unit_price, total_price, remarks, status, action_id, action_by, event_date
) 
VALUES (
    NEW.company_code, NEW.company_name, NEW.supplier_code, NEW.supplier_name, NEW.site_code, NEW.site_name, NEW.order_date, NEW.order_no, NEW.po_no, NEW.delivery_date, NEW.agent_code, NEW.agent_name, NEW.destination_code, NEW.destination_name, NEW.deliver_to_name, NEW.raw_mat_code, NEW.raw_mat_name, NEW.plant_code, NEW.plant_name, NEW.transporter_code, NEW.transporter_name, NEW.veh_number, NEW.batch_drum, NEW.exquarry_or_delivered, NEW.order_load, NEW.order_quantity, NEW.balance, NEW.converted_order_qty, NEW.converted_balance, NEW.converted_unit, NEW.unit_price, NEW.total_price, NEW.remarks, NEW.status, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_PO` BEFORE UPDATE ON `Purchase_Order`
 FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if deleted = 1, set action_id to 3, otherwise set to 2
    IF NEW.deleted = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Purchase_Order table
    INSERT INTO Purchase_Order_Log (
        company_code, company_name, supplier_code, supplier_name, site_code, site_name, order_date, order_no, po_no, delivery_date, agent_code,
        agent_name, destination_code, destination_name, deliver_to_name, raw_mat_code, raw_mat_name, plant_code, plant_name, transporter_code, transporter_name, veh_number, batch_drum, exquarry_or_delivered, order_load, order_quantity, balance, converted_order_qty, converted_balance, converted_unit, unit_price, total_price, remarks, status, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.company_code, NEW.company_name, NEW.supplier_code, NEW.supplier_name, NEW.site_code, NEW.site_name, NEW.order_date, NEW.order_no, NEW.po_no, NEW.delivery_date, NEW.agent_code, NEW.agent_name, NEW.destination_code, NEW.destination_name, NEW.deliver_to_name, NEW.raw_mat_code, NEW.raw_mat_name, NEW.plant_code, NEW.plant_name, NEW.transporter_code, NEW.transporter_name, NEW.veh_number, NEW.batch_drum, NEW.exquarry_or_delivered, NEW.order_load, NEW.order_quantity, NEW.balance, NEW.converted_order_qty, NEW.converted_balance, NEW.converted_unit, NEW.unit_price, NEW.total_price, NEW.remarks, NEW.status, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

-- 20/12/2025 --
CREATE TABLE `Assets` (
  `id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `plant_id` int(11) NOT NULL,
  `batch_drum` varchar(10) NOT NULL,
  `deleted` int(1) NOT NULL DEFAULT 0,
  `created_by` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `modified_by` varchar(50) DEFAULT NULL,
  `modified_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

ALTER TABLE `Assets` ADD PRIMARY KEY (`id`);

ALTER TABLE `Assets` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE `Assets_Log` (
  `id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `plant_id` int(11) NOT NULL,
  `batch_drum` varchar(10) NOT NULL,
  `action_id` int(11) NOT NULL,
  `action_by` varchar(50) DEFAULT '*SYSTEM',
  `event_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

ALTER TABLE `Assets_Log` ADD PRIMARY KEY (`id`);

ALTER TABLE `Assets_Log` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

DELIMITER $$

CREATE OR REPLACE TRIGGER `TRG_INS_ASSET` AFTER INSERT ON `Assets`
 FOR EACH ROW INSERT INTO Assets_Log (
    type, name, plant_id, batch_drum, action_id, action_by, event_date
) 
VALUES (
    NEW.type, NEW.name, NEW.plant_id, NEW.batch_drum, 1, NEW.created_by, NEW.created_at
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_ASSET` BEFORE UPDATE ON `Assets`
 FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if deleted = 1, set action_id to 3, otherwise set to 2
    IF NEW.deleted = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Assets_Log table
    INSERT INTO Assets_Log (
        type, name, plant_id, batch_drum, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.type, NEW.name, NEW.plant_id, NEW.batch_drum, action_value, NEW.modified_by, NEW.modified_at
    );
END
$$
DELIMITER ;

CREATE TABLE `Calculations` (
  `id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `plant_id` int(11) NOT NULL,
  `batch_drum` varchar(10) NOT NULL,
  `deleted` int(1) NOT NULL DEFAULT 0,
  `created_by` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `modified_by` varchar(50) DEFAULT NULL,
  `modified_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

ALTER TABLE `Calculations` ADD PRIMARY KEY (`id`);

ALTER TABLE `Calculations` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE `Calculation_Value` (
  `id` int(11) NOT NULL,
  `calculation_id` int(11) NOT NULL,
  `level` varchar(30) DEFAULT NULL,
  `volume` varchar(30) DEFAULT NULL,
  `temperature` varchar(30) DEFAULT NULL,
  `sg` varchar(30) DEFAULT NULL,
  `deleted` int(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

ALTER TABLE `Calculation_Value` ADD PRIMARY KEY (`id`);

ALTER TABLE `Calculation_Value` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- 25/12/2025 --
ALTER TABLE `Assets` ADD `length` VARCHAR(50) NULL AFTER `batch_drum`, ADD `height` VARCHAR(50) NULL AFTER `length`, ADD `diameter` VARCHAR(50) NULL AFTER `height`;

ALTER TABLE `Assets_Log` ADD `length` VARCHAR(50) NULL AFTER `batch_drum`, ADD `height` VARCHAR(50) NULL AFTER `length`, ADD `diameter` VARCHAR(50) NULL AFTER `height`;

DELIMITER $$

CREATE OR REPLACE TRIGGER `TRG_INS_ASSET` AFTER INSERT ON `Assets`
 FOR EACH ROW INSERT INTO Assets_Log (
    type, name, plant_id, batch_drum, length, height, diameter, action_id, action_by, event_date
) 
VALUES (
    NEW.type, NEW.name, NEW.plant_id, NEW.batch_drum, NEW.length, NEW.height, NEW.diameter, 1, NEW.created_by, NEW.created_at
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_ASSET` BEFORE UPDATE ON `Assets`
 FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if deleted = 1, set action_id to 3, otherwise set to 2
    IF NEW.deleted = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Assets_Log table
    INSERT INTO Assets_Log (
        type, name, plant_id, batch_drum, length, height, diameter, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.type, NEW.name, NEW.plant_id, NEW.batch_drum, NEW.length, NEW.height, NEW.diameter, action_value, NEW.modified_by, NEW.modified_at
    );
END
$$
DELIMITER ;

-- 26/12/2025 --
ALTER TABLE `Bitumen` ADD `other_diesel` LONGTEXT NULL AFTER `diesel`;

ALTER TABLE `Bitumen_Log` ADD `other_diesel` LONGTEXT NULL AFTER `diesel`;

DELIMITER $$

CREATE OR REPLACE TRIGGER `TRG_INS_BITUMEN` AFTER INSERT ON `Bitumen`
 FOR EACH ROW INSERT INTO Bitumen_Log (
    bitumen_id, `60/70`, pg76, crmb, lfo, diesel, hotoil, other_diesel, fibre, data, declaration_datetime, plant_id, plant_code, batch_drum, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.`60/70`, NEW.pg76, NEW.crmb, NEW.lfo, NEW.diesel, NEW.hotoil, NEW.other_diesel, NEW.fibre, NEW.data, NEW.declaration_datetime, NEW.plant_id, NEW.plant_code, NEW.batch_drum, 1, NEW.created_by, NEW.created_datetime
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_BITUMEN` BEFORE UPDATE ON `Bitumen`
 FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if deleted = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Bitumen_Log table
    INSERT INTO Bitumen_Log (
        bitumen_id, `60/70`, pg76, crmb, lfo, diesel, hotoil, other_diesel, fibre, data, declaration_datetime, plant_id, plant_code, batch_drum, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.`60/70`, NEW.pg76, NEW.crmb, NEW.lfo, NEW.diesel, NEW.hotoil, NEW.other_diesel, NEW.fibre, NEW.data, NEW.declaration_datetime, NEW.plant_id, NEW.plant_code, NEW.batch_drum, action_value, NEW.modified_by, NEW.modified_datetime
    );
END
$$
DELIMITER ;

-- 26/01/2026 --
-- BITUMEN (60/70) / Batch --
INSERT INTO Product_RawMat (product_id, raw_mat_id, raw_mat_code, raw_mat_basic_uom, basic_uom_unit_id, raw_mat_weight, plant_id, batch_drum, status)
SELECT 27, rm.id, rm.raw_mat_code, FORMAT(v.percentage / 100, 3), 6, FORMAT((v.percentage / 100) * 1000, 0), 26, 'Batch', 0
FROM (
    SELECT 1 AS ord, 'AC14' AS name, 4.90 AS percentage UNION ALL
    SELECT 2, 'AC28', 4.00 UNION ALL
    SELECT 3, 'ACW20', 5.00 UNION ALL
    SELECT 4, 'ACW14', 5.20 UNION ALL
    SELECT 5, '3/8WC', 5.20 UNION ALL
    SELECT 6, 'AC10', 5.10 UNION ALL
    SELECT 7, 'ACB28', 3.90 UNION ALL
    SELECT 8, 'ACB20', 3.90 UNION ALL
    SELECT 9, 'SFM14', 4.90 UNION ALL
    SELECT 10, 'BMR28', 3.50 UNION ALL
    SELECT 11, '20MM AGGREGATE', 4.30 UNION ALL
    SELECT 12, 'QUARRY DUST', 6.50 UNION ALL
    SELECT 13, 'BMB20', 4.14 UNION ALL
    SELECT 14, 'BMW14', 5.00 UNION ALL
    SELECT 15, 'DBM40', 3.50 UNION ALL
    SELECT 16, 'SMA20', 0 UNION ALL
    SELECT 17, 'CRMA', 6.00 UNION ALL
    SELECT 18, 'FMA14', 5.60 UNION ALL
    SELECT 19, 'PMA14', 0 UNION ALL
    SELECT 20, 'PMA28', 0 UNION ALL
    SELECT 21, 'FMA20', 5.50 UNION ALL
    SELECT 22, 'POROUS', 0 UNION ALL
    SELECT 23, 'ACBC28', 4.00 UNION ALL
    SELECT 24, 'ACWC20', 4.90 UNION ALL
    SELECT 25, 'CMA14', 4.90 UNION ALL
    SELECT 26, 'AC14RPF', 4.90 UNION ALL
    SELECT 27, 'SMA20 PLUS', 5.60 UNION ALL
    SELECT 28, 'ACWC20 PLUS', 4.80
) v
JOIN Raw_Mat rm ON rm.name = v.name
ORDER BY v.ord;

-- BITUMEN (PG76) / Batch --
INSERT INTO Product_RawMat (product_id, raw_mat_id, raw_mat_code, raw_mat_basic_uom, basic_uom_unit_id, raw_mat_weight, plant_id, batch_drum, status)
SELECT 28, rm.id, rm.raw_mat_code, FORMAT(v.percentage / 100, 3), 6, FORMAT((v.percentage / 100) * 1000, 0), 26, 'Batch', 0
FROM (
    SELECT 1 AS ord, 'AC14' AS name, 0 AS percentage UNION ALL
    SELECT 2, 'AC28', 0 UNION ALL
    SELECT 3, 'ACW20', 0 UNION ALL
    SELECT 4, 'ACW14', 0 UNION ALL
    SELECT 5, '3/8WC', 0 UNION ALL
    SELECT 6, 'AC10', 0 UNION ALL
    SELECT 7, 'ACB28', 0 UNION ALL
    SELECT 8, 'ACB20', 0 UNION ALL
    SELECT 9, 'SFM14', 0 UNION ALL
    SELECT 10, 'BMR28', 0 UNION ALL
    SELECT 11, '20MM AGGREGATE', 0 UNION ALL
    SELECT 12, 'QUARRY DUST', 0 UNION ALL
    SELECT 13, 'BMB20', 0 UNION ALL
    SELECT 14, 'BMW14', 0 UNION ALL
    SELECT 15, 'DBM40', 0 UNION ALL
    SELECT 16, 'SMA20', 5.70 UNION ALL
    SELECT 17, 'CRMA', 0 UNION ALL
    SELECT 18, 'FMA14', 0 UNION ALL
    SELECT 19, 'PMA14', 4.90 UNION ALL
    SELECT 20, 'PMA28', 4.00 UNION ALL
    SELECT 21, 'FMA20', 0 UNION ALL
    SELECT 22, 'POROUS', 0 UNION ALL
    SELECT 23, 'ACBC28', 0 UNION ALL
    SELECT 24, 'ACWC20', 0 UNION ALL
    SELECT 25, 'CMA14', 0 UNION ALL
    SELECT 26, 'AC14RPF', 0 UNION ALL
    SELECT 27, 'SMA20 PLUS', 0 UNION ALL
    SELECT 28, 'ACWC20 PLUS', 0
) v
JOIN Raw_Mat rm ON rm.name = v.name
ORDER BY v.ord;

-- 28MM / Batch --
INSERT INTO Product_RawMat (product_id, raw_mat_id, raw_mat_code, raw_mat_basic_uom, basic_uom_unit_id, raw_mat_weight, plant_id, batch_drum, status)
SELECT 22, rm.id, rm.raw_mat_code, FORMAT(v.percentage / 100, 3), 6, FORMAT((v.percentage / 100) * 1000, 0), 26, 'Batch', 0
FROM (
    SELECT 1 AS ord, 'AC14' AS name, 0 AS percentage UNION ALL
    SELECT 2, 'AC28', 19.10 UNION ALL
    SELECT 3, 'ACW20', 0 UNION ALL
    SELECT 4, 'ACW14', 0 UNION ALL
    SELECT 5, '3/8WC', 0 UNION ALL
    SELECT 6, 'AC10', 0 UNION ALL
    SELECT 7, 'ACB28', 16.00 UNION ALL
    SELECT 8, 'ACB20', 0 UNION ALL
    SELECT 9, 'SFM14', 0 UNION ALL
    SELECT 10, 'BMR28', 19.20 UNION ALL
    SELECT 11, '20MM AGGREGATE', 0 UNION ALL
    SELECT 12, 'QUARRY DUST', 0 UNION ALL
    SELECT 13, 'BMB20', 0 UNION ALL
    SELECT 14, 'BMW14', 0 UNION ALL
    SELECT 15, 'DBM40', 0 UNION ALL
    SELECT 16, 'SMA20', 0 UNION ALL
    SELECT 17, 'CRMA', 0 UNION ALL
    SELECT 18, 'FMA14', 0 UNION ALL
    SELECT 19, 'PMA14', 0 UNION ALL
    SELECT 20, 'PMA28', 22.90 UNION ALL
    SELECT 21, 'FMA20', 0 UNION ALL
    SELECT 22, 'POROUS', 9.00 UNION ALL
    SELECT 23, 'ACBC28', 21.00 UNION ALL
    SELECT 24, 'ACWC20', 0 UNION ALL
    SELECT 25, 'CMA14', 0 UNION ALL
    SELECT 26, 'AC14RPF', 0 UNION ALL
    SELECT 27, 'SMA20 PLUS', 0 UNION ALL
    SELECT 28, 'ACWC20 PLUS', 24.20
) v
JOIN Raw_Mat rm ON rm.name = v.name
ORDER BY v.ord;

-- 20MM / Batch --
INSERT INTO Product_RawMat (product_id, raw_mat_id, raw_mat_code, raw_mat_basic_uom, basic_uom_unit_id, raw_mat_weight, plant_id, batch_drum, status)
SELECT 21, rm.id, rm.raw_mat_code, FORMAT(v.percentage / 100, 3), 6, FORMAT((v.percentage / 100) * 1000, 0), 26, 'Batch', 0
FROM (
    SELECT 1 AS ord, 'AC14' AS name, 0 AS percentage UNION ALL
    SELECT 2, 'AC28', 0 UNION ALL
    SELECT 3, 'ACW20', 0 UNION ALL
    SELECT 4, 'ACW14', 0 UNION ALL
    SELECT 5, '3/8WC', 0 UNION ALL
    SELECT 6, 'AC10', 0 UNION ALL
    SELECT 7, 'ACB28', 0 UNION ALL
    SELECT 8, 'ACB20', 18.00 UNION ALL
    SELECT 9, 'SFM14', 0 UNION ALL
    SELECT 10, 'BMR28', 0 UNION ALL
    SELECT 11, '20MM AGGREGATE', 32.00 UNION ALL
    SELECT 12, 'QUARRY DUST', 0 UNION ALL
    SELECT 13, 'BMB20', 21.00 UNION ALL
    SELECT 14, 'BMW14', 0 UNION ALL
    SELECT 15, 'DBM40', 0 UNION ALL
    SELECT 16, 'SMA20', 0 UNION ALL
    SELECT 17, 'CRMA', 0 UNION ALL
    SELECT 18, 'FMA14', 0 UNION ALL
    SELECT 19, 'PMA14', 0 UNION ALL
    SELECT 20, 'PMA28', 0 UNION ALL
    SELECT 21, 'FMA20', 0 UNION ALL
    SELECT 22, 'POROUS', 0 UNION ALL
    SELECT 23, 'ACBC28', 0 UNION ALL
    SELECT 24, 'ACWC20', 17.20 UNION ALL
    SELECT 25, 'CMA14', 0 UNION ALL
    SELECT 26, 'AC14RPF', 0 UNION ALL
    SELECT 27, 'SMA20 PLUS', 15.00 UNION ALL
    SELECT 28, 'ACWC20 PLUS', 0
) v
JOIN Raw_Mat rm ON rm.name = v.name
ORDER BY v.ord;

-- 14MM / Batch --
INSERT INTO Product_RawMat (product_id, raw_mat_id, raw_mat_code, raw_mat_basic_uom, basic_uom_unit_id, raw_mat_weight, plant_id, batch_drum, status)
SELECT 20, rm.id, rm.raw_mat_code, FORMAT(v.percentage / 100, 3), 6, FORMAT((v.percentage / 100) * 1000, 0), 26, 'Batch', 0
FROM (
    SELECT 1 AS ord, 'AC14' AS name, 19.00 AS percentage UNION ALL
    SELECT 2, 'AC28', 0 UNION ALL
    SELECT 3, 'ACW20', 0 UNION ALL
    SELECT 4, 'ACW14', 10.00 UNION ALL
    SELECT 5, '3/8WC', 0 UNION ALL
    SELECT 6, 'AC10', 0 UNION ALL
    SELECT 7, 'ACB28', 0 UNION ALL
    SELECT 8, 'ACB20', 0 UNION ALL
    SELECT 9, 'SFM14', 12.00 UNION ALL
    SELECT 10, 'BMR28', 0 UNION ALL
    SELECT 11, '20MM AGGREGATE', 0 UNION ALL
    SELECT 12, 'QUARRY DUST', 0 UNION ALL
    SELECT 13, 'BMB20', 0 UNION ALL
    SELECT 14, 'BMW14', 12.50 UNION ALL
    SELECT 15, 'DBM40', 0 UNION ALL
    SELECT 16, 'SMA20', 0 UNION ALL
    SELECT 17, 'CRMA', 0 UNION ALL
    SELECT 18, 'FMA14', 0 UNION ALL
    SELECT 19, 'PMA14', 20.00 UNION ALL
    SELECT 20, 'PMA28', 0 UNION ALL
    SELECT 21, 'FMA20', 0 UNION ALL
    SELECT 22, 'POROUS', 0 UNION ALL
    SELECT 23, 'ACBC28', 0 UNION ALL
    SELECT 24, 'ACWC20', 0 UNION ALL
    SELECT 25, 'CMA14', 0 UNION ALL
    SELECT 26, 'AC14RPF', 0 UNION ALL
    SELECT 27, 'SMA20 PLUS', 0 UNION ALL
    SELECT 28, 'ACWC20 PLUS', 0
) v
JOIN Raw_Mat rm ON rm.name = v.name
ORDER BY v.ord;

-- 10MM / Batch --
INSERT INTO Product_RawMat (product_id, raw_mat_id, raw_mat_code, raw_mat_basic_uom, basic_uom_unit_id, raw_mat_weight, plant_id, batch_drum, status)
SELECT 19, rm.id, rm.raw_mat_code, FORMAT(v.percentage / 100, 3), 6, FORMAT((v.percentage / 100) * 1000, 0), 26, 'Batch', 0
FROM (
    SELECT 1 AS ord, 'AC14' AS name, 0 AS percentage UNION ALL
    SELECT 2, 'AC28', 13.40 UNION ALL
    SELECT 3, 'ACW20', 31.80 UNION ALL
    SELECT 4, 'ACW14', 0 UNION ALL
    SELECT 5, '3/8WC', 27.00 UNION ALL
    SELECT 6, 'AC10', 45.70 UNION ALL
    SELECT 7, 'ACB28', 17.00 UNION ALL
    SELECT 8, 'ACB20', 21.00 UNION ALL
    SELECT 9, 'SFM14', 30.00 UNION ALL
    SELECT 10, 'BMR28', 7.70 UNION ALL
    SELECT 11, '20MM AGGREGATE', 0 UNION ALL
    SELECT 12, 'QUARRY DUST', 0 UNION ALL
    SELECT 13, 'BMB20', 23.00 UNION ALL
    SELECT 14, 'BMW14', 0 UNION ALL
    SELECT 15, 'DBM40', 6.70 UNION ALL
    SELECT 16, 'SMA20', 49.60 UNION ALL
    SELECT 17, 'CRMA', 48.30 UNION ALL
    SELECT 18, 'FMA14', 0 UNION ALL
    SELECT 19, 'PMA14', 29.00 UNION ALL
    SELECT 20, 'PMA28', 48.30 UNION ALL
    SELECT 21, 'FMA20', 0 UNION ALL
    SELECT 22, 'POROUS', 0 UNION ALL
    SELECT 23, 'ACBC28', 17.00 UNION ALL
    SELECT 24, 'ACWC20', 31.80 UNION ALL
    SELECT 25, 'CMA14', 0 UNION ALL
    SELECT 26, 'AC14RPF', 0 UNION ALL
    SELECT 27, 'SMA20 PLUS', 7.60 UNION ALL
    SELECT 28, 'ACWC20 PLUS', 31.80
) v
JOIN Raw_Mat rm ON rm.name = v.name
ORDER BY v.ord;

-- Q.DUST / Batch --
INSERT INTO Product_RawMat (product_id, raw_mat_id, raw_mat_code, raw_mat_basic_uom, basic_uom_unit_id, raw_mat_weight, plant_id, batch_drum, status)
SELECT 25, rm.id, rm.raw_mat_code, FORMAT(v.percentage / 100, 3), 6, FORMAT((v.percentage / 100) * 1000, 0), 26, 'Batch', 0
FROM (
    SELECT 1 AS ord, 'AC14' AS name, 50.50 AS percentage UNION ALL
    SELECT 2, 'AC28', 41.50 UNION ALL
    SELECT 3, 'ACW20', 46.30 UNION ALL
    SELECT 4, 'ACW14', 55.90 UNION ALL
    SELECT 5, '3/8WC', 54.00 UNION ALL
    SELECT 6, 'AC10', 49.20 UNION ALL
    SELECT 7, 'ACB28', 45.10 UNION ALL
    SELECT 8, 'ACB20', 41.20 UNION ALL
    SELECT 9, 'SFM14', 51.20 UNION ALL
    SELECT 10, 'BMR28', 48.50 UNION ALL
    SELECT 11, '20MM AGGREGATE', 43.30 UNION ALL
    SELECT 12, 'QUARRY DUST', 93.50 UNION ALL
    SELECT 13, 'BMB20', 45.26 UNION ALL
    SELECT 14, 'BMW14', 47.50 UNION ALL
    SELECT 15, 'DBM40', 39.70 UNION ALL
    SELECT 16, 'SMA20', 14.70 UNION ALL
    SELECT 17, 'CRMA', 14.80 UNION ALL
    SELECT 18, 'FMA14', 18.10 UNION ALL
    SELECT 19, 'PMA14', 52.50 UNION ALL
    SELECT 20, 'PMA28', 38.60 UNION ALL
    SELECT 21, 'FMA20', 16.40 UNION ALL
    SELECT 22, 'POROUS', 0 UNION ALL
    SELECT 23, 'ACBC28', 0 UNION ALL
    SELECT 24, 'ACWC20', 48.12 UNION ALL
    SELECT 25, 'CMA14', 46.40 UNION ALL
    SELECT 26, 'AC14RPF', 47.10 UNION ALL
    SELECT 27, 'SMA20 PLUS', 47.70 UNION ALL
    SELECT 28, 'ACWC20 PLUS', 13.10
) v
JOIN Raw_Mat rm ON rm.name = v.name
ORDER BY v.ord;

-- OPC / Batch --
INSERT INTO Product_RawMat (product_id, raw_mat_id, raw_mat_code, raw_mat_basic_uom, basic_uom_unit_id, raw_mat_weight, plant_id, batch_drum, status)
SELECT 35, rm.id, rm.raw_mat_code, FORMAT(v.percentage / 100, 3), 6, FORMAT((v.percentage / 100) * 1000, 0), 26, 'Batch', 0
FROM (
    SELECT 1 AS ord, 'AC14' AS name, 1.90 AS percentage UNION ALL
    SELECT 2, 'AC28', 1.90 UNION ALL
    SELECT 3, 'ACW20', 1.90 UNION ALL
    SELECT 4, 'ACW14', 1.90 UNION ALL
    SELECT 5, '3/8WC', 0 UNION ALL
    SELECT 6, 'AC10', 0 UNION ALL
    SELECT 7, 'ACB28', 0 UNION ALL
    SELECT 8, 'ACB20', 0 UNION ALL
    SELECT 9, 'SFM14', 1.90 UNION ALL
    SELECT 10, 'BMR28', 1.90 UNION ALL
    SELECT 11, '20MM AGGREGATE', 0 UNION ALL
    SELECT 12, 'QUARRY DUST', 0 UNION ALL
    SELECT 13, 'BMB20', 0 UNION ALL
    SELECT 14, 'BMW14', 0 UNION ALL
    SELECT 15, 'DBM40', 0 UNION ALL
    SELECT 16, 'SMA20', 10.00 UNION ALL
    SELECT 17, 'CRMA', 1.90 UNION ALL
    SELECT 18, 'FMA14', 1.90 UNION ALL
    SELECT 19, 'PMA14', 1.90 UNION ALL
    SELECT 20, 'PMA28', 1.90 UNION ALL
    SELECT 21, 'FMA20', 1.90 UNION ALL
    SELECT 22, 'POROUS', 0 UNION ALL
    SELECT 23, 'ACBC28', 0 UNION ALL
    SELECT 24, 'ACWC20', 0 UNION ALL
    SELECT 25, 'CMA14', 1.90 UNION ALL
    SELECT 26, 'AC14RPF', 1.90 UNION ALL
    SELECT 27, 'SMA20 PLUS', 1.90 UNION ALL
    SELECT 28, 'ACWC20 PLUS', 1.90
) v
JOIN Raw_Mat rm ON rm.name = v.name
ORDER BY v.ord;

-- 11/04/2026 --
CREATE TABLE `modules` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

ALTER TABLE `modules` ADD PRIMARY KEY (`id`);

ALTER TABLE `modules` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `modules` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

ALTER TABLE `permissions` ADD PRIMARY KEY (`id`);

ALTER TABLE `permissions` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE `role_permissions` (
  `role_id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

TRUNCATE TABLE `modules`;
INSERT INTO `modules` (`id`, `name`, `category`) VALUES 
(NULL, 'Sales', 'Weighing'),
(NULL, 'Purchase', 'Weighing'),
(NULL, 'Public', 'Weighing'),
(NULL, 'WIP', 'Weighing'),
(NULL, 'Return', 'Weighing'),
(NULL, 'Sales Order (SO)', 'Accounting'),
(NULL, 'Delivery Order (DO)', 'Accounting'),
(NULL, 'Purchase Order (PO)', 'Accounting'),
(NULL, 'Goods Received (GR)', 'Accounting'),
(NULL, 'Stock Take', 'Accounting'),
(NULL, 'Stock Take', 'Stock Management'),
(NULL, 'Stock Take Log', 'Stock Management'),
(NULL, 'Inventory', 'Stock Management'),
(NULL, 'Asset Management', 'Stock Management'),
(NULL, 'Calculation Setup', 'Stock Management'),
(NULL, 'Customer', 'Master Data'),
(NULL, 'Destination', 'Master Data'),
(NULL, 'Product', 'Master Data'),
(NULL, 'Raw Material', 'Master Data'),
(NULL, 'Supplier', 'Master Data'),
(NULL, 'Vehicle', 'Master Data'),
(NULL, 'Sales Representative', 'Master Data'),
(NULL, 'Transporter', 'Master Data'),
(NULL, 'Plant', 'Master Data'),
(NULL, 'Reason', 'Master Data'),
(NULL, 'Sales', 'Report'),
(NULL, 'Purchase', 'Report'),
(NULL, 'Public', 'Report'),
(NULL, 'Audit Log', 'Report'),
(NULL, 'Api Log', 'Report'),
(NULL, 'Cronjob Setup', 'Setting'),
(NULL, 'Company Profile', 'Setting'),
(NULL, 'Port Setup', 'Setting'),
(NULL, 'User Setup', 'User Management'),
(NULL, 'Role', 'User Management'),
(NULL, 'Module', 'User Management')
;

TRUNCATE TABLE `permissions`;
INSERT INTO `permissions` (`id`, `name`, `modules`) VALUES 
(NULL, 'view', '["All"]'),
(NULL, 'create','["All"]'),
(NULL, 'edit','["All"]'),
(NULL, 'delete','["All"]'),
(NULL, 'weight_out', '["Weighing"]'),
(NULL, 'display_do', '["Weighing"]'),
(NULL, 'manual_weighing', '["Weighing"]'),
(NULL, 'manual_date_change', '["Weighing"]'),
(NULL, 'view_all_plants', '["Weighing", "Accounting", "Report", "Stock Management", "Master Data", "User Management"]'),
(NULL, 'print_slip', '["Weighing", "Report", "Accounting"]'),
(NULL, 'upload_excel', '["Accounting", "Stock Management", "Master Data", "User Management"]'),
(NULL, 'post_to_sql', '["Accounting"]'),
(NULL, 'pull_from_sql', '["Accounting", "Master Data"]'),
(NULL, 'complete', '["Accounting"]'),
(NULL, 'reactivate', '["Accounting", "Master Data", "User Management"]'),
(NULL, 'include_price', '["Accounting"]'),
(NULL, 'export', '["Accounting", "Stock Management", "Report", "Master Data", "User Management"]');

INSERT INTO `role_permissions` (`role_id`, `module_id`, `permission_id`) VALUES
(13, 7, 1),
(13, 7, 2),
(13, 7, 3),
(13, 7, 4),
(13, 7, 9),
(13, 7, 10),
(13, 7, 11),
(13, 7, 12),
(13, 7, 13),
(13, 7, 14),
(13, 7, 15),
(13, 7, 16),
(13, 7, 17),
(13, 9, 1),
(13, 9, 2),
(13, 9, 3),
(13, 9, 4),
(13, 9, 9),
(13, 9, 10),
(13, 9, 11),
(13, 9, 12),
(13, 9, 13),
(13, 9, 14),
(13, 9, 15),
(13, 9, 16),
(13, 9, 17),
(13, 8, 1),
(13, 8, 2),
(13, 8, 3),
(13, 8, 4),
(13, 8, 9),
(13, 8, 10),
(13, 8, 11),
(13, 8, 12),
(13, 8, 13),
(13, 8, 14),
(13, 8, 15),
(13, 8, 16),
(13, 8, 17),
(13, 6, 1),
(13, 6, 2),
(13, 6, 3),
(13, 6, 4),
(13, 6, 9),
(13, 6, 10),
(13, 6, 11),
(13, 6, 12),
(13, 6, 13),
(13, 6, 14),
(13, 6, 15),
(13, 6, 16),
(13, 6, 17),
(13, 10, 1),
(13, 10, 2),
(13, 10, 3),
(13, 10, 4),
(13, 10, 9),
(13, 10, 10),
(13, 10, 11),
(13, 10, 12),
(13, 10, 13),
(13, 10, 14),
(13, 10, 15),
(13, 10, 16),
(13, 10, 17),
(13, 16, 1),
(13, 16, 2),
(13, 16, 3),
(13, 16, 4),
(13, 16, 9),
(13, 16, 11),
(13, 16, 13),
(13, 16, 15),
(13, 16, 17),
(13, 17, 1),
(13, 17, 2),
(13, 17, 3),
(13, 17, 4),
(13, 17, 9),
(13, 17, 11),
(13, 17, 13),
(13, 17, 15),
(13, 17, 17),
(13, 24, 1),
(13, 24, 2),
(13, 24, 3),
(13, 24, 4),
(13, 24, 9),
(13, 24, 11),
(13, 24, 13),
(13, 24, 15),
(13, 24, 17),
(13, 18, 1),
(13, 18, 2),
(13, 18, 3),
(13, 18, 4),
(13, 18, 9),
(13, 18, 11),
(13, 18, 13),
(13, 18, 15),
(13, 18, 17),
(13, 19, 1),
(13, 19, 2),
(13, 19, 3),
(13, 19, 4),
(13, 19, 9),
(13, 19, 11),
(13, 19, 13),
(13, 19, 15),
(13, 19, 17),
(13, 25, 1),
(13, 25, 2),
(13, 25, 3),
(13, 25, 4),
(13, 25, 9),
(13, 25, 11),
(13, 25, 13),
(13, 25, 15),
(13, 25, 17),
(13, 22, 1),
(13, 22, 2),
(13, 22, 3),
(13, 22, 4),
(13, 22, 9),
(13, 22, 11),
(13, 22, 13),
(13, 22, 15),
(13, 22, 17),
(13, 20, 1),
(13, 20, 2),
(13, 20, 3),
(13, 20, 4),
(13, 20, 9),
(13, 20, 11),
(13, 20, 13),
(13, 20, 15),
(13, 20, 17),
(13, 23, 1),
(13, 23, 2),
(13, 23, 3),
(13, 23, 4),
(13, 23, 9),
(13, 23, 11),
(13, 23, 13),
(13, 23, 15),
(13, 23, 17),
(13, 21, 1),
(13, 21, 2),
(13, 21, 3),
(13, 21, 4),
(13, 21, 9),
(13, 21, 11),
(13, 21, 13),
(13, 21, 15),
(13, 21, 17),
(13, 30, 1),
(13, 30, 2),
(13, 30, 3),
(13, 30, 4),
(13, 30, 9),
(13, 30, 10),
(13, 30, 17),
(13, 29, 1),
(13, 29, 2),
(13, 29, 3),
(13, 29, 4),
(13, 29, 9),
(13, 29, 10),
(13, 29, 17),
(13, 28, 1),
(13, 28, 2),
(13, 28, 3),
(13, 28, 4),
(13, 28, 9),
(13, 28, 10),
(13, 28, 17),
(13, 27, 1),
(13, 27, 2),
(13, 27, 3),
(13, 27, 4),
(13, 27, 9),
(13, 27, 10),
(13, 27, 17),
(13, 26, 1),
(13, 26, 2),
(13, 26, 3),
(13, 26, 4),
(13, 26, 9),
(13, 26, 10),
(13, 26, 17),
(13, 32, 1),
(13, 32, 2),
(13, 32, 3),
(13, 32, 4),
(13, 31, 1),
(13, 31, 2),
(13, 31, 3),
(13, 31, 4),
(13, 33, 1),
(13, 33, 2),
(13, 33, 3),
(13, 33, 4),
(13, 14, 1),
(13, 14, 2),
(13, 14, 3),
(13, 14, 4),
(13, 14, 9),
(13, 14, 11),
(13, 14, 17),
(13, 15, 1),
(13, 15, 2),
(13, 15, 3),
(13, 15, 4),
(13, 15, 9),
(13, 15, 11),
(13, 15, 17),
(13, 13, 1),
(13, 13, 2),
(13, 13, 3),
(13, 13, 4),
(13, 13, 9),
(13, 13, 11),
(13, 13, 17),
(13, 11, 1),
(13, 11, 2),
(13, 11, 3),
(13, 11, 4),
(13, 11, 9),
(13, 11, 11),
(13, 11, 17),
(13, 12, 1),
(13, 12, 2),
(13, 12, 3),
(13, 12, 4),
(13, 12, 9),
(13, 12, 11),
(13, 12, 17),
(13, 36, 1),
(13, 36, 2),
(13, 36, 3),
(13, 36, 4),
(13, 36, 9),
(13, 36, 11),
(13, 36, 15),
(13, 36, 17),
(13, 35, 1),
(13, 35, 2),
(13, 35, 3),
(13, 35, 4),
(13, 35, 9),
(13, 35, 11),
(13, 35, 15),
(13, 35, 17),
(13, 34, 1),
(13, 34, 2),
(13, 34, 3),
(13, 34, 4),
(13, 34, 9),
(13, 34, 11),
(13, 34, 15),
(13, 34, 17),
(13, 3, 1),
(13, 3, 2),
(13, 3, 3),
(13, 3, 4),
(13, 3, 5),
(13, 3, 6),
(13, 3, 7),
(13, 3, 8),
(13, 3, 9),
(13, 3, 10),
(13, 2, 1),
(13, 2, 2),
(13, 2, 3),
(13, 2, 4),
(13, 2, 5),
(13, 2, 6),
(13, 2, 7),
(13, 2, 8),
(13, 2, 9),
(13, 2, 10),
(13, 5, 1),
(13, 5, 2),
(13, 5, 3),
(13, 5, 4),
(13, 5, 5),
(13, 5, 6),
(13, 5, 7),
(13, 5, 8),
(13, 5, 9),
(13, 5, 10),
(13, 1, 1),
(13, 1, 2),
(13, 1, 3),
(13, 1, 4),
(13, 1, 5),
(13, 1, 6),
(13, 1, 7),
(13, 1, 8),
(13, 1, 9),
(13, 1, 10),
(13, 4, 1),
(13, 4, 2),
(13, 4, 3),
(13, 4, 4),
(13, 4, 5),
(13, 4, 6),
(13, 4, 7),
(13, 4, 8),
(13, 4, 9),
(13, 4, 10);

-- 16/04/2026 --
ALTER TABLE `Company` ADD `links` TEXT NULL AFTER `fax_no`;
ALTER TABLE `Company_Log` ADD `links` TEXT NULL AFTER `fax_no`;

DELIMITER $$

CREATE OR REPLACE TRIGGER `TRG_INS_COMPANY` AFTER INSERT ON `Company`
 FOR EACH ROW INSERT INTO Company_Log (
    company_id, company_code, company_reg_no, name, address_line_1, address_line_2, address_line_3, phone_no, fax_no, links, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.company_code, NEW.company_reg_no, NEW.name, NEW.address_line_1, NEW.address_line_2, NEW.address_line_3, NEW.phone_no, NEW.fax_no, NEW.links, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_COMPANY` BEFORE UPDATE ON `Company`
 FOR EACH ROW BEGIN

    -- Insert into Company_Log table
    INSERT INTO Company_Log (
        company_id, company_code, company_reg_no, name, address_line_1, address_line_2, address_line_3, phone_no, fax_no, links, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.company_code, NEW.company_reg_no, NEW.name, NEW.address_line_1, NEW.address_line_2, NEW.address_line_3, NEW.phone_no, NEW.fax_no, NEW.links, 2, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

UPDATE Company SET links = '{"sop_link":"https:\/\/www.youtube.com\/playlist?list=PLW1NFIMVHrg4FwGXpEZZDF706enhx5Qlw","hardware_setup_link":"https:\/\/www.youtube.com\/playlist?list=PLW1NFIMVHrg5Fd1WeN0SBE3m8gnifZENH","help_link":"https:\/\/drive.google.com\/drive\/folders\/1mOGhsY3RAM7GQcchDogPKx8E31Np1-4M?usp=sharing"}' WHERE id = 1;

TRUNCATE TABLE `modules`;
INSERT INTO `modules` (`id`, `name`, `category`) VALUES
(1, 'Sales', 'Weighing'),
(2, 'Purchase', 'Weighing'),
(3, 'Public', 'Weighing'),
(4, 'WIP', 'Weighing'),
(5, 'Return', 'Weighing'),
(6, 'Sales Order (SO)', 'Accounting'),
(7, 'Delivery Order (DO)', 'Accounting'),
(8, 'Purchase Order (PO)', 'Accounting'),
(9, 'Goods Received (GR)', 'Accounting'),
(10, 'Stock Take', 'Accounting'),
(11, 'Stock Take', 'Stock Management'),
(12, 'Stock Take Log', 'Stock Management'),
(13, 'Inventory', 'Stock Management'),
(14, 'Asset Management', 'Stock Management'),
(15, 'Calculation Setup', 'Stock Management'),
(16, 'Customer', 'Master Data'),
(17, 'Destination', 'Master Data'),
(18, 'Product', 'Master Data'),
(19, 'Raw Material', 'Master Data'),
(20, 'Supplier', 'Master Data'),
(21, 'Vehicle', 'Master Data'),
(22, 'Sales Representative', 'Master Data'),
(23, 'Transporter', 'Master Data'),
(24, 'Plant', 'Master Data'),
(25, 'Reason', 'Master Data'),
(26, 'Sales', 'Report'),
(27, 'Purchase', 'Report'),
(28, 'Public', 'Report'),
(29, 'Audit Log', 'Report'),
(30, 'Api Log', 'Report'),
(31, 'Cronjob Setup', 'Setting'),
(32, 'Company Profile', 'Setting'),
(33, 'Port Setup', 'Setting'),
(34, 'User Setup', 'User Management'),
(35, 'Role', 'User Management'),
(36, 'Module', 'User Management'),
(37, 'Permission', 'User Management'),
(38, 'Dashboard', 'Dashboard');

TRUNCATE TABLE `permissions`;
INSERT INTO `permissions` (`id`, `name`, `modules`) VALUES
(4, 'view', '[\"All\"]'),
(5, 'create', '[\"8\",\"6\",\"16\",\"17\",\"24\",\"18\",\"19\",\"25\",\"22\",\"20\",\"23\",\"21\",\"31\",\"14\",\"15\",\"11\",\"36\",\"37\",\"35\",\"34\",\"3\",\"2\",\"5\",\"1\",\"4\"]'),
(6, 'edit', '[\"8\",\"6\",\"16\",\"17\",\"24\",\"18\",\"19\",\"25\",\"22\",\"20\",\"23\",\"21\",\"32\",\"31\",\"33\",\"14\",\"15\",\"13\",\"11\",\"36\",\"37\",\"35\",\"34\",\"3\",\"2\",\"5\",\"1\",\"4\"]'),
(7, 'delete', '[\"8\",\"6\",\"16\",\"17\",\"24\",\"18\",\"19\",\"25\",\"22\",\"20\",\"23\",\"21\",\"31\",\"14\",\"15\",\"36\",\"37\",\"35\",\"34\",\"3\",\"2\",\"5\",\"1\",\"4\"]'),
(8, 'weight_out', '[\"3\",\"2\",\"5\",\"1\",\"4\"]'),
(9, 'display_do', '[\"3\",\"2\",\"5\",\"1\",\"4\"]'),
(10, 'manual_weighing', '[\"3\",\"2\",\"5\",\"1\",\"4\"]'),
(11, 'manual_date_change', '[\"3\",\"2\",\"5\",\"1\",\"4\"]'),
(12, 'print', '[\"28\",\"27\",\"26\",\"11\",\"3\",\"2\",\"5\",\"1\",\"4\"]'),
(13, 'view_all_plants', '[\"7\",\"9\",\"8\",\"6\",\"10\",\"38\",\"28\",\"27\",\"26\",\"14\",\"15\",\"13\",\"11\",\"12\",\"34\",\"3\",\"2\",\"5\",\"1\",\"4\"]'),
(14, 'download_template', '[\"8\",\"6\",\"16\",\"17\",\"24\",\"18\",\"19\",\"25\",\"22\",\"20\",\"23\",\"21\",\"15\",\"34\"]'),
(15, 'import_sales_orders', '[\"6\"]'),
(16, 'import_purchase_orders', '[\"8\"]'),
(17, 'upload_bom_list', '[\"11\"]'),
(18, 'upload_excel', '[\"16\",\"17\",\"24\",\"18\",\"19\",\"25\",\"22\",\"20\",\"23\",\"21\",\"15\",\"34\"]'),
(19, 'export_excel', '[\"7\",\"9\",\"8\",\"6\",\"10\",\"16\",\"17\",\"24\",\"18\",\"19\",\"25\",\"22\",\"20\",\"23\",\"21\",\"29\",\"28\",\"27\",\"26\",\"12\",\"34\"]'),
(20, 'export_supply_excel', '[\"6\"]'),
(21, 'export_received_excel', '[\"8\"]'),
(22, 'generate_stock_take', '[\"12\"]'),
(23, 'export_summary_report', '[\"28\",\"27\",\"26\"]'),
(24, 'pull_from_sql', '[\"8\",\"6\",\"16\",\"18\",\"19\",\"22\",\"20\",\"23\"]'),
(25, 'post_to_sql', '[\"7\",\"9\"]'),
(26, 'complete', '[\"8\",\"6\"]'),
(27, 'revert', '[\"8\",\"6\"]'),
(28, 'export_sales_report', '[\"26\"]'),
(30, 'export_purchase_report', '[\"27\"]'),
(31, 'export_public_report', '[\"28\"]'),
(32, 'reactivate', '[\"16\",\"17\",\"24\",\"18\",\"19\",\"25\",\"22\",\"20\",\"23\",\"21\",\"34\"]'),
(33, 'include_price', '[\"8\",\"6\"]'),
(34, 'assign_permissions', '[\"35\"]');

-- 29/04/2026 --
CREATE TABLE `Stock_Take_List` (
  `plant_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `batch_drum` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

UPDATE permissions SET name='include_price', modules='[\"7\",\"9\",\"8\",\"6\",\"29\",\"28\",\"27\",\"26\"]' WHERE id=33;

UPDATE permissions SET name='cancelled' WHERE id=7;

-- 10/06/2026 --
INSERT INTO `permissions` (`id`, `name`, `modules`) VALUES
(35, 'reset_password', '[\"34\"]');

-- 20/06/2026 --
UPDATE Raw_Mat SET type = 'Bitumen' WHERE raw_mat_code IN ('BTBI001', 'BTBI002');

-- 10/09/2026 --
ALTER TABLE `Weight` ADD `manual_weight_reason` TEXT NULL AFTER `manual_weight`;

ALTER TABLE `Weight_Log` ADD `manual_weight_reason` TEXT NULL AFTER `manual_weight`;

DELIMITER $$

CREATE OR REPLACE TRIGGER `TRG_INS_WEIGHT` AFTER INSERT ON `Weight`
 FOR EACH ROW INSERT INTO Weight_Log (
    transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight_uom, supplier_weight, po_supply_weight, order_weight_uom, order_weight, tin_no, id_no, id_type, plant_code, plant_name, site_code, site_name, agent_code, agent_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, ex_del, raw_mat_code,raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, weight_different, balance, is_complete, is_cancel, is_approved, manual_weight, manual_weight_reason, indicator_id, weighbridge_id, indicator_id_2, unit_price, sub_total, sst, total_price, load_drum, no_of_drum, batch_drum, status, approved_by, approved_reason, cancel_id, cancelled_reason, action_id, action_by, event_date
) 
VALUES (
    NEW.transaction_id, NEW.transaction_status, NEW.weight_type, NEW.transaction_date, NEW.lorry_plate_no1, NEW.lorry_plate_no2, NEW.supplier_weight_uom, NEW.supplier_weight, NEW.po_supply_weight, NEW.order_weight_uom, NEW.order_weight, NEW.tin_no, NEW.id_no, NEW.id_type, NEW.plant_code, NEW.plant_name, NEW.site_code, NEW.site_name, NEW.agent_code, NEW.agent_name, NEW.customer_code, NEW.customer_name, NEW.supplier_code, NEW.supplier_name, NEW.product_code, NEW.product_name, NEW.product_description, NEW.ex_del, NEW.raw_mat_code, NEW.raw_mat_name, NEW.container_no, NEW.invoice_no, NEW.purchase_order, NEW.delivery_no, NEW.transporter_code, NEW.transporter, NEW.destination_code, NEW.destination, NEW.remarks, NEW.gross_weight1, NEW.gross_weight1_date, NEW.tare_weight1, NEW.tare_weight1_date, NEW.nett_weight1, NEW.gross_weight2, NEW.gross_weight2_date, NEW.tare_weight2, NEW.tare_weight2_date, NEW.nett_weight2, NEW.reduce_weight, NEW.final_weight, NEW.weight_different, NEW.balance, NEW.is_complete, NEW.is_cancel, NEW.is_approved, NEW.manual_weight, NEW.manual_weight_reason, NEW.indicator_id, NEW.weighbridge_id, NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.total_price, NEW.load_drum, NEW.no_of_drum, NEW.batch_drum, NEW.status, NEW.approved_by, NEW.approved_reason, NEW.cancel_id, NEW.cancelled_reason, 1, NEW.created_by, NEW.created_date
)

$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_WEIGHT` BEFORE UPDATE ON `Weight`
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
        transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight_uom, supplier_weight, po_supply_weight, order_weight_uom, order_weight, tin_no, id_no, id_type, plant_code, plant_name, site_code, site_name, agent_code, agent_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, ex_del, raw_mat_code,raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, weight_different, balance, is_complete, is_cancel, is_approved, manual_weight, manual_weight_reason, indicator_id, weighbridge_id, indicator_id_2, unit_price, sub_total, sst, total_price, load_drum, no_of_drum, batch_drum, status, approved_by, approved_reason, cancel_id, cancelled_reason, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.transaction_id, NEW.transaction_status, NEW.weight_type, NEW.transaction_date, 
        NEW.lorry_plate_no1, NEW.lorry_plate_no2, NEW.supplier_weight_uom, NEW.supplier_weight, NEW.po_supply_weight, 
        NEW.order_weight_uom, NEW.order_weight, NEW.tin_no, NEW.id_no, NEW.id_type, NEW.plant_code, NEW.plant_name, NEW.site_code, NEW.site_name, 
        NEW.agent_code, NEW.agent_name, NEW.customer_code, NEW.customer_name, 
        NEW.supplier_code, NEW.supplier_name, NEW.product_code, NEW.product_name, 
        NEW.product_description, NEW.ex_del, NEW.raw_mat_code, NEW.raw_mat_name, 
        NEW.container_no, NEW.invoice_no, NEW.purchase_order, NEW.delivery_no, 
        NEW.transporter_code, NEW.transporter, NEW.destination_code, NEW.destination, 
        NEW.remarks, NEW.gross_weight1, NEW.gross_weight1_date, NEW.tare_weight1, 
        NEW.tare_weight1_date, NEW.nett_weight1, NEW.gross_weight2, NEW.gross_weight2_date, 
        NEW.tare_weight2, NEW.tare_weight2_date, NEW.nett_weight2, NEW.reduce_weight, 
        NEW.final_weight, NEW.weight_different, NEW.balance, NEW.is_complete, NEW.is_cancel, 
        NEW.is_approved, NEW.manual_weight, NEW.manual_weight_reason, NEW.indicator_id, NEW.weighbridge_id, 
        NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.total_price, NEW.load_drum, 
        NEW.no_of_drum, NEW.batch_drum, NEW.status, NEW.approved_by, NEW.approved_reason, NEW.cancel_id, NEW.cancelled_reason, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

-- 21/09/2026 --
CREATE TABLE `Stock_Adjustment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adjustment_no` varchar(50) NOT NULL,
  `adjustment_date` date NOT NULL,
  `plant_id` INT(11) NOT NULL,
  `batch_drum` varchar(50) NOT NULL,
  `remark` text DEFAULT NULL,
  `total_items` int(11) DEFAULT 0,
  `total_qty` varchar(20) DEFAULT '0',
  `total_cost` varchar(20) DEFAULT '0',
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `Stock_Adjustment_Log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stk_adjustment_id` int(11) NOT NULL,
  `adjustment_no` varchar(50) NOT NULL,
  `adjustment_date` date NOT NULL,
  `plant_id` INT(11) NOT NULL,
  `batch_drum` varchar(50) NOT NULL,
  `remark` text DEFAULT NULL,
  `total_items` int(11) DEFAULT 0,
  `total_qty` varchar(20) DEFAULT '0',
  `total_cost` varchar(20) DEFAULT '0',
  `action_id` varchar(5) NOT NULL,
  `action_by` varchar(15) NOT NULL,
  `event_date` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_STK_ADJ` AFTER INSERT ON `Stock_Adjustment` FOR EACH ROW 
  INSERT INTO Stock_Adjustment_Log (
    stk_adjustment_id, adjustment_no, adjustment_date, plant_id, batch_drum, remark, total_items, total_qty, total_cost, action_id, action_by, event_date
  ) 
  VALUES (
    NEW.id, NEW.adjustment_no, NEW.adjustment_date, NEW.plant_id, NEW.batch_drum, NEW.remark, NEW.total_items, NEW.total_qty, NEW.total_cost, 1, NEW.created_by, NOW()
  )
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_STK_ADJ` BEFORE UPDATE ON `Stock_Adjustment` FOR EACH ROW BEGIN
    DECLARE action_value INT;

    IF NEW.deleted = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    INSERT INTO Stock_Adjustment_Log (
        stk_adjustment_id, adjustment_no, adjustment_date, plant_id, batch_drum, remark, total_items, total_qty, total_cost, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.adjustment_no, NEW.adjustment_date, NEW.plant_id, NEW.batch_drum, NEW.remark, NEW.total_items, NEW.total_qty, NEW.total_cost, action_value, NEW.modified_by, NOW()
    );
END
$$
DELIMITER ;

CREATE TABLE `Stock_Adjustment_Items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adjustment_id` int(11) NOT NULL,
  `raw_mat_id` int(11) NOT NULL,
  `raw_mat_code` varchar(50) DEFAULT NULL,
  `quantity_before` varchar(20) DEFAULT '0',
  `adjustment_qty` varchar(20) DEFAULT '0',
  `quantity_after` varchar(20) DEFAULT '0',
  `unit_cost` varchar(20) DEFAULT '0',
  `total_cost` varchar(20) DEFAULT '0',
  `reason` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `created_datetime` datetime DEFAULT CURRENT_TIMESTAMP,
  `modified_datetime` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `Stock_Adjustment_Item_Log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `adjustment_id` int(11) NOT NULL,
  `raw_mat_id` int(11) NOT NULL,
  `raw_mat_code` varchar(50) DEFAULT NULL,
  `quantity_before` varchar(20) DEFAULT '0',
  `adjustment_qty` varchar(20) DEFAULT '0',
  `quantity_after` varchar(20) DEFAULT '0',
  `unit_cost` varchar(20) DEFAULT '0',
  `total_cost` varchar(20) DEFAULT '0',
  `reason` varchar(255) DEFAULT NULL,
  `action_id` varchar(5) NOT NULL,
  `action_by` varchar(15) NOT NULL,
  `event_date` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_STK_ADJ_ITEM` AFTER INSERT ON `Stock_Adjustment_Items` FOR EACH ROW 
  INSERT INTO Stock_Adjustment_Item_Log (
    item_id, adjustment_id, raw_mat_id, raw_mat_code, quantity_before, adjustment_qty, quantity_after, unit_cost, total_cost, reason, action_id, action_by, event_date
  ) 
  VALUES (
    NEW.id, NEW.adjustment_id, NEW.raw_mat_id, NEW.raw_mat_code, NEW.quantity_before, NEW.adjustment_qty, NEW.quantity_after, NEW.unit_cost, NEW.total_cost, NEW.reason, 1, NEW.created_by, NOW()
  )
$$
DELIMITER ;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_STK_ADJ_ITEM` BEFORE UPDATE ON `Stock_Adjustment_Items` FOR EACH ROW BEGIN
    DECLARE action_value INT;

    IF NEW.deleted = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    INSERT INTO Stock_Adjustment_Item_Log (
        item_id, adjustment_id, raw_mat_id, raw_mat_code, quantity_before, adjustment_qty, quantity_after, unit_cost, total_cost, reason, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.adjustment_id, NEW.raw_mat_id, NEW.raw_mat_code, NEW.quantity_before, NEW.adjustment_qty, NEW.quantity_after, NEW.unit_cost, NEW.total_cost, NEW.reason, action_value, NEW.modified_by, NOW()
    );
END
$$
DELIMITER ;

ALTER TABLE `Inventory` ADD `batch_drum` VARCHAR(10) NULL AFTER `plant_code`;
ALTER TABLE `Inventory_Log` ADD `batch_drum` VARCHAR(10) NULL AFTER `plant_code`;

DROP TRIGGER TRG_INS_INV;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_INV_LOG` AFTER INSERT ON `Inventory`
 FOR EACH ROW INSERT INTO Inventory_Log (
    inventory_id, raw_mat_id, raw_mat_basic_uom, raw_mat_weight, raw_mat_count, plant_id, plant_code, batch_drum, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.raw_mat_id, NEW.raw_mat_basic_uom, NEW.raw_mat_weight, NEW.raw_mat_count, NEW.plant_id, NEW.plant_code, NEW.batch_drum, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_INV_LOG` BEFORE UPDATE ON `Inventory`
 FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if deleted = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Sales_Order table
    INSERT INTO Inventory_Log (
        inventory_id, raw_mat_id, raw_mat_basic_uom, raw_mat_weight, raw_mat_count, plant_id, plant_code, batch_drum, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.raw_mat_id, NEW.raw_mat_basic_uom, NEW.raw_mat_weight, NEW.raw_mat_count, NEW.plant_id, NEW.plant_code, NEW.batch_drum, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

-- 01/10/2026 --
INSERT INTO `modules` (`id`, `name`, `category`) VALUES
(39, 'Stock Adjustment', 'Stock Management');

UPDATE permissions SET modules = JSON_ARRAY_APPEND(modules, '$', '39') WHERE id IN (5, 6, 7, 13);

-- 05/10/2026 --
CREATE TABLE `Login_Log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `login_datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `Login_Log` ADD PRIMARY KEY (`id`), ADD KEY `idx_login_log_user` (`user_id`), ADD KEY `idx_login_log_datetime` (`login_datetime`);

ALTER TABLE `Login_Log` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `Weight` ADD `is_edit` VARCHAR(3) NOT NULL DEFAULT 'N' AFTER `is_complete`;

ALTER TABLE `Weight` ADD `edit_reason` TEXT NULL AFTER `is_edit`;

ALTER TABLE `Weight_Log` ADD `is_edit` VARCHAR(3) NULL DEFAULT 'N' AFTER `is_complete`;

ALTER TABLE `Weight_Log` ADD `edit_reason` TEXT NULL AFTER `is_edit`;

DELIMITER $$

CREATE OR REPLACE TRIGGER `TRG_INS_WEIGHT` AFTER INSERT ON `Weight`
 FOR EACH ROW INSERT INTO Weight_Log (
    transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight_uom, supplier_weight, po_supply_weight, order_weight_uom, order_weight, tin_no, id_no, id_type, plant_code, plant_name, site_code, site_name, agent_code, agent_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, ex_del, raw_mat_code,raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, weight_different, balance, is_complete, is_edit, edit_reason, is_cancel, is_approved, manual_weight, manual_weight_reason, indicator_id, weighbridge_id, indicator_id_2, unit_price, sub_total, sst, total_price, load_drum, no_of_drum, batch_drum, status, approved_by, approved_reason, cancel_id, cancelled_reason, action_id, action_by, event_date
) 
VALUES (
    NEW.transaction_id, NEW.transaction_status, NEW.weight_type, NEW.transaction_date, NEW.lorry_plate_no1, NEW.lorry_plate_no2, NEW.supplier_weight_uom, NEW.supplier_weight, NEW.po_supply_weight, NEW.order_weight_uom, NEW.order_weight, NEW.tin_no, NEW.id_no, NEW.id_type, NEW.plant_code, NEW.plant_name, NEW.site_code, NEW.site_name, NEW.agent_code, NEW.agent_name, NEW.customer_code, NEW.customer_name, NEW.supplier_code, NEW.supplier_name, NEW.product_code, NEW.product_name, NEW.product_description, NEW.ex_del, NEW.raw_mat_code, NEW.raw_mat_name, NEW.container_no, NEW.invoice_no, NEW.purchase_order, NEW.delivery_no, NEW.transporter_code, NEW.transporter, NEW.destination_code, NEW.destination, NEW.remarks, NEW.gross_weight1, NEW.gross_weight1_date, NEW.tare_weight1, NEW.tare_weight1_date, NEW.nett_weight1, NEW.gross_weight2, NEW.gross_weight2_date, NEW.tare_weight2, NEW.tare_weight2_date, NEW.nett_weight2, NEW.reduce_weight, NEW.final_weight, NEW.weight_different, NEW.balance, NEW.is_complete, NEW.is_edit, NEW.edit_reason, NEW.is_cancel, NEW.is_approved, NEW.manual_weight, NEW.manual_weight_reason, NEW.indicator_id, NEW.weighbridge_id, NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.total_price, NEW.load_drum, NEW.no_of_drum, NEW.batch_drum, NEW.status, NEW.approved_by, NEW.approved_reason, NEW.cancel_id, NEW.cancelled_reason, 1, NEW.created_by, NEW.created_date
)

$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_WEIGHT` BEFORE UPDATE ON `Weight`
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
        transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight_uom, supplier_weight, po_supply_weight, order_weight_uom, order_weight, tin_no, id_no, id_type, plant_code, plant_name, site_code, site_name, agent_code, agent_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, ex_del, raw_mat_code,raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, weight_different, balance, is_complete, is_edit, edit_reason, is_cancel, is_approved, manual_weight, manual_weight_reason, indicator_id, weighbridge_id, indicator_id_2, unit_price, sub_total, sst, total_price, load_drum, no_of_drum, batch_drum, status, approved_by, approved_reason, cancel_id, cancelled_reason, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.transaction_id, NEW.transaction_status, NEW.weight_type, NEW.transaction_date, 
        NEW.lorry_plate_no1, NEW.lorry_plate_no2, NEW.supplier_weight_uom, NEW.supplier_weight, NEW.po_supply_weight, 
        NEW.order_weight_uom, NEW.order_weight, NEW.tin_no, NEW.id_no, NEW.id_type, NEW.plant_code, NEW.plant_name, NEW.site_code, NEW.site_name, 
        NEW.agent_code, NEW.agent_name, NEW.customer_code, NEW.customer_name, 
        NEW.supplier_code, NEW.supplier_name, NEW.product_code, NEW.product_name, 
        NEW.product_description, NEW.ex_del, NEW.raw_mat_code, NEW.raw_mat_name, 
        NEW.container_no, NEW.invoice_no, NEW.purchase_order, NEW.delivery_no, 
        NEW.transporter_code, NEW.transporter, NEW.destination_code, NEW.destination, 
        NEW.remarks, NEW.gross_weight1, NEW.gross_weight1_date, NEW.tare_weight1, 
        NEW.tare_weight1_date, NEW.nett_weight1, NEW.gross_weight2, NEW.gross_weight2_date, 
        NEW.tare_weight2, NEW.tare_weight2_date, NEW.nett_weight2, NEW.reduce_weight, 
        NEW.final_weight, NEW.weight_different, NEW.balance, NEW.is_complete, NEW.is_edit, NEW.edit_reason, NEW.is_cancel, 
        NEW.is_approved, NEW.manual_weight, NEW.manual_weight_reason, NEW.indicator_id, NEW.weighbridge_id, 
        NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.total_price, NEW.load_drum, 
        NEW.no_of_drum, NEW.batch_drum, NEW.status, NEW.approved_by, NEW.approved_reason, NEW.cancel_id, NEW.cancelled_reason, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_SO` AFTER INSERT ON `Sales_Order`
 FOR EACH ROW INSERT INTO Sales_Order_Log (
    company_code, company_name, customer_code, customer_name, site_code, site_name, order_date, order_no, so_no, delivery_date, agent_code, agent_name, destination_code, destination_name, deliver_to_name, product_code, product_name, plant_code, plant_name, transporter_code, transporter_name, veh_number, batch_drum, exquarry_or_delivered, order_load, order_quantity, balance, converted_order_qty, converted_balance, converted_unit, unit_price, total_price, remarks, status, action_id, action_by, event_date
) 
VALUES (
    NEW.company_code, NEW.company_name, NEW.customer_code, NEW.customer_name, NEW.site_code, NEW.site_name, NEW.order_date, NEW.order_no, NEW.so_no, NEW.delivery_date, NEW.agent_code, NEW.agent_name, NEW.destination_code, NEW.destination_name, NEW.deliver_to_name, NEW.product_code, NEW.product_name, NEW.plant_code, NEW.plant_name, NEW.transporter_code, NEW.transporter_name, NEW.veh_number, NEW.batch_drum, NEW.exquarry_or_delivered, NEW.order_load, NEW.order_quantity, NEW.balance, NEW.converted_order_qty, NEW.converted_balance, NEW.converted_unit, NEW.unit_price, NEW.total_price, NEW.remarks, NEW.status, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_SO` BEFORE UPDATE ON `Sales_Order`
 FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if deleted = 1, set action_id to 3, otherwise set to 2
    IF NEW.deleted = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Sales_Order table
    INSERT INTO Sales_Order_Log (
        company_code, company_name, customer_code, customer_name, site_code, site_name, order_date, order_no, so_no, delivery_date, agent_code, agent_name, destination_code, destination_name, deliver_to_name, product_code, product_name, plant_code, plant_name, transporter_code, transporter_name, veh_number, batch_drum, exquarry_or_delivered, order_load, order_quantity, balance, converted_order_qty, converted_balance, converted_unit, unit_price, total_price, remarks, status, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.company_code, NEW.company_name, NEW.customer_code, NEW.customer_name, NEW.site_code, NEW.site_name, NEW.order_date, NEW.order_no, NEW.so_no, NEW.delivery_date, NEW.agent_code, NEW.agent_name, NEW.destination_code, NEW.destination_name, NEW.deliver_to_name, NEW.product_code, NEW.product_name, NEW.plant_code, NEW.plant_name, NEW.transporter_code, NEW.transporter_name, NEW.veh_number, NEW.batch_drum, NEW.exquarry_or_delivered, NEW.order_load, NEW.order_quantity, NEW.balance, NEW.converted_order_qty, NEW.converted_balance, NEW.converted_unit, NEW.unit_price, NEW.total_price, NEW.remarks, NEW.status, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

-- 09/10/2026 --
INSERT INTO `permissions` (`id`, `name`, `modules`) VALUES
(36, 'cancel_before_complete', '[\"3\",\"2\",\"5\",\"1\",\"4\"]'),
(37, 'cancel_after_complete', '[\"3\",\"2\",\"5\",\"1\",\"4\"]');

INSERT INTO role_permissions (role_id, module_id, permission_id)
SELECT role_id, module_id, 36 FROM role_permissions WHERE permission_id = 7 AND module_id IN (1, 2, 3, 4, 5);
INSERT INTO role_permissions (role_id, module_id, permission_id)
SELECT role_id, module_id, 37 FROM role_permissions WHERE permission_id = 7 AND module_id IN (1, 2, 3, 4, 5);
DELETE FROM role_permissions WHERE permission_id = 7 AND module_id IN (1, 2, 3, 4, 5);

UPDATE permissions SET modules = '[\"8\",\"6\",\"16\",\"17\",\"24\",\"18\",\"19\",\"25\",\"22\",\"20\",\"23\",\"21\",\"31\",\"14\",\"15\",\"36\",\"37\",\"35\",\"34\",\"39\"]' WHERE id = 7;

-- 22/11/2026 --
ALTER TABLE `Bitumen` ADD `remarks` TEXT NULL AFTER `batch_drum`;

ALTER TABLE `Bitumen_Log` ADD `remarks` TEXT NULL AFTER `batch_drum`;

DELIMITER $$

CREATE OR REPLACE TRIGGER `TRG_INS_BITUMEN` AFTER INSERT ON `Bitumen`
 FOR EACH ROW INSERT INTO Bitumen_Log (
    bitumen_id, `60/70`, pg76, crmb, lfo, diesel, hotoil, other_diesel, fibre, data, declaration_datetime, plant_id, plant_code, batch_drum, remarks, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.`60/70`, NEW.pg76, NEW.crmb, NEW.lfo, NEW.diesel, NEW.hotoil, NEW.other_diesel, NEW.fibre, NEW.data, NEW.declaration_datetime, NEW.plant_id, NEW.plant_code, NEW.batch_drum, NEW.remarks, 1, NEW.created_by, NEW.created_datetime
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_BITUMEN` BEFORE UPDATE ON `Bitumen`
 FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if deleted = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Bitumen_Log table
    INSERT INTO Bitumen_Log (
        bitumen_id, `60/70`, pg76, crmb, lfo, diesel, hotoil, other_diesel, fibre, data, declaration_datetime, plant_id, plant_code, batch_drum, remarks, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.`60/70`, NEW.pg76, NEW.crmb, NEW.lfo, NEW.diesel, NEW.hotoil, NEW.other_diesel, NEW.fibre, NEW.data, NEW.declaration_datetime, NEW.plant_id, NEW.plant_code, NEW.batch_drum, NEW.remarks, action_value, NEW.modified_by, NEW.modified_datetime
    );
END
$$
DELIMITER ;

-- 22/11/2026 --
ALTER TABLE `Bitumen` ADD `stock_take_no` VARCHAR(50) NULL AFTER `id`;

ALTER TABLE `Bitumen_Log` ADD `stock_take_no` VARCHAR(50) NULL AFTER `bitumen_id`;

DELIMITER $$

CREATE OR REPLACE TRIGGER `TRG_INS_BITUMEN` AFTER INSERT ON `Bitumen`
 FOR EACH ROW INSERT INTO Bitumen_Log (
    bitumen_id, stock_take_no, `60/70`, pg76, crmb, lfo, diesel, hotoil, other_diesel, fibre, data, declaration_datetime, plant_id, plant_code, batch_drum, remarks, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.stock_take_no, NEW.`60/70`, NEW.pg76, NEW.crmb, NEW.lfo, NEW.diesel, NEW.hotoil, NEW.other_diesel, NEW.fibre, NEW.data, NEW.declaration_datetime, NEW.plant_id, NEW.plant_code, NEW.batch_drum, NEW.remarks, 1, NEW.created_by, NEW.created_datetime
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_BITUMEN` BEFORE UPDATE ON `Bitumen`
 FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if deleted = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Bitumen_Log table
    INSERT INTO Bitumen_Log (
        bitumen_id, stock_take_no, `60/70`, pg76, crmb, lfo, diesel, hotoil, other_diesel, fibre, data, declaration_datetime, plant_id, plant_code, batch_drum, remarks, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.stock_take_no, NEW.`60/70`, NEW.pg76, NEW.crmb, NEW.lfo, NEW.diesel, NEW.hotoil, NEW.other_diesel, NEW.fibre, NEW.data, NEW.declaration_datetime, NEW.plant_id, NEW.plant_code, NEW.batch_drum, NEW.remarks, action_value, NEW.modified_by, NEW.modified_datetime
    );
END
$$
DELIMITER ;

ALTER TABLE `Plant` ADD `stock_take_batch` INT NOT NULL DEFAULT 1 AFTER `do_no`, ADD `stock_take_drum` INT NOT NULL DEFAULT 1 AFTER `stock_take_batch`;

ALTER TABLE `Plant_Log` ADD `stock_take_batch` INT NULL AFTER `do_no`, ADD `stock_take_drum` INT NULL AFTER `stock_take_batch`;

DELIMITER $$

CREATE OR REPLACE TRIGGER `TRG_INS_PLANT` AFTER INSERT ON `Plant`
 FOR EACH ROW INSERT INTO Plant_Log (
    plant_id, plant_code, name, address_line_1, address_line_2, address_line_3, phone_no, fax_no, sales, purchase, locals, do_no, stock_take_batch, stock_take_drum, default_type, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.plant_code, NEW.name, NEW.address_line_1, NEW.address_line_2, NEW.address_line_3, NEW.phone_no, NEW.fax_no, NEW.sales, NEW.purchase, NEW.locals, NEW.do_no, NEW.stock_take_batch, NEW.stock_take_drum, NEW.default_type, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_PLANT` BEFORE UPDATE ON `Plant`
 FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if deleted = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Plant_Log table
    INSERT INTO Plant_Log (
        plant_id, plant_code, name, address_line_1, address_line_2, address_line_3, phone_no, fax_no, sales, purchase, locals, do_no, stock_take_batch, stock_take_drum, default_type, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.plant_code, NEW.name, NEW.address_line_1, NEW.address_line_2, NEW.address_line_3, NEW.phone_no, NEW.fax_no, NEW.sales, NEW.purchase, NEW.locals, NEW.do_no, NEW.stock_take_batch, NEW.stock_take_drum, NEW.default_type, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

ALTER TABLE `Assets` ADD `supplier_id` INT(11) NULL AFTER `diameter`;

ALTER TABLE `Assets_Log` ADD `supplier_id` INT(11) NULL AFTER `diameter`;

DELIMITER $$

CREATE OR REPLACE TRIGGER `TRG_INS_ASSET` AFTER INSERT ON `Assets`
 FOR EACH ROW INSERT INTO Assets_Log (
    type, name, plant_id, batch_drum, length, height, diameter, supplier_id, action_id, action_by, event_date
) 
VALUES (
    NEW.type, NEW.name, NEW.plant_id, NEW.batch_drum, NEW.length, NEW.height, NEW.diameter, NEW.supplier_id, 1, NEW.created_by, NEW.created_at
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_ASSET` BEFORE UPDATE ON `Assets`
 FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if deleted = 1, set action_id to 3, otherwise set to 2
    IF NEW.deleted = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Assets_Log table
    INSERT INTO Assets_Log (
        type, name, plant_id, batch_drum, length, height, diameter, supplier_id, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.type, NEW.name, NEW.plant_id, NEW.batch_drum, NEW.length, NEW.height, NEW.diameter, NEW.supplier_id, action_value, NEW.modified_by, NEW.modified_at
    );
END
$$
DELIMITER ;

ALTER TABLE `Bitumen` ADD `cancel_reason` TEXT NULL AFTER `remarks`;

ALTER TABLE `Bitumen_Log` ADD `cancel_reason` TEXT NULL AFTER `remarks`;

UPDATE permissions SET modules = JSON_ARRAY_APPEND(modules, '$', '11') WHERE id = 7 AND NOT JSON_CONTAINS(modules, '"11"');

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_BITUMEN` BEFORE UPDATE ON `Bitumen`
 FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if deleted = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Bitumen_Log table
    INSERT INTO Bitumen_Log (
        bitumen_id, stock_take_no, `60/70`, pg76, crmb, lfo, diesel, hotoil, other_diesel, fibre, data, declaration_datetime, plant_id, plant_code, batch_drum, remarks, cancel_reason, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.stock_take_no, NEW.`60/70`, NEW.pg76, NEW.crmb, NEW.lfo, NEW.diesel, NEW.hotoil, NEW.other_diesel, NEW.fibre, NEW.data, NEW.declaration_datetime, NEW.plant_id, NEW.plant_code, NEW.batch_drum, NEW.remarks, NEW.cancel_reason, action_value, NEW.modified_by, NEW.modified_datetime
    );
END
$$
DELIMITER ;

-- 24/11/2026 --
ALTER TABLE `Product` ADD `description2` VARCHAR(255) NULL AFTER `description`;

ALTER TABLE `Product_Log` ADD `description2` VARCHAR(255) NULL AFTER `description`;

DELIMITER $$

CREATE OR REPLACE TRIGGER `TRG_INS_PRODUCT` AFTER INSERT ON `Product`
 FOR EACH ROW INSERT INTO Product_Log (
    product_id, product_code, name, description, description2, price, variance, high, low, basic_uom, type, action_id, action_by, event_date
)
VALUES (
    NEW.id, NEW.product_code, NEW.name, NEW.description, NEW.description2, NEW.price, NEW.variance, NEW.high, NEW.low, NEW.basic_uom, NEW.type, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_PRODUCT` BEFORE UPDATE ON `Product`
 FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if deleted = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Product_Log table
    INSERT INTO Product_Log (
        product_id, product_code, name, description, description2, price, variance, high, low, basic_uom, type, action_id, action_by, event_date
    )
    VALUES (
        NEW.id, NEW.product_code, NEW.name, NEW.description, NEW.description2, NEW.price, NEW.variance, NEW.high, NEW.low, NEW.basic_uom, NEW.type, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

-- 25/11/2026 --
ALTER TABLE `Sales_Order` ADD `closed_date` DATETIME NULL DEFAULT NULL AFTER `status`;
ALTER TABLE `Sales_Order_Log` ADD `closed_date` DATETIME NULL DEFAULT NULL AFTER `status`;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_SO` AFTER INSERT ON `Sales_Order`
 FOR EACH ROW INSERT INTO Sales_Order_Log (
    company_code, company_name, customer_code, customer_name, site_code, site_name, order_date, order_no, so_no, delivery_date, agent_code, agent_name, destination_code, destination_name, deliver_to_name, product_code, product_name, plant_code, plant_name, transporter_code, transporter_name, veh_number, batch_drum, exquarry_or_delivered, order_load, order_quantity, balance, converted_order_qty, converted_balance, converted_unit, unit_price, total_price, remarks, status, closed_date, action_id, action_by, event_date
) 
VALUES (
    NEW.company_code, NEW.company_name, NEW.customer_code, NEW.customer_name, NEW.site_code, NEW.site_name, NEW.order_date, NEW.order_no, NEW.so_no, NEW.delivery_date, NEW.agent_code, NEW.agent_name, NEW.destination_code, NEW.destination_name, NEW.deliver_to_name, NEW.product_code, NEW.product_name, NEW.plant_code, NEW.plant_name, NEW.transporter_code, NEW.transporter_name, NEW.veh_number, NEW.batch_drum, NEW.exquarry_or_delivered, NEW.order_load, NEW.order_quantity, NEW.balance, NEW.converted_order_qty, NEW.converted_balance, NEW.converted_unit, NEW.unit_price, NEW.total_price, NEW.remarks, NEW.status, NEW.closed_date, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_SO` BEFORE UPDATE ON `Sales_Order`
 FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if deleted = 1, set action_id to 3, otherwise set to 2
    IF NEW.deleted = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Sales_Order table
    INSERT INTO Sales_Order_Log (
        company_code, company_name, customer_code, customer_name, site_code, site_name, order_date, order_no, so_no, delivery_date, agent_code, agent_name, destination_code, destination_name, deliver_to_name, product_code, product_name, plant_code, plant_name, transporter_code, transporter_name, veh_number, batch_drum, exquarry_or_delivered, order_load, order_quantity, balance, converted_order_qty, converted_balance, converted_unit, unit_price, total_price, remarks, status, closed_date, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.company_code, NEW.company_name, NEW.customer_code, NEW.customer_name, NEW.site_code, NEW.site_name, NEW.order_date, NEW.order_no, NEW.so_no, NEW.delivery_date, NEW.agent_code, NEW.agent_name, NEW.destination_code, NEW.destination_name, NEW.deliver_to_name, NEW.product_code, NEW.product_name, NEW.plant_code, NEW.plant_name, NEW.transporter_code, NEW.transporter_name, NEW.veh_number, NEW.batch_drum, NEW.exquarry_or_delivered, NEW.order_load, NEW.order_quantity, NEW.balance, NEW.converted_order_qty, NEW.converted_balance, NEW.converted_unit, NEW.unit_price, NEW.total_price, NEW.remarks, NEW.status, NEW.closed_date, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

ALTER TABLE `Purchase_Order` ADD `closed_date` DATETIME NULL DEFAULT NULL AFTER `status`;
ALTER TABLE `Purchase_Order_Log` ADD `closed_date` DATETIME NULL DEFAULT NULL AFTER `status`;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_PO` AFTER INSERT ON `Purchase_Order`
 FOR EACH ROW INSERT INTO Purchase_Order_Log (
    company_code, company_name, supplier_code, supplier_name, site_code, site_name, order_date, order_no, po_no, delivery_date, agent_code, agent_name, destination_code, destination_name, deliver_to_name, raw_mat_code, raw_mat_name, plant_code, plant_name, transporter_code, transporter_name, veh_number, batch_drum, exquarry_or_delivered, order_load, order_quantity, balance, converted_order_qty, converted_balance, converted_unit, unit_price, total_price, remarks, status, closed_date, action_id, action_by, event_date
) 
VALUES (
    NEW.company_code, NEW.company_name, NEW.supplier_code, NEW.supplier_name, NEW.site_code, NEW.site_name, NEW.order_date, NEW.order_no, NEW.po_no, NEW.delivery_date, NEW.agent_code, NEW.agent_name, NEW.destination_code, NEW.destination_name, NEW.deliver_to_name, NEW.raw_mat_code, NEW.raw_mat_name, NEW.plant_code, NEW.plant_name, NEW.transporter_code, NEW.transporter_name, NEW.veh_number, NEW.batch_drum, NEW.exquarry_or_delivered, NEW.order_load, NEW.order_quantity, NEW.balance, NEW.converted_order_qty, NEW.converted_balance, NEW.converted_unit, NEW.unit_price, NEW.total_price, NEW.remarks, NEW.status, NEW.closed_date, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_PO` BEFORE UPDATE ON `Purchase_Order`
 FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if deleted = 1, set action_id to 3, otherwise set to 2
    IF NEW.deleted = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Purchase_Order table
    INSERT INTO Purchase_Order_Log (
        company_code, company_name, supplier_code, supplier_name, site_code, site_name, order_date, order_no, po_no, delivery_date, agent_code,
        agent_name, destination_code, destination_name, deliver_to_name, raw_mat_code, raw_mat_name, plant_code, plant_name, transporter_code, transporter_name, veh_number, batch_drum, exquarry_or_delivered, order_load, order_quantity, balance, converted_order_qty, converted_balance, converted_unit, unit_price, total_price, remarks, status, closed_date, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.company_code, NEW.company_name, NEW.supplier_code, NEW.supplier_name, NEW.site_code, NEW.site_name, NEW.order_date, NEW.order_no, NEW.po_no, NEW.delivery_date, NEW.agent_code, NEW.agent_name, NEW.destination_code, NEW.destination_name, NEW.deliver_to_name, NEW.raw_mat_code, NEW.raw_mat_name, NEW.plant_code, NEW.plant_name, NEW.transporter_code, NEW.transporter_name, NEW.veh_number, NEW.batch_drum, NEW.exquarry_or_delivered, NEW.order_load, NEW.order_quantity, NEW.balance, NEW.converted_order_qty, NEW.converted_balance, NEW.converted_unit, NEW.unit_price, NEW.total_price, NEW.remarks, NEW.status, NEW.closed_date, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

-- one-time backfill: orders already closed get closed_date from their last modified_date
UPDATE `Sales_Order` SET closed_date = modified_date WHERE status IN ('Close','Closed');
UPDATE `Purchase_Order` SET closed_date = modified_date WHERE status IN ('Close','Closed');

-- 26/11/2026 --
ALTER TABLE `Company` ADD `closed_order_days` INT NOT NULL DEFAULT 30 AFTER `links`;
ALTER TABLE `Company_Log` ADD `closed_order_days` INT NULL DEFAULT NULL AFTER `links`;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_COMPANY` AFTER INSERT ON `Company`
 FOR EACH ROW INSERT INTO Company_Log (
    company_id, company_code, company_reg_no, name, address_line_1, address_line_2, address_line_3, phone_no, fax_no, links, closed_order_days, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.company_code, NEW.company_reg_no, NEW.name, NEW.address_line_1, NEW.address_line_2, NEW.address_line_3, NEW.phone_no, NEW.fax_no, NEW.links, NEW.closed_order_days, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_COMPANY` BEFORE UPDATE ON `Company`
 FOR EACH ROW BEGIN

    -- Insert into Company_Log table
    INSERT INTO Company_Log (
        company_id, company_code, company_reg_no, name, address_line_1, address_line_2, address_line_3, phone_no, fax_no, links, closed_order_days, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.company_code, NEW.company_reg_no, NEW.name, NEW.address_line_1, NEW.address_line_2, NEW.address_line_3, NEW.phone_no, NEW.fax_no, NEW.links, NEW.closed_order_days, 2, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

-- 26/11/2026 --
UPDATE permissions SET modules = JSON_ARRAY_APPEND(modules, '$', '35') WHERE id = 19 AND NOT JSON_CONTAINS(modules, '"35"');

-- 27/11/2026 --
CREATE TABLE `message_resource` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message_key_code` text NOT NULL,
  `en` text NOT NULL,
  `zh` text NOT NULL,
  `my` text DEFAULT NULL,
  `ne` text DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT 0,
  `created_by` varchar(50) DEFAULT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `modified_by` varchar(50) DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES
('add_new_code', 'Add New', '添加', 'Tambah', 'புதியதைச் சேர்க்கவும்'),
('message_code_code', 'Message Code', '留言代码', 'Kod Mesej', 'செய்தி முக்கிய குறியீடு'),
('edit_code', 'Edit', '编辑', 'Tukar', 'திருத்தவும்'),
('delete_code', 'Delete', '删除', 'Padam', 'நீக்கு'),
('dispatch_code', 'Sales', '销售', 'Jualan', 'விற்பனை'),
('receiving_code', 'Purchase', '采购', 'Pembelian', 'கொள்முதல்'),
('miscellaneous_code', 'Miscellaneous', '各种各样的', 'Macam-macam', 'இதர'),
('pending_container_weighing_code', 'Pending Container Weighing', '待称重集装箱', 'Menunggu Penimbangan Bekas', 'நிலுவையில் உள்ள கொள்கலன் எடையிடுதல்'),
('pending_lorry_weighing_code', 'Pending Lorry Weighing', '待称重卡车', 'Menunggu Penimbangan Lori', 'நிலுவையில் உள்ள லாரி எடையிடுதல்'),
('customer_code', 'Customer', '顾客', 'Customer', 'ग्राहक'),
('supplier_code', 'Supplier', '供应商', 'Pembekal', 'आपूर्तिकर्ता'),
('product_code', 'Item', '物品', 'Item', 'பொருள்'),
('weighing_report_code', 'Weighing Report', '称重报告', 'Laporan Timbangan', 'तौल रिपोर्ट'),
('staff_code', 'Staff', '职员', 'Pekerja\r\n', 'कर्मचारी'),
('master_data_code', 'Master Data', '主要数据', 'Data Penting', 'मूल तथ्यांक'),
('message_resource_code', 'Message Resources', '翻译', 'Terjemah', 'अनुवाद गर्नुहोस्'),
('setting_code', 'Settings', '设置', 'Tetapan', 'सेटिङहरू'),
('company_profile_code', 'Company Profile', '公司简介', 'Profil Syarikat', 'कम्पनी प्रोफइल'),
('profile_code', 'Profiles', '简介', 'Profil', 'प्रोफाइल'),
('change_password_code', 'Change Password', '更改密码', 'Tukar kata laluan', 'पासवर्ड परिवर्तन गर्नुहोस्'),
('logout_code', 'Logout', '登出', 'Log keluar', 'बाहिर निस्कनु'),
('vehicle_code', 'Vehicles', '汽车', 'Kenderaan', 'सवारी साधन'),
('transporter_code', 'Transporter', '运输者', 'Transport', 'ट्रान्सपोर्टर'),
('plant_code', 'Plant', '厂区', 'Plant', 'தொழிற்சாலை'),
('from_date_code', 'From Date', '从', 'Dari', 'मिति देखि'),
('daily_weighing_code', 'Daily Weighing', '重量称重', 'Timbang Berat', 'तौल तौल'),
('weighing_code', 'Weighing', '称重', 'Menimbang', 'तौल गर्दै'),
('to_date_code', 'To Date', '到', 'Hingga', 'मिति सम्म'),
('search_code', 'Search', '搜索', 'Cari', 'खोज्नुहोस्'),
('destination_code', 'Destination', '目的地', 'Destinasi', 'இலக்கு'),
('raw_material_code', 'Item', '物品', 'Item', 'பொருள்'),
('search_records_code', 'Search Records', '搜索记录', 'Cari Rekod', 'रेकर्ड खोज्नुहोस्'),
('batch_drum_code', 'Batch/Drum', '批次/桶', 'Kelompok/Dram', 'ब्याच/ड्रम'),
('batch_code', 'Batch', '批次', 'Kelompok', 'ब्याच'),
('drum_code', 'Drum', '桶', 'Dram', 'ड्रम'),
('stock_take_code', 'Stock Take', '盘点', 'Ambil Stok', 'स्टक गणना'),
('upload_bom_list_code', 'Upload Bom List', '上传物料清单', 'Muat Naik Senarai BOM', 'BOM सूची अपलोड गर्नुहोस्'),
('refresh_code', 'Refresh', '刷新', 'Muat Semula', 'रिफ्रेस गर्नुहोस्'),
('add_stock_take_code', 'Add Stock Take', '添加盘点', 'Tambah Ambil Stok', 'स्टक गणना थप्नुहोस्'),
('no_code', 'No', '编号', 'No', 'क्र.सं.'),
('stock_take_no_code', 'Stock Take No', '盘点编号', 'No Ambil Stok', 'स्टक गणना नम्बर'),
('declaration_date_code', 'Declaration Date', '申报日期', 'Tarikh Pengisytiharan', 'घोषणा मिति'),
('remarks_code', 'Remarks', '备注', 'Catatan', 'कैफियत'),
('action_code', 'Action', '操作', 'Tindakan', 'कार्य'),
('refresh_previous_tooltip_code', 'Refresh Previous Stock Take & Incoming', '刷新上次盘点和进货', 'Muat Semula Ambil Stok Sebelum & Masuk', 'अघिल्लो स्टक गणना र आगमन रिफ्रेस गर्नुहोस्'),
('assigned_on_save_code', 'Assigned on save', '保存时分配', 'Diberikan semasa simpan', 'सेभ गर्दा तोकिन्छ'),
('by_batch_drum_code', 'By-Batch/By-Drum', '按批次/按桶', 'Ikut Kelompok/Ikut Dram', 'ब्याच अनुसार/ड्रम अनुसार'),
('please_select_code', 'Please Select', '请选择', 'Sila Pilih', 'कृपया छान्नुहोस्'),
('date_code', 'Date', '日期', 'Tarikh', 'मिति'),
('time_code', 'Time', '时间', 'Masa', 'समय'),
('bitumen_code', 'Bitumen', '沥青', 'Bitumen', 'बिटुमेन'),
('add_bitumen_code', 'Add Bitumen', '添加沥青', 'Tambah Bitumen', 'बिटुमेन थप्नुहोस्'),
('name_code', 'Name', '名称', 'Nama', 'नाम'),
('status_code', 'Status', '状态', 'Status', 'स्थिति'),
('temperature_code', 'Temperature', '温度', 'Suhu', 'तापक्रम'),
('level_code', 'Level', '液位', 'Paras', 'तह'),
('actual_level_code', 'Actual Level', '实际液位', 'Paras Sebenar', 'वास्तविक तह'),
('incoming_code', 'Incoming', '进货', 'Masuk', 'आगमन'),
('total_code', 'Total', '总计', 'Jumlah', 'जम्मा'),
('previous_reading_code', 'Previous Reading', '上次读数', 'Bacaan Sebelum', 'अघिल्लो रिडिङ'),
('add_lfo_code', 'Add LFO', '添加轻燃油', 'Tambah LFO', 'LFO थप्नुहोस्'),
('volume_code', 'Volume', '体积', 'Isipadu', 'आयतन'),
('weight_code', 'Weight', '重量', 'Berat', 'तौल'),
('total_usage_code', 'Total Usage', '总用量', 'Jumlah Penggunaan', 'कुल प्रयोग'),
('diesel_code', 'Diesel', '柴油', 'Diesel', 'डिजेल'),
('add_diesel_code', 'Add Diesel', '添加柴油', 'Tambah Diesel', 'डिजेल थप्नुहोस्'),
('transport_code', 'Transport', '运输', 'Pengangkutan', 'ढुवानी'),
('burner_code', 'Burner', '燃烧器', 'Pembakar', 'बर्नर'),
('add_code', 'Add', '添加', 'Tambah', 'थप्नुहोस्'),
('type_code', 'Type', '类型', 'Jenis', 'प्रकार'),
('vehicle_no_others_code', 'Vehicle No / Others', '车牌号 / 其他', 'No Kenderaan / Lain-lain', 'सवारी नम्बर / अन्य'),
('first_reading_code', '1st Reading', '第一次读数', 'Bacaan Pertama', 'पहिलो रिडिङ'),
('second_reading_code', '2nd Reading', '第二次读数', 'Bacaan Kedua', 'दोस्रो रिडिङ'),
('usage_code', 'Usage', '用量', 'Penggunaan', 'प्रयोग'),
('total_transport_usage_code', 'Total Transport Usage', '运输总用量', 'Jumlah Penggunaan Pengangkutan', 'कुल ढुवानी प्रयोग'),
('burner_usage_code', 'Burner Usage', '燃烧器用量', 'Penggunaan Pembakar', 'बर्नर प्रयोग'),
('total_production_code', 'Total Production', '总产量', 'Jumlah Pengeluaran', 'कुल उत्पादन'),
('hotoil_code', 'Hotoil', '导热油', 'Minyak Panas', 'तातो तेल'),
('add_hotoil_code', 'Add Hotoil', '添加导热油', 'Tambah Minyak Panas', 'तातो तेल थप्नुहोस्'),
('other_bitumen_code', 'Other Bitumen', '其他沥青', 'Bitumen Lain', 'अन्य बिटुमेन'),
('fiber_additive_code', 'Fiber & Additive', '纤维和添加剂', 'Gentian & Bahan Tambah', 'फाइबर र एडिटिभ'),
('no_of_bags_code', 'No. of Bags', '袋数', 'Bilangan Beg', 'झोलाको संख्या'),
('quantity_code', 'Quantity', '数量', 'Kuantiti', 'परिमाण'),
('aggregates_code', 'Aggregates', '骨料', 'Agregat', 'एग्रिगेट'),
('emulsion_code', 'Emulsion', '乳化沥青', 'Emulsi', 'इमल्सन'),
('others_code', 'Others', '其他', 'Lain-lain', 'अन्य'),
('lime_filler_code', 'Lime/Filler', '石灰/填料', 'Kapur/Pengisi', 'चुन/फिलर'),
('do_no_code', 'D/O No', '送货单号', 'No D/O', 'D/O नम्बर'),
('close_code', 'Close', '关闭', 'Tutup', 'बन्द गर्नुहोस्'),
('submit_code', 'Submit', '提交', 'Hantar', 'पेश गर्नुहोस्'),
('cancel_stock_take_code', 'Cancel Stock Take', '取消盘点', 'Batal Ambil Stok', 'स्टक गणना रद्द गर्नुहोस्'),
('cancel_reason_code', 'Cancel Reason', '取消原因', 'Sebab Pembatalan', 'रद्द गर्ने कारण'),
('fill_cancel_reason_code', 'Please fill in the cancel reason', '请填写取消原因', 'Sila isi sebab pembatalan', 'कृपया रद्द गर्ने कारण भर्नुहोस्'),
('bulk_refresh_stock_take_code', 'Bulk Refresh Stock Take', '批量刷新盘点', 'Muat Semula Pukal Ambil Stok', 'स्टक गणना एकमुष्ट रिफ्रेस'),
('start_refresh_code', 'Start Refresh', '开始刷新', 'Mula Muat Semula', 'रिफ्रेस सुरु गर्नुहोस्'),
('upload_excel_file_code', 'Upload Excel File', '上传Excel文件', 'Muat Naik Fail Excel', 'एक्सेल फाइल अपलोड गर्नुहोस्'),
('preview_data_code', 'Preview Data', '预览数据', 'Pratonton Data', 'डाटा पूर्वावलोकन'),
('save_changes_code', 'Save changes', '保存更改', 'Simpan perubahan', 'परिवर्तन सेभ गर्नुहोस्'),
('error_log_code', 'Error Log', '错误日志', 'Log Ralat', 'त्रुटि लग'),
('filled_code', 'Filled', '已装满', 'Penuh', 'भरिएको'),
('empty_code', 'Empty', '空', 'Kosong', 'खाली'),
('incoming_tooltip_code', 'Total incoming weight is pulled from supplier weight from purchase', '进货总重量取自采购的供应商重量', 'Jumlah berat masuk diambil daripada berat pembekal dalam pembelian', 'कुल आगमन तौल खरिदको आपूर्तिकर्ता तौलबाट लिइन्छ'),
('total_incoming_weight_code', 'Total incoming weight', '进货总重量', 'Jumlah berat masuk', 'कुल आगमन तौल'),
('pulled_from_purchase_code', 'pulled from supplier weight from purchase', '取自采购的供应商重量', 'diambil daripada berat pembekal dalam pembelian', 'खरिदको आपूर्तिकर्ता तौलबाट लिइएको'),
('fill_field_code', 'Please fill in the field.', '请填写此字段。', 'Sila isi ruangan ini.', 'कृपया यो फिल्ड भर्नुहोस्।'),
('failed_to_save_code', 'Failed to save', '保存失败', 'Gagal menyimpan', 'सेभ गर्न असफल'),
('failed_save_try_again_code', 'Failed to save. Please try again.', '保存失败，请重试。', 'Gagal menyimpan. Sila cuba lagi.', 'सेभ गर्न असफल। कृपया फेरि प्रयास गर्नुहोस्।'),
('failed_code', 'Failed:', '失败：', 'Gagal:', 'असफल:'),
('something_wrong_code', 'Something went wrong', '出现问题', 'Sesuatu tidak kena', 'केही गडबड भयो'),
('pdf_error_code', 'An error occurred while generating the PDF.', '生成PDF时出错。', 'Ralat berlaku semasa menjana PDF.', 'PDF बनाउँदा त्रुटि भयो।'),
('confirm_refresh_code', 'This will reset previous reading, incoming data and calculation. Continue?', '这将重置上次读数、进货数据和计算。是否继续？', 'Ini akan menetapkan semula bacaan sebelum, data masuk dan pengiraan. Teruskan?', 'यसले अघिल्लो रिडिङ, आगमन डाटा र गणना रिसेट गर्नेछ। जारी राख्ने?'),
('select_from_to_date_code', 'Please select From and To date.', '请选择开始和结束日期。', 'Sila pilih tarikh Dari dan Hingga.', 'कृपया सुरु र अन्तिम मिति छान्नुहोस्।'),
('confirm_bulk_refresh_code', 'This will reset previous reading, incoming and calculation for every stock take in the date range. Continue?', '这将重置日期范围内每个盘点的上次读数、进货和计算。是否继续？', 'Ini akan menetapkan semula bacaan sebelum, data masuk dan pengiraan untuk setiap ambil stok dalam julat tarikh. Teruskan?', 'यसले मिति दायराभित्रका सबै स्टक गणनाको अघिल्लो रिडिङ, आगमन र गणना रिसेट गर्नेछ। जारी राख्ने?'),
('loading_stock_takes_code', 'Loading stock takes...', '正在加载盘点...', 'Memuatkan ambil stok...', 'स्टक गणना लोड हुँदैछ...'),
('select_file_first_code', 'Please select a file first.', '请先选择文件。', 'Sila pilih fail dahulu.', 'कृपया पहिले फाइल छान्नुहोस्।'),
('upload_preview_first_code', 'Please upload and preview an Excel file first.', '请先上传并预览Excel文件。', 'Sila muat naik dan pratonton fail Excel dahulu.', 'कृपया पहिले एक्सेल फाइल अपलोड र पूर्वावलोकन गर्नुहोस्।'),
('upload_error_code', 'An error occurred while uploading.', '上传时出错。', 'Ralat berlaku semasa memuat naik.', 'अपलोड गर्दा त्रुटि भयो।'),
('failed_to_cancel_code', 'Failed to cancel', '取消失败', 'Gagal membatalkan', 'रद्द गर्न असफल'),
('failed_cancel_try_again_code', 'Failed to cancel, please try again', '取消失败，请重试', 'Gagal membatalkan, sila cuba lagi', 'रद्द गर्न असफल, कृपया फेरि प्रयास गर्नुहोस्'),
('refreshed_stock_takes_code', 'Refreshed {n} stock take(s).', '已刷新 {n} 个盘点。', '{n} ambil stok telah dimuat semula.', '{n} स्टक गणना रिफ्रेस भयो।'),
('refreshing_code', 'Refreshing', '正在刷新', 'Memuat semula', 'रिफ्रेस हुँदैछ'),
('could_not_load_code', 'Stopped: could not load {no}.', '已停止：无法加载 {no}。', 'Dihentikan: tidak dapat memuatkan {no}.', 'रोकियो: {no} लोड गर्न सकिएन।'),
('stopped_at_code', 'Stopped at {no}', '在 {no} 停止', 'Dihentikan pada {no}', '{no} मा रोकियो'),
('save_failed_code', 'save failed.', '保存失败。', 'gagal disimpan.', 'सेभ असफल।'),
('no_data_sheet_code', 'No data in this sheet.', '此工作表没有数据。', 'Tiada data dalam helaian ini.', 'यो पानामा डाटा छैन।'),
('litre_tonnage_code', 'Litre/Tonnage', '升/吨', 'Liter/Tan', 'लिटर/टन'),
('print_code', 'Print', '打印', 'Cetak', 'प्रिन्ट गर्नुहोस्'),
('cancel_code', 'Cancel', '取消', 'Batal', 'रद्द गर्नुहोस्'),
('translation_code', 'Translation', '翻译', 'Terjemahan', 'अनुवाद'),
('add_new_translation_code', 'Add New Translation', '添加翻译', 'Tambah Terjemahan Baharu', 'नयाँ अनुवाद थप्नुहोस्'),
('edit_translation_code', 'Edit Translation', '编辑翻译', 'Sunting Terjemahan', 'अनुवाद सम्पादन गर्नुहोस्'),
('message_key_code_code', 'Message Key Code', '消息键代码', 'Kod Kunci Mesej', 'सन्देश कुञ्जी कोड'),
('english_code', 'English', '英文', 'Bahasa Inggeris', 'अंग्रेजी'),
('chinese_code', 'Chinese', '中文', 'Bahasa Cina', 'चिनियाँ'),
('malay_code', 'Malay', '马来文', 'Bahasa Melayu', 'मलय'),
('nepali_code', 'Nepali', '尼泊尔文', 'Bahasa Nepal', 'नेपाली'),
('confirm_delete_translation_code', 'Are you sure you want to delete this translation?', '确定要删除此翻译吗？', 'Adakah anda pasti mahu memadam terjemahan ini?', 'के तपाईं यो अनुवाद मेटाउन निश्चित हुनुहुन्छ?'),
('record_not_found_code', 'Record not found', '找不到记录', 'Rekod tidak dijumpai', 'रेकर्ड फेला परेन'),
('english_required_code', 'English text is required', '必须填写英文', 'Teks Bahasa Inggeris diperlukan', 'अंग्रेजी पाठ आवश्यक छ'),
('updated_successfully_code', 'Updated Successfully!!', '更新成功！', 'Berjaya dikemas kini!', 'सफलतापूर्वक अद्यावधिक भयो!'),
('added_successfully_code', 'Added Successfully!!', '添加成功！', 'Berjaya ditambah!', 'सफलतापूर्वक थपियो!'),
('deleted_code', 'Deleted', '已删除', 'Dipadam', 'मेटाइयो'),
('invalid_key_code_code', 'Message key code may only contain letters, numbers and underscores', '消息键代码只能包含字母、数字和下划线', 'Kod kunci mesej hanya boleh mengandungi huruf, nombor dan garis bawah', 'सन्देश कुञ्जी कोडमा अक्षर, अंक र अन्डरस्कोर मात्र हुन सक्छ'),
('key_code_exists_code', 'Message key code already exists', '消息键代码已存在', 'Kod kunci mesej sudah wujud', 'सन्देश कुञ्जी कोड पहिले नै छ'),
('no_permission_code', 'You do not have permission to do this', '您没有执行此操作的权限', 'Anda tiada kebenaran untuk melakukan ini', 'तपाईंलाई यो गर्ने अनुमति छैन'),
('menu_code', 'Menu', '菜单', 'Menu', 'मेनु'),
('dashboard_code', 'Dashboard', '仪表板', 'Papan Pemuka', 'ड्यासबोर्ड'),
('weight_weighing_code', 'Weight Weighing', '称重', 'Penimbangan Berat', 'तौल गर्ने'),
('accounting_code', 'Accounting', '会计', 'Perakaunan', 'लेखा'),
('sales_order_code', 'Sales Order', '销售订单', 'Pesanan Jualan', 'बिक्री अर्डर'),
('delivery_order_code', 'Delivery Order', '送货单', 'Pesanan Penghantaran', 'डेलिभरी अर्डर'),
('purchase_order_code', 'Purchase Order', '采购订单', 'Pesanan Belian', 'खरिद अर्डर'),
('goods_received_code', 'Goods Received', '收货', 'Barang Diterima', 'प्राप्त सामान'),
('stock_management_code', 'Stock Management', '库存管理', 'Pengurusan Stok', 'स्टक व्यवस्थापन'),
('stock_take_log_code', 'Stock Take Log', '盘点日志', 'Log Ambil Stok', 'स्टक गणना लग'),
('inventory_code', 'Inventory', '库存', 'Inventori', 'मौज्दात'),
('asset_management_code', 'Asset Management', '资产管理', 'Pengurusan Aset', 'सम्पत्ति व्यवस्थापन'),
('calculation_setup_code', 'Calculation Setup', '计算设置', 'Tetapan Pengiraan', 'गणना सेटअप'),
('products_code', 'Product', '产品', 'Produk', 'उत्पादन'),
('raw_materials_code', 'Raw Materials', '原材料', 'Bahan Mentah', 'कच्चा पदार्थ'),
('unit_code', 'Unit', '单位', 'Unit', 'एकाइ'),
('reason_code', 'Reason', '原因', 'Sebab', 'कारण'),
('site_code', 'Site', '工地', 'Tapak', 'साइट'),
('sales_representative_code', 'Sales Representative', '销售代表', 'Wakil Jualan', 'बिक्री प्रतिनिधि'),
('report_code', 'Report', '报告', 'Laporan', 'रिपोर्ट'),
('sales_report_code', 'Sales Report', '销售报告', 'Laporan Jualan', 'बिक्री रिपोर्ट'),
('purchase_report_code', 'Purchase Report', '采购报告', 'Laporan Belian', 'खरिद रिपोर्ट'),
('public_report_code', 'Public Report', '公共报告', 'Laporan Awam', 'सार्वजनिक रिपोर्ट'),
('user_management_code', 'User Management', '用户管理', 'Pengurusan Pengguna', 'प्रयोगकर्ता व्यवस्थापन'),
('user_setup_code', 'User Setup', '用户设置', 'Tetapan Pengguna', 'प्रयोगकर्ता सेटअप'),
('roles_code', 'Roles', '角色', 'Peranan', 'भूमिकाहरू'),
('modules_code', 'Modules', '模块', 'Modul', 'मोड्युलहरू'),
('permissions_code', 'Permissions', '权限', 'Kebenaran', 'अनुमतिहरू'),
('audit_log_code', 'Audit Log', '审计日志', 'Log Audit', 'अडिट लग'),
('api_log_code', 'Api Log', '接口日志', 'Log API', 'API लग'),
('cronjob_setup_code', 'Cronjob Setup', '定时任务设置', 'Tetapan Cronjob', 'क्रनजब सेटअप'),
('port_setup_code', 'Port Setup', '端口设置', 'Tetapan Port', 'पोर्ट सेटअप'),
('my_profile_code', 'My Profile', '我的资料', 'Profil Saya', 'मेरो प्रोफाइल'),
('sop_code', 'SOP', '标准作业程序', 'SOP', 'SOP'),
('hardware_setup_code', 'Hardware Setup', '硬件设置', 'Tetapan Perkakasan', 'हार्डवेयर सेटअप'),
('help_code', 'Help', '帮助', 'Bantuan', 'सहायता');

ALTER TABLE `Users` ADD `language` VARCHAR(5) NOT NULL DEFAULT 'en';
ALTER TABLE `Users_Log` ADD `language` VARCHAR(5) NULL DEFAULT NULL AFTER `plant_id`;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_USERS` AFTER INSERT ON `Users`
 FOR EACH ROW INSERT INTO Users_Log (
    user_id, employee_code, username, name, useremail, status, password, plant_id, language, action_id, action_by, event_date
)
VALUES (
    NEW.id, NEW.employee_code, NEW.username, NEW.name, NEW.useremail, NEW.status, NEW.password, NEW.plant_id, NEW.language, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_USERS` BEFORE UPDATE ON `Users`
 FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if deleted = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Users_Log table
    INSERT INTO Users_Log (
        user_id, employee_code, username, name, useremail, status, password, plant_id, language, action_id, action_by, event_date
    )
    VALUES (
        NEW.id, NEW.employee_code, NEW.username, NEW.name, NEW.useremail, NEW.status, NEW.password, NEW.plant_id, NEW.language, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

INSERT INTO `modules` (`id`, `name`, `category`) VALUES
(40, 'Translation', 'Setting');

UPDATE permissions SET modules = JSON_ARRAY_APPEND(modules, '$', '40') WHERE id IN (5, 6, 7) AND NOT JSON_CONTAINS(modules, '"40"');
