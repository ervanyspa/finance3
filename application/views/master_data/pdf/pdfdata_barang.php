<?php
defined('BASEPATH') or exit('No direct script access allowed');
// error_reporting(0); // AGAR ERROR MASALAH VERSI PHP TIDAK MUNCUL


class RPDF extends FPDF{
	// Page Header
	function Header()
	{
        if ($this->PageNo() == 1) {
            $this->SetFont('Arial', 'I', 9);
            $this->SetFillColor(255, 255, 255);
            $this->Cell(90, 6, '', 0, 0, 'L', 1);
            $this->Cell(100, 6, "Printed date : " . date('d-M-Y'), 0, 1, 'R', 1);
            $this->Line(10, $this->GetY(), 200, $this->GetY());
        }else{
			$this->SetFont('Arial','I',9);
			$this->SetFillColor(255,255,255);
			$this->Cell(90,6,"Data Barang",0,0,'L',1);
			$this->Cell(100,6,"Printed date : " . date('d-M-Y'),0,1,'R',1);
		}
	}

	// Page Content
	function Content($barang){
		$this->Ln(3);
		$this->SetFont('Arial', 'B', 12);
		$this->SetFillColor(255, 255, 255);
		$this->Cell(0, 6, "TOKO ANITA",0,1,'C',1);
		$this->Cell(0, 6, "Laporan Data Barang",0,1,'C',1);
		$this->Ln(2);

		$this->SetLineWidth(0.8);
		$this->Line(10,$this->GetY(),200,$this->GetY());
		$this->SetLineWidth(0);
		
		$this->Ln(3);
		$this->SetFont('Arial', 'B', 10);
		$this->SetFillColor(255, 255, 255);
		$this->Cell(8,6,'No',1,0,'C');
		$this->Cell(25,6,'Kode Barang',1,0,'C');
		$this->Cell(29,6,'Nama Barang',1,0,'C');
		$this->Cell(28,6,'Jenis Barang',1,0,'C');
		$this->Cell(56,6,'Keterangan',1,0,'C');
		$this->Cell(30,6,'Harga',1,0,'C');
		$this->Cell(15,6,'Stok',1,1,'C');
		$this->SetFont('Arial','',10);
		$no=0;
		foreach ($barang as $br){
			$no++;
			$cellWidth=56; //lebar sel
			$cellHeight=6; //tinggi sel satu baris normal
			
			//periksa apakah teksnya melibihi kolom?
			if($this->GetStringWidth($br['keterangan']) < $cellWidth){
				//jika tidak, maka tidak melakukan apa-apa
				$line=1;
			}else{
				//jika ya, maka hitung ketinggian yang dibutuhkan untuk sel akan dirapikan
				//dengan memisahkan teks agar sesuai dengan lebar sel
				//lalu hitung berapa banyak baris yang dibutuhkan agar teks pas dengan sel
				
				$textLength=strlen($br['keterangan']);	//total panjang teks
				$errMargin=5;		//margin kesalahan lebar sel, untuk jaga-jaga
				$startChar=0;		//posisi awal karakter untuk setiap baris
				$maxChar=0;			//karakter maksimum dalam satu baris, yang akan ditambahkan nanti
				$textArray=array();	//untuk menampung data untuk setiap baris
				$tmpString="";		//untuk menampung teks untuk setiap baris (sementara)
				
				while($startChar < $textLength){ //perulangan sampai akhir teks
					//perulangan sampai karakter maksimum tercapai
					while( 
					$this->GetStringWidth( $tmpString ) < ($cellWidth-$errMargin) &&
					($startChar+$maxChar) < $textLength ) {
						$maxChar++;
						$tmpString=substr($br['keterangan'],$startChar,$maxChar);
					}
					//pindahkan ke baris berikutnya
					$startChar=$startChar+$maxChar;
					//kemudian tambahkan ke dalam array sehingga kita tahu berapa banyak baris yang dibutuhkan
					array_push($textArray,$tmpString);
					//reset variabel penampung
					$maxChar=0;
					$tmpString='';
					
				}
				//dapatkan jumlah baris
				$line=count($textArray);
			}



			$this->Cell(8,($line * $cellHeight),$no,1,0, 'C');
			$this->Cell(25,($line * $cellHeight),$br['kodeBarang'],1,0);
			$this->Cell(29,($line * $cellHeight),$br['nama'],1,0);
			$this->Cell(28,($line * $cellHeight),$br['namaJenis'],1,0);

			//memanfaatkan MultiCell sebagai ganti Cell
			//atur posisi xy untuk sel berikutnya menjadi di sebelahnya.
			//ingat posisi x dan y sebelum menulis MultiCell
			$xPos=$this->GetX();
			$yPos=$this->GetY();
			$this->MultiCell($cellWidth,$cellHeight,$br['keterangan'],1);

			//kembalikan posisi untuk sel berikutnya di samping MultiCell 
			//dan offset x dengan lebar MultiCell
			$this->SetXY($xPos + $cellWidth , $yPos);
			$this->Cell(30,($line * $cellHeight),"Rp.". number_format($br['harga'], 0, ",", ","),1,0);
			$this->Cell(15,($line * $cellHeight),number_format($br['stok'], 0, ",", ","),1,1);
		}
	}

	// Page Footer
	function Footer()
	{
		//Position at 1.5 cm from bottom
		$this->SetY(-15);
		//Buat Garis Horizontal
		$this->Line(10,$this->GetY(),200,$this->GetY());
		//Arial Italic 9
		$this->SetFont('Arial', 'I', 9);
		$this->Cell(0,10,'Copyright@'.date('Y'),0,0,'L');
		//nomor halaman
		$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'R');

	}
}

error_reporting(0);
$this->myfpdf = new RPDF();
$this->myfpdf->AliasNbPages();
$this->myfpdf->AddPage();
$this->myfpdf->SetTitle('LAPORAN DATA BARANG TOKO ANITA',true);
$this->myfpdf->Content($barang);
$this->myfpdf->Output('laporan-data-barang-toko-anita ['.date('d-M-Y').']','I');

?>
