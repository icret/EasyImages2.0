<?php

/**
 *  Methods used with the {@link resize()} method.
 */

define('ZEBRA_IMAGE_BOXED', 0);
define('ZEBRA_IMAGE_NOT_BOXED', 1);
define('ZEBRA_IMAGE_CROP_TOPLEFT', 2);
define('ZEBRA_IMAGE_CROP_TOPCENTER', 3);
define('ZEBRA_IMAGE_CROP_TOPRIGHT', 4);
define('ZEBRA_IMAGE_CROP_MIDDLELEFT', 5);
define('ZEBRA_IMAGE_CROP_CENTER', 6);
define('ZEBRA_IMAGE_CROP_MIDDLERIGHT', 7);
define('ZEBRA_IMAGE_CROP_BOTTOMLEFT', 8);
define('ZEBRA_IMAGE_CROP_BOTTOMCENTER', 9);
define('ZEBRA_IMAGE_CROP_BOTTOMRIGHT', 10);

// this enables handling of partially broken JPEG files without warnings/errors
ini_set('gd.jpeg_ignore_warning', '1');

/**
 *  A single-file, lightweight PHP library designed for efficient image manipulation featuring methods for modifying
 *  images and applying filters Supports WEBP format.
 *
 *  Read more {@link https://github.com/stefangabos/Zebra_Image/ here}
 *
 *  @author     Stefan Gabos <contact@stefangabos.ro>
 *  @version    2.8.2 (last revision: January 25, 2023)
 *  @copyright  © 2006 - 2023 Stefan Gabos
 *  @license    https://www.gnu.org/licenses/lgpl-3.0.txt GNU LESSER GENERAL PUBLIC LICENSE
 *  @package    Zebra_Image
 */
class Zebra_Image {

    /**
     *  If set to `true`, JPEG images will be auto-rotated according to the {@link http://keyj.emphy.de/exif-orientation-rant/ Exif Orientation Tag}
     *  so that they are always shown correctly.
     *
     *  >   If set to `true` you must also enable exif-support with `--enable-exif`.<br>
     *      Windows users must enable both the `php_mbstring.dll` and `php_exif.dll` DLL's in `php.ini`.<br>
     *      `php_mbstring.dll` must be loaded before `php_exif.dll`, so adjust your php.ini accordingly.
     *      See {@link https://www.php.net/manual/en/exif.installation.php the manual}.
     *
     *  Default is `false`
     *
     *  @since 2.2.4
     *
     *  @var boolean
     */
    public $auto_handle_exif_orientation;

    /**
     *  Indicates whether BMP images should be compressed with run-length encoding (RLE), or not.
     *
     *  >   Used only if PHP version is `7.2.0+` and the file at {@link target_path} is a `BMP` image, or it will be
     *      ignored otherwise.
     *
     *  Default is `TRUE`
     *
     *  @since 2.8.1
     *
     *  @var boolean
     */
    public $bmp_compressed;

    /**
     *  Indicates the file system permissions to be set for newly created images.
     *
     *  Better is to leave this setting as it is.
     *
     *  If you know what you are doing, here is how you can calculate the permission levels:
     *
     *  - 400 Owner Read
     *  - 200 Owner Write
     *  - 100 Owner Execute
     *  - 40 Group Read
     *  - 20 Group Write
     *  - 10 Group Execute
     *  - 4 Global Read
     *  - 2 Global Write
     *  - 1 Global Execute
     *
     *  Default is `0755`
     *
     *  @var integer
     */
    public $chmod_value;

    /**
     *  If set to `false`, images having both width and height smaller than the required width and height will be left
     *  untouched. {@link jpeg_quality} and {@link png_compression} will still apply!
     *
     *  Available only for the {@link resize()} method.
     *
     *  Default is `true`
     *
     *  @var boolean
     */
    public $enlarge_smaller_images;

    /**
     *  In case of an error read this property's value to see the error's code.
     *
     *  Possible error codes are:
     *
     *  - `1` - source file could not be found
     *  - `2` - source file is not readable
     *  - `3` - could not write target file
     *  - `4` - unsupported source file type *(note that you will also get this for animated WEBP images!)*
     *  - `5` - unsupported target file type
     *  - `6` - GD library version does not support target file format
     *  - `7` - GD library is not installed!
     *  - `8` - "chmod" command is disabled via configuration
     *  - `9` - "exif_read_data" function is not available
     *
     *  Default is `0` (no error)
     *
     *  @var integer
     */
    public $error;

    /**
     *  Indicates whether the created image should be saved as a progressive JPEG.
     *
     *  >   Used only if the file at {@link target_path} is a `JPG/JPEG` image, or will be ignored otherwise.
     *
     *  Default is `false`
     *
     *  @since 2.5.0
     *
     *  @var boolean
     */
    public $jpeg_interlace;

    /**
     *  Indicates the quality of the output image (better quality means bigger file size).
     *
     *  >   Used only if the file at {@link target_path} is a `JPG/JPEG` image, or it will be ignored otherwise.
     *
     *  Range is `0` - `100`.
     *
     *  Default is `85`
     *
     *  @var integer
     */
    public $jpeg_quality;

    /**
     *  Indicates the compression level of the output image (lower compression means bigger file size).
     *
     *  >   Used only if PHP version is `5.1.2+` and the file at {@link target_path} is a `PNG` image, or it will be
     *      ignored otherwise.
     *
     *  Range is `0` - `9`.
     *
     *  Default is `9`
     *
     *  @since 2.2
     *
     *  @var integer
     */
    public $png_compression;

    /**
     *  Specifies whether upon resizing images should preserve their aspect ratio.
     *
     *  >   Available only for the {@link resize()} method.
     *
     *  Default is `true`
     *
     *  @var boolean
     */
    public $preserve_aspect_ratio;

    /**
     *  Indicates whether a target file should preserve the source file's date/time.
     *
     *  Default is `true`
     *
     *  @since 1.0.4
     *
     *  @var boolean
     */
    public $preserve_time;

    /**
     *  Indicates whether the target image should have a `sharpen` filter applied to it.
     *
     *  Can be very useful when creating thumbnails and should be used only when creating thumbnails.
     *
     *  >   The sharpen filter relies on the {@link https://www.php.net/manual/en/function.imageconvolution.php imageconvolution}
     *      function which is available for PHP version `5.1.0+`, and will leave the images unaltered for older versions!
     *
     *  Default is `false`
     *
     *  @since 2.2
     *
     *  @var boolean
     */
    public $sharpen_images;

    /**
     *  Path to an image file to apply the transformations to.
     *
     *  Supported file types are `BMP`, `GIF`, `JPEG`, `PNG` and `WEBP`.
     *
     *  >   `WEBP` support is available for PHP version `7.0.0+`.<br><br>
     *      Note that even though `WEBP` support was added to PHP in version `5.5.0`, it only started working with version
     *      `5.5.1`, while support for transparency was added with version `7.0.0`. As a result, I decided to make it
     *      available only if PHP version is at least `7.0.0`<br><br>
     *      Animated `WEBP` images are not currently supported by GD.
     *      See {@link https://github.com/libgd/libgd/issues/648 here} and {@link https://bugs.php.net/bug.php?id=79809&thanks=4 here}.
     *
     *  >   `BMP` support is available for PHP version `7.2.0+`
     *
     *  @var    string
     */
    public $source_path;

    /**
     *  Path (including file name) to where to save the transformed image.
     *
     *  >   Can be a different format than the file at {@link source_path}. The format of the transformed image will be
     *      determined by the file's extension. Supported file types are `BMP`, `GIF`, `JPEG`, `PNG` and `WEBP`.
     *
     *  >   `WEBP` support is available for PHP version `7.0.0+`.<br><br>
     *      Note that even though `WEBP` support was added to PHP in version `5.5.0`, it only started working with version
     *      `5.5.1`, while support for transparency was added with version `7.0.0`. As a result, I decided to make it
     *      available only if PHP version is at least `7.0.0`<br><br>
     *      Animated `WEBP` images are not currently supported by GD.
     *      See {@link https://github.com/libgd/libgd/issues/648 here} and {@link https://bugs.php.net/bug.php?id=79809&thanks=4 here}.
     *
     *  >   `BMP` support is available for PHP version `7.2.0+`
     *
     *  @var    string
     */
    public $target_path;

