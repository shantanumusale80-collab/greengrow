-- GreenGrow Database Schema
-- Use phpMyAdmin to create database 'greengrow', then import this file.

CREATE TABLE IF NOT EXISTS users (
  ID INT AUTO_INCREMENT PRIMARY KEY,
  Name VARCHAR(100) NOT NULL,
  Email VARCHAR(150) NOT NULL UNIQUE,
  Password VARCHAR(255) NOT NULL,
  Created_At TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS plants (
  ID INT AUTO_INCREMENT PRIMARY KEY,
  Name VARCHAR(150) NOT NULL,
  Scientific_Name VARCHAR(150) DEFAULT NULL,
  Category ENUM('Indoor','Outdoor','Herbs','Succulents') NOT NULL,
  Watering_Schedule VARCHAR(150) DEFAULT NULL,
  Sunlight VARCHAR(150) DEFAULT NULL,
  Soil_Type VARCHAR(150) DEFAULT NULL,
  Pest_Info TEXT,
  Image VARCHAR(255) DEFAULT NULL,
  Created_At TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS user_plants (
  ID INT AUTO_INCREMENT PRIMARY KEY,
  User_id INT NOT NULL,
  Plant_id INT NOT NULL,
  Added_Date DATE DEFAULT (CURRENT_DATE),
  Frequency ENUM('daily','weekly') DEFAULT 'weekly',
  FOREIGN KEY (User_id) REFERENCES users(ID) ON DELETE CASCADE,
  FOREIGN KEY (Plant_id) REFERENCES plants(ID) ON DELETE CASCADE,
  UNIQUE KEY uniq_user_plant (User_id, Plant_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS reminders (
  ID INT AUTO_INCREMENT PRIMARY KEY,
  User_id INT NOT NULL,
  Plant_id INT NOT NULL,
  Reminder_Date DATE NOT NULL,
  Sent TINYINT(1) DEFAULT 0,
  Created_At TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (User_id) REFERENCES users(ID) ON DELETE CASCADE,
  FOREIGN KEY (Plant_id) REFERENCES plants(ID) ON DELETE CASCADE,
  INDEX (Reminder_Date, Sent)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed a few plants (images can be added in admin later)
INSERT INTO plants (Name, Scientific_Name, Category, Watering_Schedule, Sunlight, Soil_Type, Pest_Info, Image) VALUES
('Snake Plant', 'Sansevieria trifasciata', 'Indoor', 'Every 2-3 weeks', 'Low to bright indirect', 'Well-draining cactus mix', 'Mealybugs, spider mites; wipe leaves and use neem oil', 'snake.jpg'),
('Basil', 'Ocimum basilicum', 'Herbs', 'Keep soil consistently moist', '6-8 hours direct', 'Rich, well-draining', 'Aphids; strong water spray or insecticidal soap', 'basil.jpg'),
('Aloe Vera', 'Aloe barbadensis miller', 'Succulents', 'Every 2-3 weeks', 'Bright indirect to direct', 'Sandy, well-draining', 'Scale; cotton swab with alcohol', 'aloe.jpg'),
('Rose', 'Rosa spp.', 'Outdoor', '2-3 times/week', 'Full sun', 'Loamy, well-drained', 'Aphids, black spot; prune and use fungicide', 'rose.jpg');
