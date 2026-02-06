CREATE TABLE `learning_a03_DataStore`.`AudioLog` ( 
`FileId` TEXT NOT NULL , 
`SubjectId` TEXT NOT NULL , 
`TrialId` TEXT NULL DEFAULT NULL , 
`AudioDuration` DECIMAL(6, 3) NULL DEFAULT NULL , 
`FileSize` INT NULL DEFAULT NULL , 
`DataHash` TEXT NULL DEFAULT NULL , 
`DateTime_Write` DATETIME NULL DEFAULT NULL , 
PRIMARY KEY (`FileId`(18))) ENGINE = MyISAM;