    /**
     *  Indicates the quality level of the output image.
     *
     *  >   Used only if PHP version is `7.0.0+` and the file at {@link target_path} is a `WEBP` image, or it will be
     *      ignored otherwise.
     *
     *  Range is `0` - `100`
     *
     *  Default is `80`
     *
     *  @since 2.6.0
     *
     *  @var integer
     */
    public $webp_quality;

    /**
     *  @var resource
     */
    private $source_identifier;

    /**
     *  @var mixed
     */
    private $source_type;

    /**
     *  @var int
     */
    private $source_width;

    /**
     *  @var int
     */
    private $source_height;

    /**
     *  @var array<int>
     */
    private $source_transparent_color;

    /**
     *  @var int
     */
    private $source_transparent_color_index;

    /**
     *  @var int
     */
    private $source_time;

    /**
     *  @var string
     */
    private $target_type;

    /**
     *  Constructor of the class.
     *
     *  Initializes the class and the default properties.
     *
     *  @return void
     */
    public function __construct() {

        // set default values for properties
        $this->chmod_value = 0755;

        $this->error = 0;

        $this->jpeg_quality = 85;

        $this->png_compression = 9;

        $this->webp_quality = 80;

        $this->preserve_aspect_ratio = $this->preserve_time = $this->enlarge_smaller_images = $this->bmp_compressed = true;

        $this->sharpen_images = $this->auto_handle_exif_orientation = $this->jpeg_interlace = false;

        $this->source_path = $this->target_path = '';

    }

    /**
     *  Applies one or more filters to the image given as {@link source_path} and outputs it as the file specified as
     *  {@link target_path}.
     *
     *  >   This method is available only if the {@link https://www.php.net/manual/en/function.imagefilter.php imagefilter}
     *      function is available (available from `PHP 5+`), and will leave images unaltered otherwise.
     *
     *  <code>
     *  // include the Zebra_Image library
     *  // (you don't need this if you installed using composer)
     *  require 'path/to/Zebra_Image.php';
     *
     *  // instantiate the class
     *  $img = new Zebra_Image();
     *
     *  // a source image
     *  // (where "ext" is one of the supported file types extension)
     *  $img->source_path = 'path/to/source.ext';
     *
     *  // path to where should the resulting image be saved
     *  // note that by simply setting a different extension to the file will
     *  // instruct the script to create an image of that particular type
     *  $img->target_path = 'path/to/target.ext';
     *
     *  // apply the "grayscale" filter
     *  $img->apply_filter('grayscale');
     *
     *  // apply the "contrast" filter
     *  $img->apply_filter('contrast', -20);
     *  </code>
     *
     *  You can also apply multiple filters at once. In this case, the method requires a single argument, an array of
     *  arrays, containing the filters and associated arguments, where applicable:
     *
     *  <code>
     *  // create a sepia effect
     *  // note how we're applying multiple filters at once
     *  // each filter is in its own array
     *  $img->apply_filter(array(
     *
     *      // first we apply the "grayscale" filter
     *      array('grayscale'),
     *
     *      // then we apply the "colorize" filter with 90, 60, 40 as
     *      // the values for red, green and blue
     *      array('colorize', 90, 60, 40),
     *
     *  ));
     *  </code>
     *
     *  @param  mixed   $filter     The case-insensitive name of the filter to apply. Can be one of the following:
     *
     *                              -   **brightness**          -   changes the brightness of the image; use `arg1` to set
     *                                                              the level of brightness; the range of brightness is
     *                                                              `-255` - `255`
     *                              -   **colorize**            -   adds specified RGB values to each pixel; use `arg1`,
     *                                                              `arg2` and `arg3` in the form of `red`, `green` and
     *                                                              `blue`, and `arg4` for the `alpha` channel; the range
     *                                                              for each color is `-255` to `255` and `0` to `127` for
     *                                                              the `alpha` where `0` indicates completely opaque
     *                                                              while `127` indicates completely transparent; *alpha
     *                                                              support is available for PHP 5.2.5+*
     *                              -   **contrast**            -   changes the contrast of the image; use `arg1` to set
     *                                                              the level of contrast; the range of contrast is `-100`
     *                                                              to `100`
     *                              -   **edgedetect**          -   uses edge detection to highlight the edges in the image
     *                              -   **emboss**              -   embosses the image
     *                              -   **gaussian_blur**       -   blurs the image using the Gaussian method
     *                              -   **grayscale**           -   converts the image into grayscale by changing the red,
     *                                                              green and blue components to their weighted sum using
     *                                                              the same coefficients as the REC.601 luma (Y') calculation;
     *                                                              the alpha components are retained; for palette images
     *                                                              the result may differ due to palette limitations
     *                              -   **mean_removal**        -   uses mean removal to achieve a *"sketchy"* effect
     *                              -   **negate**              -   reverses all the colors of the image
     *                              -   **pixelate**            -   applies pixelation effect to the image; use `arg1` to
     *                                                              set the block size and `arg2` to set the pixelation
     *                                                              effect mode; *this filter is available only for PHP
     *                                                              5.3.0+*
     *                              -   **selective_blur**      -   blurs the image
     *                              -   **scatter**             -   applies scatter effect to the image; use `arg1` and
     *                                                              `arg2` to define the effect strength and additionally
     *                                                              `arg3` to only apply the on select pixel colors
     *                              -   **smooth**              -   makes the image smoother; use `arg1` to set the level
     *                                                              of smoothness; applies a 9-cell convolution matrix
     *                                                              where center pixel has the weight of `arg1` and others
     *                                                              weight of 1.0; the result is normalized by dividing
     *                                                              the sum with `arg1` + 8.0 (sum of the matrix); any
     *                                                              float is accepted, large value (in practice: 2048 or)
     *                                                              more) = no change
     *
     *  @param  mixed   $arg1       Used by the following filters:
     *                              -   **brightness**          -   sets the brightness level (`-255` to `255`)
     *                              -   **contrast**            -   sets the contrast level (`-100` to `100`)
     *                              -   **colorize**            -   sets the value of the red component (`-255` to `255`)
     *                              -   **pixelate**            -   sets the block size, in pixels
     *                              -   **scatter**             -   effect subtraction level; this must not be higher or
     *                                                              equal to the addition level set with `arg3`
     *                              -   **smooth**              -   sets the smoothness level
     *
     *  @param  mixed   $arg2       Used by the following filters:
     *                              -   **colorize**            -   sets the value of the green component (`-255` to `255`)
     *                              -   **pixelate**            -   whether to use advanced pixelation effect or not (defaults to `false`)
     *                              -   **scatter**             -   effect addition level
     *
     *  @param  mixed   $arg3       Used by the following filters:
     *                              -   **colorize**            -   sets the value of the blue component (`-255` to `255`)
     *                              -   **scatter**             -   optional array indexed color values to apply effect at
     *
     *  @param  mixed   $arg4       Used by the following filters:
     *                              -   **colorize**            -   alpha channel; a value between `0` and `127`. `0` indicates
     *                                                              completely opaque while `127` indicates completely
     *                                                              transparent
     *
     *  @since 2.2.2
     *
     *  @return boolean             Returns `true` on success or false on error.
     *
     *                              If {@link https://www.php.net/manual/en/function.imagefilter.php imagefilter} is not
     *                              available, the method will return `false` without setting an {@link error} code.
     *
     *                              If the requested filter doesn't exist, or invalid arguments are passed, the method
     *                              will trigger a warning.
     *
     *                              If `false` is returned and you are sure that
     *                              {@link https://www.php.net/manual/en/function.imagefilter.php imagefilter} exists and that
     *                              the requested filter is valid, check the {@link error} property to see the error code.
     */
    public function apply_filter($filter, $arg1 = '', $arg2 = '', $arg3 = '', $arg4 = '') {

        // if "imagefilter" function exists and the requested filter exists
        if (function_exists('imagefilter')) {

            // if image resource was successfully created
            if ($this->_create_from_source()) {

                // prepare the target image
                $target_identifier = $this->_prepare_image($this->source_width, $this->source_height, -1);

                // copy the original image
                imagecopyresampled(
                    $target_identifier,
                    $this->source_identifier,
                    0,
                    0,
                    0,
                    0,
                    $this->source_width,
                    $this->source_height,
                    $this->source_width,
                    $this->source_height
                );

                // if multiple filters are to be applied at once
                if (is_array($filter)) {

                    // iterate through the filters
                    foreach ($filter as $arguments) {

                        // if filter exists
                        if (defined('IMG_FILTER_' . strtoupper($arguments[0]))) {

                            // try to apply the filter and trigger an error if the filter could not be applied
                            if (!@call_user_func_array('imagefilter', array_merge(array($target_identifier, constant('IMG_FILTER_' . strtoupper($arguments[0]))), array_slice($arguments, 1)))) {
                                trigger_error('Invalid arguments used for "' . strtoupper($arguments[0]) . '" filter', E_USER_WARNING);
                            }

                        // if filter doesn't exists, trigger an error
                        } else {
                            trigger_error('Filter "' . strtoupper($arguments[0]) . '" is not available', E_USER_WARNING);
                        }

                    }

                // if a single filter is to be applied and it is available
                } elseif (defined('IMG_FILTER_' . strtoupper($filter))) {

                    // get all the arguments passed to the method
                    $arguments = func_get_args();

                    // try to apply the filter and trigger an error if the filter could not be applied
                    if (!@call_user_func_array('imagefilter', array_merge(array($target_identifier, constant('IMG_FILTER_' . strtoupper($filter))), array_slice($arguments, 1)))) {
                        trigger_error('Invalid arguments used for "' . strtoupper($arguments[0]) . '" filter', E_USER_WARNING);
                    }

                // if filter doesn't exists, trigger an error
                } else {
                    trigger_error('Filter "' . strtoupper($filter) . '" is not available', E_USER_WARNING);
                }

                // write image
                return $this->_write_image($target_identifier);

            }

        }

        // if script gets this far, return false
        // note that we do not set the error level as it has been already set
        // by the _create_from_source() method earlier, if the case
        return false;

    }

