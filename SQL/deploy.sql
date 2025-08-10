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