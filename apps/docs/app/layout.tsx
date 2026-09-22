import { RootProvider } from 'fumadocs-ui/provider/next';
import type { Metadata } from 'next';
import { Instrument_Sans } from 'next/font/google';
import type { ReactNode } from 'react';
import './global.css';

const instrumentSans = Instrument_Sans({
	display: 'swap',
	subsets: ['latin'],
	variable: '--font-instrument-sans',
});

const description =
	'Setup, architecture, security, and release documentation for PromptBridge for Divi.';

export const metadata: Metadata = {
	metadataBase: new URL('https://mrdemonwolf.github.io/promptbridge-for-divi/'),
	title: {
		default: 'PromptBridge for Divi',
		template: '%s | PromptBridge for Divi',
	},
	description,
	openGraph: {
		title: 'PromptBridge for Divi',
		description,
		type: 'website',
		siteName: 'PromptBridge for Divi',
	},
};

export default function RootLayout({ children }: { children: ReactNode }) {
	return (
		<html
			lang="en"
			className={instrumentSans.variable}
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
