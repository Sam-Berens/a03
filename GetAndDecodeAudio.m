function [] = GetAndDecodeAudio()

% Get the AudioLog data from the server
AudioLog = struct2table(webread('https://a03.learningandinference.org/CallGetAudioLog.php'));
AudioLog.AudioDuration = cellfun(@str2double,AudioLog.AudioDuration);
AudioLog.FileSize = cellfun(@str2double,AudioLog.FileSize);
writetable(AudioLog,'AudioLog.csv');

%% Create a temp dir if needs be
MadeNewTempFolder = false;
try
    cd('temp');
    cd ..;
catch
    MadeNewTempFolder = true;
    mkdir('temp');
end

%% Loop to download all the files into the temp dir
for iAudioLog = 1:size(AudioLog,1)
    cFileId = AudioLog.FileId{iAudioLog};
    websave(['.',filesep,'temp',filesep,cFileId,'.dat'],...
        ['https://a03.learningandinference.org/AudioData/',cFileId,'.dat']);
end

%% Create an Audio dir if needs be
try
    cd('Audio');
    cd ..;
catch
    mkdir('Audio');
end

%% Loop to convert all the files
for iAudioLog = 1:size(AudioLog,1)
    cFileId = AudioLog.FileId{iAudioLog};
    AudioData = fileread(['.',filesep,'temp',filesep,cFileId,'.dat']);
    DecodedData = matlab.net.base64decode(AudioData);
    cFileH = fopen(['.',filesep,'Audio',filesep,cFileId,'.webm'],'w');
    fwrite(cFileH,DecodedData);
    fclose(cFileH);
    delete(['.',filesep,'temp',filesep,cFileId,'.dat']);
end

%% Remove the temp dir (if needs be)
if MadeNewTempFolder
    rmdir(['.',filesep,'temp']);
end

return