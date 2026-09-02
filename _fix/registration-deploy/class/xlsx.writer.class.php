<?php
/**
 * Жинхэнэ .xlsx файл үүсгэгч — гуравдагч сан, composer, ZipArchive шаардахгүй.
 *
 * ZIP архивыг гараар (local file header + central directory) бүтээдэг тул
 * зөвхөн zlib (gzdeflate) байхад хангалттай; тэр ч байхгүй бол шахалтгүйгээр
 * (STORE) бичнэ. Кирилл үсгийг UTF-8-аар шууд хадгалдаг тул Excel дээр
 * нээхэд ямар ч тохиргоо хэрэггүй.
 *
 * Хэрэглээ:
 *   $xlsx = new XlsxWriter("Бүртгэл");
 *   $xlsx->setColumns(array(6, 28, 18, 30));
 *   $xlsx->addHeader(array("№", "Нэр", "Утас", "И-мэйл"));
 *   $xlsx->addRow(array(1, "Болд", "99112233", "bold@mail.mn"));
 *   $xlsx->download("burtgel.xlsx");
 */

class XlsxWriter
{
	private $sheetName = "Sheet1";
	private $rows = array();
	private $headerRow = -1;
	private $colWidths = array();

	public function __construct($sheetName = "Sheet1")
	{
		$this->setSheetName($sheetName);
	}

	public function setSheetName($name)
	{
		/* Excel-ийн хориотой тэмдэгтүүд, 31 тэмдэгтийн хязгаар */
		$name = str_replace(array("\\", "/", "*", "[", "]", ":", "?"), " ", (string)$name);
		$name = trim($name);
		if ($name === "") {
			$name = "Sheet1";
		}
		if (function_exists("mb_substr")) {
			$name = mb_substr($name, 0, 31, "UTF-8");
		}

		$this->sheetName = $name;
	}

	/** Баганын өргөн (тэмдэгтээр). array(6, 28, 18, ...) */
	public function setColumns($widths)
	{
		$this->colWidths = is_array($widths) ? $widths : array();
	}

	public function addHeader($cells)
	{
		$this->headerRow = count($this->rows);
		$this->rows[] = $cells;
	}

	public function addRow($cells)
	{
		$this->rows[] = $cells;
	}

	public function rowCount()
	{
		return count($this->rows);
	}

	/* ------------------------------------------------------------------
	   Гаралт
	   ------------------------------------------------------------------ */

	public function download($fileName = "export.xlsx")
	{
		$body = $this->build();

		/* Өмнөх ямар нэг гаралт байвал цэвэрлэнэ — эс бөгөөс файл эвдэрнэ */
		while (ob_get_level() > 0) {
			ob_end_clean();
		}

		$ascii = preg_replace('/[^A-Za-z0-9._-]/', "_", $fileName);

		header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
		header("Content-Disposition: attachment; filename=\"" . $ascii . "\"; filename*=UTF-8''" . rawurlencode($fileName));
		header("Content-Length: " . strlen($body));
		header("Content-Transfer-Encoding: binary");
		header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
		header("Pragma: no-cache");
		header("Expires: 0");

		echo $body;
	}

	/** Бэлэн .xlsx-ийн binary агуулга */
	public function build()
	{
		$files = array(
			"[Content_Types].xml"      => $this->contentTypesXml(),
			"_rels/.rels"              => $this->relsXml(),
			"xl/workbook.xml"          => $this->workbookXml(),
			"xl/_rels/workbook.xml.rels" => $this->workbookRelsXml(),
			"xl/styles.xml"            => $this->stylesXml(),
			"xl/worksheets/sheet1.xml" => $this->sheetXml()
		);

		return $this->zip($files);
	}

	/* ------------------------------------------------------------------
	   XML хэсгүүд
	   ------------------------------------------------------------------ */

	private function contentTypesXml()
	{
		return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
			. '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
			. '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
			. '<Default Extension="xml" ContentType="application/xml"/>'
			. '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
			. '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
			. '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
			. '</Types>';
	}

	private function relsXml()
	{
		return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
			. '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
			. '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
			. '</Relationships>';
	}

