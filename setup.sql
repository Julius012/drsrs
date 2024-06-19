-- Create user table
CREATE TABLE `user` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_type` VARCHAR(50) NOT NULL,
    `full_name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `role` VARCHAR(50) NOT NULL
) ENGINE=InnoDB;

-- Create file_upload table
CREATE TABLE `file_upload` (
    `user_id` INT NOT NULL,
    `uploaded_file_url` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`user_id`),
    CONSTRAINT `fk_user`
        FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;
