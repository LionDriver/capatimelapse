//ajax request get/post setup
function createXHR() {
    try {
        return new XMLHttpRequest();
    } catch (e) {
        try {
            return new ActiveXObject("Microsoft.XMLHTTP");
        } catch (e) {
            return new ActiveXObject("Msxml2.XMLHTTP");
        }
    }
}

function addLoadEvent(func) {
  var oldonload = window.onload;
  if (typeof window.onload != 'function') {
    window.onload = func;
  } else {
    window.onload = function() {
      if (oldonload) {
        oldonload();
      }
      func();
    }
  }
}

var imagereload;
addLoadEvent(sys_ip);
addLoadEvent(sys_hostname);
addLoadEvent(loadBatt);
addLoadEvent(loadCpuTemp);
addLoadEvent(viewPort);
addLoadEvent(getPicInfo);
addLoadEvent(startClock);

var link = document.getElementById("snap");

document.onkeydown = function (e) {
    if (e.keyCode == 32) {
        link.click();
    }
};

imagereload = setInterval("viewPort()", 60000); //60000
var interval = setInterval("loadBatt()", 20000);
var cputemp = setInterval("loadCpuTemp()", 20000);
var picinfo = setInterval("getPicInfo()", 2000);
var timerID = null;
var timerRunning = false;

//display video stream or set to timelapse/single picture mode
// function streamMode() {    
//   if (document.getElementsByName("streamOn")[0].checked == true) {
//     clearInterval(imagereload);
//     var div = document.getElementById("viewport");
//     div.innerHTML = "";
//     var canvas = document.createElement('canvas');
//     canvas.id = "videoCanvas";
//     canvas.style.border   = "1px solid";
//     canvas.style.height = "480px";
//     canvas.style.width = "640px";
//     //<img class="img-responsive img-rounded center-block" src="" alt="Please Wait...">

//     var vidurl = window.URL || window.webKitURL;
//     alert(vidurl);

//     setTimeout(function() {
//       div.appendChild(canvas)
//       var ctx = canvas.getContext('2d');
//       ctx.fillText('Loading...', canvas.width/2-30, canvas.height/3);
//       var client = new WebSocket('ws://'+location.host+':8080/stream');
//       var player = new jsmpeg(client, {canvas:canvas});
//     }, 200);
//   } else {
//     document.getElementById("viewport").innerHTML = "";
//     viewPort();
//     imagereload = setInterval("viewPort()", 60000);
//   }
// }

//determine timelapse or single pic mode based upon interval
function checkSelected() {
  var e = document.getElementById("interval").value;
  if (e != 0) {
    document.getElementById("snap").value = "Start Timelapse";
    document.getElementById("duration").disabled = false;
  } else {
    document.getElementById("snap").value = "Take Picture";
    document.getElementById("duration").disabled = true;
  }
}

//Make sure txtinput is limited to num
function isNumberKey(evt) {
  if (evt.keyCode == 9) {
    return true;
  }
  var charCode = (evt.which) ? evt.which : event.keyCode
  if (charCode == 45) {
    return true;
  } else if (charCode > 31 && (charCode < 48 || charCode > 57)) {
    return false;
  }
  return true;
}

//Limits for all -100 to 100 txtinput
function limitInput(input) {
  if (input.value > 100) {
    input.value = 100;
  } else if (input.value < -100) {
    input.value = -100;
  }
}

//Brightness and jpgquality has 0-100 txtinput
function limitToPositive(input) {
  if (input.value > 100) {
    input.value = 100;
  } else if (input.value < 0) {
    input.value = 0;
  }
}

function limitISO() {
  var e = document.getElementById("isoOutputId").value;
  if (e > 800) {
    document.getElementById("isoOutputId").value = 800;
  } else if (e < 100) {
    document.getElementById("isoOutputId").value = 100;
  }
}

function limitSS() {
  var e = document.getElementById("ssOutputId").value;
  if (e > 1000000) {
    document.getElementById("ssOutputId").value = 1000000;
  } else if (e < 0) {
    document.getElementById("ssOutputId").value = 0;
  }
}

//main picture viewport
function viewPort() {
  var xhr = createXHR();
  xhr.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
     document.getElementById("viewport").innerHTML = this.responseText;
    }
  };
  xhr.open("GET", "../php/viewport.php", true);
  xhr.send();
}

//Show current time
function stopClock(){
  if(timerRunning) {
     clearTimeout(timerID);
  }
  timerRunning = false;
}

function showTime() {
  var now = new Date();
  var hours = now.getHours();
  var minutes = now.getMinutes();
  var seconds = now.getSeconds();
  var timeValue = "" + ((hours >12) ? hours -12 :hours);
  timeValue += ((minutes < 10) ? ":0" : ":") + minutes;
  timeValue += ((seconds < 10) ? ":0" : ":") + seconds;
  timeValue += (hours >= 12) ? " PM" : " AM";
  document.getElementById("clock").innerHTML = timeValue;
  timerID = setTimeout("showTime()",1000);
  timerRunning = true;
}

