module.exports = function () {
	var trackdebug = !!mw.util.getParamValue( 'trackdebug' );

	if ( !mw.config.exists( 'wgWMESchemaEditAttemptStepSamplingRate' ) ) {
		return;
	}

	// VisualEditorFeatureUse is intended to log whenever EditAttemptStep
	// does, so this file references its config for sampling rates and
	// oversampling.

	( function () {
		var // Schema class provided by ext.eventLogging
			Schema = mw.eventLog.Schema,
			user = mw.user,
			sampleRate = mw.config.get( 'wgWMESchemaEditAttemptStepSamplingRate' ),
			/**
			 * Feature use schema
			 * https://meta.wikimedia.org/wiki/Schema:VisualEditorFeatureUse
			 */
			/* eslint-disable camelcase */
			schemaVisualEditorFeatureUse = new Schema(
				'VisualEditorFeatureUse',
				sampleRate,
				{
					user_id: user.getId(),
					user_editcount: mw.config.get( 'wgUserEditCount', 0 ),
					platform: 'phone',
					integration: 'page'
				}
			);
			/* eslint-enable camelcase */

		function log() {
			// mw.log is a no-op unless resource loader is in debug mode, so
			// this allows trackdebug to work independently (T211698)
			// eslint-disable-next-line no-console
			console.log.apply( console, arguments );
		}

		mw.trackSubscribe( 'mf.schemaVisualEditorFeatureUse', function ( topic, data ) {
			var event = {
				feature: data.feature,
				action: data.action,
				editingSessionId: data.editing_session_id,
				// eslint-disable-next-line camelcase
				editor_interface: data.editor_interface
			};

			if ( trackdebug ) {
				log( topic, event, schemaVisualEditorFeatureUse.defaults );
			} else {
				schemaVisualEditorFeatureUse.log( event, (
					mw.config.get( 'wgWMESchemaEditAttemptStepOversample' ) ||
					mw.config.get( 'wgMFSchemaEditAttemptStepOversample' ) === 'visualeditor' ||
					mw.config.get( 'wgMFSchemaEditAttemptStepOversample' ) === 'all'
				) ? 1 : sampleRate );

				// T309602: Also log via the Metrics Platform:
				var eventName = 'vefu.' + data.action;

				/* eslint-disable camelcase */
				var customData = {
					feature: data.feature,
					editing_session_id: data.editing_session_id,
					editor_interface: data.editor_interface,
					integration: 'page'
				};
				/* eslint-enable camelcase */

				mw.eventLog.dispatch( eventName, customData );
			}
		} );

	}() );
};