    /**
     *  Crops a portion of the image given as {@link source_path} and outputs it as the file specified as {@link target_path}.
     *
     *  <code>
     *  // include the Zebra_Image library
     *  // (you don't need this if you installed using composer)
     *  require 'path/to/Zebra_Image.php';
     *
     *  // instantiate the class
     *  $img = new Zebra_Image();
     *
     *  // a source image
     *  // (where "ext" is one of the supported file types extension)
     *  $img->source_path = 'path/to/source.ext';
     *
     *  // path to where should the resulting image be saved
     *  // note that by simply setting a different extension to the file will
     *  // instruct the script to create an image of that particular type
     *  $img->target_path = 'path/to/target.ext';
     *
     *  // crop a rectangle of 100x100 pixels, starting from the top-left corner
     *  $img->crop(0, 0, 100, 100);
     *  </code>
     *
     *  @param  integer     $start_x            x coordinate to start cropping from
     *
     *  @param  integer     $start_y            y coordinate to start cropping from
     *
     *  @param  integer     $end_x              x coordinate where to end the cropping
     *
     *  @param  integer     $end_y              y coordinate where to end the cropping
     *
     *  @param  mixed       $background_color   (Optional) A hexadecimal color value (like `#FFFFFF` or `#FFF`) used when
     *                                          the cropping coordinates are off-scale (negative values and/or values
     *                                          greater than the image's size) to fill the remaining space.
     *
     *                                          When set to `-1` the script will preserve transparency for transparent `GIF`
     *                                          and `PNG` images. For non-transparent images the background will be black
     *                                          (`#000000`) in this case.
     *
     *                                          Default is `-1`
     *
     *  @since  1.0.4
     *
     *  @return boolean     Returns `true` on success or `false` on error.
     *
     *                      If `false` is returned, check the {@link error} property to see the error code.
     */
    public function crop($start_x, $start_y, $end_x, $end_y, $background_color = -1) {

        // this method might be also called internally
        // in this case, there's a sixth argument that points to an already existing image identifier
        $args = func_get_args();

        // if a sixth argument exists
        // for PHP 8.0.0+ GD functions return and accept \GdImage objects instead of resources (https://php.watch/versions/8.0/gdimage)
        if (isset($args[5]) && (is_resource($args[5]) || (version_compare(PHP_VERSION, '8.0.0', '>=') && $args[5] instanceof \GdImage))) {

            // that it is the image identifier that we'll be using further on
            $this->source_identifier = $args[5];

            // set this to true so that the script will continue to execute at the next IF
            $result = true;

            // we need to make sure these are integers or PHP 8.1+ will show a warning
            // https://php.watch/versions/8.1/deprecate-implicit-conversion-incompatible-float-string
            $start_x = (int)$start_x;
            $start_y = (int)$start_y;
            $end_x = (int)$end_x;
            $end_y = (int)$end_y;

        // if method is called as usually
        // try to create an image resource from source path
        } else {
            $result = $this->_create_from_source();
        }

        // if image resource was successfully created
        if ($result !== false) {

            // compute width and height
            $width = $end_x - $start_x;
            $height = $end_y - $start_y;

            // prepare the target image
            $target_identifier = $this->_prepare_image($width, $height, $background_color);

            $dest_x = 0;
            $dest_y = 0;

            // if starting x is negative
            if ($start_x < 0) {

                // we are adjusting the position where we place the cropped image on the target image
                $dest_x = abs($start_x);

                // and crop starting from 0
                $start_x = 0;

            }

            // if ending x is larger than the image's width, adjust the width we're showing
            if ($end_x > ($image_width = imagesx($this->source_identifier))) {
                $width = $image_width - $start_x;
            }

            // if starting y is negative
            if ($start_y < 0) {

                // we are adjusting the position where we place the cropped image on the target image
                $dest_y = abs($start_y);

                // and crop starting from 0
                $start_y = 0;

            }

            // if ending y is larger than the image's height, adjust the height we're showing
            if ($end_y > ($image_height = imagesy($this->source_identifier))) {
                $height = $image_height - $start_y;
            }

            // crop the image
            imagecopyresampled(
                $target_identifier,
                $this->source_identifier,
                $dest_x,
                $dest_y,
                $start_x,
                $start_y,
                $width,
                $height,
                $width,
                $height
            );

            // write image
            return $this->_write_image($target_identifier);

        }

        // if script gets this far, return false
        // note that we do not set the error level as it has been already set
        // by the _create_from_source() method earlier
        return false;

    }

    /**
     *  Flips both horizontally and vertically the image given as {@link source_path} and outputs the resulted image as
     *  {@link target_path}.
     *
     *  <code>
     *  // include the Zebra_Image library
     *  // (you don't need this if you installed using composer)
     *  require 'path/to/Zebra_Image.php';
     *
     *  // instantiate the class
     *  $img = new Zebra_Image();
     *
     *  // a source image
     *  // (where "ext" is one of the supported file types extension)
     *  $img->source_path = 'path/to/source.ext';
     *
     *  // path to where should the resulting image be saved
     *  // note that by simply setting a different extension to the file will
     *  // instruct the script to create an image of that particular type
     *  $img->target_path = 'path/to/target.ext';
     *
     *  // flip the image both horizontally and vertically
     *  $img->flip_both();
     *  </code>
     *
     *  @since 2.1
     *
     *  @return boolean     Returns `true` on success or `false` on error.
     *
     *                      If `false` is returned, check the {@link error} property to see the error code.
     */
    public function flip_both() {

        return $this->_flip('both');

    }

