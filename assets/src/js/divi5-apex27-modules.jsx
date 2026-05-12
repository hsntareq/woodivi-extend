import React from 'react';
import { registerModule } from '@divi/module-library';
import { ServerRenderedModule } from '@divi/module';

// Import metadata from module.json files
import listingsMetadata from '../../../modules/apex27-listings/module.json';
import searchFormMetadata from '../../../modules/apex27-search-form/module.json';

const hooks = window.vendor?.wp?.hooks;

if (hooks && hooks.addAction) {
	hooks.addAction('divi.moduleLibrary.registerModuleLibraryStore.after', 'woodivi-extend/apex27-modules', () => {
		
		// Register Listings Module
		registerModule(listingsMetadata, {
			renderers: {
				edit: (props) => <ServerRenderedModule {...props} />
			}
		});

		// Register Search Form Module
		registerModule(searchFormMetadata, {
			renderers: {
				edit: (props) => <ServerRenderedModule {...props} />
			}
		});

		console.log('WooDivi Extend: Apex27 Divi 5 modules successfully registered via registerModule API with metadata.');
	});
}