	private function workbookXml()
	{
		return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
			. '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"'
			. ' xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
			. '<sheets><sheet name="' . $this->esc($this->sheetName) . '" sheetId="1" r:id="rId1"/></sheets>'
			. '</workbook>';
	}

	private function workbookRelsXml()
	{
		return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
			. '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
			. '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
			. '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
			. '</Relationships>';
	}

	/**
	 * cellXfs: 0 = энгийн текст, 1 = толгойн мөр (bold, дэвсгэртэй)
	 * Бүх нүд текст форматтай (numFmtId 49) — утасны дугаарын эхний 0 арилахгүй.
	 */
	private function stylesXml()
	{
		return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
			. '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
			. '<fonts count="2">'
			. '<font><sz val="11"/><name val="Calibri"/></font>'
			. '<font><b/><sz val="11"/><color rgb="FFFFFFFF"/><name val="Calibri"/></font>'
			. '</fonts>'
			. '<fills count="3">'
			. '<fill><patternFill patternType="none"/></fill>'
			. '<fill><patternFill patternType="gray125"/></fill>'
			. '<fill><patternFill patternType="solid"><fgColor rgb="FF1F1F1F"/><bgColor indexed="64"/></patternFill></fill>'
			. '</fills>'
			. '<borders count="2">'
			. '<border><left/><right/><top/><bottom/><diagonal/></border>'
			. '<border><left style="thin"><color rgb="FFD9D9D9"/></left><right style="thin"><color rgb="FFD9D9D9"/></right>'
			. '<top style="thin"><color rgb="FFD9D9D9"/></top><bottom style="thin"><color rgb="FFD9D9D9"/></bottom><diagonal/></border>'
			. '</borders>'
			. '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
			. '<cellXfs count="2">'
			. '<xf numFmtId="49" fontId="0" fillId="0" borderId="1" applyNumberFormat="1" applyBorder="1"><alignment vertical="center" wrapText="1"/></xf>'
			. '<xf numFmtId="49" fontId="1" fillId="2" borderId="1" applyNumberFormat="1" applyFont="1" applyFill="1" applyBorder="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>'
			. '</cellXfs>'
			. '<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
			. '</styleSheet>';
	}

	private function sheetXml()
	{
		$colCount = 0;
		foreach ($this->rows as $cells) {
			$colCount = max($colCount, count($cells));
		}
		$colCount = max($colCount, count($this->colWidths));
		if ($colCount < 1) {
			$colCount = 1;
		}

		$rowCount = max(1, count($this->rows));
		$dimension = "A1:" . self::colName($colCount - 1) . $rowCount;

		$xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
			. '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
			. '<dimension ref="' . $dimension . '"/>';

		/* Толгойн мөр байвал царцаана */
		if ($this->headerRow === 0) {
			$xml .= '<sheetViews><sheetView workbookViewId="0">'
				. '<pane ySplit="1" topLeftCell="A2" activePane="bottomLeft" state="frozen"/>'
				. '</sheetView></sheetViews>';
		}

		$xml .= '<sheetFormatPr defaultRowHeight="18"/>';

		if (count($this->colWidths) > 0) {
			$xml .= '<cols>';
			foreach ($this->colWidths as $i => $w) {
				$w = (float)$w;
				if ($w <= 0) {
					$w = 16;
				}
				$xml .= '<col min="' . ($i + 1) . '" max="' . ($i + 1) . '" width="' . $w . '" customWidth="1"/>';
			}
			$xml .= '</cols>';
		}

		$xml .= '<sheetData>';

		foreach ($this->rows as $r => $cells) {
			$isHeader = ($r === $this->headerRow);
			$style = $isHeader ? 1 : 0;
			$rowNum = $r + 1;

			$xml .= '<row r="' . $rowNum . '"' . ($isHeader ? ' ht="26" customHeight="1"' : '') . '>';

			$c = 0;
			foreach ($cells as $cell) {
				$ref = self::colName($c) . $rowNum;
				$val = $this->flatten($cell);

				if ($val === "") {
					$xml .= '<c r="' . $ref . '" s="' . $style . '" t="inlineStr"/>';
				} else {
					$xml .= '<c r="' . $ref . '" s="' . $style . '" t="inlineStr"><is><t xml:space="preserve">'
						. $this->esc($val) . '</t></is></c>';
				}

				$c++;
			}

			$xml .= '</row>';
		}

		$xml .= '</sheetData>';

		/* Шүүлтүүр — толгойн мөртэй үед */
		if ($this->headerRow === 0 && $rowCount > 1) {
			$xml .= '<autoFilter ref="A1:' . self::colName($colCount - 1) . $rowCount . '"/>';
		}

		$xml .= '</worksheet>';

		return $xml;
	}

