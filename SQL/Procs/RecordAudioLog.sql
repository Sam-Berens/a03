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