<?php
/*
Template Name: ACP Admin Statistic Results (Registrations)
Page ID: 86
*/

if (!$is_admin) {
	wp_redirect(get_page_link(51));
	exit(0);
}

if (empty($_POST)) {
	wp_redirect(get_page_link(85));
	exit(0);
}

@ini_set('display_errors', 'On');

$inc_path = str_replace('wp-content', 'wp-includes', WP_CONTENT_DIR);
$admin_path = str_replace('wp-content', 'wp-admin', WP_CONTENT_DIR);
$admin_url = get_admin_url();
require_once $inc_path . '/PHPExcel/PHPExcel.php';
require_once $inc_path . '/PHPExcel/PHPExcel/Writer/Excel2007.php';
require_once $inc_path . '/PHPExcel/PHPExcel/IOFactory.php';
require_once $inc_path . '/PHPExcel/PHPExcel/Shared/Date.php';

$stats = ACPManager::sql_stats_event($_POST['statinfo']['filters']['event']);
$bmi = $stats['bmi'];
$bp = $stats['bp'];
$vision = $stats['vision'];
$total = $stats['totals'];
$tips = $stats['tips_breakdown'];

$excel = new PHPExcel(); 
$excel->setActiveSheetIndex(0); 
$sheet = $excel->getActiveSheet();

$sheet->setCellValueByColumnAndRow(0, 1, 'Registration Statistics');

$headpos = 0;
$sheet->setCellValueByColumnAndRow($headpos, 2, '');
$sheet->setCellValueByColumnAndRow(++$headpos, 2, 'Registered');
$sheet->setCellValueByColumnAndRow($headpos, 3, $bmi['registered']);
$sheet->setCellValueByColumnAndRow($headpos, 4, $vision['registered']);
$sheet->setCellValueByColumnAndRow($headpos, 5, $bp['registered']);
$sheet->setCellValueByColumnAndRow($headpos, 6, $total['registered']);

$sheet->setCellValueByColumnAndRow(++$headpos, 2, 'Requested More Info');
$sheet->setCellValueByColumnAndRow($headpos, 3, $bmi['moreinfo']);
$sheet->setCellValueByColumnAndRow($headpos, 4, $vision['moreinfo']);
$sheet->setCellValueByColumnAndRow($headpos, 5, $bp['moreinfo']);
$sheet->setCellValueByColumnAndRow($headpos, 6, $total['moreinfo']);

$sheet->setCellValueByColumnAndRow(++$headpos, 2, 'Already Patient');
$sheet->setCellValueByColumnAndRow($headpos, 3, $bmi['patient']);
$sheet->setCellValueByColumnAndRow($headpos, 4, $vision['patient']);
$sheet->setCellValueByColumnAndRow($headpos, 5, $bp['patient']);
$sheet->setCellValueByColumnAndRow($headpos, 6, $total['patient']);

$sheet->setCellValueByColumnAndRow(++$headpos, 2, 'Chose ACP Tips');
$sheet->setCellValueByColumnAndRow($headpos, 3, $bmi['tips']);
$sheet->setCellValueByColumnAndRow($headpos, 4, $vision['tips']);
$sheet->setCellValueByColumnAndRow($headpos, 5, $bp['tips']);
$sheet->setCellValueByColumnAndRow($headpos, 6, $total['tips']);

$sheet->setCellValueByColumnAndRow(++$headpos, 2, 'Total');
$sheet->setCellValueByColumnAndRow($headpos, 3, $bmi['total']);
$sheet->setCellValueByColumnAndRow($headpos, 4, $vision['total']);
$sheet->setCellValueByColumnAndRow($headpos, 5, $bp['total']);
$sheet->setCellValueByColumnAndRow($headpos, 6, $total['total']);

$headpos = 3;
$sheet->setCellValueByColumnAndRow(0, $headpos, 'BMI');
$sheet->setCellValueByColumnAndRow(0, ++$headpos, 'Vision');
$sheet->setCellValueByColumnAndRow(0, ++$headpos, 'BP');
$sheet->setCellValueByColumnAndRow(0, ++$headpos, 'Total');

$sheet->setCellValueByColumnAndRow(0, 8, 'ACP Tips Breakdown');
$sheet->setCellValueByColumnAndRow(0, 9, '');
$sheet->setCellValueByColumnAndRow(1, 9, 'Amount of Requests');

$headpos = 10;
$sheet->setCellValueByColumnAndRow(0, $headpos, 'Diabetes Management');
$sheet->setCellValueByColumnAndRow(1, $headpos, $tips['diabetes']);
$sheet->setCellValueByColumnAndRow(0, ++$headpos, 'Fitness');
$sheet->setCellValueByColumnAndRow(1, $headpos, $tips['fitness']);
$sheet->setCellValueByColumnAndRow(0, ++$headpos, 'Healthy Eating');
$sheet->setCellValueByColumnAndRow(1, $headpos, $tips['eating']);
$sheet->setCellValueByColumnAndRow(0, ++$headpos, 'Child Health');
$sheet->setCellValueByColumnAndRow(1, $headpos, $tips['child']);
$sheet->setCellValueByColumnAndRow(0, ++$headpos, "Women's Health Issues");
$sheet->setCellValueByColumnAndRow(1, $headpos, $tips['women']);
$sheet->setCellValueByColumnAndRow(0, ++$headpos, 'Total');
$sheet->setCellValueByColumnAndRow(1, $headpos, $tips['total']);

