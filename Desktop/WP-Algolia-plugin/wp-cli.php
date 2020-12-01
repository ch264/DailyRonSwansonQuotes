<?php

if (!(defined('WP_CLI') && WP_CLI)) {
    return;
}


class Algolia_Command {
  public function reindex_post($args, $assoc_args) {
      global $algolia;
      $index = $algolia->initIndex('<name of your Algolia Index>');

      $index->clearObjects()->wait();

      $paged = 1;
      $count = 0;

      do {
          $posts = new WP_Query([
              'posts_per_page' => 100,
              'paged' => $paged,
              'post_type' => 'post',
	      'post_status' => 'publish'
          ]);

          if (!$posts->have_posts()) {
              break;
          }

          $records = [];
	foreach ($posts->posts as $post) {
   		if ($assoc_args['verbose']) {
        		WP_CLI::line('Serializing ['.$post->post_title.']');
    		}

    		$split = apply_filters('post_to_record', $post);
    		$records = array_merge($records, $split);
    		$count++;
	}

          if ($assoc_args['verbose']) {
              WP_CLI::line('Sending batch');
          }

          $index->saveObjects($records);

          $paged++;

      } while (true);

      WP_CLI::success("$count posts indexed in Algolia");
  }
}

WP_CLI::add_command('algolia', 'Algolia_Command');
?>
