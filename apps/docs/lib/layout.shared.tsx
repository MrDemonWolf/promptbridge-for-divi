import type { BaseLayoutProps } from 'fumadocs-ui/layouts/shared';

const basePath =
	process.env.NODE_ENV === 'production' ? '/promptbridge-for-divi' : '';

export function baseOptions(): BaseLayoutProps {
	return {
		nav: {
			title: (
				<span className="docs-brand">
					<img
						className="brand-icon"
						src={`${basePath}/icon.svg`}
						alt=""
						aria-hidden="true"
					/>
					<span className="brand-wordmark">PromptBridge for Divi</span>
				</span>
			),
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
