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

function delimg($image){
  var r = confirm("Are you sure you want to delete this image?");
  if (r == true){
    var xhr = createXHR();
    xhr.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        alert(this.responseText);
      }
    };
    xhr.open("GET", "../php/cmd_func?cmd=delimg&img=$image", true);
    xhr.send();   
  }
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
