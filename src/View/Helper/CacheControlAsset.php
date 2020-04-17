<?php
/**
 * @author Glenn Schmidt <glenn@codeacious.com>
 */

namespace Codeacious\Mvc\View\Helper;

use Laminas\View\Helper\AbstractHelper;

/**
 * Outputs the URL path to an asset file, with a suffix based on the file's last modification time.
 * The suffix is designed to force the browser to re-request the asset if it has changed.
 * 
 * If the file cannot be found under the 'public' directory (suggesting that it is served
 * dynamically) then it will receive a suffix based on the current time, so that it is always
 * re-requested.
 */
class CacheControlAsset extends AbstractHelper
{
    public function __invoke($file)
    {
        $path = $this->view->basePath($file);
        
        $filename = 'public'.$path;
        $ts = null;
        if (file_exists($filename))
            $ts = filemtime($filename);
        if (!$ts)
            $ts = time();
        
        return $path.'?v='.$ts;
    }
}