$sheet->getStyle('A1')->getFont()->setBold(true)->setItalic(true);
$sheet->getStyle('A8')->getFont()->setBold(true)->setItalic(true);
$sheet->getStyle('B9')->getFont()->setBold(true);
$sheet->getStyle('A2:B2')->getFont()->setBold(true);
$sheet->getStyle('C2:D2')->getFont()->setBold(true);
$sheet->getStyle('E2:F2')->getFont()->setBold(true);
$sheet->getStyle('A3:A4')->getFont()->setBold(true);
$sheet->getStyle('A5:A6')->getFont()->setBold(true);
$sheet->getStyle('A10:A11')->getFont()->setBold(true);
$sheet->getStyle('A12:A13')->getFont()->setBold(true);
$sheet->getStyle('A14:A15')->getFont()->setBold(true);

for ($i = 0; $i <= 5; ++$i) {
	$sheet->getColumnDimensionByColumn($i)->setWidth(25);
}

$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
$filename = "ACP_Registrations_Stats_Report_" . date('Y-m-d_h:iA') . '.xlsx';
/*header('Content-Type: application/octet-stream');
header("Content-Disposition: attachment; filename=\"{$filename}\";");
header('Content-Transfer-Encoding: binary');*/
$writer->save($admin_path . '/reports/' . $filename);
$stats_link = "{$admin_url}/reports/{$filename}";

if (!empty($_POST['statinfo']['data'])) {
	$data = ACPManager::sql_stats_event_data($_POST['statinfo']['filters']['event']);
	$fields = $_POST['statinfo']['data'];
	
	$excel = new PHPExcel(); 
	$excel->setActiveSheetIndex(0); 
	$sheet = $excel->getActiveSheet();
	
	$headpos = -1;
	$widths = array();
	$cols = array();
	if (!empty($fields['name'])) {
		$sheet->setCellValueByColumnAndRow(++$headpos, 1, 'First Name');
		$sheet->getStyleByColumnAndRow($headpos, 1)->getFont()->setBold(true);
		$sheet->setCellValueByColumnAndRow(++$headpos, 1, 'Last Name');
		$sheet->getStyleByColumnAndRow($headpos, 1)->getFont()->setBold(true);
		$widths[] = 25;
		$widths[] = 25;
		$cols[] = 'reg_fname';
		$cols[] = 'reg_lname';
	}
	if (!empty($fields['email'])) {
		$sheet->setCellValueByColumnAndRow(++$headpos, 1, 'Email');
		$sheet->getStyleByColumnAndRow($headpos, 1)->getFont()->setBold(true);
		$widths[] = 30;
		$cols[] = 'rec_email';
	}
	if (!empty($fields['phone'])) {
		$sheet->setCellValueByColumnAndRow(++$headpos, 1, 'Phone');
		$sheet->getStyleByColumnAndRow($headpos, 1)->getFont()->setBold(true);
		$widths[] = 15;
		$cols[] = 'reg_phone';
	}
	if (!empty($fields['zip'])) {
		$sheet->setCellValueByColumnAndRow(++$headpos, 1, 'Zip');
		$sheet->getStyleByColumnAndRow($headpos, 1)->getFont()->setBold(true);
		$widths[] = 15;
		$cols[] = 'reg_zip';
	}
	if (!empty($fields['type'])) {
		$sheet->setCellValueByColumnAndRow(++$headpos, 1, 'Type');
		$sheet->getStyleByColumnAndRow($headpos, 1)->getFont()->setBold(true);
		$widths[] = 30;
		$cols[] = 'rec_type';
	}
	if (!empty($fields['event'])) {
		$sheet->setCellValueByColumnAndRow(++$headpos, 1, 'Event');
		$sheet->getStyleByColumnAndRow($headpos, 1)->getFont()->setBold(true);
		$widths[] = 50;
		$cols[] = 'event_name';
	}
	if (!empty($fields['date'])) {
		$sheet->setCellValueByColumnAndRow(++$headpos, 1, 'Date/Time');
		$sheet->getStyleByColumnAndRow($headpos, 1)->getFont()->setBold(true);
		$widths[] = 25;
		$cols[] = 'date';
	}
	if (!empty($fields['moreinfo'])) {
		$sheet->setCellValueByColumnAndRow(++$headpos, 1, 'ACP Tips');
		$sheet->getStyleByColumnAndRow($headpos, 1)->getFont()->setBold(true);
		$widths[] = 50;
		$cols[] = 'reg_info_choices';
	}
	if (!empty($fields['patient'])) {
		$sheet->setCellValueByColumnAndRow(++$headpos, 1, 'Already Patient');
		$sheet->getStyleByColumnAndRow($headpos, 1)->getFont()->setBold(true);
		$widths[] = 20;
		$cols[] = 'reg_patient';
	}
	if (!empty($fields['scrninfo'])) {
		$sheet->setCellValueByColumnAndRow(++$headpos, 1, 'Requested More Info');
		$sheet->getStyleByColumnAndRow($headpos, 1)->getFont()->setBold(true);
		$widths[] = 25;
		$cols[] = 'reg_send_screen_info';
	}
	
	foreach ($widths as $k => $v) {
		$sheet->getColumnDimensionByColumn($k)->setWidth($v);
	}
	
	foreach ($data as $k => $v) {
		foreach ($cols as $key => $val) {
			if ($val == 'rec_type') {
				switch ($v->rec_type) {
					case ACPRecord::TYPE_EVENTREG:
						$val = 'Regular Registration';
						break;
					case ACPRecord::TYPE_SCREENBP:
						$val = 'Blood Pressure Screening';
						break;
					case ACPRecord::TYPE_SCREENBMI:
						$val = 'BMI Screening';
						break;
					case ACPRecord::TYPE_SCREENVISION:
						$val = 'Vision Screening';
						break;
					case ACPRecord::TYPE_SCREENVITALS:
						$val = 'Vitals Screening';
						break;
					case ACPRecord::TYPE_FAQDOCTOR:
						$val = 'Choose Doctor FAQ Email';
						break;
					default:
						$val = '-';
						break;
				}
				$sheet->setCellValueByColumnAndRow($key, $k + 2, $val);
			}
			else {
				$sheet->setCellValueByColumnAndRow($key, $k + 2, $v->$val);
			}
		}
	}
	
	$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
	$filename = "ACP_Registrations_Data_Report_" . date('Y-m-d_h:iA') . '.xlsx';
	$writer->save($admin_path . '/reports/' . $filename);
	$data_link = "{$admin_url}/reports/{$filename}";
}