    /**
     *  Flips horizontally the image given as {@link source_path} and outputs the resulted image as {@link target_path}.
     *
     *  <code>
     *  // include the Zebra_Image library
     *  // (you don't need this if you installed using composer)
     *  require 'path/to/Zebra_Image.php';
     *
     *  // instantiate the class
     *  $img = new Zebra_Image();
     *
     *  // a source image
     *  // (where "ext" is one of the supported file types extension)
     *  $img->source_path = 'path/to/source.ext';
     *
     *  // path to where should the resulting image be saved
     *  // note that by simply setting a different extension to the file will
     *  // instruct the script to create an image of that particular type
     *  $img->target_path = 'path/to/target.ext';
     *
     *  // flip the image horizontally
     *  $img->flip_horizontal();
     *  </code>
     *
     *  @return boolean     Returns `true` on success or `false` on error.
     *
     *                      If `false` is returned, check the {@link error} property to see the error code.
     */
    public function flip_horizontal() {

        return $this->_flip('horizontal');

    }

    /**
     *  Flips vertically the image given as {@link source_path} and outputs the resulted image as {@link target_path}.
     *
     *  <code>
     *  // include the Zebra_Image library
     *  // (you don't need this if you installed using composer)
     *  require 'path/to/Zebra_Image.php';
     *
     *  // instantiate the class
     *  $img = new Zebra_Image();
     *
     *  // a source image
     *  // (where "ext" is one of the supported file types extension)
     *  $img->source_path = 'path/to/source.ext';
     *
     *  // path to where should the resulting image be saved
     *  // note that by simply setting a different extension to the file will
     *  // instruct the script to create an image of that particular type
     *  $img->target_path = 'path/to/target.ext';
     *
     *  // flip the image vertically
     *  $img->flip_vertical();
     *  </code>
     *
     *  @return boolean     Returns `true` on success or `false` on error.
     *
     *                      If `false` is returned, check the {@link error} property to see the error code.
     */
    public function flip_vertical() {

        return $this->_flip('vertical');

    }

