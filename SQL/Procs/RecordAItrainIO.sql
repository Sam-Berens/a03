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