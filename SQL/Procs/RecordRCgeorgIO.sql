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