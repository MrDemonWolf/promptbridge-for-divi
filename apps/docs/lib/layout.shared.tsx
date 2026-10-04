import type { BaseLayoutProps } from 'fumadocs-ui/layouts/shared';
import { BrandLockup } from './brand-lockup';

export function baseOptions(): BaseLayoutProps {
	return {
		nav: {
			title: <BrandLockup className="docs-brand" />,
			url: '/',
		},
		githubUrl: 'https://github.com/MrDemonWolf/promptbridge-for-divi',
		links: [
			{ text: 'Overview', url: '/docs', active: 'nested-url' },
			{ text: 'Setup', url: '/docs/setup', active: 'url' },
			{ text: 'Security', url: '/docs/security', active: 'url' },
			{ text: 'CI/CD', url: '/docs/ci-cd', active: 'url' },
		],
		themeSwitch: { enabled: true, mode: 'light-dark-system' },
	};
}
