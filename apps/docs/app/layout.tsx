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
	'Draft text for Divi 5 with a server-owned Codex runtime, administrator opt-in, and a separate review before applying.';

export const metadata: Metadata = {
	metadataBase: new URL('https://promptbridge.mrdemonwolf.dev/'),
	title: {
		default: 'PromptBridge',
		template: '%s | PromptBridge',
	},
	description,
	openGraph: {
		title: 'PromptBridge',
		description,
		type: 'website',
		siteName: 'PromptBridge',
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
					theme={{ defaultTheme: 'dark' }}
				>
					{children}
				</RootProvider>
			</body>
		</html>
	);
}
