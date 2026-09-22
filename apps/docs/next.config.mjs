import { createMDX } from 'fumadocs-mdx/next';

const isProduction = process.env.NODE_ENV === 'production';

/** @type {import('next').NextConfig} */
const config = {
	reactStrictMode: true,
	output: 'export',
	trailingSlash: true,
	images: { unoptimized: true },
	basePath: isProduction ? '/promptbridge-for-divi' : '',
};

export default createMDX()(config);
