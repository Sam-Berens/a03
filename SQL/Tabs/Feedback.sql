CREATE TABLE `learning_a03_DataStore`.`Feedback` ( 
`SubjectId` TEXT NOT NULL , 
`Feedback` TEXT NULL DEFAULT NULL , 
`DateTime_Feedback` DATETIME NULL DEFAULT NULL , 
PRIMARY KEY (`SubjectId`(8))) ENGINE = MyISAM;