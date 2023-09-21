<?php

if(!defined('ABSPATH')){exit;}

/** delete child media attachments when delete the post */
if(get_option('delete_child_media')){
	function delete_associated_media($id)
	{
		$media = get_children(array(
			'post_parent' => $id,
			'post_type' => 'attachment'
		));
		if (empty($media)) return;
		foreach ($media as $file) {
			wp_delete_attachment($file->ID);
		}
	}
	add_action('before_delete_post', 'delete_associated_media');
}