    /**
     *  Resizes the image given as {@link source_path} and outputs the resulted image as {@link target_path}.
     *
     *  <code>
     *  // include the Zebra_Image library
     *  // (you don't need this if you installed using composer)
     *  require 'path/to/Zebra_Image.php';
     *
     *  // instantiate the class
     *  $img = new Zebra_Image();
     *
     *  // a source image
     *  // (where "ext" is one of the supported file types extension)
     *  $img->source_path = 'path/to/source.ext';
     *
     *  // path to where should the resulting image be saved
     *  // note that by simply setting a different extension to the file will
     *  // instruct the script to create an image of that particular type
     *  $img->target_path = 'path/to/target.ext';
     *
     *  // apply a "sharpen" filter to the resulting images
     *  $img->sharpen_images = true;
     *
     *  // resize the image to exactly 150x150 pixels, without altering
     *  // aspect ratio, by using the CROP_CENTER method
     *  $img->resize(150, 150, ZEBRA_IMAGE_CROP_CENTER);
     *  </code>
     *
     *  @param  integer     $width              The width to resize the image to.
     *
     *                                          If set to `0`, the width will be automatically adjusted, depending on the
     *                                          value of the `height` argument so that the image preserves its aspect ratio.
     *
     *                                          If {@link preserve_aspect_ratio} is set to `true` and both this and the
     *                                          `height` arguments are values greater than `0`, the image will be resized
     *                                          to the exact required width and height and the aspect ratio will be
     *                                          preserved (see also the description for the `method` argument below on
     *                                          how can this be done).
     *
     *                                          If {@link preserve_aspect_ratio} is set to `false`, the image will be
     *                                          resized to the required width and the aspect ratio will be ignored.
     *
     *                                          If both `width` and `height` are set to `0`, a copy of the source image
     *                                          will be created. {@link jpeg_quality} and {@link png_compression} will
     *                                          still apply!
     *
     *                                          If either `width` or `height` are set to `0`, the script will consider
     *                                          the value of {@link preserve_aspect_ratio} to bet set to `true` regardless
     *                                          of its actual value!
     *
     *  @param  integer     $height             The height to resize the image to.
     *
     *                                          If set to `0`, the height will be automatically adjusted, depending on
     *                                          the value of the `width` argument so that the image preserves its aspect
     *                                          ratio.
     *
     *                                          If {@link preserve_aspect_ratio} is set to `true` and both this and the
     *                                          `width` arguments are values greater than `0`, the image will be resized
     *                                          to the exact required width and height and the aspect ratio will be
     *                                          preserved (see also the description for the `method` argument below on
     *                                          how can this be done).
     *
     *                                          If {@link preserve_aspect_ratio} is set to `false`, the image will be
     *                                          resized to the required height and the aspect ratio will be ignored.
     *
     *                                          If both `width` and `height` are set to `0`, a copy of the source image
     *                                          will be created. {@link jpeg_quality} and {@link png_compression} will
     *
     *                                          If either `width` or `height` are set to `0`, the script will consider
     *                                          the value of {@link preserve_aspect_ratio} to bet set to `true` regardless
     *                                          of its actual value!
     *
     *  @param  int         $method             (Optional) Method to use when resizing images to exact width and height
     *                                          while preserving aspect ratio.
     *
     *                                          If the {@link preserve_aspect_ratio} property is set to `true` and both
     *                                          the `width` and `height` arguments are values greater than `0`, the image
     *                                          will be resized to the exact given width and height and the aspect ratio
     *                                          will be preserved by using on of the following methods:
     *
     *                                          -   **ZEBRA_IMAGE_BOXED** - the image will be scaled so that it will fit
     *                                              in a box with the given width and height (both width/height will be
     *                                              smaller or equal to the required width/height) and then it will
     *                                              be centered both horizontally and vertically; the blank area will be
     *                                              filled with the color specified by the `bgcolor` argument. (the blank
     *                                              area will be filled only if the image is not transparent!)
     *
     *                                          -   **ZEBRA_IMAGE_NOT_BOXED** - the image will be scaled so that it
     *                                              *could* fit in a box with the given width and height but will not be
     *                                              enclosed in a box with given width and height. The new width/height
     *                                              will be both smaller or equal to the required width/height.
     *
     *                                          -   **ZEBRA_IMAGE_CROP_TOPLEFT**
     *                                          -   **ZEBRA_IMAGE_CROP_TOPCENTER**
     *                                          -   **ZEBRA_IMAGE_CROP_TOPRIGHT**
     *                                          -   **ZEBRA_IMAGE_CROP_MIDDLELEFT**
     *                                          -   **ZEBRA_IMAGE_CROP_CENTER**
     *                                          -   **ZEBRA_IMAGE_CROP_MIDDLERIGHT**
     *                                          -   **ZEBRA_IMAGE_CROP_BOTTOMLEFT**
     *                                          -   **ZEBRA_IMAGE_CROP_BOTTOMCENTER**
     *                                          -   **ZEBRA_IMAGE_CROP_BOTTOMRIGHT**
     *
     *                                          For the methods involving crop, first the image is scaled so that both
     *                                          its sides are equal or greater than the respective sizes of the bounding
     *                                          box; next, a region of required width and height will be cropped from
     *                                          indicated region of the resulted image.
     *
     *                                          Default is `ZEBRA_IMAGE_CROP_CENTER`
     *
     *  @param  mixed       $background_color   (Optional) The hexadecimal color (like `#FFFFFF` or `#FFF`) of the blank
     *                                          area. See the `method` argument.
     *
     *                                          When set to `-1` the script will preserve transparency for transparent `GIF`
     *                                          and `PNG` images. For non-transparent images the background will be white
     *                                          (`#FFFFFF`) in this case.
     *
     *                                          Default is `-1`
     *
     *  @return boolean                         Returns `true` on success or `false` on error.
     *
     *                                          If `false` is returned, check the {@link error} property to see what went
     *                                          wrong.
     */
    public function resize($width = 0, $height = 0, $method = ZEBRA_IMAGE_CROP_CENTER, $background_color = -1) {

        // we need to make sure these are integers or PHP 8.1+ will show a warning
        // https://php.watch/versions/8.1/deprecate-implicit-conversion-incompatible-float-string
        $width = (int)$width;
        $height = (int)$height;

        // if image resource was successfully created
        if ($this->_create_from_source()) {

            // if either width or height is to be adjusted automatically
            // set a flag telling the script that, even if $preserve_aspect_ratio is set to false
            // treat everything as if it was set to true
            if ($width == 0 || $height == 0) {
                $auto_preserve_aspect_ratio = true;
            }

            // if aspect ratio needs to be preserved
            if ($this->preserve_aspect_ratio || isset($auto_preserve_aspect_ratio)) {

                // if height is given and width is to be computed accordingly
                if ($width == 0 && $height > 0) {

                    // get the original image's aspect ratio
                    $aspect_ratio = $this->source_width / $this->source_height;

                    // the target image's height is as given as argument to the method
                    $target_height = $height;

                    // compute the target image's width, preserving the aspect ratio
                    $target_width = round($height * $aspect_ratio);

                // if width is given and height is to be computed accordingly
                } elseif ($width > 0 && $height == 0) {

                    // get the original image's aspect ratio
                    $aspect_ratio = $this->source_height / $this->source_width;

                    // the target image's width is as given as argument to the method
                    $target_width = $width;

                    // compute the target image's height, preserving the aspect ratio
                    $target_height = round($width * $aspect_ratio);

                // if both width and height are given and ZEBRA_IMAGE_BOXED or ZEBRA_IMAGE_NOT_BOXED methods are to be used
                } elseif ($width > 0 && $height > 0 && ($method == 0 || $method == 1)) {

                    // compute the horizontal and vertical aspect ratios
                    $vertical_aspect_ratio = $height / $this->source_height;
                    $horizontal_aspect_ratio = $width / $this->source_width;

                    // if the image's newly computed height would be inside the bounding box
                    if (round($horizontal_aspect_ratio * $this->source_height) < $height) {

                        // the target image's width is as given as argument to the method
                        $target_width = $width;

                        // compute the target image's height so that the image will stay inside the bounding box
                        $target_height = round($horizontal_aspect_ratio * $this->source_height);

                    // otherwise
                    } else {

                        // the target image's height is as given as argument to the method
                        $target_height = $height;

                        // compute the target image's width so that the image will stay inside the bounding box
                        $target_width = round($vertical_aspect_ratio * $this->source_width);

                    }

                // if both width and height are given and image is to be cropped in order to get to the required size
                } elseif ($width > 0 && $height > 0 && $method > 1 && $method < 11) {

                    // compute the horizontal and vertical aspect ratios
                    $vertical_aspect_ratio = $this->source_height / $height;
                    $horizontal_aspect_ratio = $this->source_width /  $width;

                    // we'll use one of the two
                    $aspect_ratio =

                        $vertical_aspect_ratio < $horizontal_aspect_ratio ?

                        $vertical_aspect_ratio :

                        $horizontal_aspect_ratio;

                    // compute the target image's width, preserving the aspect ratio
                    $target_width = round($this->source_width / $aspect_ratio);

                    // compute the target image's height, preserving the aspect ratio
                    $target_height = round($this->source_height / $aspect_ratio);

                // for any other case
                } else {

                    // we will create a copy of the source image
                    $target_width = $this->source_width;
                    $target_height = $this->source_height;

                }

            // if aspect ratio does not need to be preserved
            } else {

                // compute the target image's width
                $target_width = ($width > 0 ? $width : $this->source_width);

                // compute the target image's height
                $target_height = ($height > 0 ? $height : $this->source_height);

            }

            // if
            if (

                // all images are to be resized - including images that are smaller than the given width/height
                $this->enlarge_smaller_images ||

                // smaller images than the given width/height are to be left untouched
                // but current image has at leas one side that is larger than the required width/height
                ($width > 0 && $height > 0 ?

                    ($this->source_width > $width || $this->source_height > $height) :

                    ($this->source_width > $target_width || $this->source_height > $target_height)

                )

            ) {

                // if
                if (

                    // aspect ratio needs to be preserved AND
                    ($this->preserve_aspect_ratio || isset($auto_preserve_aspect_ratio)) &&

                    // both width and height are given
                    ($width > 0 && $height > 0) &&

                    // images are to be cropped
                    ($method > 1 && $method < 11)

                ) {

                    // prepare the target image
                    $target_identifier = $this->_prepare_image($target_width, $target_height, $background_color);

                    imagecopyresampled(
                        $target_identifier,
                        $this->source_identifier,
                        0,
                        0,
                        0,
                        0,
                        $target_width,
                        $target_height,
                        $this->source_width,
                        $this->source_height
                    );

                    // do the crop according to the required method
                    switch ($method) {

                        // if image needs to be cropped from the top-left corner
                        case ZEBRA_IMAGE_CROP_TOPLEFT:

                            // crop accordingly
                            return $this->crop(
                                0,
                                0,
                                $width,
                                $height,
                                $background_color,
                                $target_identifier // crop this resource instead
                            );

                        // if image needs to be cropped from the top-center
                        case ZEBRA_IMAGE_CROP_TOPCENTER:

                            // crop accordingly
                            return $this->crop(
                                intval(floor(($target_width - $width) / 2)),
                                0,
                                intval(floor(($target_width - $width) / 2) + $width),
                                $height,
                                $background_color,
                                $target_identifier // crop this resource instead
                            );

                        // if image needs to be cropped from the top-right corner
                        case ZEBRA_IMAGE_CROP_TOPRIGHT:

                            // crop accordingly
                            return $this->crop(
                                $target_width - $width,
                                0,
                                $target_width,
                                $height,
                                $background_color,
                                $target_identifier // crop this resource instead
                            );

                        // if image needs to be cropped from the middle-left
                        case ZEBRA_IMAGE_CROP_MIDDLELEFT:

                            // crop accordingly
                            return $this->crop(
                                0,
                                intval(floor(($target_height - $height) / 2)),
                                $width,
                                intval(floor(($target_height - $height) / 2) + $height),
                                $background_color,
                                $target_identifier // crop this resource instead
                            );

                        // if image needs to be cropped from the center of the image
                        case ZEBRA_IMAGE_CROP_CENTER:

                            // crop accordingly
                            return $this->crop(
                                intval(floor(($target_width - $width) / 2)),
                                intval(floor(($target_height - $height) / 2)),
                                intval(floor(($target_width - $width) / 2) + $width),
                                intval(floor(($target_height - $height) / 2) + $height),
                                $background_color,
                                $target_identifier // crop this resource instead
                            );

                        // if image needs to be cropped from the middle-right
                        case ZEBRA_IMAGE_CROP_MIDDLERIGHT:

                            // crop accordingly
                            return $this->crop(
                                $target_width - $width,
                                intval(floor(($target_height - $height) / 2)),
                                $target_width,
                                intval(floor(($target_height - $height) / 2) + $height),
                                $background_color,
                                $target_identifier // crop this resource instead
                            );

                        // if image needs to be cropped from the bottom-left corner
                        case ZEBRA_IMAGE_CROP_BOTTOMLEFT:

                            // crop accordingly
                            return $this->crop(
                                0,
                                $target_height - $height,
                                $width,
                                $target_height,
                                $background_color,
                                $target_identifier // crop this resource instead
                            );

                        // if image needs to be cropped from the bottom-center
                        case ZEBRA_IMAGE_CROP_BOTTOMCENTER:

                            // crop accordingly
                            return $this->crop(
                                intval(floor(($target_width - $width) / 2)),
                                $target_height - $height,
                                intval(floor(($target_width - $width) / 2) + $width),
                                $target_height,
                                $background_color,
                                $target_identifier // crop this resource instead
                            );

                        // if image needs to be cropped from the bottom-right corner
                        case ZEBRA_IMAGE_CROP_BOTTOMRIGHT:

                            // crop accordingly
                            return $this->crop(
                                $target_width - $width,
                                $target_height - $height,
                                $target_width,
                                $target_height,
                                $background_color,
                                $target_identifier // crop this resource instead
                            );

                    }

                // if aspect ratio doesn't need to be preserved or
                // it needs to be preserved and method is ZEBRA_IMAGE_BOXED or ZEBRA_IMAGE_NOT_BOXED
                } else {

                    // prepare the target image
                    $target_identifier = $this->_prepare_image(
                        ($width > 0 && $height > 0 && $method !== ZEBRA_IMAGE_NOT_BOXED ? $width : $target_width),
                        ($width > 0 && $height > 0 && $method !== ZEBRA_IMAGE_NOT_BOXED ? $height : $target_height),
                        $background_color
                    );

                    imagecopyresampled(
                        $target_identifier,
                        $this->source_identifier,
                        ($width > 0 && $height > 0 && $method !== ZEBRA_IMAGE_NOT_BOXED ? ($width - $target_width) / 2 : 0),
                        ($width > 0 && $height > 0 && $method !== ZEBRA_IMAGE_NOT_BOXED ? ($height - $target_height) / 2 : 0),
                        0,
                        0,
                        $target_width,
                        $target_height,
                        $this->source_width,
                        $this->source_height
                    );

                    // if script gets this far, write the image to disk
                    return $this->_write_image($target_identifier);

                }

            // if we get here it means that
            // smaller images than the given width/height are to be left untouched
            // therefore, we save the image as it is
            } else {

                // prepare the target image
                $target_identifier = $this->_prepare_image($this->source_width, $this->source_height, $background_color);

                imagecopyresampled(
                    $target_identifier,
                    $this->source_identifier,
                    0,
                    0,
                    0,
                    0,
                    $this->source_width,
                    $this->source_height,
                    $this->source_width,
                    $this->source_height
                );

                // previously to 2.2.7 I was simply calling the _write_images() method without the code from above this
                // comment and therefore, when resizing transparent images to a format which doesn't support transparency
                // and the "enlarge_smaller_images" property being set to FALSE, the "background_color" argument was not
                // applied and lead to unexpected background colors for the resulting images
                return $this->_write_image($target_identifier);

            }

        }

        // if script gets this far return false
        // note that we do not set the error level as it has been already set
        // by the _create_from_source() method earlier
        return false;

    }

