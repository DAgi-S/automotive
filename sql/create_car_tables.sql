USE automotive;

CREATE TABLE IF NOT EXISTS car_brands (
    id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    brand_name varchar(100) NOT NULL UNIQUE,
    created_at timestamp NOT NULL DEFAULT current_timestamp()
);

CREATE TABLE IF NOT EXISTS car_models (
    id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    brand_id int(11) NOT NULL,
    model_name varchar(100) NOT NULL,
    year_from int(4),
    year_to int(4),
    created_at timestamp NOT NULL DEFAULT current_timestamp(),
    FOREIGN KEY (brand_id) REFERENCES car_brands(id) ON DELETE CASCADE,
    UNIQUE KEY unique_model_per_brand (brand_id, model_name)
); 