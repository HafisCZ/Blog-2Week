function getFileContent(container, file) {
  if (window.XMLHttpRequest) {
    xmlhttp=new XMLHttpRequest();
  } else {
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
  xmlhttp.onreadystatechange=function() {
    if (xmlhttp.readyState==4 && xmlhttp.status==200) {
      document.getElementById(container).innerHTML=xmlhttp.responseText;
    }
  }
  xmlhttp.open("GET", file,true);
  xmlhttp.send();
}