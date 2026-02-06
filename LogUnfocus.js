function LogUnfocus() {
    var JsonPost = {
        type: "POST",
        url: './LogUnfocus.php',
        dataType: 'json',
        data: {FunctionCall: 'LogUnfocus', Args: {SubjectId: SubjectId, Location: window.location.href}},
        success: function (Obj,Textstatus) {
            if( !('error' in Obj) ) {
                  return Obj.result;
              }
        }
    };
	Data = jQuery.ajax(JsonPost);
	return new Promise(resolve => {resolve(Data)});
}

function LogRefocus() {
    var JsonPost = {
        type: "POST",
        url: './LogUnfocus.php',
        dataType: 'json',
        data: {FunctionCall: 'LogRefocus', Args: {SubjectId: SubjectId}},
        success: function (Obj,Textstatus) {
            if( !('error' in Obj) ) {
                  return Obj.result;
              }
        }
    };
	Data = jQuery.ajax(JsonPost);
	return new Promise(resolve => {resolve(Data)});
}

var EnforceUnfocus = false;
document.addEventListener("visibilitychange", (event) => {
    if (document.visibilityState != "visible") {
        if (Boolean(SubjectId) && EnforceUnfocus) {
            LogUnfocus().then(function(P1){
                if (P1.Count < 4) {
                    alert(P1.Notice);
                    LogRefocus().then(function(P2){
                        if (P2.Bool) {
                            window.location.replace('./Coventry.html?SubjectId='+SubjectId+'#');
                        }
                    });
                } else {
                    window.location.replace('./Coventry.html?SubjectId='+SubjectId+'#');
                }
            });
        }
    }
});