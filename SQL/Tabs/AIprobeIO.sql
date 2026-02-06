CREATE TABLE `learning_a03_DataStore`.`AIprobeIO` (
`SubjectId` TEXT NOT NULL ,
`DateTime_Write` DATETIME NULL DEFAULT NULL ,
`ClientTimeZone` TEXT NULL DEFAULT NULL ,
`AIprobeIO` TEXT NULL DEFAULT NULL ,
PRIMARY KEY (`SubjectId`(8))) ENGINE = MyISAM;