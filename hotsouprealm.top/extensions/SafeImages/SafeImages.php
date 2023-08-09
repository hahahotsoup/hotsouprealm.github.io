<?php
/**
 * SafeImages extension - Allows users to toggle safe images on the homepage.
 *
 * @file
 * @ingroup Extensions
 */

$wgExtensionCredits['other'][] = array(
    'path'           => __FILE__,
    'name'           => 'SafeImages',
    'version'        => '1.0',
    'author'         => 'Your Name',
    'url'            => 'URL to your wiki or extension documentation',
    'descriptionmsg' => 'safeimages-desc',
);

$wgExtensionMessagesFiles['SafeImages'] = __DIR__ . '/SafeImages.i18n.php';

$wgResourceModules['ext.SafeImages'] = array(
    'styles' => 'SafeImages.css',
    'localBasePath' => __DIR__,
    'remoteExtPath' => 'SafeImages',
);

$wgHooks['BeforePageDisplay'][] = 'fnSafeImagesBeforePageDisplay';

function fnSafeImagesBeforePageDisplay( OutputPage &$out, Skin &$skin ) {
    $out->addModules( 'ext.SafeImages' );
    return true;
}
// 添加一个系统消息，用于显示开关的文字
$wgMessagesDirs['SafeImages'] = __DIR__ . '/i18n';

// 在首页添加一个开关来控制是否加载不适宜未成年人的照片
$wgHooks['BeforePageDisplay'][] = function (OutputPage &$out, Skin &$skin) {
    $out->addHTML('<label for="safeImagesToggle">' . wfMessage('safeimages-toggle-label')->text() . '</label>');
    $out->addHTML('<input type="checkbox" id="safeImagesToggle">');
    return true;
};
