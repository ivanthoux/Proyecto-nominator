<?php
$this->load->library(['excel']);
// echo '<pre>'.print_r($title, true);
// echo '<pre>'.print_r($filename, true);
// echo '<pre>'.print_r($data, true);
// die();

$objPHPExcel = new PHPExcel();
$objPHPExcel->setActiveSheetIndex(0);
$center = array(
  'alignment' => array(
    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
  )
);
$rowCount = 1;
$objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $title);
$objPHPExcel->getActiveSheet()->mergeCells('A' . $rowCount . ':I' . $rowCount);
$objPHPExcel->getActiveSheet()->getStyle('A' . $rowCount . ':I' . $rowCount)->applyFromArray($center);
$objPHPExcel->getActiveSheet()->getStyle('A' . $rowCount . ':I' . $rowCount)->getFont()->setBold(true);

// set Header
$rowCount++;
$objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, 'Fecha');
$objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, 'Cliente/Detalle');
$objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, 'Tipo');
$objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, 'Valor');
$objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, 'Hoja de Ruta');
$objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, 'Modo');
$objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount, 'Recibido Por');
$objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount, 'Caja Cerrada');
$objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount, 'Descripción');
$objPHPExcel->getActiveSheet()->getStyle('A' . $rowCount . ':I' . $rowCount)->applyFromArray($center);

// die("<pre>".print_r($rows, true));
foreach ($data as $row) {
  $rowCount++;

  $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, date('d-m-Y', strtotime($row[0])));
  $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, $row[1]);
  $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, $row[2] == 'payments' ? 'Ingreso' : 'Egreso');
  $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, $row[3]);
  $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, $row[4]);
  $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, $row[5]);
  $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount, $row[6]);
  $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount, $row[7] ? 'SI' : 'NO');
  $objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount, $row[8]);
}

foreach (range('A', 'I') as $value) {
  $objPHPExcel->getActiveSheet()->getColumnDimension($value)->setAutoSize(true);
}

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

header('Content-type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="' . $filename . '.xls"');
$objWriter->save('php://output');
