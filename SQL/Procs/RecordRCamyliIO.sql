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