<?php
class PLib_ImageHandler {
    public static function copy_bounded_image($source_file, $destination_file, $file_ext, $bound_w=0, $bound_h=0, $quality=100, $force_processing=false) {
        list($src_w, $src_h) = GetImageSize($source_file);

        // Changing image dimensions appropriate to bounds
        list($dest_w, $dest_h) = self::inscribe_box($src_w, $src_h, $bound_w, $bound_h);

        // Dimensions not changed
        if(($dest_w==$src_w) && ($dest_h==$src_h) && !$force_processing) {
            return move_uploaded_file($source_file, $destination_file);
        }
        // Dimensions changed
        else {
            if(($file_ext=="jpg") || ($file_ext=="jpeg")) {
                $src_img = ImageCreateFromJPEG($source_file);

                if($src_img=="") {
                    return move_uploaded_file($source_file, $destination_file);
                }
                else {
                    $dest_img = ImageCreateTrueColor($dest_w, $dest_h);
                    imagecopyresampled($dest_img, $src_img, 0, 0, 0, 0, $dest_w, $dest_h, $src_w, $src_h);
                    return ImageJpeg($dest_img, $destination_file, $quality);
                }
            }
            else if(($file_ext=="gif")) {
                $src_img = ImageCreateFromGIF($source_file);
                if($src_img=="") {
                    return move_uploaded_file($source_file, $destination_file);
                }
                else {
                    $dest_img = ImageCreateTrueColor($dest_w, $dest_h);
                    imagecopyresampled($dest_img, $src_img, 0, 0, 0, 0, $dest_w, $dest_h, $src_w, $src_h);
                    return ImagePNG($dest_img, $destination_file, $quality);
                }
            }
            else if(($file_ext=="png")) {
                $src_img = ImageCreateFromPNG($source_file);
                if($src_img=="") {
                    return move_uploaded_file($source_file, $destination_file);
                }
                else {
                    $dest_img = ImageCreateTrueColor($dest_w, $dest_h);
                    imagecopyresampled($dest_img, $src_img, 0, 0, 0, 0, $dest_w, $dest_h, $src_w, $src_h);
                    return ImagePNG($dest_img, $destination_file, $quality);
                }
            }
            else if(($file_ext=="swf")) {
                return move_uploaded_file($source_file, $destination_file);
            }
            else {
                return move_uploaded_file($source_file, $destination_file);
            }
        }
    }

    private static function inscribe_box($src_w, $src_h, $bound_w, $bound_h) {
        $dest_w = $src_w;
        $dest_h = $src_h;

        if(($bound_w != null) && ($bound_h != null)) {
            $overlap_w = $src_w - $bound_w;
            $overlap_h = $src_h - $bound_h;
            if(($overlap_w>0) || ($overlap_h>0)) {
                $src_ratio = $src_w / $src_h;

                $scale_w = $bound_w / $src_w;
                $scale_h = $bound_h / $src_h;
                if($scale_h>$scale_w) {
                    $dest_w = $bound_w;
                    $dest_h = $bound_w / $src_ratio;
                }
                else {
                    $dest_h = $bound_h;
                    $dest_w = $bound_h * $src_ratio;
                }
            }
        }

        $dest_h = round($dest_h);
        $dest_w = round($dest_w);

        return array($dest_w, $dest_h);
    }
}
?>