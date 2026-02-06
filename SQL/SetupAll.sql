-- ============================================================================
-- MASTER SETUP SCRIPT FOR learning_a03_DataStore
-- ============================================================================
-- This script combines all table and procedure definitions
-- Tables are created first, followed by stored procedures
-- ============================================================================

-- ============================================================================
-- SECTION 1: CREATE TABLES
-- ============================================================================

CREATE TABLE `learning_a03_DataStore`.`AIprobeIO` (
`SubjectId` TEXT NOT NULL ,
`DateTime_Write` DATETIME NULL DEFAULT NULL ,
`ClientTimeZone` TEXT NULL DEFAULT NULL ,
`AIprobeIO` TEXT NULL DEFAULT NULL ,
PRIMARY KEY (`SubjectId`(8))) ENGINE = MyISAM;

CREATE TABLE `learning_a03_DataStore`.`AItrainIO` (
`SubjectId` TEXT NOT NULL ,
`DateTime_Write` DATETIME NULL DEFAULT NULL ,
`ClientTimeZone` TEXT NULL DEFAULT NULL ,
`AItrainIO` TEXT NULL DEFAULT NULL ,
PRIMARY KEY (`SubjectId`(8))) ENGINE = MyISAM;

CREATE TABLE `learning_a03_DataStore`.`AudioLog` ( 
`FileId` TEXT NOT NULL , 
`SubjectId` TEXT NOT NULL , 
`TrialId` TEXT NULL DEFAULT NULL , 
`AudioDuration` DECIMAL(6, 3) NULL DEFAULT NULL , 
`FileSize` INT NULL DEFAULT NULL , 
`DataHash` TEXT NULL DEFAULT NULL , 
`DateTime_Write` DATETIME NULL DEFAULT NULL , 
PRIMARY KEY (`FileId`(18))) ENGINE = MyISAM;

CREATE TABLE `learning_a03_DataStore`.`ConsentLog` ( 
`SubjectId` TEXT NOT NULL , 
`Initials` TEXT NULL DEFAULT NULL , 
`DateTime_Consent` DATETIME NULL DEFAULT NULL , 
PRIMARY KEY (`SubjectId`(8))) ENGINE = MyISAM;

CREATE TABLE `learning_a03_DataStore`.`Exclusions` ( 
`PoolId` TEXT NULL DEFAULT NULL , 
`SubjectId` TEXT NULL DEFAULT NULL , 
`OS` TEXT NULL DEFAULT NULL , 
`Browser` TEXT NULL DEFAULT NULL , 
`DateTime_Exclude` DATETIME NULL DEFAULT NULL ) 
ENGINE = MyISAM;

CREATE TABLE `learning_a03_DataStore`.`Feedback` ( 
`SubjectId` TEXT NOT NULL , 
`Feedback` TEXT NULL DEFAULT NULL , 
`DateTime_Feedback` DATETIME NULL DEFAULT NULL , 
PRIMARY KEY (`SubjectId`(8))) ENGINE = MyISAM;

CREATE TABLE `learning_a03_DataStore`.`InstructNaughtiness` ( 
`SubjectId` TEXT NULL DEFAULT NULL , 
`State` INT NULL DEFAULT NULL , 
`TaskId` TEXT NULL DEFAULT NULL , 
`DateTime_Naughty` DATETIME NULL DEFAULT NULL ) ENGINE = MyISAM;

CREATE TABLE `learning_a03_DataStore`.`RCamyliIO` (
`SubjectId` TEXT NOT NULL ,
`DateTime_Write` DATETIME NULL DEFAULT NULL ,
`ClientTimeZone` TEXT NULL DEFAULT NULL ,
`RCamyliIO` TEXT NULL DEFAULT NULL ,
PRIMARY KEY (`SubjectId`(8))) ENGINE = MyISAM;

