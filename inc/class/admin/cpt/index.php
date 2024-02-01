<?php

declare (strict_types = 1);

namespace J7\WpReactPlugin\Admin;

use J7\WpReactPlugin\Functions;
use J7\WpReactPlugin\Utils;

class CPT
{
    public $post_type  = '';
    public $post_metas = [  ];
    public $rewrite    = [  ];

    function __construct($cpt, $args)
    {
        $this->post_type  = $cpt;
        $this->post_metas = $args[ 'post_metas' ];
        $this->rewrite    = $args[ 'rewrite' ] ?? [  ];

        if (empty($this->post_type)) {
            return;
        }

        \add_action('init', [ $this, 'init' ]);
        \add_filter('user_has_cap', [ $this, 'allow_user_edit_race_post' ], 10, 4);

        if (!empty($args[ 'post_metas' ])) {
            \add_action('rest_api_init', [ $this, 'add_post_meta' ]);
        }

        \add_action('load-post.php', [ $this, 'init_metabox' ]);
        \add_action('load-post-new.php', [ $this, 'init_metabox' ]);

        // if (!empty($args['rewrite'])) {
        //     \add_filter('query_vars', [$this, 'add_query_var']);
        //     \add_filter('template_include', [$this, 'load_custom_template'], 99);
        // }
    }

    //使已登入用戶能夠編輯和發布文章
    function allow_user_edit_race_post($all_caps, $caps, $name, $user)
    {
        if (is_user_logged_in()) {
            global $current_user;
            if (in_array('subscriber', $current_user->roles)) {
                $all_caps[ 'read_posts' ]             = true;
                $all_caps[ 'read_others_posts' ]      = true;
                $all_caps[ 'read_private_posts' ]     = true;
                $all_caps[ 'edit_posts' ]             = true;
                $all_caps[ 'edit_private_posts' ]     = true;
                $all_caps[ 'publish_posts' ]          = true;
                $all_caps[ 'delete_posts' ]           = true;
                $all_caps[ 'delete_published_posts' ] = true;
                $all_caps[ 'delete_private_posts' ]   = true;
            }
        }
        // wp_send_json(print_r($all_caps));
        return $all_caps;
    }

    public function init(): void
    {
        Functions::register_cpt($this->post_type);

        // add {$this->post_type}/{slug}/test rewrite rule
        // if (!empty($this->rewrite)) {
        //     \add_rewrite_rule('^' . $this->post_type . '/([^/]+)/' . $this->rewrite['slug'] . '/?$', 'index.php?post_type=' . $this->post_type . '&name=$matches[1]&' . $this->rewrite['var'] . '=1', 'top');
        //     \flush_rewrite_rules();
        // }
    }

    public function add_post_meta(): void
    {
        foreach ($this->post_metas as $meta_key) {
            \register_meta(
                'post',
                Utils::SNAKE . '_' . $meta_key,
                array(
                    'type'         => 'string',
                    'show_in_rest' => true,
                    'single'       => true,
                ));
        }
    }

    /**
     * Meta box initialization.
     */
    public function init_metabox(): void
    {
        \add_action('add_meta_boxes', [ $this, 'add_metabox' ]);
        \add_action('save_post', [ $this, 'save_metabox' ], 10, 2);
        // \add_filter('rewrite_rules_array', [$this, 'custom_post_type_rewrite_rules']);
    }

    /**
     * Adds the meta box.
     */
    public function add_metabox(string $post_type): void
    {
        if (in_array($post_type, [ Utils::KEBAB ])) {
            \add_meta_box(
                Utils::KEBAB . '-metabox',
                __('My App', Utils::TEXT_DOMAIN),
                [ $this, 'render_meta_box' ],
                $post_type,
                'advanced',
                'high'
            );
        }
    }

    public function render_meta_box(): void
    {
        echo '<div id="' . Utils::RENDER_ID_2 . '"></div>';
    }

    public function add_query_var($vars)
    {
        $vars[  ] = $this->rewrite[ 'var' ];
        return $vars;
    }

    public function custom_post_type_rewrite_rules($rules)
    {
        global $wp_rewrite;
        $wp_rewrite->flush_rules();
        return $rules;
    }

    public function save_metabox($post_id, $post)
    {

        /*
         * We need to verify this came from the our screen and with proper authorization,
         * because save_post can be triggered at other times.
         */

        // Check if our nonce is set.
        if (!isset($_POST[ '_wpnonce' ])) {
            return $post_id;
        }

        $nonce = $_POST[ '_wpnonce' ];

        /*
         * If this is an autosave, our form has not been submitted,
         * so we don't want to do anything.
         */
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }

        $post_type = \sanitize_text_field($_POST[ 'post_type' ] ?? '');

        // Check the user's permissions.
        if ($this->post_type !== $post_type) {
            return $post_id;
        }

        if (!\current_user_can('edit_post', $post_id)) {
            return $post_id;
        }

        /* OK, it's safe for us to save the data now. */

        // Sanitize the user input.
        $meta_data = \sanitize_text_field($_POST[ Utils::SNAKE . '_meta' ]);

        // Update the meta field.
        \update_post_meta($post_id, Utils::SNAKE . '_meta', $meta_data);
    }

    /**
     * 設定 {Utils::KEBAB}/{slug}/report 的 php template
     */
    public function load_custom_template($template)
    {
        $repor_template_path = Utils::get_plugin_dir() . '/inc/templates/' . $this->rewrite[ 'template_path' ];

        if (\get_query_var($this->rewrite[ 'var' ])) {
            if (file_exists($repor_template_path)) {
                return $repor_template_path;
            }
        }
        return $template;
    }
}

new CPT(Utils::KEBAB, array(
    'post_metas' => [ 'meta', 'settings' ],
    'rewrite'    => array(
        'template_path' => 'test.php',
        'slug'          => 'test',
        'var'           => Utils::SNAKE . '_test',
    ),
));