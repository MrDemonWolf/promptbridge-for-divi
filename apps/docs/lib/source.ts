import { loader } from 'fumadocs-core/source';
import { docs } from '@/.source/server';

const raw = docs.toFumadocsSource();
const files =
	typeof raw.files === 'function'
		? (raw.files as unknown as () => typeof raw.files)()
		: raw.files;

export const source = loader({
	baseUrl: '/docs',
	// Fumadocs versions expose the same data through two compatible shapes.
	// eslint-disable-next-line @typescript-eslint/no-explicit-any
	source: { ...raw, files } as any,
});