CREATE TABLE `learning_a03_DataStore`.`RCgeorgIO` (
`SubjectId` TEXT NOT NULL ,
`DateTime_Write` DATETIME NULL DEFAULT NULL ,
`ClientTimeZone` TEXT NULL DEFAULT NULL ,
`RCgeorgIO` TEXT NULL DEFAULT NULL ,
PRIMARY KEY (`SubjectId`(8))) ENGINE = MyISAM;

CREATE TABLE `learning_a03_DataStore`.`Register` ( 
`PoolId` TEXT NULL DEFAULT NULL , 
`SubjectId` TEXT NOT NULL , 
`DOB` DATE NULL DEFAULT NULL , 
`Gender` TEXT NULL DEFAULT NULL , 
`L1` TEXT NULL DEFAULT NULL , 
`Handedness` TEXT NULL DEFAULT NULL , 
`State` INT NULL DEFAULT NULL , 
`TaskPerm` INT NULL DEFAULT NULL , 
`Assignment` TEXT NULL DEFAULT NULL , 
`DateTime_Landing` DATETIME NULL DEFAULT NULL , 
`DateTime_Consent` DATETIME NULL DEFAULT NULL , 
`DateTime_Register` DATETIME NULL DEFAULT NULL , 
`DateTime_MicTest` DATETIME NULL DEFAULT NULL , 
`DateTime_AIinstr` DATETIME NULL DEFAULT NULL , 
`DateTime_AItrain` DATETIME NULL DEFAULT NULL , 
`DateTime_AIprobe` DATETIME NULL DEFAULT NULL , 
`DateTime_RCinstr` DATETIME NULL DEFAULT NULL , 
`DateTime_RCamyli` DATETIME NULL DEFAULT NULL , 
`DateTime_RCgeorg` DATETIME NULL DEFAULT NULL , 
`DateTime_Complete` DATETIME NULL DEFAULT NULL , 
PRIMARY KEY (`SubjectId`(8))) ENGINE = MyISAM;

CREATE TABLE `learning_a03_DataStore`.`Relandings` ( 
`PoolId` TEXT NULL DEFAULT NULL , 
`SubjectId` TEXT NULL DEFAULT NULL , 
`State` INT NULL DEFAULT NULL , 
`DateTime_Reland` DATETIME NULL DEFAULT NULL ) 
ENGINE = MyISAM;

CREATE TABLE `learning_a03_DataStore`.`TIprobeIO` (
`SubjectId` TEXT NOT NULL ,
`DateTime_Write` DATETIME NULL DEFAULT NULL ,
`ClientTimeZone` TEXT NULL DEFAULT NULL ,
`TIprobeIO` TEXT NULL DEFAULT NULL ,
PRIMARY KEY (`SubjectId`(8))) ENGINE = MyISAM;

CREATE TABLE `learning_a03_DataStore`.`TItrainIO` (
`SubjectId` TEXT NOT NULL ,
`DateTime_Write` DATETIME NULL DEFAULT NULL ,
`ClientTimeZone` TEXT NULL DEFAULT NULL ,
`TItrainIO` TEXT NULL DEFAULT NULL ,
PRIMARY KEY (`SubjectId`(8))) ENGINE = MyISAM;

CREATE TABLE `learning_a03_DataStore`.`Unfocuses` ( 
`SubjectId` TEXT NULL DEFAULT NULL , 
`State` INT NULL DEFAULT NULL , 
`Location` TEXT NULL DEFAULT NULL , 
`DateTime_Unfocus` DATETIME NULL DEFAULT NULL ) ENGINE = MyISAM;

-- ============================================================================
-- SECTION 2: CREATE STORED PROCEDURES
-- ============================================================================

DELIMITER $$
CREATE PROCEDURE RecordAIprobeIO(
	IN In_SubjectId TEXT,
	IN In_DateTime_Write DATETIME,
	IN In_ClientTimeZone TEXT,
	IN In_AIprobeIO TEXT
)
BEGIN
IF (SELECT COUNT(SubjectId) FROM AIprobeIO WHERE SubjectId=In_SubjectId)=0 THEN 
	INSERT INTO AIprobeIO (SubjectId, DateTime_Write, ClientTimeZone, AIprobeIO) VALUES (In_SubjectId, In_DateTime_Write, In_ClientTimeZone, In_AIprobeIO);
