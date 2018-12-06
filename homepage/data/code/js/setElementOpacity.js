setElementOpacity() Функция для задания прозрачности элементу страницы
<script type="text/javascript">
//<![CDATA[
/**
 * Функция setElementOpacity(sElemId, nOpacity) принимает два аргумента: sElemId - id элемента,
 * nOpacity - вещественное число от 0.0 до 1.0
 * задающее прозрачность в стиле CSS3 opacity.
 * @copyright http://www.tigir.com/opacity.htm
 */
function getOpacityProperty()
{
  if (typeof document.body.style.opacity == 'string') // CSS3 compliant (Moz 1.7+, Safari 1.2+, Opera 9)
    return 'opacity';
  else if (typeof document.body.style.MozOpacity == 'string') // Mozilla 1.6 и младше, Firefox 0.8 
    return 'MozOpacity';
  else if (typeof document.body.style.KhtmlOpacity == 'string') // Konqueror 3.1, Safari 1.1
    return 'KhtmlOpacity';
  else if (document.body.filters && navigator.appVersion.match(/MSIE ([\d.]+);/)[1]>=5.5) // Internet Exploder 5.5+
    return 'filter';

  return false; //нет прозрачности
}
function setElementOpacity(sElemId, nOpacity)
{
  var opacityProp = getOpacityProperty();
  var elem = document.getElementById(sElemId);

  if (!elem || !opacityProp) return; // Если не существует элемент с указанным id или браузер не поддерживает ни один из известных функции способов управления прозрачностью
  
  if (opacityProp=="filter")  // Internet Exploder 5.5+
  {
    nOpacity *= 100;
	
    // Если уже установлена прозрачность, то меняем её через коллекцию filters, иначе добавляем прозрачность через style.filter
    var oAlpha = elem.filters['DXImageTransform.Microsoft.alpha'] || elem.filters.alpha;
    if (oAlpha) oAlpha.opacity = nOpacity;
    else elem.style.filter += "progid:DXImageTransform.Microsoft.Alpha(opacity="+nOpacity+")"; // Для того чтобы не затереть другие фильтры используем "+="
  }
  else // Другие браузеры
    elem.style[opacityProp] = nOpacity;
}
//]]>
</script>