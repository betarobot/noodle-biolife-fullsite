<?php
// $Id: page.tpl.php,v 1.47 2010/11/05 01:25:33 dries Exp $

/**
 * @file
 * Default theme implementation to display a single Drupal page.
 *
 * Available variables:
 *
 * General utility variables:
 * - $base_path: The base URL path of the Drupal installation. At the very
 *   least, this will always default to /.
 * - $directory: The directory the template is located in, e.g. modules/system
 *   or themes/bartik.
 * - $is_front: TRUE if the current page is the front page.
 * - $logged_in: TRUE if the user is registered and signed in.
 * - $is_admin: TRUE if the user has permission to access administration pages.
 *
 * Site identity:
 * - $front_page: The URL of the front page. Use this instead of $base_path,
 *   when linking to the front page. This includes the language domain or
 *   prefix.
 * - $logo: The path to the logo image, as defined in theme configuration.
 * - $site_name: The name of the site, empty when display has been disabled
 *   in theme settings.
 * - $site_slogan: The slogan of the site, empty when display has been disabled
 *   in theme settings.
 *
 * Navigation:
 * - $main_menu (array): An array containing the Main menu links for the
 *   site, if they have been configured.
 * - $secondary_menu (array): An array containing the Secondary menu links for
 *   the site, if they have been configured.
 * - $breadcrumb: The breadcrumb trail for the current page.
 *
 * Page content (in order of occurrence in the default page.tpl.php):
 * - $title_prefix (array): An array containing additional output populated by
 *   modules, intended to be displayed in front of the main title tag that
 *   appears in the template.
 * - $title: The page title, for use in the actual HTML content.
 * - $title_suffix (array): An array containing additional output populated by
 *   modules, intended to be displayed after the main title tag that appears in
 *   the template.
 * - $messages: HTML for status and error messages. Should be displayed
 *   prominently.
 * - $tabs (array): Tabs linking to any sub-pages beneath the current page
 *   (e.g., the view and edit tabs when displaying a node).
 * - $action_links (array): Actions local to the page, such as 'Add menu' on the
 *   menu administration interface.
 * - $feed_icons: A string of all feed icons for the current page.
 * - $node: The node object, if there is an automatically-loaded node
 *   associated with the page, and the node ID is the second argument
 *   in the page's path (e.g. node/12345 and node/12345/revisions, but not
 *   comment/reply/12345).
 *
 * Regions:
 * - $page['help']: Dynamic help text, mostly for admin pages.
 * - $page['highlighted']: Items for the highlighted content region.
 * - $page['content']: The main content of the current page.
 * - $page['sidebar_first']: Items for the first sidebar.
 * - $page['sidebar_second']: Items for the second sidebar.
 * - $page['header']: Items for the header region.
 * - $page['footer']: Items for the footer region.
 *
 * @see template_preprocess()
 * @see template_preprocess_page()
 * @see template_process()
 */
?>

  <div id="page-wrapper"><div id="page">
  
    <?php print render($page['header_messages']); ?>
    
  <div id="header-wrapper">
    <div id="header"><div class="section clearfix">

      <div class="navigation section">
      
        <?php if ($logo): ?>
          <div id="logo">
            <a href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>" rel="home" class="logo">
              <img src="<?php print $logo; ?>" alt="<?php print t('Home'); ?>" />
            </a>
          </div>
        <?php endif; ?>

        <?php print render($page['header_navigation']); ?>

      </div> <!-- /.section, /#navigation -->
      
      <?php print render($page['header_top']); ?>
      <?php if ($page['header_blocks_1']||$page['header_blocks_2']||$page['header_blocks_3']): ?>
        <div class="region-header-blocks">
          <?php print render($page['header_blocks_1']); ?>
          <?php print render($page['header_blocks_2']); ?>
          <?php print render($page['header_blocks_3']); ?>
        </div><!-- /.region-header-blocks -->
      <?php endif; ?>
      <?php print render($page['header_bottom']); ?>

    </div></div> <!-- /.section, /#header -->
  </div> <!--  /#header-wrapper -->

    <div id="main-wrapper"><div id="main" class="clearfix">

      <div id="content" class="column">
        
        <a id="main-content"></a>

        <?php print $messages; ?>

        <?php print render($title_prefix); ?>
        <?php if ($title): ?><h1 class="title" id="page-title"><?php print $title; ?></h1><?php endif; ?>
        <?php print render($title_suffix); ?>
        <?php if ($tabs): ?><div class="tabs"><?php print render($tabs); ?></div><?php endif; ?>
        <?php print render($page['help']); ?>
        <?php if ($action_links): ?><ul class="action-links"><?php print render($action_links); ?></ul><?php endif; ?>

        <?php print render($page['content_top']); ?>
        <?php print render($page['content']); ?>
        <?php print render($page['content_bottom']); ?>
        <div class="feed-icons-wrapper"><?php print $feed_icons; ?></div>
      </div> <!-- /#content -->
      
      <?php if ($page['sidebar_first']||$page['sidebar_accordion']||$page['sidebar_second']): ?>
        <div id="sidebar-first" class="column sidebar">
          <?php print render($page['sidebar_first']); ?>
          <?php print render($page['sidebar_accordion']); ?>
          <?php print render($page['sidebar_second']); ?>
        </div> <!-- /.section, /#sidebar-first -->
      <?php endif; ?>

    </div></div> <!-- /#main, /#main-wrapper -->

    <div id="footer-wrapper" class="section">
      <div id="footer">
        <?php print render($page['footer_top']); ?>
        <?php if ($page['footer_blocks_1']||$page['footer_blocks_2']||$page['footer_blocks_3']): ?>
          <div class="region-footer-blocks">
            <?php print render($page['footer_blocks_1']); ?>
            <?php print render($page['footer_blocks_2']); ?>
            <?php print render($page['footer_blocks_3']); ?>
          </div><!-- /.region-footer-blocks -->
        <?php endif; ?>
        <?php print render($page['footer_bottom']); ?>
      </div><!-- /#footer -->
    </div> <!-- /#footer-wrapper, /.section -->
  
    <?php if ($page['message_bottom']): ?>
      <div id="message-bottom" class="section">
        
        <span class="close-meerkat"><?php print t('close');?></span>
        <span class="forget-meerkat"><?php print t('forget');?></span>
        
        <?php print render($page['message_bottom']); ?>
      </div> <!-- /.section, /#message-bottom -->
    <?php endif; ?>
    
    
  </div></div> <!-- /#page, /#page-wrapper -->