ELSE
	UPDATE AIprobeIO SET DateTime_Write = In_DateTime_Write, ClientTimeZone = In_ClientTimeZone, AIprobeIO = In_AIprobeIO WHERE SubjectId = In_SubjectId;
END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE RecordAItrainIO(
	IN In_SubjectId TEXT,
	IN In_DateTime_Write DATETIME,
	IN In_ClientTimeZone TEXT,
	IN In_AItrainIO TEXT
)
BEGIN
IF (SELECT COUNT(SubjectId) FROM AItrainIO WHERE SubjectId=In_SubjectId)=0 THEN 
	INSERT INTO AItrainIO (SubjectId, DateTime_Write, ClientTimeZone, AItrainIO) VALUES (In_SubjectId, In_DateTime_Write, In_ClientTimeZone, In_AItrainIO);
ELSE
	UPDATE AItrainIO SET DateTime_Write = In_DateTime_Write, ClientTimeZone = In_ClientTimeZone, AItrainIO = In_AItrainIO WHERE SubjectId = In_SubjectId;
END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE RecordAudioLog(
	IN In_FileId TEXT,
	IN In_SubjectId TEXT,
	IN In_TrialId TEXT,
	IN In_AudioDuration DECIMAL(6,3),
	IN In_FileSize INT,
	IN In_DataHash TEXT,
	IN In_DateTime_Write DATETIME
)
BEGIN
IF (SELECT COUNT(FileId) FROM AudioLog WHERE FileId=In_FileId)=0 THEN 
	INSERT INTO AudioLog (FileId, SubjectId, TrialId, AudioDuration, FileSize, DataHash, DateTime_Write) VALUES (In_FileId, In_SubjectId, In_TrialId, In_AudioDuration, In_FileSize, In_DataHash, In_DateTime_Write);
ELSE
	UPDATE AudioLog SET SubjectId = In_SubjectId, TrialId = In_TrialId, AudioDuration = In_AudioDuration, FileSize = In_FileSize, DataHash = In_DataHash, DateTime_Write = In_DateTime_Write WHERE FileId = In_FileId;
END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE RecordConsentLog(
	IN In_SubjectId TEXT,
	IN In_Initials TEXT,
	IN In_DateTime_Consent DATETIME
)
BEGIN
IF (SELECT COUNT(SubjectId) FROM ConsentLog WHERE SubjectId=In_SubjectId)=0 THEN 
	INSERT INTO ConsentLog (SubjectId, 	Initials, DateTime_Consent) VALUES (In_SubjectId, In_Initials, In_DateTime_Consent);
ELSE
	UPDATE ConsentLog SET Initials = In_Initials, DateTime_Consent = In_DateTime_Consent WHERE SubjectId = In_SubjectId;
END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE RecordFeedback(
	IN In_SubjectId TEXT,
	IN In_Feedback TEXT,
	IN In_DateTime_Feedback DATETIME
)
BEGIN
IF (SELECT COUNT(SubjectId) FROM Feedback WHERE SubjectId=In_SubjectId)=0 THEN 
	INSERT INTO Feedback (SubjectId, Feedback, DateTime_Feedback) VALUES (In_SubjectId, In_Feedback, In_DateTime_Feedback);
ELSE
	UPDATE Feedback SET Feedback = In_Feedback, DateTime_Feedback = In_DateTime_Feedback WHERE SubjectId = In_SubjectId;
END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE RecordRCamyliIO(
	IN In_SubjectId TEXT,
	IN In_DateTime_Write DATETIME,
	IN In_ClientTimeZone TEXT,
	IN In_RCamyliIO TEXT
)
BEGIN
IF (SELECT COUNT(SubjectId) FROM RCamyliIO WHERE SubjectId=In_SubjectId)=0 THEN 
	INSERT INTO RCamyliIO (SubjectId, DateTime_Write, ClientTimeZone, RCamyliIO) VALUES (In_SubjectId, In_DateTime_Write, In_ClientTimeZone, In_RCamyliIO);
