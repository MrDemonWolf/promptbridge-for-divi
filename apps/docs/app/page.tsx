import {
	ArrowUpRight,
	BookOpen,
	Check,
	GitBranch,
	ShieldCheck,
} from 'lucide-react';
import Link from 'next/link';

const basePath =
	process.env.NODE_ENV === 'production' ? '/promptbridge-for-divi' : '';

const capabilities = [
	{
		label: 'Host',
		title: 'Your server owns the runtime',
		copy: 'Codex stays independently installed, pinned, and private to the host.',
	},
	{
		label: 'Models',
		title: 'The catalog stays current',
		copy: 'Diagnostics discover supported models and keep Luna as the safe fallback.',
	},
	{
		label: 'Consent',
		title: 'Nothing happens silently',
		copy: 'OpenAI contact remains gated behind an administrator’s explicit consent.',
	},
];

export default function HomePage() {
	return (
		<main id="nd-page" className="home-shell">
			<div className="signal-grid" aria-hidden="true" />

			<nav className="home-nav" aria-label="Primary navigation">
				<a
					className="home-brand"
					href="#top"
					aria-label="PromptBridge for Divi home"
				>
					<img
						className="brand-icon"
						src={`${basePath}/icon.svg`}
						alt=""
						aria-hidden="true"
					/>
					<span>PromptBridge</span>
				</a>
				<div className="home-nav-links">
					<a href="#current-status">Overview</a>
					<Link href="/docs">Docs</Link>
					<a href="https://github.com/MrDemonWolf/promptbridge-for-divi">
						GitHub
						<ArrowUpRight aria-hidden="true" size={14} />
					</a>
				</div>
			</nav>

			<header id="top" className="home-hero">
				<div className="home-copy">
					<p className="eyebrow">
						<span className="signal-dot" aria-hidden="true" />
						Staging alpha · Local-first
					</p>
					<h1>
						Build in Divi.
						<span>Prompt with Codex.</span>
					</h1>
					<p className="home-summary">
						A guarded bridge between Divi 5 and an independently installed Codex
						runtime—designed to prove the boundary before it generates a word.
					</p>
					<div className="home-actions">
						<Link className="primary-link" href="/docs">
							<BookOpen aria-hidden="true" size={18} />
							Read the docs
						</Link>
						<a href="https://github.com/MrDemonWolf/promptbridge-for-divi">
							<GitBranch aria-hidden="true" size={18} />
							View repository
							<ArrowUpRight aria-hidden="true" size={16} />
						</a>
					</div>
					<ul className="hero-meta" aria-label="Project qualities">
						<li>
							<Check aria-hidden="true" size={15} /> Local-first
						</li>
						<li>
							<Check aria-hidden="true" size={15} /> Consent-gated
						</li>
						<li>
							<Check aria-hidden="true" size={15} /> Open source
						</li>
					</ul>
				</div>

				<div
					className="runtime-preview"
					aria-label="PromptBridge connection preview"
				>
					<div className="preview-titlebar">
						<span aria-hidden="true">
							<i />
							<i />
							<i />
						</span>
						<strong>Runtime check</strong>
						<small>
							<span aria-hidden="true" /> Ready
						</small>
					</div>
					<div className="preview-body">
						<div className="preview-card">
							<img
								src={`${basePath}/brands/wordpress.svg`}
								alt=""
								aria-hidden="true"
							/>
							<span>
								<small>WordPress</small>
								<strong>Divi 5</strong>
							</span>
							<em>Licensed test site</em>
						</div>

						<div className="preview-rail" aria-hidden="true">
							<span />
							<strong>PromptBridge</strong>
							<ShieldCheck size={16} />
						</div>

						<div className="preview-card preview-card--codex">
							<span className="codex-mark">
								<img
									src={`${basePath}/brands/codex.svg`}
									alt=""
									aria-hidden="true"
								/>
							</span>
							<span>
								<small>Local runtime</small>
								<strong>Codex</strong>
							</span>
							<em>0.155.1</em>
						</div>
					</div>
					<div className="preview-status">
						<span>
							<Check aria-hidden="true" size={15} /> 9 checks passing
						</span>
						<span>Model: Luna</span>
					</div>
				</div>
			</header>

			<section className="status-panel" aria-labelledby="current-status">
				<div className="status-heading">
					<p className="section-index">01 / Current build</p>
					<h2 id="current-status">The safe foundation is live.</h2>
					<p>
						Activation, diagnostics, model discovery, and cleanup are working on
						the licensed Divi test site.
					</p>
				</div>
				<ul className="status-grid">
					{capabilities.map((item) => (
						<li key={item.label}>
							<span>{item.label}</span>
							<strong>{item.title}</strong>
							<p>{item.copy}</p>
						</li>
					))}
				</ul>
			</section>

			<section className="home-cta" aria-labelledby="next-step">
				<div>
					<p className="section-index">02 / Go deeper</p>
					<h2 id="next-step">See exactly what the alpha proves.</h2>
					<p>
						Follow the setup, inspect the security boundary, and reproduce the
						local test results.
					</p>
				</div>
				<Link className="primary-link" href="/docs">
					Open documentation
					<ArrowUpRight aria-hidden="true" size={17} />
				</Link>
			</section>

			<footer className="home-footer">
				<span className="footer-watermark" aria-hidden="true">
					PromptBridge
				</span>
				<div className="footer-row">
					<p>
						© 2026 PromptBridge for Divi by{' '}
						<a href="https://www.mrdemonwolf.com">MrDemonWolf, Inc.</a>
					</p>
					<nav aria-label="Footer navigation">
						<Link href="/docs">Docs</Link>
						<a href="https://github.com/MrDemonWolf/promptbridge-for-divi">
							GitHub
						</a>
						<Link href="/docs/security">Security</Link>
					</nav>
				</div>
				<p className="trademark-note">
					Divi is a registered trademark of Elegant Themes, Inc. This website is
					not affiliated with nor endorsed by Elegant Themes.
				</p>
			</footer>
		</main>
	);
}
