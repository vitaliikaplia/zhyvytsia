<?php

if(!defined('ABSPATH')){exit;}

add_action("init", function() {
    // First line of defence defused
    add_filter('upload_mimes', function ($mimes) {
        $mimes['svg'] = 'image/svg+xml';
        return $mimes;
    });

    // Add the XML Declaration if it's missing (otherwise WordPress does not allow uploads)
    add_filter("wp_handle_upload_prefilter", function ($upload) {
        if (!empty($upload["type"]) && $upload["type"] === "image/svg+xml") {
            $contents = file_get_contents($upload["tmp_name"]);
            if (strpos($contents, "<?xml") === false) {
                file_put_contents($upload["tmp_name"], '<?xml version="1.0" encoding="UTF-8"?>' . $contents);
            }
        }
        return $upload;
    }, 10, 1);
});