    /**
     *  Rotates the image given as {@link source_path} and outputs the resulted image as {@link target_path}.
     *
     *  <code>
     *  // include the Zebra_Image library
     *  // (you don't need this if you installed using composer)
     *  require 'path/to/Zebra_Image.php';
     *
     *  // instantiate the class
     *  $img = new Zebra_Image();
     *
     *  // a source image
     *  // (where "ext" is one of the supported file types extension)
     *  $img->source_path = 'path/to/source.ext';
     *
     *  // path to where should the resulting image be saved
     *  // note that by simply setting a different extension to the file will
     *  // instruct the script to create an image of that particular type
     *  $img->target_path = 'path/to/target.ext';
     *
     *  // rotate the image 45 degrees, clockwise
     *  $img->rotate(45);
     *  </code>
     *
     *  @param  double  $angle                  Angle by which to rotate the image clockwise.
     *
     *                                          Between `0` and `360`.
     *
     *  @param  mixed   $background_color       (Optional) The hexadecimal color (like `#FFFFFF` or `#FFF`) of the
     *                                          uncovered zone after the rotation.
     *
     *                                          When set to `-1` the script will preserve transparency for transparent `GIF`
     *                                          and `PNG` images. For non-transparent images the background will be white
     *                                          (`#FFFFFF`) in this case.
     *
     *                                          Default is `-1`.
     *
     *  @return boolean                         Returns `true` on success or `false` on error.
     *
     *                                          If `false` is returned, check the {@link error} property to see the error
     *                                          code.
     */
    public function rotate($angle, $background_color = -1) {

        // don't do anything if no angle is given
        if ($angle == 0 || $angle == 360) {
            return true;
        }

        // get function arguments
        $arguments = func_get_args();

        // if a third argument exists
        $use_existing_source = (isset($arguments[2]) && $arguments[2] === false);

        // if we came here just to fix orientation or if image resource was successfully created
        if ($use_existing_source || $this->_create_from_source()) {

            // there is a bug in GD when angle is 90, 180, 270
            // transparency is not preserved
            if ($angle % 90 === 0) {
                $angle += 0.001;
            }

            // angles are given clockwise but imagerotate works counterclockwise so we need to negate our value
            $angle = -$angle;

            // if the uncovered zone after the rotation is to be transparent
            if ($background_color == -1) {

                // if target image is a PNG or an WEBP
                if ($this->target_type === 'png' || $this->target_type === 'webp') {

                    // allocate a transparent color
                    $background_color = imagecolorallocatealpha($this->source_identifier, 0, 0, 0, 127);

                // if target image is a GIF
                } elseif ($this->target_type === 'gif') {

                    // if source image was a GIF and a transparent color existed
                    if ($this->source_type == IMAGETYPE_GIF && $this->source_transparent_color_index >= 0) {

                        // use that color
                        $background_color = imagecolorallocate(
                            $this->source_identifier,
                            $this->source_transparent_color['red'],
                            $this->source_transparent_color['green'],
                            $this->source_transparent_color['blue']
                        );

                    // if image had no transparent color
                    } else {

                        // allocate a transparent color
                        $background_color = imagecolorallocate($this->source_identifier, 255, 255, 255);

                        // make color transparent
                        // (imagecolorallocate may return FALSE, that's why the elvis operator)
                        imagecolortransparent($this->source_identifier, $background_color ? : null);

                    }

                // for other image types
                } else {

                    // use white as the color of uncovered zone after the rotation
                    $background_color = imagecolorallocate($this->source_identifier, 255, 255, 255);

                }

            // if a background color is given
            } else {

                // convert the color to RGB values
                $background_color = $this->_hex2rgb($background_color);

                // allocate the color to the image identifier
                $background_color = imagecolorallocate(
                    $this->source_identifier,
                    $background_color['r'],
                    $background_color['g'],
                    $background_color['b']
                );

            }

            // rotate the image
            $target_identifier = imagerotate($this->source_identifier, $angle, $background_color ?: 0);

            // if we called this method from the _create_from_source() method
            // because we are fixing orientation
            if ($use_existing_source) {

                // make any further method work on the rotated image
                $this->source_identifier = $target_identifier;

                // update the width and height of the image to the values
                // of the rotated image
                $this->source_width = imagesx($target_identifier);
                $this->source_height = imagesy($target_identifier);

                return true;

            // write image otherwise
            } else {
                return $this->_write_image($target_identifier);
            }

        }

        // if script gets this far return false
        // note that we do not set the error level as it has been already set
        // by the _create_from_source() method earlier
        return false;

    }

