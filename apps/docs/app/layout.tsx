import { RootProvider } from 'fumadocs-ui/provider/next';
import type { Metadata } from 'next';
import { Bricolage_Grotesque, IBM_Plex_Sans } from 'next/font/google';
import type { ReactNode } from 'react';
import './global.css';
import './marketing.css';

const bricolageGrotesque = Bricolage_Grotesque({
	display: 'swap',
	subsets: ['latin'],
	variable: '--font-bricolage-grotesque',
});

const ibmPlexSans = IBM_Plex_Sans({
	display: 'swap',
	subsets: ['latin'],
	weight: ['400', '500', '600', '700'],
	variable: '--font-ibm-plex-sans',
});

const description =
	'PromptBridge for Divi is a staging-alpha WordPress plugin for drafting and reviewing AI text in Divi 5 with a server-managed Codex runtime.';

export const metadata: Metadata = {
	metadataBase: new URL('https://promptbridge.mrdemonwolf.dev/'),
	title: {
		default: 'PromptBridge for Divi | AI Text Drafting',
		template: '%s | PromptBridge',
	},
	description,
	openGraph: {
		title: 'PromptBridge for Divi | AI Text Drafting',
		description,
		type: 'website',
		siteName: 'PromptBridge for Divi',
	},
};

export default function RootLayout({ children }: { children: ReactNode }) {
	return (
		<html
			lang="en"
			className={`${bricolageGrotesque.variable} ${ibmPlexSans.variable}`}
			suppressHydrationWarning
		>
			<body>
				<a href="#nd-page" className="skip-link">
					Skip to content
				</a>
				<RootProvider
					search={{ enabled: false }}
					theme={{ defaultTheme: 'system' }}
				>
					{children}
				</RootProvider>
			</body>
		</html>
	);
}
