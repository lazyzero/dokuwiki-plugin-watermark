<?php
/**
 * DokuWiki Plugin watermark (Action Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Christian Moll <christian@chrmoll.de>
 */
 
if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../').'/');
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

class action_plugin_watermark_watermark extends DokuWiki_Action_Plugin {

    /**
     * Registers a callback function for a given event
     *
     * @param Doku_Event_Handler $controller DokuWiki's event controller object
     * @return void
     */
    public function register(Doku_Event_Handler &$controller) {

       $controller->register_hook('MEDIA_UPLOAD_FINISH', 'AFTER', $this, 'handle_media_upload_finish');
   
    }

    /**
     * [Custom event handler which performs action]
     *
     * @param Doku_Event $event  event object by reference
     * @param mixed      $param  [the parameters passed as fifth argument to register_hook() when this
     *                           handler was registered]
     * @return void
     */

    public function handle_media_upload_finish(Doku_Event &$event, $param) {
	$watermark = imagecreatefrompng(DOKU_PLUGIN.'watermark/watermark.png');
	$sourcefile = $event->data[1];
	$fileType = strtolower(substr($sourcefile, strlen($sourcefile)-3));

	switch($fileType) {
	    case('gif'):
		$image = imagecreatefromgif($sourcefile);
		break;
	      
	    case('png'):
		$image = imagecreatefrompng($sourcefile);
		break;
	      
	    default:
		$image = imagecreatefromjpeg($sourcefile);
	}
	
	$watermark_width = imagesx($watermark);  
	$watermark_height = imagesy($watermark);  
	
	$size = getimagesize($sourcefile);
	
	$dest_x = $size[0] - $watermark_width - 5;  
	$dest_y = $size[1] - $watermark_height - 5;  
	
	imagesavealpha($image, true);
	imagealphablending($image, true);
	imagesavealpha($watermark, true);
	imagealphablending($watermark, true);
	
	imagecopy($image, $watermark, $dest_x, $dest_y, 0, 0, $watermark_width, $watermark_height);
	
	
	switch($fileType) {
    
	  // remember we don't need gif any more, so we use only png or jpeg.
	  // See the upsaple code immediately above to see how we handle gifs
	  case('png'):
	      imagepng($image, $sourcefile, 0);
	      break; 
	  default:
	      imagejpeg($image, $sourcefile, 90);  
	}        
	

	imagedestroy($image);  
	imagedestroy($watermark);  
	
	var_dump($image);
    }

}

// vim:ts=4:sw=4:et:
