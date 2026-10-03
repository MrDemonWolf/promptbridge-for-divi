/* global MDW_PBD_EDITOR */
(function () {
	'use strict';

	if (!window.vendor?.wp?.element || !window.vendor?.wp?.hooks || !window.divi?.data) {
		return;
	}

	const { createElement, Fragment } = window.vendor.wp.element;
	const { addFilter } = window.vendor.wp.hooks;
	const TooltipContainer = window.divi.tooltip?.TooltipContainer;
	const MODULE = 'divi/text';
	const ATTRIBUTE = 'content.innerContent';

	const attrText = (attribute) => {
		if (!attribute) return '';
		if (typeof attribute.getIn === 'function') return String(attribute.getIn(['desktop', 'value']) || '');
		return String(attribute.desktop?.value || '');
	};

	const getCurrentText = (moduleId) => {
		const attribute = window.divi.data.select('divi/edit-post').getModuleAttr(moduleId, ATTRIBUTE);
		return attrText(attribute);
	};

	const digest = async (value) => {
		const bytes = new TextEncoder().encode(value);
		const hash = await window.crypto.subtle.digest('SHA-256', bytes);
		return Array.from(new Uint8Array(hash), (byte) => byte.toString(16).padStart(2, '0')).join('');
	};

	const request = async (url, options) => {
		const response = await fetch(url, {
			...options,
			credentials: 'same-origin',
			headers: {
				'Content-Type': 'application/json',
				'X-WP-Nonce': MDW_PBD_EDITOR.nonce,
				...(options?.headers || {}),
			},
		});
		const data = await response.json();
		if (!response.ok) throw new Error(data.message || 'PromptBridge request failed.');
		return data;
	};

	const waitForResult = async (jobId) => {
		for (let attempt = 0; attempt < 60; attempt += 1) {
			const job = await request(`${MDW_PBD_EDITOR.jobsUrl}/${jobId}`, { method: 'GET' });
			if (job.state === 'complete') return job.result;
			if (job.state === 'failed') throw new Error(job.error || 'Codex could not generate text.');
			await new Promise((resolve) => window.setTimeout(resolve, 2000));
		}
		throw new Error('Generation is still queued. Check WordPress Cron and try again.');
	};

	const showPreview = (moduleId, baseline, generated) => {
		const overlay = document.createElement('div');
		overlay.setAttribute('role', 'dialog');
		overlay.setAttribute('aria-modal', 'true');
		overlay.setAttribute('aria-label', 'Review generated text');
		Object.assign(overlay.style, {
			position: 'fixed', inset: '0', zIndex: '2147483000', background: 'rgba(0,0,0,.55)',
			display: 'grid', placeItems: 'center', padding: '24px',
		});

		const panel = document.createElement('section');
		Object.assign(panel.style, {
			width: 'min(760px, 100%)', maxHeight: '90vh', overflow: 'auto', background: '#fff',
			borderRadius: '8px', padding: '20px', color: '#091533', boxShadow: '0 12px 48px rgba(0,0,0,.3)',
		});
		const heading = document.createElement('h2');
		heading.textContent = 'Review generated text';
		const note = document.createElement('p');
		note.textContent = 'Edit the draft here. It will not change the page until you choose Apply.';
		const textarea = document.createElement('textarea');
		textarea.value = generated;
		textarea.setAttribute('aria-label', 'Generated text preview');
		Object.assign(textarea.style, { display: 'block', width: '100%', minHeight: '240px', margin: '12px 0' });
		const error = document.createElement('p');
		error.setAttribute('role', 'alert');
		error.style.color = '#b32d2e';
		const cancel = document.createElement('button');
		cancel.type = 'button';
		cancel.textContent = 'Cancel';
		const apply = document.createElement('button');
		apply.type = 'button';
		apply.textContent = 'Apply to Divi field';
		apply.style.marginLeft = '8px';
		const close = () => overlay.remove();
		cancel.addEventListener('click', close);
		apply.addEventListener('click', () => {
			if (getCurrentText(moduleId) !== baseline) {
				error.textContent = 'This field changed while generation ran. Cancel and generate again to avoid overwriting your edits.';
				return;
			}
			window.divi.data.dispatch('divi/edit-post').editModuleAttribute({
				id: moduleId,
				attrName: ATTRIBUTE,
				value: { desktop: { value: textarea.value } },
				subName: 'desktop.value',
			});
			close();
		});
		panel.append(heading, note, textarea, error, cancel, apply);
		overlay.append(panel);
		document.body.append(overlay);
		textarea.focus();
	};

	const generate = async (moduleId) => {
		const instruction = window.prompt('What should Codex change in this Text module?');
		if (!instruction || !instruction.trim()) return;
		const baseline = getCurrentText(moduleId);
		const postId = new URLSearchParams(window.location.search).get('post');
		if (!postId || !window.crypto?.subtle) throw new Error('Could not verify the current Divi page.');
		const created = await request(MDW_PBD_EDITOR.jobsUrl, {
			method: 'POST',
			body: JSON.stringify({
				postId: Number(postId),
				moduleId,
				prompt: instruction.trim(),
				content: baseline,
				baselineDigest: await digest(baseline),
			}),
		});
		const result = await waitForResult(created.id);
		showPreview(moduleId, baseline, result);
	};

	addFilter('divi.module.modal.field.labelButtons', 'mdw-promptbridge', (defaultButtons, context) => {
		if (context.moduleName !== MODULE || context.attrName !== ATTRIBUTE) return defaultButtons;
		const tooltipId = `mdw-pbd-field-${context.fieldWrapperId}`;
		return createElement(
			Fragment,
			null,
			defaultButtons,
			createElement(
				'button',
				{
					type: 'button',
					className: 'et-vb-field-button',
					'aria-label': 'Generate with PromptBridge',
					tabIndex: -1,
					'data-tooltip-id': tooltipId,
					onClick: () => generate(context.moduleId).catch((error) => window.alert(error.message)),
				},
				'✦',
				TooltipContainer && createElement(TooltipContainer, { id: tooltipId, windowLocation: 'top' }, 'Generate text with Codex')
			)
		);
	});
})();
