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

function sys_delOne($img){
  alert("What the fucking fuck");
  var xhr = createXHR();
  xhr.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      window.alert(this.responseText);
    }
  };
  xhr.open("GET", "../php/cmd_img.php?cmd=delimg&img="+$img, true);
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
