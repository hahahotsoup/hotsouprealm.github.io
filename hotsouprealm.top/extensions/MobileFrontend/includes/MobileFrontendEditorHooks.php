<?php

use MediaWiki\MediaWikiServices;
use MediaWiki\ResourceLoader\ResourceLoader;

class MobileFrontendEditorHooks {
	/**
	 * Generate config for usage inside MobileFrontend
	 * This should be used for variables which:
	 *  - vary with the html
	 *  - variables that should work cross skin including anonymous users
	 *  - used for both, stable and beta mode (don't use
	 *    MobileContext::isBetaGroupMember in this function - T127860)
	 *
	 * @return array
	 */
	public static function getResourceLoaderMFConfigVars() {
		$config = MediaWikiServices::getInstance()->getService( 'MobileFrontend.Config' );
		$extensionRegistry = ExtensionRegistry::getInstance();

		return [
			// schemaEditAttemptStep.js
			'wgMFSchemaEditAttemptStepOversample' => $config->get( 'MFSchemaEditAttemptStepOversample' ),
			// MFDefaultEditor should be `source`, `visual`, or `preference`
			// `preference` means to fall back on the desktop `visualeditor-editor` setting (if VE has been used)
			// editor.js
			'wgMFDefaultEditor' => $config->get( 'MFDefaultEditor' ),
			// wgMFEditorAvailableSkins is defined by skins and means that the skins defined their
			// MobileFrontend-specific styles, so users can edit on mobile with the skins.
			'wgMFEditorAvailableSkins' => $extensionRegistry->getAttribute( 'MobileFrontendEditorAvailableSkins' ),
		];
	}

	/**
	 * Handler for MakeGlobalVariablesScript hook.
	 * For values that depend on the current page, user or request state.
	 *
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/MakeGlobalVariablesScript
	 * @param array &$vars Variables to be added into the output
	 * @param OutputPage $out OutputPage instance calling the hook
	 */
	public static function onMakeGlobalVariablesScript( array &$vars, OutputPage $out ) {
		$title = $out->getTitle();
		$context = MediaWikiServices::getInstance()->getService( 'MobileFrontend.Context' );

		if ( $context->shouldDisplayMobileView() ) {
			// mobile.init
			$vars['wgMFIsPageContentModelEditable'] = self::isPageContentModelEditable( $title );
			// Accesses getBetaGroupMember so does not belong in onResourceLoaderGetConfigVars
		}
	}

	/**
	 * ResourceLoaderRegisterModules hook handler.
	 *
	 * Registers:
	 *
	 * * Modules for the Visual Editor overlay, if the VisualEditor extension is loaded
	 *
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/ResourceLoaderRegisterModules
	 *
	 * @param ResourceLoader $resourceLoader
	 */
	public static function onResourceLoaderRegisterModules( ResourceLoader $resourceLoader ) {
		$resourceBoilerplate = [
			'localBasePath' => dirname( __DIR__ ),
			'remoteExtPath' => 'MobileFrontend',
		];

		// add VisualEditor related modules only, if VisualEditor seems to be installed - T85007
		if ( ExtensionRegistry::getInstance()->isLoaded( 'VisualEditor' ) ) {
			$resourceLoader->register( [
				'mobile.editor.ve' => $resourceBoilerplate + [
					'dependencies' => [
						'ext.visualEditor.mobileArticleTarget',
						'mobile.editor.overlay',
						'mobile.startup',
					],
					'scripts' => 'resources/dist/mobile.editor.ve.js',
					'targets' => [
						'mobile',
					],
				],
			] );
		}
	}

	/**
	 * Checks whether the editor can handle the existing content handler type.
	 *
	 * @param Title $title
	 * @return bool
	 */
	protected static function isPageContentModelEditable( Title $title ): bool {
		$contentHandler = MediaWikiServices::getInstance()->getContentHandlerFactory()
			->getContentHandler( $title->getContentModel() );

		return $contentHandler->supportsDirectEditing()
			&& $contentHandler->supportsDirectApiEditing();
	}

}
