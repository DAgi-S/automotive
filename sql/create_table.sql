USE automotive;

DROP TABLE IF EXISTS tbl_info;

CREATE TABLE tbl_info (
    id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    userid int(11),
    car_brand varchar(100),
    car_year varchar(10),
    car_model varchar(100),
    service_date varchar(20),
    mile_age varchar(20),
    oil_change varchar(20),
    insurance varchar(10),
    bolo varchar(10),
    rd_wegen varchar(10),
    yemenged_fend varchar(10),
    img_name1 varchar(255),
    img_name2 varchar(255),
    img_name3 varchar(255),
    created_at timestamp NOT NULL DEFAULT current_timestamp()
); 