function startClock() {
  stopClock();
  showTime();
}

function writeSettings() {
  var hflip = (document.getElementsByName("horizontalFlip")[0]).checked;
  var vflip = (document.getElementsByName("vertFlip")[0]).checked;
  var resolution = document.getElementById("resolution").value;
  var interval = document.getElementById("interval").value;
  var duration = document.getElementById("duration").value;
  var wb = document.getElementById("whitebalance").value;
  var exposure = document.getElementById("exposure").value;
  var metering = document.getElementById("metering").value;
  var effects = document.getElementById("effects").value;
  var sharpness = document.getElementById("sharpness").value;
  var contrast = document.getElementById("contrast").value;
  var brightness = document.getElementById("brightness").value;
  var saturation = document.getElementById("saturation").value;
  var iso = document.getElementById("iso").value;
  var drc = document.getElementById("drc").value;
  var ss = document.getElementById("ss").value;
  var jpgquality = document.getElementById("jpgquality").value;
  
  var xhr = createXHR();
  var paramenters ="hflip="+hflip+"&vflip="+vflip+"&resolution="+resolution+"&interval="+interval
    +"&duration="+duration+"&whitebalance="+wb+"&exposure="+exposure+"&metering="+metering
    +"&effects="+effects+"&sharpness="+sharpness+"&contrast="+contrast+"&brightness="+brightness
    +"&saturation="+saturation+"&iso="+iso+"&drc="+drc+"&ss="+ss+"&jpgquality="+jpgquality;

  xhr.open("POST", "../php/process.php", true)
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
  xhr.send(paramenters);  
}

function loadBatt() {
  var xhr = createXHR();
  xhr.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
     document.getElementById("battery").innerHTML = this.responseText;
    }
  };
  xhr.open("GET", "../php/cmd_func.php?cmd=sysbatt", true);
  xhr.send();
};

function loadCpuTemp() {
  var xhr = createXHR();
  xhr.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      var cpu = parseFloat(this.responseText);
      var n = cpu.toFixed(2);
      document.getElementById("cputemp").innerHTML = n;
    }
  };
  xhr.open("GET", "../php/cmd_func.php?cmd=systemp", true);
  xhr.send();
}

function getPicInfo() {
  var xhr = createXHR();
  xhr.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      document.getElementById("picinfo").innerHTML = this.responseText;
    }
  };
  xhr.open("GET", "../php/cmd_info.php", true);
  xhr.send();
}

//create zip/tar file and serve download
function sys_servefiles() {
  document.body.style.cursor = 'wait';
  var xhr = createXHR();
  xhr.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      document.body.style.cursor = 'default';
    }
  };
  xhr.open("GET", "../php/cmd_func.php?cmd=zipit", true);
  xhr.send();
}

function sys_shutdown() {
  var xhr = createXHR();
  xhr.open("GET", "../php/cmd_func.php?cmd=shutdown", true);
  xhr.send();
  alert("pi shutdown");
}

function sys_reboot() {
  var xhr = createXHR();
  xhr.open("GET", "../php/cmd_func.php?cmd=reboot", true);
  xhr.send();
  alert("Rebooting Pi, wait for a few seconds and refresh")
}

function sys_delete() {
  var r = confirm("Are you sure you want to delete all captured images?");
  if(r == true){
    var xhr = createXHR();
    xhr.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        getPicInfo();
        viewPort();
      }
    };
    xhr.open("GET", "../php/cmd_func.php?cmd=bobhope", true);
    xhr.send();
  }
};

function sys_deletetar() {
  var r = confirm("Are you sure you want to delete all tar files?");
  if(r == true){
    var xhr = createXHR();
    xhr.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {  
        getPicInfo();
        viewPort();
      }
    };
    xhr.open("GET", "../php/cmd_func.php?cmd=bobby", true);
    xhr.send();
  }
};

function sys_hostname() {
  var xhr = createXHR();
  xhr.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {  
        document.getElementById("hostname").innerHTML = this.responseText;
        document.title = this.responseText;
    }
  };
  xhr.open("GET", "../php/cmd_func.php?cmd=hostname", true);
  xhr.send();
}

function sys_ip() {
  var xhr = createXHR();
  xhr.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {  
        document.getElementById("ip").innerHTML = this.responseText;
    }
  };
  xhr.open("GET", "../php/cmd_func.php?cmd=ip", true);
  xhr.send();
}

function sys_kill() {
  var xhr = createXHR();
  xhr.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {  
        alert('Camera has been put to rest.');
    }
  };
  xhr.open("GET", "../php/cmd_func.php?cmd=worldbank", true);
  xhr.send();
}

function setcur() {
  setTimeout(function() {
    viewPort();
    $("body").removeClass('wait');
  }, 2000);
}

//take picture or timelapse
function sys_snap() {
  writeSettings();
  $("body").toggleClass('wait');
  var xhr = createXHR();
  xhr.open("GET", "../php/cmd_func.php?cmd=snap", true);
  xhr.send();
  setTimeout(function (){
    getPicInfo();
    setcur();
  }, 3000);
}
