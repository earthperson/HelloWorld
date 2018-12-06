avatarDo() ‘ункци€ дл€ создани€ аватора (например при регистрации)
<?php
/**
 * ‘ункци€ предназначена дл€ создани€ аватора (например при регистрации)
 * Ѕлок 1 предназначен дл€ пропорционального изменени€ размеров создаваемого аватора, по
 * отношению к исходному изображению в соответствии с отношением его(изображени€) ширины и высоты.
 * ‘ункци€ провер€ет тип переданного изображени€, поддерживаютс€ GIF, JPEG, PNG форматы,
 * но также можно добавить и другие.
 * $up_dir - определ€ет директорию дл€ загрузки аватора
 * $name - им€ создаваемого файла (без расширени€)
 * $up_file - им€ пол€ формы типа file
 * $w, $h - ширина и высота соответственно создаваемого аватора
 * ¬озвращает им€ созданного файла в случае удачи (с правильным расширением, даже если файл имел
 * расширение, например *.txt, но €вл€лс€ корректным изображением), false в противном случае, например при
 * попытке загрузки текстового документа, или не POST методом.
 *
 * @param string $up_dir
 * @param string $up_file
 * @param int $w
 * @param int $h
 * @param string $name
 * @return mixed
 * @copyright http://dmitry-ponomarev.ru
 */
function avatarDo($up_dir, $name, $up_file, $w, $h) {
	if(is_dir($up_dir)) {
		$up_ext = pathinfo($_FILES[$up_file]['name'], PATHINFO_EXTENSION);
		$path = $up_dir . $name . '.' . $up_ext;
		if($_FILES[$up_file]['error'] == 0 && $_FILES[$up_file]['size'] > 0) {
			if(@move_uploaded_file($_FILES[$up_file]['tmp_name'], $path)) {
				$info = getimagesize($path);
				// 1
				if(($info[0] > $w) || ($info[1] > $h)) {
					if ($info[0] > $info[1]) {
						$h = floor(($info[1] / $info[0]) * $h);
					}
					else if ($info[0] < $info[1]) {
						$w = floor(($info[0] / $info[1]) * $w);
					}
				}
				else {
					$w = $info[0]; $h = $info[1];
				}
				// END 1
				switch($info[2]) {
					case IMAGETYPE_GIF: $im = @imagecreatefromgif($path); // собака т.к. ошибка обрабатываетс€
					break;
					case IMAGETYPE_JPEG: $im = @imagecreatefromjpeg($path);
					break;
					case IMAGETYPE_PNG: $im = @imagecreatefrompng($path);
					break;
					default: $im = false;
					break;
				}
				if($im) {
					$dest = imagecreatetruecolor($w, $h);
					$backgr = imagecolorallocate($dest, 0x00, 0x00, 0x00);
					imagefill($dest, 0, 0, $backgr);
					imagecopyresampled($dest, $im, 0, 0, 0, 0, $w, $h, imagesx($im), imagesy($im));
					if(unlink($path)) {
						imageinterlace($dest, 1);
						switch($info[2]) {
							case IMAGETYPE_GIF:
								imagegif($dest, $up_dir . $name . '.gif');
								imagedestroy($dest);
								return $name . '.gif';
								break;
							case IMAGETYPE_JPEG:
								imagejpeg($dest, $up_dir . $name . '.jpeg');
								imagedestroy($dest);
								return $name . '.jpeg';
								break;
							case IMAGETYPE_PNG:
								imagepng($dest, $up_dir . $name . '.png');
								imagedestroy($dest);
								return $name . '.png';
								break;
						}
					}
					else {
						return false;
					}
				}
				else {
					return false;
				}
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
	}
	else {
		return false;
	}
}
?>