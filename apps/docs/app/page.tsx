import {
	ArrowRight,
	ArrowUpRight,
	Check,
	CircleAlert,
	LockKeyhole,
	Server,
	ShieldCheck,
} from 'lucide-react';
import Link from 'next/link';

const safeguards = [
	{
		number: '01',
		icon: Server,
		title: 'Your host owns the runtime',
		copy: 'The site owner installs Codex and configures its private home. PromptBridge never installs or signs in to Codex.',
	},
	{
		number: '02',
		icon: LockKeyhole,
		title: 'An admin opts in first',
		copy: 'An administrator reviews the service disclosure before an editor can request a draft.',
	},
	{
		number: '03',
		icon: ShieldCheck,
		title: 'You decide what gets applied',
		copy: 'Generated text appears in a separate preview. The editor chooses when to apply it to the page.',
	},
];

export default function HomePage() {
	return (
		<div className="marketing-shell">
			<div className="marketing-grid" aria-hidden="true" />

			<nav className="marketing-nav" aria-label="Primary navigation">
				<a
					className="marketing-brand"
					href="#top"
					aria-label="PromptBridge home"
				>
					<img src="/icon.svg" alt="" aria-hidden="true" />
					<span>PromptBridge</span>
				</a>
				<div className="marketing-nav-links">
					<a href="#workflow">How it works</a>
					<Link href="/docs/setup">Setup</Link>
					<a
						className="marketing-nav-source"
						href="https://github.com/MrDemonWolf/promptbridge-for-divi"
					>
						GitHub <ArrowUpRight size={15} aria-hidden="true" />
					</a>
				</div>
			</nav>

			<main id="nd-page">
				<header id="top" className="marketing-hero">
					<div className="marketing-hero-copy">
						<p className="marketing-eyebrow">
							<span className="marketing-status-dot" aria-hidden="true" />
							Divi 5 <span aria-hidden="true">/</span> Staging alpha
						</p>
						<h1>
							Prompt in Divi.
							<span>Review before you apply.</span>
						</h1>
						<p className="marketing-lede">
							PromptBridge adds a Codex-powered text workflow to the Divi 5 Text
							module. Your server runs its own Codex install; you review each
							draft before it reaches the page.
						</p>
						<div className="marketing-actions">
							<Link
								className="marketing-button marketing-button-primary"
								href="/docs/setup"
							>
								Explore the setup <ArrowRight size={17} aria-hidden="true" />
							</Link>
							<a
								className="marketing-button marketing-button-secondary"
								href="https://github.com/MrDemonWolf/promptbridge-for-divi"
							>
								View the source <ArrowUpRight size={16} aria-hidden="true" />
							</a>
						</div>
						<p className="marketing-disclosure">
							<ShieldCheck size={19} aria-hidden="true" />
							<span>
								<strong>Know where your text goes.</strong> After administrator
								opt-in, generation sends your prompt and selected Text field
								content to OpenAI through the separately installed Codex
								runtime.{' '}
								<Link href="/docs/security">Read the privacy details.</Link>
							</span>
						</p>
					</div>

					<figure
						className="workflow-illustration"
						aria-label="Illustrative preview of the Divi text generation and review flow"
					>
						<div className="illustration-label">
							<span>Workflow preview</span>
							<span>Illustrative · alpha</span>
						</div>
						<div className="editor-window" aria-hidden="true">
							<div className="editor-window-bar">
								<span className="window-lights">
									<i />
									<i />
									<i />
								</span>
								<span>
									Divi 5 <b>/</b> Text module
								</span>
								<span className="window-indicator">STAGING</span>
							</div>
							<div className="editor-window-body">
								<div className="field-heading">
									<span>BODY FIELD</span>
									<span>TEXT MODULE</span>
								</div>
								<div className="sample-copy">
									<span className="sample-line sample-line-long" />
									<span className="sample-line" />
									<span className="sample-line sample-line-short" />
								</div>
								<div className="prompt-field">
									<span>PROMPT</span>
									<strong>Make this warmer and more concise.</strong>
									<span className="prompt-send">
										<ArrowUpRight size={16} />
									</span>
								</div>
								<div className="generation-rail">
									<span />
									<small>Codex draft</small>
									<span />
								</div>
								<div className="draft-card">
									<div className="draft-card-heading">
										<span>PREVIEW</span>
										<span className="review-chip">
											<Check size={12} /> Ready to review
										</span>
									</div>
									<span className="sample-line sample-line-long" />
									<span className="sample-line sample-line-mid" />
									<div className="draft-actions">
										<span>Review draft</span>
										<strong>
											Apply to page <ArrowRight size={14} />
										</strong>
									</div>
								</div>
							</div>
						</div>
						<figcaption>
							Illustrative UI preview; the staging interface may change.
						</figcaption>
					</figure>
				</header>

				<section
					className="workflow-section"
					id="workflow"
					aria-labelledby="workflow-title"
				>
					<div className="section-heading">
						<p className="marketing-eyebrow">01 / A deliberate path</p>
						<h2 id="workflow-title">
							Useful AI starts with <em>clear boundaries.</em>
						</h2>
						<p>
							Three checks keep the host, administrator, and editor in control
							of the workflow.
						</p>
					</div>
					<ol className="safeguard-grid">
						{safeguards.map((step) => {
							const Icon = step.icon;
							return (
								<li key={step.number}>
									<div className="safeguard-topline">
										<span>{step.number}</span>
										<Icon size={21} aria-hidden="true" />
									</div>
									<h3>{step.title}</h3>
									<p>{step.copy}</p>
								</li>
							);
						})}
					</ol>
				</section>

				<section className="alpha-section" aria-labelledby="alpha-title">
					<div className="alpha-copy">
						<p className="marketing-eyebrow">02 / Current scope</p>
						<h2 id="alpha-title">Built to show its work.</h2>
						<p>
							PromptBridge is an independent staging alpha with a separate Codex
							workflow alongside Divi’s built-in AI Agent. It is for developers
							and site owners who want to inspect the workflow before relying on
							it.
						</p>
						<Link className="text-link" href="/docs/limitations">
							See what is still unproven{' '}
							<ArrowRight size={16} aria-hidden="true" />
						</Link>
					</div>
					<div className="scope-list">
						<div className="scope-row">
							<span className="scope-icon scope-icon-ready">
								<Check size={16} aria-hidden="true" />
							</span>
							<div>
								<h3>Working in the staging alpha</h3>
								<p>
									Text drafts for the Divi 5 Text module, separate preview, and
									a guarded Apply action.
								</p>
							</div>
						</div>
						<div className="scope-row">
							<span className="scope-icon scope-icon-caution">
								<CircleAlert size={16} aria-hidden="true" />
							</span>
							<div>
								<h3>Not a production compatibility claim</h3>
								<p>
									Codex App Server stability, account lifecycle, cPanel, and
									live Divi Undo and save behavior still need verification.
								</p>
							</div>
						</div>
						<div className="scope-row">
							<span className="scope-icon scope-icon-caution">
								<CircleAlert size={16} aria-hidden="true" />
							</span>
							<div>
								<h3>Text only</h3>
								<p>
									Image generation and generated code execution are not
									included.
								</p>
							</div>
						</div>
					</div>
				</section>

				<section className="marketing-cta" aria-labelledby="cta-title">
					<div>
						<p className="marketing-eyebrow">Start with the details</p>
						<h2 id="cta-title">See the setup. Read the limits. Then decide.</h2>
					</div>
					<div className="cta-actions">
						<Link
							className="marketing-button marketing-button-primary"
							href="/docs/setup"
						>
							Read the setup guide <ArrowRight size={17} aria-hidden="true" />
						</Link>
						<Link className="text-link" href="/docs/security">
							Privacy and security
						</Link>
					</div>
				</section>
			</main>

			<footer className="marketing-footer">
				<div className="footer-main">
					<a
						className="marketing-brand"
						href="#top"
						aria-label="PromptBridge home"
					>
						<img src="/icon.svg" alt="" aria-hidden="true" />
						<span>PromptBridge</span>
					</a>
					<p>
						© 2026 PromptBridge by{' '}
						<a href="https://www.mrdemonwolf.com">MrDemonWolf, Inc.</a>
					</p>
					<nav aria-label="Footer navigation">
						<Link href="/docs">Docs</Link>
						<Link href="/docs/security">Security</Link>
						<a href="https://github.com/MrDemonWolf/promptbridge-for-divi">
							GitHub
						</a>
					</nav>
				</div>
				<p className="trademark-note">
					Divi is a registered trademark of Elegant Themes, Inc. PromptBridge is
					not affiliated with nor endorsed by Elegant Themes.
				</p>
			</footer>
		</div>
	);
}
