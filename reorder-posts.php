<?php
/*
 * Plugin Name: Reorder Posts
 * Plugin URI:  https://wordpress.org/plugins/Reorder-Posts/
 * Description: Allows you to reorder the posts.
 * Version:     0.0.1
 * Author:      Zeng xiao luan
 * Author URI:  https://zengxiaoluan.com
 * Text Domain: reorder
 * License:     GPL-2.0+
 */

if (!defined('ABSPATH')) {
  exit();
}

if (!class_exists('WP_Reorder_Posts')) {
  class WP_Reorder_Posts
  {
    private $textDomain = 'reorder-wp-posts';

    private $orderByOption = 'zxl_reorderby_post_order';

    private $orderOption = 'zxl_reorder_post_order';

    private $orderArray = array('DESC', 'ASC');

    // Used to filter values.
    private $allowed_keys = array(
      'post_name',
      'post_author',
      'post_date',
      'post_title',
      'post_modified',
      'post_parent',
      'post_type',
      'name',
      'author',
      'date',
      'title',
      'modified',
      'parent',
      'type',
      'ID',
      'menu_order',
      'comment_count',
      'rand',
      'post__in',
      'post_parent__in',
      'post_name__in'
    );

    private function basename()
    {
      return plugin_basename(__FILE__);
    }

    public function __construct()
    {
      register_activation_hook($this->basename(), array($this, 'activate'));
      register_uninstall_hook($this->basename(), array(
        'WP_Reorder_Posts',
        'uninstall'
      ));

      add_action('admin_init', array($this, 'admin_init'));

      add_action('wpmu_options', array($this, 'wpmu_options'));
      add_action('update_wpmu_options', array($this, 'update_wpmu_options'));

      add_action('pre_get_posts', array($this, 'reorder_posts'));
    }

    function reorder_posts($query)
    {
      if ($query->is_home() && $query->is_main_query()) {
        $orderBy = get_site_option($this->orderByOption);

        if (in_array($orderBy, $this->allowed_keys)) {
          $query->set('orderby', $orderBy);
        }

        $order = get_site_option($this->orderOption);
        if (in_array($order, $this->orderArray)) {
          $query->set('order', $order);
        }
      }
    }

    public static function uninstall()
    {
    }

    public function activate()
    {
      add_option('zxl_reorder_post', '1');
    }

    public function reorder_posts_section_desc()
    {
      if (
        is_multisite() &&
        is_super_admin() &&
        is_plugin_active_for_network($this->basename())
      ) {
        echo __('hello', $this->textDomain);
      }
    }

    public function orderbySelect()
    {
      $orderBy = get_site_option($this->orderByOption);

      if (!is_string($orderBy)) {
        $orderBy = 'post_date';
      }
      ?>
<select id="<?php echo $this->orderByOption; ?>" name="<?php echo $this->orderByOption; ?>">
<?php foreach ($this->allowed_keys as $key => $value) {
  $selected = $value === $orderBy ? 'selected ' : '';
  echo '<option ' .
    $selected .
    'value="' .
    $value .
    '">' .
    $value .
    '</option>';
} ?>
    </select>
    <?php
    }
    /**
     * Includes desc asc radio
     */
    public function orderRadio()
    {
      $order = get_site_option($this->orderOption);

      if (!is_string($order)) {
        $order = 'DESC';
      }
      ?>
      <p>
        <label>
          <input id="<?php echo $this->orderOption; ?>" <?php echo $order === 'DESC'
            ? 'checked'
            : ''; ?> name="<?php echo $this->orderOption; ?>" type="radio" value="DESC">DESC
        </label><br>

        <label>
          <input id="<?php echo $this->orderOption; ?>" <?php echo $order === 'ASC'
            ? 'checked'
            : ''; ?> name="<?php echo $this->orderOption; ?>" type="radio" value="ASC">ASC
        </label>
      </p>
      <?php
    }

    public function admin_init()
    {
      global $pagenow;

      $page = 'reading';
      $section = 'reorder-posts-section';

      add_settings_section(
        $section,
        _x(
          'Reorder Posts',
          'Text string for settings page',
          'reorder-wp-posts'
        ),
        array($this, 'reorder_posts_section_desc'),
        $page
      );

      add_settings_field(
        $this->orderByOption,
        '<label for="' .
          $this->orderByOption .
          '">' .
          __('Order by', $this->textDomain) .
          '</label>',
        array($this, 'orderbySelect'),
        $page,
        $section
      );

      add_settings_field(
        $this->orderOption,
        '<label for="' .
          $this->orderOption .
          '">' .
          __('Order', $this->textDomain) .
          '</lable>',
        array($this, 'orderRadio'),
        $page,
        $section
      );

      if (isset($_POST[$this->orderOption]) && $pagenow === 'options.php') {
        $order = $_POST[$this->orderOption];
        $orderBy = $_POST[$this->orderByOption];

        if (in_array($order, $this->orderArray)) {
          update_option($this->orderOption, $order);
        }

        if (in_array($orderBy, $this->allowed_keys)) {
          update_option($this->orderByOption, $orderBy);
        }
      }
    }
  }

  new WP_Reorder_Posts();
}
