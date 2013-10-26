<?php
App::uses('AbstractPdfEngine', 'CakePdf.Pdf/Engine');
App::uses('Multibyte', 'I18n');

class TcpdfEngine extends AbstractPdfEngine {

/**
 * Constructor
 *
 * @param $Pdf CakePdf instance
 */
	public function __construct(CakePdf $Pdf) {
		parent::__construct($Pdf);
        define('K_PATH_IMAGES', WWW_ROOT . 'img' . DS);
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
		$TCPDF = new TCPDF($this->_Pdf->orientation(), 'mm', $this->_Pdf->pageSize());
        $pdf = &$TCPDF;
        $TCPDF->setHeaderData($header['logo'], $header['logo_width'], $header['title'], $header['text']);
        //$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 063', PDF_HEADER_STRING);

        // set header and footer fonts
        $TCPDF->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $TCPDF->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $TCPDF->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $TCPDF->SetMargins($margin['left'], $margin['top'], $margin['right']);
        $TCPDF->SetHeaderMargin(PDF_MARGIN_HEADER);
        $TCPDF->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $TCPDF->AddPage();
        $html = $this->_Pdf->html();
        $tidy = preg_replace('/\s+\s+/', ' ', $html);
		$TCPDF->writeHTML($tidy);
		return $TCPDF->Output('', 'S');
	}
}