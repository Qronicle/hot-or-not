<?php
/**
 * SubjectController.php
 */

namespace App\Http\Controllers\HotOrNot;

use App\Http\Controllers\Controller;
use App\Subject;
use Illuminate\Http\Request;

/**
 * SubjectController
 *
 * @since       2016-10-17 18:59
 * @author      ruud.seberechts
 */
class SubjectController extends Controller
{
    public function add()
    {
        return view('public.add-user');
    }

    public function post(Request $request)
    {
        // Filter input
        $request->merge([
            'name' => trim(strip_tags(($request->get('name')))),
        ]);
        // Validate input
        $this->validate($request, [
            'name'        => 'required|max:24',
            'image'       => 'required|mimes:png,jpeg,jpg,gif|max:1024'
        ]);
        $subject = new Subject([
            'name' => $request->get('name'),
        ]);
        $subject->save();
        $target = base_path() . '/public/img/subjects/' . $subject->id . '.jpg';
        $request->file('image')->move(base_path() . '/public/img/subjects', $subject->id . '.jpg');
        $this->resizeImage($target, $target, 500, 500);

        if (file_exists($target)) {
            return redirect('/add-user')->with('message', 'User added!');
        }
    }

    /*
     * Crop-to-fit PHP-GD
     * http://salman-w.blogspot.com/2009/04/crop-to-fit-image-using-aspphp.html
     *
     * Resize and center crop an arbitrary size image to fixed width and height
     * e.g. convert a large portrait/landscape image to a small square thumbnail
     */
    protected function resizeImage($src, $target, $width, $height)
    {        
        list($source_width, $source_height, $source_type) = getimagesize($src);
        
        switch ($source_type) {
            case IMAGETYPE_GIF:
                $source_gdim = imagecreatefromgif($src);
                break;
            case IMAGETYPE_JPEG:
                $source_gdim = imagecreatefromjpeg($src);
                break;
            case IMAGETYPE_PNG:
                $source_gdim = imagecreatefrompng($src);
                break;
        }
        
        $source_aspect_ratio = $source_width / $source_height;
        $desired_aspect_ratio = $width / $height;
        
        if ($source_aspect_ratio > $desired_aspect_ratio) {
            /*
             * Triggered when source image is wider
             */
            $temp_height = $height;
            $temp_width = ( int ) ($height * $source_aspect_ratio);
        } else {
            /*
             * Triggered otherwise (i.e. source image is similar or taller)
             */
            $temp_width = $width;
            $temp_height = ( int ) ($width / $source_aspect_ratio);
        }
        
        /*
         * Resize the image into a temporary GD image
         */
        
        $temp_gdim = imagecreatetruecolor($temp_width, $temp_height);
        imagecopyresampled(
            $temp_gdim,
            $source_gdim,
            0, 0,
            0, 0,
            $temp_width, $temp_height,
            $source_width, $source_height
        );
        
        /*
         * Copy cropped region from temporary image into the desired GD image
         */
        
        $x0 = ($temp_width - $width) / 2;
        $y0 = ($temp_height - $height) / 2;
        $desired_gdim = imagecreatetruecolor($width, $height);
        imagecopy(
            $desired_gdim,
            $temp_gdim,
            0, 0,
            $x0, $y0,
            $width, $height
        );
        
        /*
         * Render the image
         * Alternatively, you can save the image in file-system or database
         */
        imagejpeg($desired_gdim, $target, 80);
    }
}