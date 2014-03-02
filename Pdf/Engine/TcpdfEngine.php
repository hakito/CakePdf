<?php
App::uses('AbstractPdfEngine', 'CakePdf.Pdf/Engine');
App::uses('Multibyte', 'I18n');
define('K_PATH_IMAGES', WWW_ROOT . 'img' . DS);

class TcpdfEngine extends AbstractPdfEngine {

/**
 * Constructor
 *
 * @param $Pdf CakePdf instance
 */
	public function __construct(CakePdf $Pdf) {
		parent::__construct($Pdf);       
		App::import('Vendor', 'TCPDF', array('file' => 'tecnick.com' . DS . 'tcpdf' . DS . 'tcpdf.php'));
	}

/**
 * Generates Pdf from html
 *
 * @return string raw pdf data
 */
	public function output() {
        $config = Configure::read('CakePdf');
        $margin = &$config['margin'];
        $options = &$config['options'];
        $header = &$options['header'];
		//TCPDF often produces a whole bunch of errors, although there is a pdf created when debug = 0
		//Configure::write('debug', 0);
		$TCPDF = new MyTCPDF($this->_Pdf->orientation(), 'mm', $this->_Pdf->pageSize());
        //debug(K_PATH_IMAGES);
        $fontfile = APP . 'Lib' . DS . 'Fonts' . DS . 'Open_Sans' . DS;
        $customfont = $TCPDF->addTTFfont($fontfile . 'OpenSans-Regular.ttf', 'TrueTypeUnicode', '', 32);
        $TCPDF->addTTFfont($fontfile . 'OpenSans-Bold.ttf', 'TrueTypeUnicode', '', 32);
        $TCPDF->setHeaderData($header['logo'], $header['logo_width'], $header['title'], $header['text']);

        // set header and footer fonts
        $TCPDF->setHeaderFont(Array($customfont, '', PDF_FONT_SIZE_MAIN));
        $TCPDF->setFooterFont(Array($customfont, '', PDF_FONT_SIZE_DATA));
        $TCPDF->Setfont($customfont, '', PDF_FONT_SIZE_MAIN);
        // set default monospaced font
        $TCPDF->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $TCPDF->SetMargins($margin['left'], $margin['top'], $margin['right']);
        $TCPDF->SetHeaderMargin(PDF_MARGIN_HEADER*2);
        $TCPDF->SetFooterMargin(PDF_MARGIN_FOOTER*2);

        // set auto page breaks
        $TCPDF->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $TCPDF->AddPage();
        $html = $this->_Pdf->html();
        $tidy = preg_replace('/\s+\s+/', ' ', $html);
		$TCPDF->writeHTML($tidy);
		return $TCPDF->Output('', 'S');
	}
}

class MyTCPDF extends TCPDF {
   public function Footer()
    {        
        if (!empty($this->title))
        {
            $this->SetY(-15);
            $this->Cell(0, 0, $this->title, 'LTRB', true, 'C', 0, '', 0, false, 'M', 'C');
        }
        parent::Footer();
    }
}
