export function mountBilanStepper(root) {
    const steps = Array.from(root.querySelectorAll('[data-step]'));
    const bar = root.querySelector('[data-step-bar]');
    const currentLabel = root.querySelector('[data-step-current]');
    const prev = root.querySelector('[data-step-prev]');
    const next = root.querySelector('[data-step-next]');
    const total = steps.length;
    let index = Number(root.dataset.start || 0);

    if (total === 0) {
        return;
    }

    const answered = (step) => {
        const field = step.querySelector('textarea, input[type="number"], input[type="radio"]:checked');
        if (!field) {
            return false;
        }
        if (field.type === 'radio') {
            return true;
        }

        return String(field.value || '').trim() !== '';
    };

    const paint = () => {
        steps.forEach((step, position) => {
            step.classList.toggle('hidden', position !== index);
        });
        currentLabel.textContent = String(index + 1);
        bar.style.width = `${Math.round(((index + 1) / total) * 100)}%`;
        prev.disabled = index === 0;
        const last = index === total - 1;
        next.textContent = last ? 'Terminer' : 'Suivant';
        next.setAttribute('type', last ? 'submit' : 'button');
        const focusable = steps[index].querySelector('textarea, input:not([type="radio"])');
        focusable?.focus();
    };

    const go = (to) => {
        index = Math.min(total - 1, Math.max(0, to));
        paint();
    };

    prev.addEventListener('click', () => go(index - 1));
    next.addEventListener('click', (event) => {
        if (!answered(steps[index])) {
            event.preventDefault();
            steps[index].querySelector('textarea, input')?.reportValidity?.();
            const field = steps[index].querySelector('textarea, input[type="number"]');
            if (field && String(field.value || '').trim() === '') {
                field.focus();
            }

            return;
        }
        if (index < total - 1) {
            event.preventDefault();
            go(index + 1);
        }
    });

    root.addEventListener('change', (event) => {
        if (!event.target.hasAttribute('data-step-choice')) {
            return;
        }
        if (index < total - 1) {
            window.setTimeout(() => go(index + 1), 160);
        } else {
            paint();
        }
    });

    paint();
}