    /**
     *  Returns an array containing the image identifier representing the image obtained from {@link $source_path}, the
     *  image's width and height and the image's type.
     *
     *  @return mixed
     *
     *  @access private
     */
    private function _create_from_source() {

        // perform some error checking first
        // if the GD library is not installed
        if (!function_exists('gd_info')) {

            // save the error level and stop the execution of the script
            $this->error = 7;

            return false;

        // if source file does not exist
        } elseif (!is_file($this->source_path)) {

            // save the error level and stop the execution of the script
            $this->error = 1;

            return false;

        // if source file is not readable
        } elseif (!is_readable($this->source_path)) {

            // save the error level and stop the execution of the script
            $this->error = 2;

            return false;

        // if target file is same as source file and source file is not writable
        } elseif ($this->target_path == $this->source_path && !is_writable($this->source_path)) {

            // save the error level and stop the execution of the script
            $this->error = 3;

            return false;

        // try to get source file width, height and type
        // and if it founds an unsupported file type
        } elseif (

            ($this->source_type = strtolower(substr($this->source_path, strrpos($this->source_path, '.') + 1))) &&

            !(version_compare(PHP_VERSION, '7.0.0') < 0 && $this->source_type === 'webp') &&
            !(version_compare(PHP_VERSION, '7.2.0') < 0 && $this->source_type === 'bmp') &&

            // getimagesize() doesn't support WEBP until 7.1.0 so we will handle that differently
            ($this->source_path !== 'webp' && !list($this->source_width, $this->source_height, $this->source_type) = @getimagesize($this->source_path))

        ) {

            // save the error level and stop the execution of the script
            $this->error = 4;

            return false;

        // if no errors so far
        } else {

            // get target file's type based on the file extension
            $this->target_type = strtolower(substr($this->target_path, strrpos($this->target_path, '.') + 1));

            // if we are working with WEBP images
            if ($this->source_type === 'webp') {

                // define this constant which is not available until PHP 7.1.0
                if (!defined('IMAGETYPE_WEBP')) {
                    define('IMAGETYPE_WEBP', 18);
                }

                // if PHP version is less than 7.1.0
                if (version_compare(PHP_VERSION, '7.1.0') < 0) {

                    // flag these so we compute them later on
                    $this->source_width = -1;
                    $this->source_height = -1;

                }

                // set value to newly created constant
                $this->source_type = IMAGETYPE_WEBP;

            }

            // create an image from file using extension dependant function
            // checks for file extension
            switch ($this->source_type) {

                // if BMP
                case IMAGETYPE_BMP:

                    // create an image from file
                    $identifier = imagecreatefrombmp($this->source_path);

                    break;

                // if GIF
                case IMAGETYPE_GIF:

                    // create an image from file
                    $identifier = imagecreatefromgif($this->source_path);

                    // get the index of the transparent color (if any)
                    if (($this->source_transparent_color_index = imagecolortransparent($identifier)) >= 0) {

                        // get the transparent color's RGB values
                        // there are GIF images which *are* transparent and everything works as expected, but
                        // imagecolortransparent() returns a color that is outside the range of colors in the image's pallette...
                        // therefore, we check first if the index is in range
                        if ($this->source_transparent_color_index < imagecolorstotal($identifier)) {

                            // if transparent color index is in range, get the transparent color's RGB values
                            $this->source_transparent_color = @imagecolorsforindex($identifier, $this->source_transparent_color_index);

                        // if transparent color index is outside the range of colors in the image's pallette
                        } else {

                            // get RGB values for color at index 0
                            // (so that we don't have error further in the code)
                            $this->source_transparent_color = @imagecolorsforindex($identifier, 0);

                        }

                    }

                    break;

                // if PNG
                case IMAGETYPE_PNG:

                    // create an image from file
                    $identifier = imagecreatefrompng($this->source_path);

                    // disable blending
                    imagealphablending($identifier, false);

                    // save full alpha channel information
                    imagesavealpha($identifier, true);

                    break;

                // if JPEG
                case IMAGETYPE_JPEG:

                    // create an image from file
                    $identifier = imagecreatefromjpeg($this->source_path);

                    break;

                // if WEBP
                case IMAGETYPE_WEBP:

                    // because animated WEBP images are not supported
                    // we need to check if this is such a file
                    // WEBP file header https://developers.google.com/speed/webp/docs/riff_container
                    // solution from by Sven Liivak https://stackoverflow.com/questions/45190469/how-to-identify-whether-webp-image-is-static-or-animated#answer-52333192

                    $fh = fopen($this->source_path, 'rb');

                    // let's see if this is the "Extended" file format
                    fseek($fh, 12);

                    // if this is the extended file format
                    if (fread($fh, 4) === 'VP8X') {

                        // look for the "Animation (A)" bit
                        fseek($fh, 20);

                        // is this is an animated WEBP?
                        $is_animated = ((ord(fread($fh, 1)) >> 1) & 1);

                    }

                    fclose($fh);

                    // if this is an animated WEBP
                    if (isset($is_animated) && $is_animated) {

                        // flag as unsupported file type
                        $this->error = 4;

                        return false;

                    }

                    // create an image from file
                    $identifier = imagecreatefromwebp($this->source_path);

                    // if we are working with WEBP images but PHP version is less than 7.1.0
                    if ($this->source_width === -1) {

                        // use these to get image's width and height as support for WEBP in getimagesize() was added only
                        // beginning with PHP 7.1.0
                        $this->source_width = imagesx($identifier);
                        $this->source_height = imagesy($identifier);

                    }

                    // disable blending
                    imagealphablending($identifier, false);

                    // save full alpha channel information
                    imagesavealpha($identifier, true);

                    break;

                default:

                    // if unsupported file type
                    // note that we call this if the file is not BMP, GIF, JPG, PNG or WEBP even though the getimagesize function
                    // might handle more image types
                    $this->error = 4;

                    return false;

            }

        }

        // if target file has to have the same timestamp as the source image
        // save it as a global property of the class
        if ($this->preserve_time) {
            $this->source_time = filemtime($this->source_path);
        }

        // make available the source image's identifier
        $this->source_identifier = $identifier;

        // for JPEG files, if we need to handle exif orientation automatically
        if ($this->auto_handle_exif_orientation && $this->source_type === IMAGETYPE_JPEG) {

            // if "exif_read_data" function is not available, return false
            if (!function_exists('exif_read_data')) {

                // save the error level and stop the execution of the script
                $this->error = 9;

                return false;

            // if "exif_read_data" function is available, EXIF information is available, orientation information is available and orientation needs fixing
            } elseif (($exif = @exif_read_data($this->source_path)) && isset($exif['Orientation']) && in_array($exif['Orientation'], array(3, 6, 8))) {

                // fix the orientation
                switch ($exif['Orientation']) {

                    case 3:

                        // 180 rotate left
                        $this->rotate(180, -1, false);
                        break;

                    case 6:

                        // 90 rotate right
                        $this->rotate(90, -1, false);
                        break;

                    case 8:

                        // 90 rotate left
                        $this->rotate(-90, -1, false);
                        break;

                }

            }

        }

        return true;

    }

    /**
     *  Flips horizontally, vertically or both ways the image given as {@link source_path}.
     *
     *  @param  string      $orientation    How to flip the image.
     *
     *                                      Allowed values are `horizontal`, `vertical` and `both`.
     *
     *  @since 2.1
     *
     *  @return boolean     Returns TRUE on success or FALSE on error.
     *
     *                      If FALSE is returned, check the {@link error} property to see the error code.
     *  @access private
     *
     */
    private function _flip($orientation) {

        // if image resource was successfully created
        if ($this->_create_from_source()) {

            // prepare the target image
            $target_identifier = $this->_prepare_image($this->source_width, $this->source_height, -1);

            // flip according to $orientation
            switch ($orientation) {

                case 'horizontal':

                    imagecopyresampled(
                        $target_identifier,
                        $this->source_identifier,
                        0,
                        0,
                        ($this->source_width - 1),
                        0,
                        $this->source_width,
                        $this->source_height,
                        -$this->source_width,
                        $this->source_height
                    );

                    break;

                case 'vertical':

                    imagecopyresampled(
                        $target_identifier,
                        $this->source_identifier,
                        0,
                        0,
                        0,
                        ($this->source_height - 1),
                        $this->source_width,
                        $this->source_height,
                        $this->source_width,
                        -$this->source_height
                    );
                    break;

                case 'both':

                    imagecopyresampled(
                        $target_identifier,
                        $this->source_identifier,
                        0,
                        0,
                        ($this->source_width - 1),
                        ($this->source_height - 1),
                        $this->source_width,
                        $this->source_height,
                        -$this->source_width,
                        -$this->source_height
                    );

                    break;

            }

            // write image
            return $this->_write_image($target_identifier);

        }

        // if script gets this far, return false
        // note that we do not set the error level as it has been already set
        // by the _create_from_source() method earlier
        return false;

    }

