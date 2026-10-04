const siteUrl = 'https://promptbridge.mrdemonwolf.dev/';
const logoUrl = `${siteUrl}icon.svg`;
const githubUrl = 'https://github.com/MrDemonWolf/promptbridge-for-divi';

export const promptBridgeStructuredData = {
	'@context': 'https://schema.org',
	'@graph': [
		{
			'@type': 'WebSite',
			'@id': `${siteUrl}#website`,
			url: siteUrl,
			name: 'PromptBridge',
			alternateName: 'PromptBridge for Divi',
			publisher: { '@id': `${siteUrl}#organization` },
			about: { '@id': `${siteUrl}#software` },
		},
		{
			'@type': 'Organization',
			'@id': `${siteUrl}#organization`,
			name: 'MrDemonWolf, Inc.',
			url: 'https://www.mrdemonwolf.com/',
			brand: { '@id': `${siteUrl}#brand` },
		},
		{
			'@type': 'Brand',
			'@id': `${siteUrl}#brand`,
			name: 'PromptBridge',
			alternateName: 'PromptBridge for Divi',
			url: siteUrl,
			logo: { '@id': `${siteUrl}#brand-logo` },
			sameAs: githubUrl,
		},
		{
			'@type': 'ImageObject',
			'@id': `${siteUrl}#brand-logo`,
			url: logoUrl,
			width: 512,
			height: 512,
			caption: 'PromptBridge for Divi logo',
		},
		{
			'@type': 'SoftwareApplication',
			'@id': `${siteUrl}#software`,
			name: 'PromptBridge for Divi',
			url: siteUrl,
			description:
				'An independent staging-alpha WordPress plugin for drafting and reviewing AI text in Divi 5 with a separately installed Codex runtime.',
			image: { '@id': `${siteUrl}#brand-logo` },
			brand: { '@id': `${siteUrl}#brand` },
			softwareRequirements:
				'A WordPress site with Divi 5 and a separately installed server-side Codex runtime.',
		},
	],
};