get_header('admin');
?>
<!--main starts-->
<div id="main">
	<div class="container">
		<h1 class="title">Registration Statistics</h1>
		
		<div id="reg-stats" class="dashboard-block">
		<!--h2>Registration Statistics</h2-->
		<table class="data orange" style="max-width: 1110px;">
		<tr class="header">
			<td>&#160;</td>
			<td>Registered</td>
			<td>Requested More Info</td>
			<td>Already Patient</td>
			<td>Chose ACP Tips</td>
			<td>Total</td>
		</tr>
		<tr>
			<th>BMI</th>
			<td><?php echo $bmi['registered']; ?></td>
			<td><?php echo $bmi['moreinfo']; ?></td>
			<td><?php echo $bmi['patient']; ?></td>
			<td><?php echo $bmi['tips']; ?></td>
			<td><?php echo $bmi['total']; ?></td>
		</tr>
		<tr>
			<th>Vision</th>
			<td><?php echo $vision['registered']; ?></td>
			<td><?php echo $vision['moreinfo']; ?></td>
			<td><?php echo $vision['patient']; ?></td>
			<td><?php echo $vision['tips']; ?></td>
			<td><?php echo $vision['total']; ?></td>
		</tr>
		<tr>
			<th>Blood Pressure</th>
			<td><?php echo $bp['registered']; ?></td>
			<td><?php echo $bp['moreinfo']; ?></td>
			<td><?php echo $bp['patient']; ?></td>
			<td><?php echo $bp['tips']; ?></td>
			<td><?php echo $bp['total']; ?></td>
		</tr>
		<tr>
			<th>Total</th>
			<td><?php echo $total['registered']; ?></td>
			<td><?php echo $total['moreinfo']; ?></td>
			<td><?php echo $total['patient']; ?></td>
			<td><?php echo $total['tips']; ?></td>
			<td><b><?php echo $total['total']; ?></b></td>
		</tr>
		</table>
		</div>
		
		<div id="reg-stats" class="dashboard-block">
		<h2>ACP Tips Breakdown</h2>
		<table class="data orange" style="max-width: 500px;">
		<tr class="header">
			<td>&#160;</td>
			<td>Amount of Requests</td>
		</tr>
		<tr>
			<th>Diabetes Management</th>
			<td><?php echo $tips['diabetes']; ?></td>
		</tr>
		<tr>
			<th>Fitness</th>
			<td><?php echo $tips['fitness']; ?></td>
		</tr>
		<tr>
			<th>Healthy Eating</th>
			<td><?php echo $tips['eating']; ?></td>
		</tr>
		<tr>
			<th>Child Health</th>
			<td><?php echo $tips['child']; ?></td>
		</tr>
		<tr>
			<th>Women's Health Issues</th>
			<td><?php echo $tips['women']; ?></td>
		</tr>
		<tr>
			<th>Total</th>
			<td><b><?php echo $tips['total']; ?></b></td>
		</tr>
		</table>
		</div>
		
		<p class="download-link"><a href="<?php echo $stats_link; ?>">Download Stats</a></p>
		
		<?php if (isset($data_link)): ?>
		<p class="download-link"><a href="<?php echo $data_link; ?>">Download Data</a></p>
		<?php endif; ?>
</div>
<!--main ends-->

<?php
get_footer('admin');
?>