type BrandLockupProps = {
	className?: string;
};

/** Pair the existing PromptBridge mark with a consistent product wordmark. */
export function BrandLockup({ className = '' }: BrandLockupProps) {
	return (
		<span className={`brand-lockup ${className}`.trim()}>
			<img
				className="brand-lockup-mark"
				src="/icon.svg"
				alt=""
				aria-hidden="true"
			/>
			<span className="brand-lockup-copy">
				<span className="brand-lockup-name">
					Prompt<span className="brand-lockup-accent">Bridge</span>
				</span>
				<span className="brand-lockup-descriptor">For Divi</span>
			</span>
		</span>
	);
}
