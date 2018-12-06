addBookmark() ƒобавление страницы в избранное (закладки)
<script type="text/javascript">
//<![CDATA[
/**
 * ƒобавление страницы в избранное (закладки)
 * @return bool
 * @copyright http://www.tigir.com/addbookmark.htm
 */
function addBookmark(url, title) {
	if (!url) url = location.href;
	if (!title) title = document.title;
	//Gecko
	if ((typeof window.sidebar == "object") && (typeof window.sidebar.addPanel == "function")) {
		window.sidebar.addPanel (title, url, "");
	}
	//IE4+
	else if (typeof window.external == "object") {
		window.external.AddFavorite(url, title);
	}
	else {
		return false;
	}
	return true;
}
//]]>
</script>