	/* ------------------------------------------------------------------
	   Туслах
	   ------------------------------------------------------------------ */

	private function flatten($cell)
	{
		if (is_array($cell)) {
			$cell = implode(", ", $cell);
		}
		if (is_bool($cell)) {
			$cell = $cell ? "1" : "";
		}
		if ($cell === null) {
			$cell = "";
		}

		return (string)$cell;
	}

	/** XML-д зөвшөөрөгдөхгүй хяналтын тэмдэгтүүдийг цэвэрлэж escape хийнэ */
	private function esc($str)
	{
		$str = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/u', "", (string)$str);
		if ($str === null) {
			$str = "";
		}

		return htmlspecialchars($str, ENT_QUOTES | ENT_XML1, "UTF-8");
	}

	/** 0 -> A, 25 -> Z, 26 -> AA */
	public static function colName($index)
	{
		$index = (int)$index;
		$name = "";

		while ($index >= 0) {
			$name = chr(65 + ($index % 26)) . $name;
			$index = intval($index / 26) - 1;
		}

		return $name;
	}

	/* ------------------------------------------------------------------
	   ZIP (ZipArchive-гүйгээр)
	   ------------------------------------------------------------------ */

	private function zip($files)
	{
		$local = "";
		$central = "";
		$offset = 0;
		$count = 0;

		list($dosTime, $dosDate) = self::dosStamp();

		foreach ($files as $name => $data) {
			$crc = crc32($data);
			$uncompressed = strlen($data);

			$compressed = false;
			if (function_exists("gzdeflate")) {
				$compressed = @gzdeflate($data, 6);
			}

			if ($compressed !== false && strlen($compressed) < $uncompressed) {
				$method = 8;
				$payload = $compressed;
			} else {
				$method = 0;
				$payload = $data;
			}

			$compressedSize = strlen($payload);
			$nameLen = strlen($name);

			/* Local file header */
			$header = "PK\x03\x04"
				. pack("v", 20)            /* version needed */
				. pack("v", 0)             /* flags */
				. pack("v", $method)
				. pack("v", $dosTime)
				. pack("v", $dosDate)
				. pack("V", $crc)
				. pack("V", $compressedSize)
				. pack("V", $uncompressed)
				. pack("v", $nameLen)
				. pack("v", 0);            /* extra len */

			$local .= $header . $name . $payload;

			/* Central directory entry */
			$central .= "PK\x01\x02"
				. pack("v", 20)            /* version made by */
				. pack("v", 20)            /* version needed */
				. pack("v", 0)
				. pack("v", $method)
				. pack("v", $dosTime)
				. pack("v", $dosDate)
				. pack("V", $crc)
				. pack("V", $compressedSize)
				. pack("V", $uncompressed)
				. pack("v", $nameLen)
				. pack("v", 0)             /* extra */
				. pack("v", 0)             /* comment */
				. pack("v", 0)             /* disk */
				. pack("v", 0)             /* internal attrs */
				. pack("V", 32)            /* external attrs */
				. pack("V", $offset)
				. $name;

			$offset += strlen($header) + $nameLen + $compressedSize;
			$count++;
		}

		$eocd = "PK\x05\x06"
			. pack("v", 0)
			. pack("v", 0)
			. pack("v", $count)
			. pack("v", $count)
			. pack("V", strlen($central))
			. pack("V", $offset)
			. pack("v", 0);

		return $local . $central . $eocd;
	}

	private static function dosStamp()
	{
		$t = getdate();

		$year = $t["year"] < 1980 ? 1980 : $t["year"];

		$dosTime = ($t["hours"] << 11) | ($t["minutes"] << 5) | (intval($t["seconds"] / 2));
		$dosDate = (($year - 1980) << 9) | ($t["mon"] << 5) | $t["mday"];

		return array($dosTime, $dosDate);
	}
}
