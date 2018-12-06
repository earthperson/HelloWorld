<?php
class Schedule {
    protected $headersHeight = 20;          // Высота шапки
    protected $scheduleViewWidth = 600;     // Ширина графика
    protected $legendViewWidth = 300;       // Ширина легенды
    protected $scheduleLineHeight = 8;      // Высота линии графика
    protected $scheduleLineIndent = 12;     // Вертикальный отступ между линиями графика
    protected $indents = 2;                 // Отступы
    protected $leftBoundary = '1970-01-01';
    protected $scale = 1;
    
    public function __construct() {
        
    }
    
    public function setScale($scale=1) {
        $scale = (float)$scale;
        if($scale <= 0) {$scale = 1;}
        if($scale > 20) {$scale = 20;}
        $this->scale = $scale;
    }
    
    public function setLeftBound($date) {
        // Will need to make checkdate
        $this->leftBoundary = $date;
    }
    
    protected function getMonthName($ts) {
        if($this->scale > 2) {
            return date('F', $ts);
        }
        elseif($this->scale >= 1 && $this->scale <= 2) {
            return date('M', $ts);
        }
        elseif($this->scale < 1) {
            static $cnt = 0; $cnt++;
            if($cnt%2!=0) {return date('M', $ts);}
        }
    }
    
