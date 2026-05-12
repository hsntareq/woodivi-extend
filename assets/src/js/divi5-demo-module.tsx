import { registerModule } from '@divi/module-library';
import { staticModule } from '../../../modules/d5-demo-module/index';

const hooks = window.vendor?.wp?.hooks;

if (hooks && hooks.addAction) {
	hooks.addAction('divi.moduleLibrary.registerModuleLibraryStore.after', 'woodivi-extend/demo-module', () => {
		// Import metadata and pass the module definition excluding the metadata prop
		const { metadata, ...moduleDef } = staticModule;
		registerModule(metadata, moduleDef);
		console.log('WooDivi Extend: Demo Divi 5 module successfully registered via registerModule API.');
	});
}
