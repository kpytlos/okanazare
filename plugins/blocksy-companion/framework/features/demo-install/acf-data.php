<?php

namespace Blocksy;

class DemoInstallAcfData {
	protected $has_streaming = true;
	protected $demo_name = null;

	public function __construct($args = []) {
		$args = wp_parse_args($args, [
			'has_streaming' => true,
			'demo_name' => null
		]);

		if (
			! $args['demo_name']
			&&
			isset($_REQUEST['demo_name'])
			&&
			$_REQUEST['demo_name']
		) {
			$args['demo_name'] = $_REQUEST['demo_name'];
		}

		$this->has_streaming = $args['has_streaming'];
		$this->demo_name = $args['demo_name'];
	}

	public function import() {
		if ($this->has_streaming) {
			Plugin::instance()->demo->start_streaming();

			if (! current_user_can('edit_theme_options')) {
				Plugin::instance()->demo->emit_sse_message([
					'action' => 'complete',
					'error' => 'No permission.',
				]);

				exit;
			}

			if (! $this->demo_name) {
				Plugin::instance()->demo->emit_sse_message([
					'action' => 'complete',
					'error' => 'No demo name passed.',
				]);
				exit;
			}

			if (! function_exists('acf_determine_internal_post_type')) {
				Plugin::instance()->demo->emit_sse_message([
					'action' => 'complete',
					'error' => 'ACF plugin is not present.',
				]);
				exit;
			}
		}

		$demo_name = explode(':', $this->demo_name);

		if (! isset($demo_name[1])) {
			$demo_name[1] = '';
		}

		$demo = $demo_name[0];
		$builder = $demo_name[1];

		if ($this->has_streaming) {
			Plugin::instance()->demo->emit_sse_message([
				'action' => 'download_demo_acf_data',
				'error' => false,
			]);
		}

		$demo_content = Plugin::instance()->demo->fetch_single_demo([
			'demo' => $demo,
			'builder' => $builder,
			'field' => 'acf'
		]);

		if (! isset($demo_content['acf']) && $this->has_streaming) {
			Plugin::instance()->demo->emit_sse_message([
				'action' => 'complete',
				'error' => __('Downloaded acf data is corrupted.'),
			]);

			exit;
		}

		if ($demo_content['acf'] && $this->has_streaming) {
			Plugin::instance()->demo->emit_sse_message([
				'action' => 'downloaded_demo_acf_data',
				'error' => false,
				'data' => $demo_content['acf']
			]);
		}



		foreach ($demo_content['acf'] as $to_import) {
			$post_type = acf_determine_internal_post_type($to_import['key']);
			$post = acf_get_internal_post_type_post(
				$to_import['key'],
				$post_type
			);

			if ($post) {
				$to_import['ID'] = $post->ID;
			}

			// Import the post.
			$to_import = acf_import_internal_post_type($to_import, $post_type);
		}

		if ($this->has_streaming) {
			Plugin::instance()->demo->emit_sse_message([
				'action' => 'complete',
				'error' => false,
			]);

			exit;
		}
	}
}