    /**
     *  Converts a hexadecimal representation of a color (i.e. `#123456` or `#AAA`) to a RGB representation.
     *
     *  The RGB values will be a value between `0` and `255` each.
     *
     *  @param  string  $color              Hexadecimal representation of a color (i.e. `#123456` or `#AAA`).
     *
     *  @param  string  $default_on_error   (Optional) Hexadecimal representation of a color to be used in case `$color`
     *                                      is not recognized as a hexadecimal color.
     *
     *                                      Default is `#FFFFFF`
     *
     *  @return array<int>                  Returns an associative array with the values of (R)ed, (G)reen and (B)lue
     *
     *  @access private
     */
    private function _hex2rgb($color, $default_on_error = '#FFFFFF') {

        // if color is not formatted correctly
        // use the default color
        if (preg_match('/^#?([a-f]|[0-9]){3}(([a-f]|[0-9]){3})?$/i', $color) == 0) {
            $color = $default_on_error;
        }

        // trim off the "#" prefix from $background_color
        $color = ltrim($color, '#');

        // if color is given using the shorthand (i.e. "FFF" instead of "FFFFFF")
        if (strlen($color) == 3) {

            $tmp = '';

            // take each value
            // and duplicate it
            for ($i = 0; $i < 3; $i++) {
                $tmp .= str_repeat($color[$i], 2);
            }

            // the color in it's full, 6 characters length notation
            $color = $tmp;

        }

        // decimal representation of the color
        $int = hexdec($color);

        // extract and return the RGB values
        return array(

            'r' =>  0xFF & ($int >> 0x10),
            'g' =>  0xFF & ($int >> 0x8),
            'b' =>  0xFF & $int

        );

    }

    /**
     *  Creates a blank image of given width, height and background color.
     *
     *  @param  integer     $width              Width of the new image.
     *
     *  @param  integer     $height             Height of the new image.
     *
     *  @param  mixed       $background_color   (Optional) The hexadecimal color of the background.
     *
     *                                          Can also be `-1` case in which the script will try to create a transparent
     *                                          image, if possible.
     *
     *                                          Default is `#FFFFFF`.
     *
     *  @return resource                        Returns the identifier of the newly created image.
     *
     *  @access private
     */
    private function _prepare_image($width, $height, $background_color = '#FFFFFF') {

        // create a blank image
        $identifier = imagecreatetruecolor((int)$width <= 0 ? 1 : (int)$width, (int)$height <= 0 ? 1 : (int)$height);

        // if we are creating a transparent image, and image type supports transparency
        if ($background_color === -1 && $this->target_type !== 'jpg') {

            // disable blending
            imagealphablending($identifier, false);

            // allocate a transparent color
            $background_color = imagecolorallocatealpha($identifier, 0, 0, 0, 127);

            // we also need to set this for saving GIFs
            imagecolortransparent($identifier, $background_color);

            // save full alpha channel information
            imagesavealpha($identifier, true);

        // if we are not creating a transparent image
        } else {

            // convert hex color to rgb
            $background_color = $this->_hex2rgb($background_color);

            // prepare the background color
            $background_color = imagecolorallocate($identifier, $background_color['r'], $background_color['g'], $background_color['b']);

        }

        // fill the image with the background color
        imagefill($identifier, 0, 0, $background_color);

        // return the image's identifier
        return $identifier;

    }

    /**
     *  Sharpens images. Useful when creating thumbnails.
     *
     *  Code taken from the comments at {@link https://www.php.net/manual/en/function.imageconvolution.php}.
     *
     *  >   This function will yield a result only for PHP version 5.1.0+ and will leave the image unaltered for older
     *      versions!
     *
     *  @param  resource    $image  An image identifier
     *
     *  @return resource    Returns the sharpened image
     *
     *  @access private
     */
    private function _sharpen_image($image) {

        // if the "sharpen_images" is set to true and we're running an appropriate version of PHP
        // (the "imageconvolution" is available only for PHP 5.1.0+)
        if ($this->sharpen_images && version_compare(PHP_VERSION, '5.1.0') >= 0) {

            // the convolution matrix as an array of three arrays of three floats
            $matrix = array(
                array(-1.2, -1, -1.2),
                array(-1, 20, -1),
                array(-1.2, -1, -1.2),
            );

            // the divisor of the matrix
            $divisor = array_sum(array_map('array_sum', $matrix));

            // color offset
            $offset = 0;

            // sharpen image
            imageconvolution($image, $matrix, $divisor, $offset);

        }

        // return the image's identifier
        return $image;

    }

    /**
     *  Creates a new image from given image identifier having the extension as specified by {@link target_path}.
     *
     *  @param  resource    $identifier An image identifier
     *
     *  @return boolean                 Returns `true` on success or `false` on error.
     *
     *                                  If `false` is returned, check the {@link error} property to see the error code.
     *
     *  @access private
     */
    private function _write_image($identifier) {

        // sharpen image if it's required
        $this->_sharpen_image($identifier);

        // save interlaced JPEG images if required
        if (in_array($this->target_type, array('jpg', 'jpeg')) && $this->jpeg_interlace) {
            imageinterlace($identifier, 1);
        }

        // image saving process goes according to required extension
        switch ($this->target_type) {

            // if BMP
            case 'bmp':

                // if GD support for this file type is not available
                if (!function_exists('imagebmp')) {

                    // save the error level and stop the execution of the script
                    $this->error = 6;

                    return false;

                // if, for some reason, file could not be created
                } elseif (@!imagebmp($identifier, $this->target_path, $this->bmp_compressed)) {

                    // save the error level and stop the execution of the script
                    $this->error = 3;

                    return false;

                }

                break;

            // if GIF
            case 'gif':

                // if GD support for this file type is not available
                // in version 1.6 of GD the support for GIF files was dropped see
                // https://www.php.net/manual/en/function.imagegif.php#function.imagegif.notes
                if (!function_exists('imagegif')) {

                    // save the error level and stop the execution of the script
                    $this->error = 6;

                    return false;

                // if, for some reason, file could not be created
                } elseif (@!imagegif($identifier, $this->target_path)) {

                    // save the error level and stop the execution of the script
                    $this->error = 3;

                    return false;

                }

                break;

            // if JPEG
            case 'jpg':
            case 'jpeg':

                // if GD support for this file type is not available
                if (!function_exists('imagejpeg')) {

                    // save the error level and stop the execution of the script
                    $this->error = 6;

                    return false;

                // if, for some reason, file could not be created
                } elseif (@!imagejpeg($identifier, $this->target_path, $this->jpeg_quality)) {

                    // save the error level and stop the execution of the script
                    $this->error = 3;

                    return false;

                }

                break;

            // if PNG
            case 'png':

                // if GD support for this file type is not available
                if (!function_exists('imagepng')) {

                    // save the error level and stop the execution of the script
                    $this->error = 6;

                    return false;

                // if, for some reason, file could not be created
                } elseif (@!imagepng($identifier, $this->target_path, $this->png_compression)) {

                    // save the error level and stop the execution of the script
                    $this->error = 3;

                    return false;

                }

                break;

            // if WEBP
            case 'webp':

                // if GD support for this file type is not available
                if (!function_exists('imagewebp')) {

                    // save the error level and stop the execution of the script
                    $this->error = 6;

                    return false;

                // if, for some reason, file could not be created
                } elseif (@!imagewebp($identifier, $this->target_path, $this->webp_quality)) {

                    // save the error level and stop the execution of the script
                    $this->error = 3;

                    return false;

                }

                break;

            // if not a supported file extension
            default:

                // save the error level and stop the execution of the script
                $this->error = 5;

                return false;

        }

        // get a list of functions disabled via configuration
        $disabled_functions = @ini_get('disable_functions');

        // if the 'chmod' function is not disabled via configuration
        if ($disabled_functions === '' || strpos('chmod', $disabled_functions) === false) {

            // chmod the file
            chmod($this->target_path, intval($this->chmod_value, 8));

        // save the error level
        } else {
            $this->error = 8;
        }

        // if target file has to have the same timestamp as the source image
        if ($this->preserve_time) {

            // touch the newly created file
            @touch($this->target_path, $this->source_time);

        }

        // free memory
        imagedestroy($this->source_identifier);
        imagedestroy($identifier);

        // for PHP 8.0.0+ imagedestroy is no-op (https://php.watch/versions/8.0/gdimage)
        // and we have to use unset()
        if (version_compare(PHP_VERSION, '8.0.0', '>=')) {
            unset($this->source_identifier);
            unset($identifier);
        }

        // return true
        return true;

    }

}
