<?php      
	header("Content-Type: application/vnd.ms-excel; charset=utf-8");
	header("Content-type: application/x-msexcel; charset=utf-8");
	header("content-disposition: attachment;filename=Planilla_consolidada.xls");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private", false);
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<style type="text/css">
		table tbody tr td{
		  mso-number-format:"\@";
		}
		table tbody tr td.okc-numero{
		  mso-number-format:"#,##0.00";
		}
		table tbody tr td.okc-order{
		  mso-number-format:"#,##0";
		}
	</style>
</head>
<body>
	<h2>Planilla Consolidada <?=$companyExport?></h2>
	<h4><?=$perExport?></h4>
	<table border="1">
		<thead><?=$headExport?></thead>
		<tbody><?=$dataExport?></tbody>
	</table>
</body>
</html>