Динамическое создание iframe
<script type="text/javascript">
//<![CDATA[
/**
 * Динамически создаем iframe и обновляем его каждые updateCountOnlineUser
 * @copyright http://developer.apple.com/internet/webcontent/iframe.html
 */
var IFrameObj; // our IFrame object
var updateCountOnlineUser = 10000;
var URL = "";
function callToServer() {
  if (!document.createElement) {return true};
  var IFrameDoc;  
  if (!IFrameObj && document.createElement) {
    // create the IFrame and assign a reference to the
    // object to our global variable IFrameObj.
    // this will only happen the first time 
    // callToServer() is called
   try {
      var tempIFrame=document.createElement('iframe');
      tempIFrame.setAttribute('id','RSIFrame');
      tempIFrame.style.border='0px';
      tempIFrame.style.width='0px';
      tempIFrame.style.height='0px';
      IFrameObj = document.body.appendChild(tempIFrame);
      
      if (document.frames) {
        // this is for IE5 Mac, because it will only
        // allow access to the document object
        // of the IFrame if we access it through
        // the document.frames array
        IFrameObj = document.frames['RSIFrame'];
      }
    } catch(exception) {
      // This is for IE5 PC, which does not allow dynamic creation
      // and manipulation of an iframe object. Instead, we'll fake
      // it up by creating our own objects.
      iframeHTML='\<iframe id="RSIFrame" style="';
      iframeHTML+='border:0px;';
      iframeHTML+='width:0px;';
      iframeHTML+='height:0px;';
      iframeHTML+='"><\/iframe>';
      document.body.innerHTML+=iframeHTML;
      IFrameObj = new Object();
      IFrameObj.document = new Object();
      IFrameObj.document.location = new Object();
      IFrameObj.document.location.iframe = document.getElementById('RSIFrame');
      IFrameObj.document.location.replace = function(location) {
        this.iframe.src = location;
      }
    }
  }
  
  if (navigator.userAgent.indexOf('Gecko') !=-1 && !IFrameObj.contentDocument) {
    // we have to give NS6 a fraction of a second
    // to recognize the new IFrame
    setTimeout('callToServer()',10);
    return false;
  }
  
  if (IFrameObj.contentDocument) {
    // For NS6
    IFrameDoc = IFrameObj.contentDocument; 
  } else if (IFrameObj.contentWindow) {
    // For IE5.5 and IE6
    IFrameDoc = IFrameObj.contentWindow.document;
  } else if (IFrameObj.document) {
    // For IE5
    IFrameDoc = IFrameObj.document;
  } else {
    return true;
  }
  
  IFrameDoc.location.replace(URL);
  setTimeout('callToServer()', updateCountOnlineUser);
  return false;
  
}

/* функция которая получает msg из iframe */
function handleResponse(msg, id) {
	if (document.getElementById) {
		document.getElementById(id).firstChild.nodeValue = msg;
		}
}
//]]>
</script>