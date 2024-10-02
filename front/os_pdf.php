<?php
/*
   ------------------------------------------------------------------------
   Plugin OS
   Copyright (C) 2016-2024 by Junior Marcati
   https://github.com/juniormarcati/os
   ------------------------------------------------------------------------
   LICENSE
   This file is part of Plugin OS project.
   Plugin OS is free software: you can redistribute it and/or modify
   it under the terms of the GNU Affero General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.
   Plugin OS is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
   GNU Affero General Public License for more details.
   You should have received a copy of the GNU Affero General Public License
   along with Plugin OS. If not, see <http://www.gnu.org/licenses/>.
   ------------------------------------------------------------------------
   @package   Plugin OS
   @author    Junior Marcati
   @co-author
   @copyright Copyright (c) 2016-2024 OS Plugin Development team
   @license   AGPL License 3.0 or (at your option) any later version
              http://www.gnu.org/licenses/agpl-3.0-standalone.html
   @link      https://github.com/juniormarcati/os
   @since     2016
   ------------------------------------------------------------------------
 */
include ('../../../inc/includes.php');
include ('configOs.php');
include ('../inc/pdf/fpdf.php');
include ('../inc/qrcode/vendor/autoload.php');
global $DB;
Session::checkLoginUser();
class PDF extends FPDF {
	// Page header
	function Header() {
		include ('../inc/qrcode/vendor/autoload.php');
		global $EmpresaPlugin;
		global $CnpjPlugin;
		global $EnderecoPlugin;
		global $CidadePlugin;
		global $SitePlugin;
		global $TelefonePlugin;
		global $DataOs;
		global $OsId;
		// Logo
		$this->Cell(30);
		$this->Image('../pics/logo_os.png',8,15,45);
		// Title - Line 1: Company name & OS
		$this->Cell(20);
		$this->SetFont('Arial','B',11);
		$this->Cell(90,5,'Prefeitura Municipal de Campo Largo',0,0,'C');
		$this->Cell(53,5,"",0,0,'C');

		// Title - Line 2: Phone number & OS Number
		$this->Ln();
		$this->Cell(50);
		$this->SetFont('Arial','B',9);
		$this->Cell(90,3,mb_convert_encoding(strip_tags(htmlspecialchars_decode("$EmpresaPlugin")), "ISO-8859-1", "UTF-8"),0,0,'C');
		$this->SetFont('Arial','B',9);
		$this->Cell(33,3,mb_convert_encoding("OS Nº", "ISO-8859-1", "UTF-8"),0,0,'C');
		$this->Cell(20,3,"",0,0,'C');

		// Title - Line 3: Company registration number & Os date
		$this->Ln();
		$this->SetFont('Arial','',9);
		$this->SetTextColor(0,0,0);
		$this->Cell(50);
		$this->Cell(90,3,mb_convert_encoding(strip_tags(htmlspecialchars_decode("$TelefonePlugin")), "ISO-8859-1", "UTF-8"),0,0,'C');
		$this->SetFont('Arial','B',11);
		$this->SetTextColor(250,0,0);
		$this->Cell(33,3,"$OsId",0,0,'C');
		$this->SetFont('Arial','',9);
		$this->SetTextColor(0,0,0);
		$this->Cell(20,3,"",0,0,'C');
		$this->Ln();
		// Title - Line 4: Company address
		$this->Cell(50);
		$this->SetFont('Arial','',9);
		$this->Cell(90,3,mb_convert_encoding(strip_tags(htmlspecialchars_decode("$EnderecoPlugin - $CidadePlugin")), "ISO-8859-1", "UTF-8"),0,0,'C');
		$this->Cell(33,3,"$DataOs",0,0,'C');
		$this->Cell(20,3,"",0,0,'C');
		$this->Image('../pics/qr.png',180,10,22);
		// Title - Line 5: URL
		$this->Ln();
		$this->Cell(50);
		$this->SetFont('Arial','',8);
		$this->Cell(90,3,"$SitePlugin",0,0,'C');
		$this->Cell(33,3,"",0,0,'C');
		$this->Cell(20,3,"",0,0,'C');
		$this->Ln(8);
	}
}
// QR Code
$url = $CFG_GLPI['url_base'];
$url2 = "/front/ticket.form.php?id=".$_GET['id']."";
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
$options = new QROptions([
  'version' => 5,
  'eccLevel' => QRCode::ECC_L,
  'outputType' => QRCode::OUTPUT_IMAGE_PNG,
  'imageBase64' => false
]);
file_put_contents('../pics/qr.png',(new QRCode($options))->render("$url$url2"));
// Instanciation of inherited class
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
// Entity
$pdf->SetDrawColor(75,75,85);
$pdf->setFillColor(55,55,64);
$pdf->SetFont('Arial','B',9);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(190,5,mb_convert_encoding("Dados do cliente", "ISO-8859-1", "UTF-8"),1,0,'C',true);
$pdf->Ln();
$pdf->SetFont('Arial','',8);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(23,4,mb_convert_encoding("Localização", "ISO-8859-1", "UTF-8"),1,0,'L',true);
$pdf->SetFont('Arial','',8);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(167,4,mb_convert_encoding(strip_tags(htmlspecialchars_decode("$Locations")), "ISO-8859-1", "UTF-8"),1,0,'L');
$pdf->SetFont('Arial','',8);
$pdf->SetTextColor(255,255,255);
$pdf->Ln();
$pdf->Cell(23,4,mb_convert_encoding(__("Requester"), "ISO-8859-1", "UTF-8"),1,0,'L',true);
$pdf->SetFont('Arial','',8);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(72,4,mb_convert_encoding("$UserName", "ISO-8859-1", "UTF-8"),1,0,'L');
$pdf->SetFont('Arial','',8);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(20,4,mb_convert_encoding(__("Phone"), "ISO-8859-1", "UTF-8"),1,0,'L',true);
$pdf->SetFont('Arial','',8);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(75,4,"$UserTelefone",1,0,'L');
$pdf->Ln();
$pdf->SetFont('Arial','',8);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(23,4,mb_convert_encoding(__("Address"), "ISO-8859-1", "UTF-8"),1,0,'L',true);
$pdf->SetFont('Arial','',6);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(72,4,mb_convert_encoding(strip_tags(htmlspecialchars_decode("$UserEndereco")), "ISO-8859-1", "UTF-8"),1,0,'L');
$pdf->SetFont('Arial','',8);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(20,4,"CEP",1,0,'L',true);
$pdf->SetFont('Arial','',8);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(75,4,"$UserCep",1,0,'L');
$pdf->Ln();
$pdf->SetFont('Arial','',8);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(23,4,mb_convert_encoding(__("Email"), "ISO-8859-1", "UTF-8"),1,0,'L',true);
$pdf->SetFont('Arial','',8);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(72,4,mb_convert_encoding(strip_tags(htmlspecialchars_decode("$UserEmail")), "ISO-8859-1", "UTF-8"),1,0,'L');
$pdf->Ln();
// Details
$pdf->SetFont('Arial','B',9);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(190,5,mb_convert_encoding("Detalhes do chamado", "ISO-8859-1", "UTF-8"),1,0,'C',true);
$pdf->Ln();
$pdf->SetFont('Arial','',8);
$pdf->Cell(23,4,mb_convert_encoding(__("Title"), "ISO-8859-1", "UTF-8"),1,0,'L',true);
$pdf->SetFont('Arial','',8);
$pdf->SetTextColor(0,0,0);
$pdf->MultiCell(167,4,mb_convert_encoding(strip_tags(htmlspecialchars_decode("$OsNome")), "ISO-8859-1", "UTF-8"),1,0);
$pdf->SetFont('Arial','',8);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(23,4,mb_convert_encoding(__("Technician"), "ISO-8859-1", "UTF-8"),1,0,'L',true);
$pdf->SetFont('Arial','',8);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(167,4,mb_convert_encoding(strip_tags(htmlspecialchars_decode("$OsResponsavel")), "ISO-8859-1", "UTF-8"),1,0,'L');
$pdf->Ln();
$pdf->SetFont('Arial','',8);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(23,4,mb_convert_encoding(__("Start date"), "ISO-8859-1", "UTF-8"),1,0,'L',true);
$pdf->SetFont('Arial','',8);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(72,4,"$OsData",1,0,'L');
$pdf->SetFont('Arial','',8);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(20,4,mb_convert_encoding(__("Closure"), "ISO-8859-1", "UTF-8"),1,0,'L',true);
$pdf->SetFont('Arial','',8);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(75,4,"$OsDataEntrega",1,0,'L');
$pdf->Ln();
// Items
if ( $ItensId == null ) {
} else {
	$pdf->SetFont('Arial','B',9);
	$pdf->SetTextColor(255,255,255);
	$pdf->Cell(190,5,mb_convert_encoding(__("Linked item"), "ISO-8859-1", "UTF-8"),1,0,'C',true);
	$pdf->Ln();
	$pdf->SetFont('Arial','',8);
	$pdf->SetTextColor(255,255,255);
	$pdf->Cell(23,4,mb_convert_encoding(__("Name"), "ISO-8859-1", "UTF-8"),1,0,'L',true);
	$pdf->SetFont('Arial','',8);
	if ( $ItemType == 'Computer' ) {
		$pdf->SetTextColor(0,0,0);
		$pdf->Cell(72,4,mb_convert_encoding(strip_tags(htmlspecialchars_decode("$ComputerName")), "ISO-8859-1", "UTF-8"),1,0,'L');
		$pdf->SetFont('Arial','',8);
		$pdf->SetTextColor(255,255,255);
		$pdf->Cell(23,4,mb_convert_encoding(__("Serial Number"), "ISO-8859-1", "UTF-8"),1,0,'L',true);
		$pdf->SetFont('Arial','',8);
		$pdf->SetTextColor(0,0,0);
		$pdf->Cell(72,4,mb_convert_encoding(strip_tags(htmlspecialchars_decode("$ComputerSerial")), "ISO-8859-1", "UTF-8"),1,0,'L');
		$pdf->Ln();
	} else if ( $ItemType == 'Monitor' ) {
		$pdf->SetTextColor(0,0,0);
		$pdf->Cell(72,4,mb_convert_encoding(strip_tags(htmlspecialchars_decode("$MonitorName")), "ISO-8859-1", "UTF-8"),1,0,'L');
		$pdf->SetFont('Arial','',8);
		$pdf->SetTextColor(255,255,255);
		$pdf->Cell(20,4,mb_convert_encoding(__("Serial Number"), "ISO-8859-1", "UTF-8"),1,0,'L',true);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(75,4,mb_convert_encoding(strip_tags(htmlspecialchars_decode("$MonitorSerial")), "ISO-8859-1", "UTF-8"),1,0,'L');
		$pdf->Ln();
	} else if ( $ItemType == 'Printer' ) {
		$pdf->SetTextColor(0,0,0);
		$pdf->Cell(72,4,mb_convert_encoding(strip_tags(htmlspecialchars_decode("$PrinterName")), "ISO-8859-1", "UTF-8"),1,0,'L');
		$pdf->SetFont('Arial','',8);
		$pdf->SetTextColor(255,255,255);
		$pdf->Cell(20,4,mb_convert_encoding(__("Serial Number"), "ISO-8859-1", "UTF-8"),1,0,'L',true);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(75,4,mb_convert_encoding(strip_tags(htmlspecialchars_decode("$PrinterSerial")), "ISO-8859-1", "UTF-8"),1,0,'L');
		$pdf->Ln();
	}
}
// Description
$pdf->SetFont('Arial','B',9);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(190,5,mb_convert_encoding(__("Description"), "ISO-8859-1", "UTF-8"),1,0,'C',true);
$pdf->Ln();
$pdf->SetFont('Arial','',8);
$pdf->SetTextColor(0,0,0);
$pdf->Multicell(190,5,mb_convert_encoding(strip_tags(htmlspecialchars_decode("$OsDescricao")), "ISO-8859-1", "UTF-8"),1,'J');
// Solution
$pdf->SetFont('Arial','B',9);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(190,5,mb_convert_encoding(__("Solution"), "ISO-8859-1", "UTF-8"),1,0,'C',true);
$pdf->Ln();
$pdf->SetFont('Arial','',8);
$pdf->SetTextColor(0,0,0);
// Lines if solution is empty
if ( $OsSolucao == null ) {
	$pdf->SetFont('Arial','',8);
	$pdf->Cell(190,10,mb_convert_encoding("Descreva a solução:", "ISO-8859-1", "UTF-8"),0,0);
	$pdf->Ln(0);
	$pdf->Cell(190,45,mb_convert_encoding("", "ISO-8859-1", "UTF-8"),1,0);
	$pdf->Ln();
} else {
	$pdf->MultiCell(190,5,mb_convert_encoding(strip_tags(htmlspecialchars_decode("$OsSolucao")), "ISO-8859-1", "UTF-8"),1,0);
}
// Signatures
$pdf->SetFont('Arial','B',9);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(190,5,mb_convert_encoding("Assinaturas", "ISO-8859-1", "UTF-8"),1,0,'C',true);
$pdf->Ln();
$pdf->SetTextColor(0,0,0);
// Signatures Lines
$pdf->Cell(190,40,"",'LRB',0,'L');
$pdf->SetY($pdf->GetY() +20);
$pdf->SetFont('Arial','',8);
$pdf->Cell(95,4,mb_convert_encoding("_______________________________________", "ISO-8859-1", "UTF-8"),0,0,'C');
$pdf->SetFont('Arial','',8);
$pdf->Cell(95,4,mb_convert_encoding("_______________________________________", "ISO-8859-1", "UTF-8"),0,0,'C');
$pdf->Ln();
$pdf->SetFont('Arial','',8);
$pdf->Cell(95,4,mb_convert_encoding(strip_tags(htmlspecialchars_decode("$OsResponsavel")), "ISO-8859-1", "UTF-8"),0,0,'C');
$pdf->SetFont('Arial','',8);
$pdf->Cell(95,4,mb_convert_encoding(strip_tags(htmlspecialchars_decode("$UserName")), "ISO-8859-1", "UTF-8"),0,0,'C');
$pdf->Ln();
$pdf->SetFont('Arial','',8);
$pdf->Cell(95,4,mb_convert_encoding(strip_tags(htmlspecialchars_decode("$EmpresaPlugin")), "ISO-8859-1", "UTF-8"),0,0,'C');
$pdf->SetFont('Arial','',8);
$pdf->Cell(95,4,mb_convert_encoding(strip_tags(htmlspecialchars_decode("$Locations")), "ISO-8859-1", "UTF-8"),0,0,'C');
// Output PDF
$fileName = ''. $EmpresaPlugin .' - OS#'. $OsId .'.pdf';
$pdf->Output('I',$fileName);
