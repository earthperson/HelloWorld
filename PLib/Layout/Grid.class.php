<?php
class PLib_Layout_Grid {
	protected $cols;
	protected $n = 0;
	protected $newRow = false;
	protected $rowSpacer;
	protected $colSpacer;
	protected $nCol = 0;
	protected $nRow = 0;
	protected $tableAttributes;
	protected $tdValign;

	public function __construct($cols, $colSpacer = 0, $rowSpacer = 0, $tdValign = 'top') {
		$this->cols = $cols;
		$this->tdValign = $tdValign;
		if ($rowSpacer==0) {
			$rowSpacer=$colSpacer;
		}
		$this->rowSpacer = $rowSpacer;
		$this->colSpacer = $colSpacer;
		$this->tableAttributes=" border='0' cellpadding='0' cellspacing='0' width='100%'";
	}

	public function setTableAttribute($tableAttributesNew=" border='0' cellpadding='0' cellspacing='0' width='100%'") {
		$this->tableAttributes = $tableAttributesNew;
	}

	public function newCell() {
		if ($this->n != 0) {
			$this->cellEnd();
		}
		else {
			print "<table ".$this->tableAttributes."> \n";
		}
		if ($this->colSpacer) {
			if (($this->n % $this->cols) != 0) {
				$this->spacerColl();
			}
		}
		$this->cellBegin();
		$this->n ++;
	}

	public function end() {
		if ($this->n > 0) {
			$this->cellEnd();
			if ($this->newRow) {
				print "</tr> \n";
			}
			print "</table> \n";
		}
	}

	protected function cellBegin() {
		if (($this->n % $this->cols == 0)&&($this->n!=0)) {
			print "</tr> \n";
			$nCol=0;
			$this->newRow = false;
			if ($this->rowSpacer) {
				$this->spacerRow();
			}
		}
		if($this->n % $this->cols == 0) {
			$this->nRow++;
			$this->nCol = 0;
			$this->nRow%2==0 ? $even="evenTr" : $even="oddTr";
			print "<tr class='".$even."'> \n";
			$this->newRow = true;
		}
		$this->nCol++;
		$this->nCol%2==0 ? $even="evenTd" : $even="oddTd";
		print "  <td valign='{$this->tdValign}' width='".ceil(100/$this->cols)."%' class='".$even."'>";
	}

	protected function cellEnd() {
		print "</td> \n";
	}

	protected function spacerColl() {
		print "  <td><img src='/images/i.gif' width='".$this->colSpacer."' height='1'></td> \n";
	}

	protected function spacerRow() {
		if ($this->colSpacer) {
			$count=$this->cols*2-1;
		}
		else {
			$count=$this->cols;
		}
		print "<tr> \n  <td colspan='".$count."'><img src='/images/i.gif' width='1' height='".$this->rowSpacer."'></td> \n</tr> \n";
	}
}
?>