    final public function render() {
        $house_part_separator = ' / ';
        $res = mysql_query("SELECT COUNT(*) FROM house INNER JOIN part USING(house_id)");
        $rec = mysql_fetch_row($res);
        $H = $rec[0]*($this->scheduleLineHeight+$this->scheduleLineIndent)+$this->indents*3+$this->headersHeight+$this->scheduleLineIndent; // Высота полотна
        $W = $this->scheduleViewWidth+$this->indents*3+$this->legendViewWidth; // Ширина полотна

        // Рисуем полотно       
        print "<div class='canvas' style='width:{$W}px; height:{$H}px;'>";
        
        // Рисуем прямоугольник легенды
        print "<div style='position: absolute;'><div class='legend' style='width:{$this->legendViewWidth}px; height:" . ($H-$this->indents*3-$this->headersHeight) . "px; top:" . ($this->indents*2+$this->headersHeight) . "px; left:{$this->indents}px;'></div></div>";
        
        // Рисуем прямоугольник графика
        print "<div style='position: absolute;'><div class='schedule' style='width:{$this->scheduleViewWidth}px; height:" . ($H-$this->indents*3-$this->headersHeight) . "px; top:" . ($this->indents*2+$this->headersHeight) . "px; left:" . ($this->indents*2+$this->legendViewWidth) . "px;'></div></div>";
        
        // Рисуем прямоугольник заголовка легенды
        print "<div style='position: absolute;'><div class='legend-header' style='width:{$this->legendViewWidth}px; height:{$this->headersHeight}px; top:{$this->indents}px; left:{$this->indents}px;'></div></div>";
        
        // Рисуем заголовки в прямоугольнике заголовка легенды
        print "<div style='position: absolute;'><div class='name' style='width:{$this->legendViewWidth}px; height: 12px; top:" . ($this->headersHeight+$this->indents-12) . "px; left:{$this->indents}px; color: white; text-align: center;'>House {$house_part_separator} Part</div></div>";
        
        // Рисуем прямоугольник заголовка графика
        print "<div style='position: absolute;'><div class='schedule-header' style='width:{$this->scheduleViewWidth}px; height:{$this->headersHeight}px; top:{$this->indents}px; left:" . ($this->indents*2+$this->legendViewWidth) . "px;'></div></div>";
        
        // Рисовать названия месяцов и вертикальные линии будем в рамке, скрывающей выступающие части
        print "<div style='position: absolute;'><div class='schedule-frame' style='width:{$this->scheduleViewWidth}px; height:" . ($H-$this->indents*2) . "px; top:{$this->indents}px; left:" . ($this->indents*2+$this->legendViewWidth) . "px;'>";
        for(
            $ts=strtotime($this->leftBoundary.' 01:00:00'), $i=0;
            $i<ceil($this->scheduleViewWidth/$this->scale);
            $ts+=date('t',$ts)*24*3600, $i+=date('t',$ts)) {
                // Рисуем названия месяцов в прямоугольнике заголовок графика
                print "<div style='position: absolute;'><div class='name' style='height:12px; top:" . ($this->headersHeight-12) . "px; left:" . ceil($i*$this->scale) . "px;'>" . $this->getMonthName($ts) . "</div></div>";
                // Рисуем вертикальные линии месяца в прямоугольнике графика
                print "<div style='position: absolute;'><div class='month-line' style='height:" . ($H-$this->indents*3-$this->headersHeight) . "px; top:" . ($this->indents+$this->headersHeight) . "px; left:" . ceil($i*$this->scale) . "px;'></div></div>";
        }
        print '</div></div>';
        
        // Формируем список домов-квартир
        $res = mysql_query("SELECT house.*, part.* FROM house INNER JOIN part USING(house_id) ORDER BY address, code");
        if(mysql_num_rows($res)) {
            $y = 0;
            $i = 0;
            // В это массиве будет храниться соответсвие между id квартиры
            // и ее физическим порядковым номером вывода в прямоугольнике легенды
            $house_part_list = array();
            while ($rec = mysql_fetch_assoc($res)) {
            	print "<div style='position: absolute;'><div class='legend-item' style='width:" . ($this->legendViewWidth-2*$this->indents) . "px; height:{$this->scheduleLineHeight}px; top:" . ($this->scheduleLineIndent+$this->indents*2+$this->headersHeight+$y) . "px; left:" . ($this->indents*2) . "px;'>" . htmlspecialchars($rec['address']) . htmlspecialchars($house_part_separator) . htmlspecialchars($rec['code']) . "</div></div>";
            	$y+=($this->scheduleLineHeight+$this->scheduleLineIndent); // Увеличиваем вертикальную координату
            	$house_part_list[$rec['part_id']] = $i++;
            }
        }

        // Формируем список договоров
        $res = mysql_query("
            SELECT
                arrival, departure,
                part_id, 
                CEILING(DATEDIFF(arrival, '{$this->leftBoundary}') * {$this->scale}) AS x,
                CEILING(IFNULL(DATEDIFF(departure, arrival), " . ($this->scheduleViewWidth*2) . ") * {$this->scale}) AS w
            FROM
                renter
            WHERE
                IFNULL(departure >= '{$this->leftBoundary}',1) AND arrival <= '{$this->leftBoundary}' + INTERVAL CEILING({$this->scheduleViewWidth} / {$this->scale}) DAY
            ORDER BY
                arrival, departure
        ");
        if(mysql_num_rows($res)) {
            // Рисовать график будем в рамке, скрывающей выступающие части
            print "<div style='position: absolute;'><div class='schedule-frame' style='width:{$this->scheduleViewWidth}px; height:" . ($H-$this->indents*3-$this->headersHeight) . "px; top:" . ($this->indents*2+$this->headersHeight) . "px; left:" . ($this->indents*2+$this->legendViewWidth) . "px;'>";
            while ($rec = mysql_fetch_assoc($res)) {
               // Собственно график
               print "<div style='position: absolute;'><div class='schedule-item' style='width:{$rec['w']}px; height:{$this->scheduleLineHeight}px; top:" . ($house_part_list[$rec['part_id']]*($this->scheduleLineIndent+$this->scheduleLineHeight)) . "px; left:{$rec['x']}px; margin: {$this->scheduleLineIndent}px 0px; color: white;' title='{$rec['arrival']} - " . ((int)$rec['departure'] > 0 ? $rec['departure'] : '...') . "'></div></div>";
            }
            print '</div></div>';
        }
        
        // Закрываем полотно
        print "</div>";
    }       
}
?>