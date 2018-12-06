setElementOpacity() ������� ��� ������� ������������ �������� ��������
<script type="text/javascript">
//<![CDATA[
/**
 * ������� setElementOpacity(sElemId, nOpacity) ��������� ��� ���������: sElemId - id ��������,
 * nOpacity - ������������ ����� �� 0.0 �� 1.0
 * �������� ������������ � ����� CSS3 opacity.
 * @copyright http://www.tigir.com/opacity.htm
 */
function getOpacityProperty()
{
  if (typeof document.body.style.opacity == 'string') // CSS3 compliant (Moz 1.7+, Safari 1.2+, Opera 9)
    return 'opacity';
  else if (typeof document.body.style.MozOpacity == 'string') // Mozilla 1.6 � ������, Firefox 0.8 
    return 'MozOpacity';
  else if (typeof document.body.style.KhtmlOpacity == 'string') // Konqueror 3.1, Safari 1.1
    return 'KhtmlOpacity';
  else if (document.body.filters && navigator.appVersion.match(/MSIE ([\d.]+);/)[1]>=5.5) // Internet Exploder 5.5+
    return 'filter';

  return false; //��� ������������
}
function setElementOpacity(sElemId, nOpacity)
{
  var opacityProp = getOpacityProperty();
  var elem = document.getElementById(sElemId);

  if (!elem || !opacityProp) return; // ���� �� ���������� ������� � ��������� id ��� ������� �� ������������ �� ���� �� ��������� ������� �������� ���������� �������������
  
  if (opacityProp=="filter")  // Internet Exploder 5.5+
  {
    nOpacity *= 100;
	
    // ���� ��� ����������� ������������, �� ������ � ����� ��������� filters, ����� ��������� ������������ ����� style.filter
    var oAlpha = elem.filters['DXImageTransform.Microsoft.alpha'] || elem.filters.alpha;
    if (oAlpha) oAlpha.opacity = nOpacity;
    else elem.style.filter += "progid:DXImageTransform.Microsoft.Alpha(opacity="+nOpacity+")"; // ��� ���� ����� �� �������� ������ ������� ���������� "+="
  }
  else // ������ ��������
    elem.style[opacityProp] = nOpacity;
}
//]]>
</script>