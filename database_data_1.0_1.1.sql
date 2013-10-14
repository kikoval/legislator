-- db data update from version 1.0 to 1.1

-- 1. add new column
ALTER TABLE `Comment` ADD `result` SMALLINT DEFAULT NULL;

-- 2. transform data
UPDATE `Comment` SET `result`=0 WHERE `isAccepted`=1;
UPDATE `Comment` SET `result`=2 WHERE `isAccepted`=0;

-- 3. remove old column
ALTER TABLE `Comment` DROP `isAccepted`;
