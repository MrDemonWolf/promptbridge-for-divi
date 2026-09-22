import {
	DocsBody,
	DocsDescription,
	DocsPage,
	DocsTitle,
} from 'fumadocs-ui/page';
import type { Metadata } from 'next';
import { notFound } from 'next/navigation';
import { source } from '@/lib/source';
import { getMDXComponents } from '@/mdx-components';

interface Props {
	params: Promise<{ slug?: string[] }>;
}

export default async function Page({ params }: Props) {
	const { slug } = await params;
	const page = source.getPage(slug);
	if (!page) notFound();

	// Generated Fumadocs fields are wider than its public loader type.
	// eslint-disable-next-line @typescript-eslint/no-explicit-any
	const data = page.data as any;
	const MDX = data.body;

	return (
		<DocsPage toc={data.toc} full={data.full}>
			<DocsTitle>{page.data.title}</DocsTitle>
			<DocsDescription>{page.data.description}</DocsDescription>
			<DocsBody>
				<MDX components={getMDXComponents()} />
			</DocsBody>
		</DocsPage>
	);
}

export async function generateStaticParams() {
	return source.generateParams();
}

export async function generateMetadata({ params }: Props): Promise<Metadata> {
	const { slug } = await params;
	const page = source.getPage(slug);
	if (!page) notFound();

	return { title: page.data.title, description: page.data.description };
}
