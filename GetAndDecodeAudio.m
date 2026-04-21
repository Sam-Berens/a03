function [] = GetAndDecodeAudio()

Credentials = GetCredentials();

%% Create an Audio dir if needs be
try
    cd('Audio');
    cd ..;
catch
    mkdir('Audio');
end

%% Get the AudioLog data from the server
AudioLog = struct2table(...
    webwrite('https://a03.learningandinference.org/CallGetAudioLog.php',...
        'Hello',Credentials.Hello));
AudioLog.AudioDuration = cellfun(@str2double,AudioLog.AudioDuration);
AudioLog.FileSize = cellfun(@str2double,AudioLog.FileSize);
writetable(AudioLog,fullfile('.','Audio','AudioLog.csv'));

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
AudioLog.Todo = ones(size(AudioLog,1),1);
for iAudioLog = 1:size(AudioLog,1)
    cFileId = AudioLog.FileId{iAudioLog};
    if exist(fullfile('.','Audio',[AudioLog.FileId{iAudioLog},'.webm']),'file')==2
        AudioLog.Todo(iAudioLog) = 0;
        continue
    end
    websave(['.',filesep,'temp',filesep,cFileId,'.dat'],...
        ['https://a03.learningandinference.org/AudioData/',cFileId,'.dat']);
end

%% Loop to convert all the files
for iAudioLog = 1:size(AudioLog,1)
    if ~AudioLog.Todo(iAudioLog)
        continue
    end
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