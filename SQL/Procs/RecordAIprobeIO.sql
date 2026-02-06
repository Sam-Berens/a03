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