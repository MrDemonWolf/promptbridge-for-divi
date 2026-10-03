import { RootProvider } from 'fumadocs-ui/provider/next';
import type { Metadata } from 'next';
import { Instrument_Sans, Instrument_Serif } from 'next/font/google';
import type { ReactNode } from 'react';
import './global.css';
import './marketing.css';

const instrumentSans = Instrument_Sans({
	display: 'swap',
	subsets: ['latin'],
	variable: '--font-instrument-sans',
});

const instrumentSerif = Instrument_Serif({
	display: 'swap',
	style: ['normal', 'italic'],
	subsets: ['latin'],
	weight: '400',
	variable: '--font-instrument-serif',
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
			className={`${instrumentSans.variable} ${instrumentSerif.variable}`}
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
