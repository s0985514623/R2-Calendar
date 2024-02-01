<?php

declare (strict_types = 1);

namespace J7\WpReactPlugin;

use J7\WpReactPlugin\Utils;

class Functions
{
    /**
     * Register CPT
     *
     * @param string $label - the name of CPT
     * @param array $meta_keys - the meta keys of CPT ex ['meta', 'settings']
     * @return void
     */
    public static function register_cpt($label): void
    {

        $kebab = str_replace(' ', '-', strtolower($label));
        $snake = str_replace(' ', '_', strtolower($label));

        $labels = [
            'name'                     => \esc_html__($label, Utils::KEBAB),
            'singular_name'            => \esc_html__($label, Utils::KEBAB),
            'add_new'                  => \esc_html__('Add new', Utils::KEBAB),
            'add_new_item'             => \esc_html__('Add new item', Utils::KEBAB),
            'edit_item'                => \esc_html__('Edit', Utils::KEBAB),
            'new_item'                 => \esc_html__('New', Utils::KEBAB),
            'view_item'                => \esc_html__('View', Utils::KEBAB),
            'view_items'               => \esc_html__('View', Utils::KEBAB),
            'search_items'             => \esc_html__('Search ' . $label, Utils::KEBAB),
            'not_found'                => \esc_html__('Not Found', Utils::KEBAB),
            'not_found_in_trash'       => \esc_html__('Not found in trash', Utils::KEBAB),
            'parent_item_colon'        => \esc_html__('Parent item', Utils::KEBAB),
            'all_items'                => \esc_html__('All', Utils::KEBAB),
            'archives'                 => \esc_html__($label . ' archives', Utils::KEBAB),
            'attributes'               => \esc_html__($label . ' attributes', Utils::KEBAB),
            'insert_into_item'         => \esc_html__('Insert to this ' . $label, Utils::KEBAB),
            'uploaded_to_this_item'    => \esc_html__('Uploaded to this ' . $label, Utils::KEBAB),
            'featured_image'           => \esc_html__('Featured image', Utils::KEBAB),
            'set_featured_image'       => \esc_html__('Set featured image', Utils::KEBAB),
            'remove_featured_image'    => \esc_html__('Remove featured image', Utils::KEBAB),
            'use_featured_image'       => \esc_html__('Use featured image', Utils::KEBAB),
            'menu_name'                => \esc_html__($label, Utils::KEBAB),
            'filter_items_list'        => \esc_html__('Filter ' . $label . ' list', Utils::KEBAB),
            'filter_by_date'           => \esc_html__('Filter by date', Utils::KEBAB),
            'items_list_navigation'    => \esc_html__($label . ' list navigation', Utils::KEBAB),
            'items_list'               => \esc_html__($label . ' list', Utils::KEBAB),
            'item_published'           => \esc_html__($label . ' published', Utils::KEBAB),
            'item_published_privately' => \esc_html__($label . ' published privately', Utils::KEBAB),
            'item_reverted_to_draft'   => \esc_html__($label . ' reverted to draft', Utils::KEBAB),
            'item_scheduled'           => \esc_html__($label . ' scheduled', Utils::KEBAB),
            'item_updated'             => \esc_html__($label . ' updated', Utils::KEBAB),
         ];
        $args = [
            'label'                 => \esc_html__($label, Utils::KEBAB),
            'labels'                => $labels,
            'description'           => '',
            'public'                => true,
            'hierarchical'          => false,
            'exclude_from_search'   => true,
            'publicly_queryable'    => true,
            'show_ui'               => true,
            'show_in_nav_menus'     => false,
            'show_in_admin_bar'     => false,
            'show_in_menu'          => false,
            'show_in_rest'          => true,
            'query_var'             => false,
            'can_export'            => true,
            'delete_with_user'      => true,
            'has_archive'           => false,
            'rest_base'             => '',
            'menu_position'         => 6,
            'menu_icon'             => 'dashicons-store',
            'capability_type'       => 'post',
            'supports'              => [ 'title', 'editor', 'thumbnail', 'custom-fields', 'author' ],
            'taxonomies'            => [  ],
            'rest_controller_class' => 'WP_REST_Posts_Controller',
            'rewrite'               => [
                'with_front' => true,
             ],
         ];

        \register_post_type($kebab, $args);
    }
    public static function add_metabox(array $args): void
    {
        \add_meta_box(
            $args[ 'id' ],
            __($args[ 'label' ], Utils::KEBAB),
            array(__CLASS__, 'render_metabox'),
            $args[ 'post_type' ],
            'advanced',
            'default',
            array('id' => $args[ 'id' ])
        );
    }

    /**
     * Renders the meta box.
     */
    public static function render_metabox($post, $metabox): void
    {
        echo "<div id='{$metabox[ 'args' ][ 'id' ]}'></div>";
    }

    /**
     * JSON Parse
     */
    public static function json_parse($stringfy, $default = [  ], $associative = null)
    {
        $out_put = '';
        try {
            $out_put = json_decode(str_replace('\\', '', $stringfy), $associative) ?? $default;
        } catch (\Throwable $th) {
            $out_put = $default;
        } finally {
            return $out_put;
        }
    }

    public static function disable_admin_bar()
    {
        add_action('after_setup_theme', function () {
// 如果當前用戶不是管理員
            if (!current_user_can('administrator')) {
                // 隱藏管理工具欄
                add_filter('show_admin_bar', '__return_false');
            }
        });
    }
}
