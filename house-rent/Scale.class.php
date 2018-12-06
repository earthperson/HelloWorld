<?php
class Scale {
    protected $max = 20;
    protected $min = 0.1;
    protected $interval = 0.2;
    
    public function setMax($max) {
        $this->max = abs($max);
    }
    
    public function setMin($min) {
        $this->min = abs($min);
    }
    
    public function setInterval($interval) {
        $this->interval = abs($interval);
    }
    
    public function getNewOffsets($scale) {
        $scale = (float)$scale;
        if($scale <= 0) {
            $plus = 1 + $this->interval;
            $minus = $this->absz(1 - $this->interval);
        }
        elseif(($scale > $this->min) && ($scale <= $this->max - $this->interval)) {
            $plus = $scale + $this->interval;
            $minus = $this->absz($scale - $this->interval);
        }
        elseif($scale <= $this->min){
            $plus = $this->min + $this->interval;
            $minus = $this->min;
        }
        elseif($scale >= $this->max){
            $plus = $this->max;
            $minus = $this->absz($this->max - $this->interval);
        }
        return array('plus' => $plus, 'minus' => $minus);
    }
    
    protected function absz($n) {
        return $n<=0 ? $this->interval : abs($n);
    }
}
?>