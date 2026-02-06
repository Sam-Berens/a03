CREATE TABLE `learning_a03_DataStore`.`ConsentLog` ( 
`SubjectId` TEXT NOT NULL , 
`Initials` TEXT NULL DEFAULT NULL , 
`DateTime_Consent` DATETIME NULL DEFAULT NULL , 
PRIMARY KEY (`SubjectId`(8))) ENGINE = MyISAM;