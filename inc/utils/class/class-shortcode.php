<?php

declare (strict_types = 1);

namespace J7\WpReactPlugin;

use J7\WpReactPlugin\Utils;

class ShortCode
{

    function __construct($shortcode = '')
    {
        if (!empty($shortcode)) {
            \add_shortcode($shortcode, [ $this, 'shortcode_callback' ]);
        }
    }

    public function shortcode_callback()
    {

        $html = '';
        ob_start();
        ?>
<div id="<?=Utils::RENDER_ID_1?>"></div>
<?php
$html .= ob_get_clean();

        return $html;
    }
}