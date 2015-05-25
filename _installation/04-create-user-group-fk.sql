ALTER TABLE `users` ADD CONSTRAINT `fk_group` FOREIGN KEY (`user_group`) REFERENCES `workflow`.`user_groups`(`group_name`) ON DELETE RESTRICT ON UPDATE CASCADE;

/* delete key ALTER TABLE users
DROP FOREIGN KEY fk_group */