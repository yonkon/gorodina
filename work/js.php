<html>
<head></head>
<body>

<!-- ���� CSS -->
<style type="text/css">
#rabotaSearchFormContainer {width:400px;border:1px solid #a6a6ff;background-color:#e1f0fa;color:#000000;font-family:Arial;font-size:11px}
#rabotaSearchFormContainer select {width:100%}
#rabotaSearchListContainer {font-family: Arial; color: #000000;}
#rabotaSearchListContainer a {color: #000080;}
#rabotaSearchListContainer a.title, span.title {font-weight: bold;}
#rabotaSearchListContainer input, select  {font-family:Arial;background-color:#ffffff;color:#000000;}
#rabotaSearchListContainer th, #rabotaSearchListContainer td  {padding:3px 0 4px 10px; vertical-align:middle;}
#rabotaSearchListContainer th:first-child, #rabotaSearchListContainer td:first-child  {padding:3px 0 4px 5px;}
tr.stripped {background-color: #eeeeee;}
</style>
<!-- ����� ����� CSS -->

<!-- ����� ������ -->
<form id="rabotaSearchForm" name="rabotaSearchForm">
<input type="hidden" name="mode" value="search">
<table id="rabotaSearchFormContainer">
<tr>
<td colspan="2" align="center"><a href="http://www.rabota.ru/" title="������.Ru"><img id="logo" src="http://www.rabota.ru/img/logo_for_informer.png" width="100" height="19" border="0" /></a></td>
</tr>
<tr>
<td colspan="2">
<input type="text" id="w" name="w" value="" class="rabota-input" />
</td>
</tr>
<tr>
<td colspan="2">
<input type="radio" id="t1" name="t" value="1">&nbsp;<label for="t1">��������</label>
<input type="radio" id="t2" name="t" value="2">&nbsp;<label for="t2">������</label>
</td>
</tr>
<tr>
<td colspan="2">
<select id="c" name="c" class="rabota-select"><option value="1">�������� �����!!!</option></select>
</td>
</tr>
<tr>
<td colspan="2">
<select id="r" name="r" class="rabota-select"><option value="">- �������� ������� ������������ -</option></select>
</td>
</tr>
<tr>
<td width="70%">
<select id="s" name="s" class="rabota-select"><option value="7">�� ����</option><option value="3">�� ��������</option><option value="2">�� �������������</option></select>
</td>
<td width="30%">
<select id="d" name="d" class="rabota-select"><option value="asc">&Delta;</option><option value="desc">&nabla;</option></select>
</td>
</tr>
<tr>
<td>
<input type="submit" value="�����" />
</td>
<td>
<select id="pp" name="pp" class="rabota-select"><option value="10">10</option><option value="30">30</option><option value="50">50</option></select>
</td>
</tr>
</table>
</form>
<!-- ����� ����� ������ -->

<!-- ����� ����������� ������ -->
<div style="padding:0 60px 0 70px;" id="rabotaSearchListContainer"></div>
<!-- ����� ������ ����������� ������ -->

<!-- ����� JS-������� -->
<script type="text/javascript">
var rabota_informer_count = 0;
function rabota_print_informer()
{
	var s = document.createElement("script");
	s.src = 'http://www.rabota.ru/v3_viewSearchForm.html?'+document.location.search.substring(1);
	s.type = "text/javascript";
	s.charset = "UTF-8";
	document.getElementsByTagName("head")[0].appendChild(s);
	s.onreadystatechange = function() {
		if(s.readyState == "loaded" || s.readyState == "complete") {
			try {
				var r2 = new rabotaSearchForm();
				window.status = "";
			} catch (e) {
				window.status = "��� ���� �������...";
				rabota_informer_count++;
				if(rabota_informer_count < 5) {
					rabota_print_informer();
				} else {
					alert("����������� ���������� � ��������. ���������� �������� ��������.");
				}
			}
		}
	}
}
rabota_print_informer();
</script>
<!-- ����� ������ JS-������� -->

</body>
</html>