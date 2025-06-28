<?php
include("partial-front/db_con.php");

class CarImporter extends DB_con {
    private $connection;

    public function __construct() {
        parent::__construct();
        $this->connection = new mysqli("localhost", "root", "", "automotive2");
        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        }
    }

    public function importCars() {
        // Common car brands and their models
        $car_data = [
            'Toyota' => [
                'Camry' => ['year_from' => 1982, 'year_to' => 2024],
                'Corolla' => ['year_from' => 1966, 'year_to' => 2024],
                'RAV4' => ['year_from' => 1994, 'year_to' => 2024],
                'Highlander' => ['year_from' => 2000, 'year_to' => 2024],
                'Prius' => ['year_from' => 1997, 'year_to' => 2024]
            ],
            'Honda' => [
                'Civic' => ['year_from' => 1972, 'year_to' => 2024],
                'Accord' => ['year_from' => 1976, 'year_to' => 2024],
                'CR-V' => ['year_from' => 1995, 'year_to' => 2024],
                'Pilot' => ['year_from' => 2002, 'year_to' => 2024]
            ],
            'Ford' => [
                'F-150' => ['year_from' => 1975, 'year_to' => 2024],
                'Mustang' => ['year_from' => 1964, 'year_to' => 2024],
                'Explorer' => ['year_from' => 1990, 'year_to' => 2024],
                'Escape' => ['year_from' => 2000, 'year_to' => 2024]
            ],
            'Chevrolet' => [
                'Silverado' => ['year_from' => 1998, 'year_to' => 2024],
                'Malibu' => ['year_from' => 1964, 'year_to' => 2024],
                'Equinox' => ['year_from' => 2004, 'year_to' => 2024],
                'Tahoe' => ['year_from' => 1994, 'year_to' => 2024]
            ],
            'Nissan' => [
                'Altima' => ['year_from' => 1992, 'year_to' => 2024],
                'Maxima' => ['year_from' => 1981, 'year_to' => 2024],
                'Rogue' => ['year_from' => 2007, 'year_to' => 2024],
                'Sentra' => ['year_from' => 1982, 'year_to' => 2024]
            ],
            'Hyundai' => [
                'Elantra' => ['year_from' => 1990, 'year_to' => 2024],
                'Sonata' => ['year_from' => 1985, 'year_to' => 2024],
                'Tucson' => ['year_from' => 2004, 'year_to' => 2024],
                'Santa Fe' => ['year_from' => 2000, 'year_to' => 2024]
            ],
            'Kia' => [
                'Forte' => ['year_from' => 2008, 'year_to' => 2024],
                'Optima' => ['year_from' => 2000, 'year_to' => 2024],
                'Sorento' => ['year_from' => 2002, 'year_to' => 2024],
                'Sportage' => ['year_from' => 1993, 'year_to' => 2024]
            ],
            'Volkswagen' => [
                'Jetta' => ['year_from' => 1979, 'year_to' => 2024],
                'Passat' => ['year_from' => 1973, 'year_to' => 2024],
                'Golf' => ['year_from' => 1974, 'year_to' => 2024],
                'Tiguan' => ['year_from' => 2007, 'year_to' => 2024]
            ]
        ];

        try {
            foreach ($car_data as $brand_name => $models) {
                // Insert brand
                $query = "INSERT IGNORE INTO car_brands (brand_name) VALUES (?)";
                $stmt = $this->connection->prepare($query);
                $stmt->bind_param("s", $brand_name);
                $stmt->execute();
                
                // Get brand ID
                $brand_id = $stmt->insert_id;
                if (!$brand_id) {
                    $result = $this->connection->query("SELECT id FROM car_brands WHERE brand_name = '$brand_name'");
                    if ($row = $result->fetch_object()) {
                        $brand_id = $row->id;
                    }
                }
                
                // Insert models
                if ($brand_id) {
                    foreach ($models as $model_name => $years) {
                        $query = "INSERT IGNORE INTO car_models (brand_id, model_name, year_from, year_to) VALUES (?, ?, ?, ?)";
                        $stmt = $this->connection->prepare($query);
                        $stmt->bind_param("isii", $brand_id, $model_name, $years['year_from'], $years['year_to']);
                        $stmt->execute();
                    }
                }
            }
            return true;
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    public function __destruct() {
        if ($this->connection) {
            $this->connection->close();
        }
    }
}

$importer = new CarImporter();
if ($importer->importCars()) {
    echo "Car data imported successfully!";
} else {
    echo "Error importing car data.";
} 