ELSE
	UPDATE RCamyliIO SET DateTime_Write = In_DateTime_Write, ClientTimeZone = In_ClientTimeZone, RCamyliIO = In_RCamyliIO WHERE SubjectId = In_SubjectId;
END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE RecordRCgeorgIO(
	IN In_SubjectId TEXT,
	IN In_DateTime_Write DATETIME,
	IN In_ClientTimeZone TEXT,
	IN In_RCgeorgIO TEXT
)
BEGIN
IF (SELECT COUNT(SubjectId) FROM RCgeorgIO WHERE SubjectId=In_SubjectId)=0 THEN 
	INSERT INTO RCgeorgIO (SubjectId, DateTime_Write, ClientTimeZone, RCgeorgIO) VALUES (In_SubjectId, In_DateTime_Write, In_ClientTimeZone, In_RCgeorgIO);
ELSE
	UPDATE RCgeorgIO SET DateTime_Write = In_DateTime_Write, ClientTimeZone = In_ClientTimeZone, RCgeorgIO = In_RCgeorgIO WHERE SubjectId = In_SubjectId;
END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE RecordTIprobeIO(
	IN In_SubjectId TEXT,
	IN In_DateTime_Write DATETIME,
	IN In_ClientTimeZone TEXT,
	IN In_TIprobeIO TEXT
)
BEGIN
IF (SELECT COUNT(SubjectId) FROM TIprobeIO WHERE SubjectId=In_SubjectId)=0 THEN 
	INSERT INTO TIprobeIO (SubjectId, DateTime_Write, ClientTimeZone, TIprobeIO) VALUES (In_SubjectId, In_DateTime_Write, In_ClientTimeZone, In_TIprobeIO);
ELSE
	UPDATE TIprobeIO SET DateTime_Write = In_DateTime_Write, ClientTimeZone = In_ClientTimeZone, TIprobeIO = In_TIprobeIO WHERE SubjectId = In_SubjectId;
END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE RecordTItrainIO(
	IN In_SubjectId TEXT,
	IN In_DateTime_Write DATETIME,
	IN In_ClientTimeZone TEXT,
	IN In_TItrainIO TEXT
)
BEGIN
IF (SELECT COUNT(SubjectId) FROM TItrainIO WHERE SubjectId=In_SubjectId)=0 THEN 
	INSERT INTO TItrainIO (SubjectId, DateTime_Write, ClientTimeZone, TItrainIO) VALUES (In_SubjectId, In_DateTime_Write, In_ClientTimeZone, In_TItrainIO);
ELSE
	UPDATE TItrainIO SET DateTime_Write = In_DateTime_Write, ClientTimeZone = In_ClientTimeZone, TItrainIO = In_TItrainIO WHERE SubjectId = In_SubjectId;
END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`learning`@`localhost` PROCEDURE `GetAudioLog`()
    NO SQL
SELECT * FROM AudioLog$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE DropAllRows()
BEGIN
DELETE FROM AIprobeIO;
DELETE FROM AItrainIO;
DELETE FROM AudioLog;
DELETE FROM ConsentLog;
DELETE FROM Exclusions;
DELETE FROM Feedback;
DELETE FROM InstructNaughtiness;
DELETE FROM RCamyliIO;
DELETE FROM RCgeorgIO;
DELETE FROM Register;
DELETE FROM Relandings;
DELETE FROM TIprobeIO;
DELETE FROM TItrainIO;
DELETE FROM Unfocuses;

END$$
DELIMITER ;

-- ============================================================================
-- SETUP COMPLETE
-- ============================================================================
-- All 14 tables and 11 stored procedures have been created successfully.
-- ============================================================================
